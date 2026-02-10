<?php

namespace Database\Seeders;

use App\Models\FundRequest;
use Illuminate\Database\Seeder;

class GenerateFundRequestNumbersSeeder extends Seeder
{
    /**
     * Run the database seeder to generate request numbers for existing fund requests.
     */
    public function run(): void
    {
        $fundRequests = FundRequest::whereNull('request_number')
            ->orderBy('created_at')
            ->get();

        if ($fundRequests->isEmpty()) {
            $this->command->info('No fund requests without request numbers found.');
            return;
        }

        $this->command->info("Generating request numbers for {$fundRequests->count()} fund requests...");

        foreach ($fundRequests as $fundRequest) {
            $requestNumber = $this->generateRequestNumber($fundRequest->created_at);
            $fundRequest->update(['request_number' => $requestNumber]);

            $this->command->info("Generated: {$requestNumber} for Fund Request ID {$fundRequest->id}");
        }

        $this->command->info('Request numbers generated successfully!');
    }

    private function generateRequestNumber($createdAt): string
    {
        $companyProfile = \App\Models\CompanyProfile::current();
        $companyAbbreviation = $companyProfile->abbreviation ?? 'KSN';

        $year = $createdAt->year;
        $month = $createdAt->month;
        $romanMonth = $this->toRoman($month);

        // Count existing requests in that month (before this one)
        $count = FundRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('request_number')
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Format: 001/KSN/I/2026
        return sprintf('%s/%s/%s/%s', $sequence, $companyAbbreviation, $romanMonth, $year);
    }

    private function toRoman(int $number): string
    {
        $map = [
            12 => 'XII', 11 => 'XI', 10 => 'X',
            9 => 'IX', 8 => 'VIII', 7 => 'VII',
            6 => 'VI', 5 => 'V', 4 => 'IV',
            3 => 'III', 2 => 'II', 1 => 'I'
        ];

        return $map[$number] ?? 'I';
    }
}
