<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use Gemini\Data\Content;
use Gemini\Data\FunctionCall;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\FunctionResponse;
use Gemini\Data\Part;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

class GeminiFinanceService
{
    /**
     * Ask financial question with function calling capabilities
     */
    public function ask(string $question, array $conversationHistory = []): string
    {
        try {
            // 1. Get tool definitions
            $tool = new Tool($this->getFunctionDeclarations());

            // 2. Build system instruction as Content
            $systemInstruction = Content::parse($this->getSystemInstruction(), Role::MODEL);

            // 3. Generate response with tools
            // Using gemini-2.0-flash (available in free tier)
            $model = Gemini::generativeModel('models/gemini-2.0-flash')
                ->withSystemInstruction($systemInstruction)
                ->withTool($tool);

            // 4. Build content from conversation history + question
            $contents = $this->buildContentArray($question, $conversationHistory);

            $response = $model->generateContent(...$contents);

            // 5. Check for function calls in response parts
            $parts = $response->parts();
            $hasFunctionCalls = false;
            foreach ($parts as $part) {
                if ($part->functionCall !== null) {
                    $hasFunctionCalls = true;
                    break;
                }
            }

            if ($hasFunctionCalls) {
                return $this->handleFunctionCalls($response, $question, $model);
            }

            // 6. Direct text response
            return $response->text();

        } catch (\Exception $e) {
            \Log::error('GeminiFinanceService error', [
                'message' => $e->getMessage(),
                'question' => $question,
                'trace' => $e->getTraceAsString()
            ]);

            // Better error message for quota exceeded
            if (str_contains($e->getMessage(), 'quota') || str_contains($e->getMessage(), 'Quota')) {
                // Extract retry time if available
                if (preg_match('/retry in ([\d.]+)s/', $e->getMessage(), $matches)) {
                    $seconds = ceil((float) $matches[1]);
                    // Format with special marker for countdown
                    return "QUOTA_EXCEEDED:{$seconds}";
                }
                return "⏰ **API Quota Habis**\n\nGemini AI sedang mencapai batas penggunaan gratis. Silakan tunggu beberapa saat dan coba lagi.\n\n💡 *Tips: Gunakan API key berbayar untuk quota unlimited.*";
            }

            return "❌ **Terjadi Kesalahan**\n\n" . $e->getMessage() . "\n\n*Silakan coba pertanyaan lain atau refresh halaman.*";
        }
    }

    /**
     * Build content array from conversation history
     */
    private function buildContentArray(string $question, array $conversationHistory): array
    {
        $contents = [];

        // Add conversation history (limit to last 10 turns = 20 messages)
        if (\count($conversationHistory) > 20) {
            $conversationHistory = \array_slice($conversationHistory, -20);
        }

        foreach ($conversationHistory as $message) {
            $role = $message['role'] === 'user' ? Role::USER : Role::MODEL;
            $contents[] = Content::parse($message['message'], $role);
        }

        // Add current question
        $contents[] = Content::parse($question, Role::USER);

        return $contents;
    }

    /**
     * Handle function calls from Gemini response
     */
    private function handleFunctionCalls($response, string $question, $model): string
    {
        $functionResponseParts = [];

        // Extract function calls from response parts
        foreach ($response->parts() as $part) {
            if ($part->functionCall !== null) {
                $result = $this->executeFunctionCall($part->functionCall);

                // Create function response part
                $functionResponseParts[] = new Part(
                    functionResponse: new FunctionResponse(
                        $part->functionCall->name,
                        $result
                    )
                );
            }
        }

        // Send results back to model for final answer
        $finalResponse = $model->generateContent(
            Content::parse($question, Role::USER),
            new Content($response->parts(), Role::MODEL),
            new Content($functionResponseParts, Role::USER)
        );

        return $finalResponse->text();
    }

    /**
     * Execute function call based on name
     */
    private function executeFunctionCall(FunctionCall $functionCall): array
    {
        $name = $functionCall->name;
        $args = $functionCall->args ?? [];

        return match($name) {
            'get_monthly_revenue' => $this->executeGetMonthlyRevenue(
                $args['month'] ?? now()->month,
                $args['year'] ?? now()->year
            ),
            'get_monthly_expenses' => $this->executeGetMonthlyExpenses(
                $args['month'] ?? now()->month,
                $args['year'] ?? now()->year
            ),
            'get_bank_balance' => $this->executeGetBankBalance($args['account_name'] ?? null),
            'get_outstanding_invoices' => $this->executeGetOutstandingInvoices(),
            default => ['error' => 'Unknown function: ' . $name]
        };
    }

    /**
     * Get monthly revenue from invoices
     */
    private function executeGetMonthlyRevenue(int $month, int $year): array
    {
        try {
            // Validate month
            if ($month < 1 || $month > 12) {
                return ['error' => 'Invalid month. Month must be between 1 and 12.'];
            }

            $revenue = Invoice::whereMonth('issue_date', $month)
                ->whereYear('issue_date', $year)
                ->whereNotIn('status', ['draft'])
                ->sum('total_amount');

            $invoiceCount = Invoice::whereMonth('issue_date', $month)
                ->whereYear('issue_date', $year)
                ->whereNotIn('status', ['draft'])
                ->count();

            $monthName = \Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F');

            return [
                'month' => $month,
                'month_name' => $monthName,
                'year' => $year,
                'total_revenue' => $revenue,
                'formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
                'invoice_count' => $invoiceCount
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to get revenue data: ' . $e->getMessage()];
        }
    }

    /**
     * Get monthly expenses from bank transactions
     */
    private function executeGetMonthlyExpenses(int $month, int $year): array
    {
        try {
            // Validate month
            if ($month < 1 || $month > 12) {
                return ['error' => 'Invalid month. Month must be between 1 and 12.'];
            }

            $expenses = BankTransaction::where('transaction_type', 'debit')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $transactionCount = BankTransaction::where('transaction_type', 'debit')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->count();

            $monthName = \Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F');

            return [
                'month' => $month,
                'month_name' => $monthName,
                'year' => $year,
                'total_expenses' => $expenses,
                'formatted' => 'Rp ' . number_format($expenses, 0, ',', '.'),
                'transaction_count' => $transactionCount
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to get expenses data: ' . $e->getMessage()];
        }
    }

    /**
     * Get bank account balance
     */
    private function executeGetBankBalance(?string $accountName = null): array
    {
        try {
            if ($accountName) {
                // Search for specific account
                $account = BankAccount::where('account_name', 'like', "%{$accountName}%")
                    ->orWhere('bank_name', 'like', "%{$accountName}%")
                    ->first();

                if (!$account) {
                    return ['error' => 'Bank account not found. Please check the account name.'];
                }

                return [
                    'account_name' => $account->account_name,
                    'bank_name' => $account->bank_name,
                    'account_number' => $account->account_number,
                    'balance' => $account->balance,
                    'formatted' => $account->formatted_balance
                ];
            } else {
                // Return all accounts
                $accounts = BankAccount::all()->map(function($acc) {
                    return [
                        'account_name' => $acc->account_name,
                        'bank_name' => $acc->bank_name,
                        'account_number' => $acc->account_number,
                        'balance' => $acc->balance,
                        'formatted' => $acc->formatted_balance
                    ];
                })->toArray();

                $totalBalance = BankAccount::all()->sum('balance');

                return [
                    'accounts' => $accounts,
                    'total_accounts' => count($accounts),
                    'total_balance' => $totalBalance,
                    'formatted_total' => 'Rp ' . number_format($totalBalance, 0, ',', '.')
                ];
            }
        } catch (\Exception $e) {
            return ['error' => 'Failed to get bank balance: ' . $e->getMessage()];
        }
    }

    /**
     * Get outstanding (unpaid) invoices
     */
    private function executeGetOutstandingInvoices(): array
    {
        try {
            $invoices = Invoice::whereIn('status', ['draft', 'partially_paid'])
                ->with('client:id,name')
                ->orderBy('due_date', 'asc')
                ->limit(50) // Limit to 50 for performance
                ->get()
                ->map(function($invoice) {
                    return [
                        'invoice_number' => $invoice->invoice_number,
                        'client_name' => $invoice->client->name ?? 'Unknown',
                        'total_amount' => $invoice->total_amount,
                        'amount_paid' => $invoice->amount_paid,
                        'amount_remaining' => $invoice->amount_remaining,
                        'formatted_remaining' => 'Rp ' . number_format($invoice->amount_remaining, 0, ',', '.'),
                        'due_date' => $invoice->due_date->format('d/m/Y'),
                        'status' => $invoice->status
                    ];
                });

            $totalOutstanding = $invoices->sum('amount_remaining');

            return [
                'invoices' => $invoices->toArray(),
                'total_count' => $invoices->count(),
                'total_outstanding' => $totalOutstanding,
                'formatted_total' => 'Rp ' . number_format($totalOutstanding, 0, ',', '.')
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to get outstanding invoices: ' . $e->getMessage()];
        }
    }

    /**
     * Get function declarations for Gemini
     */
    private function getFunctionDeclarations(): array
    {
        return [
            new FunctionDeclaration(
                name: 'get_monthly_revenue',
                description: 'Get total company revenue from invoices for a specific month and year. Revenue comes from all invoices that are not in draft status.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'month' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Month number (1-12). Example: 1 for January, 2 for February.'
                        ),
                        'year' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Year (e.g., 2026)'
                        ),
                    ],
                    required: ['month', 'year']
                )
            ),

            new FunctionDeclaration(
                name: 'get_monthly_expenses',
                description: 'Get total company expenses from bank transactions for a specific month and year. Expenses are all debit transactions.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'month' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Month number (1-12). Example: 1 for January, 2 for February.'
                        ),
                        'year' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Year (e.g., 2026)'
                        ),
                    ],
                    required: ['month', 'year']
                )
            ),

            new FunctionDeclaration(
                name: 'get_bank_balance',
                description: 'Get current balance of bank accounts. If account_name is provided, returns specific account. If not provided, returns all accounts with total balance.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'account_name' => new Schema(
                            type: DataType::STRING,
                            description: 'Bank account name or bank name (e.g., "BCA", "Mandiri"). Optional - if not provided, returns all accounts.'
                        ),
                    ],
                    required: []
                )
            ),

            new FunctionDeclaration(
                name: 'get_outstanding_invoices',
                description: 'Get list of unpaid or partially paid invoices with client information. Returns invoices that still have remaining balance to be paid.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [],
                    required: []
                )
            ),
        ];
    }

    /**
     * Get system instruction for Gemini
     */
    private function getSystemInstruction(): string
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $formattedDate = $now->locale('id')->translatedFormat('d F Y');

        return <<<INSTRUCTION
You are a helpful financial assistant for an Indonesian company's finance management system.

**Current Context:**
- Today's date: {$formattedDate}
- Current month: {$currentMonth} (use this when user asks about "bulan ini" or "this month")
- Current year: {$currentYear}

**Response Guidelines:**
1. Always use Indonesian language in your responses
2. Format all currency as Rupiah with thousand separators (e.g., Rp 1.500.000)
3. Be concise but informative
4. Use bullet points for lists
5. When showing multiple items (like invoices), show the most important ones first

**Date Interpretation:**
- "bulan ini" / "this month" = month {$currentMonth}, year {$currentYear}
- "bulan lalu" / "last month" = calculate previous month
- "tahun ini" / "this year" = year {$currentYear}
- If specific month name mentioned (e.g., "Januari", "February"), map to month number

**Function Calling:**
- When user asks about revenue/pendapatan, call get_monthly_revenue
- When user asks about expenses/pengeluaran, call get_monthly_expenses
- When user asks about bank balance/saldo, call get_bank_balance
- When user asks about outstanding/belum bayar invoices, call get_outstanding_invoices
- You can call multiple functions if the question requires it

**Tone:**
- Professional but friendly
- Clear and straightforward
- Focus on the numbers and facts
INSTRUCTION;
    }
}
