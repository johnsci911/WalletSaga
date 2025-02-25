<div class="flex flex-col items-center px-4 py-6 mx-auto mt-20 max-w-7xl lg:px-0">
    <h1 class="text-3xl font-bold text-center text-blue-400">Expense Tracker</h1>

    <div class="flex flex-col mt-4 w-full lg:flex-row">
        <!-- Earning and Expense Forms -->
        <div class="flex lg:sticky lg:top-24 flex-col justify-start self-start p-4 mb-4 space-y-4 w-full rounded-2xl lg:w-2/5 bg-slate-200 lg:mb-0 lg:mr-4">
            <form wire:submit.prevent="submitEarning" class="flex flex-col p-4 space-y-4 w-full rounded-xl bg-slate-50">
                <p class="text-lg font-bold text-green-500">Earning</p>
                <div>
                    <label class="text-gray-600 font-bold">Date:</label>
                    <input type="datetime-local" wire:model="earningForm.date" class="border border-gray-200 rounded-xl p-2 w-full" required>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Amount:</label>
                    <input type="number" wire:model="earningForm.amount" class="border border-gray-200 rounded-xl p-2 w-full" required>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Category:</label>
                    <select wire:model="earningForm.category" class="border border-gray-200 rounded-xl p-2 w-full">
                        @foreach($earningCategories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Description:</label>
                    <textarea wire:model="earningForm.description" class="border border-gray-200 rounded-xl p-2 w-full" required></textarea>
                </div>
                <div class="flex-grow"></div>
                <div class="self-end">
                    <button type="submit" class="bg-green-500 text-white font-extrabold rounded-xl p-2">Add Earning</button>
                </div>
            </form>

            <form wire:submit.prevent="submitExpense" class="flex flex-col bg-gray-50 p-4 rounded-xl w-full space-y-4">
                <p class="text-lg font-bold text-red-500">Expense</p>
                <div>
                    <label class="text-gray-600 font-bold">Date:</label>
                    <input type="datetime-local" wire:model="expenseForm.date" class="border border-gray-200 rounded-xl p-2 w-full" required>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Amount:</label>
                    <input type="number" wire:model="expenseForm.amount" class="border border-gray-200 rounded-xl p-2 w-full" required>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Category:</label>
                    <select wire:model="expenseForm.category" class="border border-gray-200 rounded-xl p-2 w-full">
                        @foreach($expenseCategories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-600 font-bold">Description:</label>
                    <textarea wire:model="expenseForm.description" class="border border-gray-200 rounded-xl p-2 w-full" required></textarea>
                </div>
                <div class="flex-grow"></div>
                <div class="self-end">
                    <button type="submit" class="bg-red-500 text-white font-extrabold rounded-xl p-2">Add Expense</button>
                </div>
            </form>
        </div>

        <div class="p-4 w-full rounded-2xl lg:w-3/5 bg-slate-200">
            <div class="flex sticky top-24 flex-col items-center min-w-full">
                <!-- Scrollable container for table and links -->
                <div class="overflow-x-scroll w-full rounded-xl">
                    <!-- Budget Table -->
                    <table class="w-full min-w-max border-collapse table-auto bg-slate-300 font-fantasque text-sm">
                        <style>
                            .expense-row {
                                background-color: rgba(242, 222, 222, 0.5);
                            }
                            .income-row {
                                background-color: rgba(223, 240, 216, 0.5);
                            }
                            .zebra-row:nth-child(even) {
                                background-color: rgba(203, 213, 225, 0.1);
                            }
                        </style>
                        <thead class="bg-slate-800 text-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Date Time</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                                <th class="px-4 py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($entries['data']) == 0)
                                <tr>
                                    <td colspan="6" class="px-4 py-2 text-center bg-white text-2xl font-bold text-slate-500">No entries found.</td>
                                </tr>
                            @else
                                @foreach($entries['data'] as $entry)
                                    <tr class="zebra-row {{ $entry['type'] == 'Expense' ? 'expense-row' : 'income-row' }}">
                                        <td class="px-4 py-2">{{ $entry['date'] }}</td>
                                        <td class="px-4 py-2 {{ $entry['type'] == 'Expense' ? 'text-red-600' : 'text-green-600' }}">{{ $entry['type'] }}</td>
                                        <td class="px-4 py-2 font-bold order-gray-400">{{ $entry['category'] }}</td>
                                        <td class="px-4 py-2">{{ $entry['description'] }}</td>
                                        <td class="px-4 py-2 text-right border-r border-l border-gray-400 {{ $entry['type'] == 'Expense' ? 'text-red-600' : 'text-green-600' }}">{{ $entry['amount'] }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <button wire:click="deleteEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="p-2 text-white bg-red-500 rounded">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="border-t border-black">
                                <th colspan="4" class="px-4 py-2 font-light text-right text-green-300 bg-slate-800">Total Earnings:</th>
                                <th class="px-4 py-2 text-right text-green-300 bg-slate-800 border-r border-l border-gray-400">{{ $totalEarnings }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-red-300 bg-slate-800">Total Expenses:</th>
                                <th class="px-4 py-2 text-right text-red-300 bg-slate-800 border-r border-l border-gray-400">{{ $totalExpenses }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-white bg-slate-700">Page {{ $entries['current_page'] }} Total Balance:</th>
                                <th class="px-4 py-2 text-right text-white bg-slate-700 border-r border-l border-white">{{ $currentPageBalace }}</th>
                                <th class="bg-slate-700"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-white bg-slate-700 rounded-bl-xl">All Pages Total Balance:</th>
                                <th class="px-4 py-2 text-right text-white bg-slate-700 border-r border-l border-white">{{ $totalBalance }}</th>
                                <th class="bg-slate-700 rounded-br-xl"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(isset($entries['links']))
                    <div class="overflow-x-auto p-2 mt-4 w-full whitespace-nowrap rounded-xl bg-slate-300">
                        @foreach($entries['links'] as $link)
                            @php
                                $pageNumber = $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : null;
                            @endphp
                            <button
                                wire:click="gotoPage({{ $pageNumber ?: 'null' }})"
                                class="px-4 py-2 rounded {{ $link['active'] ? 'font-bold bg-blue-400 text-white' : 'font-light bg-slate-50 text-slate-400' }}"
                                {{ $link['url'] ? '' : 'disabled' }}
                            >
                                {!! $link['label'] !!}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
