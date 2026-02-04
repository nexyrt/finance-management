<div class="max-w-2xl mx-auto p-6 space-y-6">
    <!-- Input Form -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <input wire:model.live="question" type="text" placeholder="Tulis pertanyaan Anda di sini..."
            class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg">
        <div class="flex gap-3 mt-4">
            <button wire:click="askGemini"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Ask Gemini</span>
                <span wire:loading>Mengetik...</span>
            </button>
            <button wire:click="listModels"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200">
                List Models
            </button>
        </div>
    </div>

    <!-- Response -->
    @if ($response)
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 shadow-lg animate-fade-in">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-900">🤖 Jawaban Gemini</h3>
                <button onclick="navigator.clipboard.writeText('{{ $response }}')"
                    class="p-2 hover:bg-blue-200 rounded-full transition-colors" title="Copy">
                    📋
                </button>
            </div>

            <div class="response-content max-h-96 overflow-y-auto prose prose-blue max-w-none p-4 bg-white rounded-lg">
                {!! Illuminate\Mail\Markdown::parse($response) !!}
            </div>

            <div class="mt-4 text-sm text-gray-500 text-right">
                Model: Gemini 2.5 Flash
            </div>
        </div>
    @endif
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
