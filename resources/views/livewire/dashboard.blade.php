<div class="flex flex-col items-center max-w-7xl mx-auto py-6 mt-20">
    <h1 class="text-3xl font-bold text-blue-600 text-center">Expense Tracker</h1>

    <div class="flex flex-row mt-4 sticky top-0">
        <!-- Earning and Expense Forms -->
        <div class="flex flex-col w-2/5 justify-between space-y-4 p-4 bg-slate-200 rounded-xl mr-4">
            <form wire:submit.prevent="submitEarning" class="flex flex-col bg-slate-50 p-4 rounded-xl w-full space-y-4">
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

        <div class="p-4 bg-slate-200 rounded-xl">
            <div class="sticky top-24 flex flex-col items-center">
                <!-- Budget Table -->
                <table class="w-full border-collapse table-auto zebra rounded-lg bg-slate-300">
                    <style>
                        .expense-row {
                            background-color: #f2dede;
                        }
                        .income-row {
                            background-color: #dff0d8;
                        }
                    </style>
                    <thead>
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
                        @if(count($entries) == 0)
                            <tr>
                                <td colspan="6" class="px-4 py-2 text-center bg-white">No entries found.</td>
                            </tr>
                        @else
                            @foreach($entries['data'] as $entry)
                                <tr class="{{ $entry['type'] == 'Expense' ? 'expense-row' : 'income-row' }}">
                                    <td class="px-4 py-2">{{ $entry['date'] }}</td>
                                    <td class="px-4 py-2 {{ $entry['type'] == 'Expense' ? 'text-red-600' : 'text-green-600' }}">{{ $entry['type'] }}</td>
                                    <td class="px-4 py-2 order-gray-400 font-bold">{{ $entry['category'] }}</td>
                                    <td class="px-4 py-2">{{ $entry['description'] }}</td>
                                    <td class="px-4 py-2 border-l border-r border-gray-400 text-right">{{ $entry['amount'] }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <button wire:click="deleteEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="bg-red-500 text-white rounded p-2">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <th colspan="4" class="px-4 py-2 text-right font-light">Page {{ $entries['current_page'] }} Balance:</th>
                            <th class="px-4 py-2 text-right border-l border-r border-gray-400">{{ $currentPageBalace }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="px-4 py-2 text-right font-light">Total Balance:</th>
                            <th class="px-4 py-2 text-right border-l border-r border-gray-400">{{ $totalBalance }}</th>
                        </tr>
                    </tbody>
                </table>

                @if(isset($entries['links']))
                    <div class="mt-4">
                        @foreach($entries['links'] as $link)
                            @php
                                $pageNumber = $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : null;
                            @endphp
                            <button
                                wire:click="gotoPage({{ $pageNumber ?: 'null' }})"
                                class="px-4 py-2 border rounded {{ $link['active'] ? 'bg-blue-500 text-white' : 'text-blue-500' }}"
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
