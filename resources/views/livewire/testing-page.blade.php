<section class="w-full bg-zinc-800 text-gray-200 p-6">
    <header class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Bank Accounts</h1>
                <p class="mt-1 text-gray-400">Manage your company bank accounts and view transaction history</p>
            </div>
            <button id="createAccountBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add New Account
            </button>
        </div>
    </header>

    <!-- Bank Accounts List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- Bank Account Card 1 -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-700 shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 h-10 w-10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Bank Mandiri</h3>
                        <p class="text-sm text-gray-400">Primary Account</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-gray-400 hover:text-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-400">Account Number</p>
                <p class="text-lg font-medium text-white">1234 5678 9012 3456</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-400">Balance</p>
                    <p class="text-xl font-bold text-emerald-400">Rp 125,000,000</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Last Updated</p>
                    <p class="text-white">Today, 10:45 AM</p>
                </div>
            </div>

            <button class="w-full bg-zinc-700 hover:bg-zinc-600 text-white py-2 rounded-lg transition-colors">
                View Transactions
            </button>
        </div>

        <!-- Bank Account Card 2 -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-700 shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-red-600 h-10 w-10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">BCA</h3>
                        <p class="text-sm text-gray-400">Secondary Account</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-gray-400 hover:text-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-400">Account Number</p>
                <p class="text-lg font-medium text-white">9876 5432 1098 7654</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-400">Balance</p>
                    <p class="text-xl font-bold text-emerald-400">Rp 78,500,000</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Last Updated</p>
                    <p class="text-white">Yesterday, 3:20 PM</p>
                </div>
            </div>

            <button class="w-full bg-zinc-700 hover:bg-zinc-600 text-white py-2 rounded-lg transition-colors">
                View Transactions
            </button>
        </div>

        <!-- Bank Account Card 3 -->
        <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-700 shadow-lg">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-600 h-10 w-10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">BNI</h3>
                        <p class="text-sm text-gray-400">Operations Account</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-gray-400 hover:text-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-400">Account Number</p>
                <p class="text-lg font-medium text-white">5678 1234 9876 5432</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-400">Balance</p>
                    <p class="text-xl font-bold text-emerald-400">Rp 42,750,000</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Last Updated</p>
                    <p class="text-white">2 days ago</p>
                </div>
            </div>

            <button class="w-full bg-zinc-700 hover:bg-zinc-600 text-white py-2 rounded-lg transition-colors">
                View Transactions
            </button>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="mt-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-xl font-bold text-white mb-4 md:mb-0">Transaction History</h2>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <select class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                        <option value="all">All Accounts</option>
                        <option value="mandiri">Bank Mandiri</option>
                        <option value="bca">BCA</option>
                        <option value="bni">BNI</option>
                    </select>
                </div>
                
                <div class="flex-1 md:flex-none">
                    <select class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                        <option value="all">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                
                <div class="flex-1 md:flex-none">
                    <input type="date" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl shadow-lg border border-zinc-700">
            <table class="min-w-full bg-zinc-900 divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    <!-- Transaction 1 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-15</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Client Payment - PT Maju Jaya</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Bank Mandiri</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-900 text-emerald-300">Income</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-400">+ Rp 25,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 125,000,000</td>
                    </tr>
                    
                    <!-- Transaction 2 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-12</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Office Rent Payment</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BCA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 15,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 78,500,000</td>
                    </tr>
                    
                    <!-- Transaction 3 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-10</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Server Maintenance</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BNI</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 7,250,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 42,750,000</td>
                    </tr>
                    
                    <!-- Transaction 4 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-08</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Client Payment - PT Sukses Makmur</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Bank Mandiri</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-900 text-emerald-300">Income</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-400">+ Rp 32,500,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 100,000,000</td>
                    </tr>
                    
                    <!-- Transaction 5 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-05</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Employee Salaries</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BCA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 45,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 93,500,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-400">Showing 5 of 24 transactions</p>
            <div class="flex gap-2">
                <button class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">Previous</button>
                <button class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- Add Bank Account Modal -->
    <div id="addAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6 border border-zinc-600">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">Add New Bank Account</h3>
                <button id="closeAddAccountModal" class="text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form class="space-y-4">
                <div>
                    <label for="bankName" class="block text-sm font-medium text-gray-300 mb-1">Bank Name</label>
                    <input type="text" id="bankName" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                </div>
                
                <div>
                    <label for="accountType" class="block text-sm font-medium text-gray-300 mb-1">Account Type</label>
                    <select id="accountType" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                        <option value="savings">Savings Account</option>
                        <option value="checking">Checking Account</option>
                        <option value="business">Business Account</option>
                    </select>
                </div>
                
                <div>
                    <label for="accountNumber" class="block text-sm font-medium text-gray-300 mb-1">Account Number</label>
                    <input type="text" id="accountNumber" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                </div>
                
                <div>
                    <label for="accountBalance" class="block text-sm font-medium text-gray-300 mb-1">Initial Balance</label>
                    <input type="number" id="accountBalance" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white">
                </div>
                
                <div>
                    <label for="accountDesc" class="block text-sm font-medium text-gray-300 mb-1">Description (Optional)</label>
                    <textarea id="accountDesc" rows="2" class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-white"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelAddAccount" class="px-4 py-2 bg-zinc-600 text-white rounded-lg hover:bg-zinc-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal handling for Add Account
        const createAccountBtn = document.getElementById('createAccountBtn');
        const addAccountModal = document.getElementById('addAccountModal');
        const closeAddAccountModal = document.getElementById('closeAddAccountModal');
        const cancelAddAccount = document.getElementById('cancelAddAccount');

        createAccountBtn.addEventListener('click', () => {
            addAccountModal.classList.remove('hidden');
        });

        closeAddAccountModal.addEventListener('click', () => {
            addAccountModal.classList.add('hidden');
        });

        cancelAddAccount.addEventListener('click', () => {
            addAccountModal.classList.add('hidden');
        });
    </script>
</section>
