<div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Recurring Invoice Flow</h1>
        
        <!-- Database Schema -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-6 text-center">Database Schema</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recurring Templates -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-blue-600">recurring_templates</h3>
                    <div class="space-y-2 text-sm">
                        <div class="bg-gray-50 p-2 rounded">id (PK)</div>
                        <div class="bg-gray-50 p-2 rounded">client_id (FK)</div>
                        <div class="bg-gray-50 p-2 rounded">template_name</div>
                        <div class="bg-gray-50 p-2 rounded">start_date</div>
                        <div class="bg-gray-50 p-2 rounded">end_date</div>
                        <div class="bg-gray-50 p-2 rounded">frequency ('monthly')</div>
                        <div class="bg-gray-50 p-2 rounded">next_generation_date</div>
                        <div class="bg-gray-50 p-2 rounded">status (active/inactive)</div>
                        <div class="bg-gray-50 p-2 rounded">invoice_template (JSON)</div>
                    </div>
                </div>

                <!-- Recurring Invoices -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-green-600">recurring_invoices</h3>
                    <div class="space-y-2 text-sm">
                        <div class="bg-gray-50 p-2 rounded">id (PK)</div>
                        <div class="bg-gray-50 p-2 rounded">template_id (FK)</div>
                        <div class="bg-gray-50 p-2 rounded">client_id (FK)</div>
                        <div class="bg-gray-50 p-2 rounded">scheduled_date</div>
                        <div class="bg-gray-50 p-2 rounded">invoice_data (JSON)</div>
                        <div class="bg-gray-50 p-2 rounded">status (draft/published)</div>
                        <div class="bg-gray-50 p-2 rounded">published_invoice_id (NULL)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flow Steps -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-6 text-center">Process Flow</h2>
            <div class="flex flex-col lg:flex-row items-center justify-center space-y-4 lg:space-y-0 lg:space-x-8">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center max-w-sm flow-arrow">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">1</div>
                    <h3 class="font-semibold mb-2">Create Template</h3>
                    <p class="text-sm text-gray-600">Setup recurring template dengan kontrak start/end date</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center max-w-sm flow-arrow">
                    <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">2</div>
                    <h3 class="font-semibold mb-2">Auto Generate</h3>
                    <p class="text-sm text-gray-600">Cron job generate draft invoices sesuai schedule</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center max-w-sm">
                    <div class="w-12 h-12 bg-purple-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">3</div>
                    <h3 class="font-semibold mb-2">Review & Publish</h3>
                    <p class="text-sm text-gray-600">User review draft lalu publish ke table invoices</p>
                </div>
            </div>
        </div>

        <!-- UI Mockup -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-6 text-center">UI Interface Mockup</h2>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Recurring Invoices 2025</h3>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">+ New Template</button>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <div class="flex space-x-8">
                        <button class="py-2 px-1 border-b-2 border-blue-500 font-medium text-blue-600">Jan</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Feb</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Mar</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Apr</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">May</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Jun</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Jul</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Aug</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Sep</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Oct</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Nov</button>
                        <button class="py-2 px-1 text-gray-500 hover:text-gray-700">Dec</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">PT. Example Corp</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Monthly Service</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp 5.000.000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button class="text-green-600 hover:text-green-900 mr-3">Publish</button>
                                    <button class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">CV. Sample Ltd</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Consulting Fee</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp 2.500.000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">View Invoice</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">January 2025 Summary:</span>
                        <div class="flex space-x-4">
                            <span class="text-sm"><span class="font-medium">Draft:</span> Rp 5.000.000</span>
                            <span class="text-sm"><span class="font-medium">Published:</span> Rp 2.500.000</span>
                            <span class="text-sm font-semibold"><span class="font-medium">Total:</span> Rp 7.500.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Flow -->
        <div>
            <h2 class="text-2xl font-semibold mb-6 text-center">Data Relationships</h2>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex flex-col lg:flex-row items-center justify-center space-y-6 lg:space-y-0 lg:space-x-12">
                    <!-- Template -->
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-lg p-4 mb-2">
                            <h4 class="font-semibold">Template</h4>
                            <p class="text-xs text-gray-600">Contract: Jun 2025 - Jun 2026</p>
                        </div>
                        <div class="text-sm text-gray-500">1 Template → Many Drafts</div>
                    </div>

                    <div class="text-2xl text-gray-400">↓</div>

                    <!-- Drafts -->
                    <div class="text-center">
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div class="bg-yellow-100 rounded p-2 text-xs">Jan Draft</div>
                            <div class="bg-yellow-100 rounded p-2 text-xs">Feb Draft</div>
                            <div class="bg-yellow-100 rounded p-2 text-xs">Mar Draft</div>
                        </div>
                        <div class="text-sm text-gray-500">Auto-generated monthly</div>
                    </div>

                    <div class="text-2xl text-gray-400">↓</div>

                    <!-- Published -->
                    <div class="text-center">
                        <div class="bg-green-100 rounded-lg p-4 mb-2">
                            <h4 class="font-semibold">Invoice</h4>
                            <p class="text-xs text-gray-600">In invoices table</p>
                        </div>
                        <div class="text-sm text-gray-500">When published</div>
                    </div>
                </div>
            </div>
        </div>
    </div>