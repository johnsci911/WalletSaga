<div class="flex flex-col items-center px-4 py-6 mx-auto mt-20 max-w-7xl lg:px-0">
    <div class="flex flex-col mt-4 w-full lg:flex-row">
        <!-- Earning and Expense Forms -->
        <div class="flex lg:sticky lg:top-24 flex-col justify-start self-start p-4 mb-4 space-y-4 w-full rounded-2xl lg:w-2/5 bg-slate-700 lg:mb-0 lg:mr-4">
            <p class="font-fantasque font-bold text-2xl text-slate-100">Add Entries</p>
            <div class="sticky top-24"
                x-data="{ shouldFocus: @entangle('shouldFocusDate') }"
                x-init="$watch('shouldFocus', value => {
                     if (value === 'earning') {
                         $nextTick(() => $refs.earningDateInput.focus());
                     } else if (value === 'expense') {
                         $nextTick(() => $refs.expenseDateInput.focus());
                     }
                })">
                <div class="flex flex-col {{ $editingEntryType === null ? 'space-y-4' : '' }}">
                    <!-- Earning Form -->
                    <form wire:submit.prevent="submitEarning" class="flex flex-col p-4 space-y-4 w-full rounded-xl bg-slate-800 {{ $editingEntryType === 'Expense' ? 'hidden' : '' }}">
                        <p class="text-lg font-bold text-green-500">Earning</p>
                        <div>
                            <label class="text-slate-300 font-bold">Date:</label>
                            <div class="flex items-center space-x-2">
                                <input type="datetime-local" wire:model="earningForm.date" x-ref="earningDateInput" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                            </div>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Amount:</label>
                            <input type="decimal" wire:model="earningForm.amount" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Category:</label>
                            <select wire:model="earningForm.category" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                @foreach($earningCategories as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Description:</label>
                            <textarea wire:model="earningForm.description" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required></textarea>
                        </div>
                        <div class="flex-grow"></div>
                        <div class="self-end flex space-x-2">
                            @if($editingEntryId && $editingEntryType === 'Earning')
                                <button type="button" wire:click="cancelEdit" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                    Cancel Edit
                                </button>
                            @endif
                            <button type="submit" class="bg-green-500 text-white font-extrabold rounded-xl p-2">
                                {{ $editingEntryId && $editingEntryType === 'Earning' ? 'Update Earning' : 'Add Earning' }}
                            </button>
                        </div>
                    </form>

                    <!-- Expense Form -->
                    <form wire:submit.prevent="submitExpense" class="flex flex-col p-4 space-y-4 w-full rounded-xl bg-slate-800 {{ $editingEntryType === 'Earning' ? 'hidden' : '' }}">
                        <p class="text-lg font-bold text-red-500">Expense</p>
                        <div>
                            <label class="text-slate-300 font-bold">Date:</label>
                            <div class="flex items-center space-x-2">
                                <input type="datetime-local" wire:model="expenseForm.date" x-ref="expenseDateInput" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                            </div>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Amount:</label>
                            <input type="decimal" wire:model="expenseForm.amount" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Category:</label>
                            <select wire:model="expenseForm.category" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-slate-300 font-bold">Description:</label>
                            <textarea wire:model="expenseForm.description" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required></textarea>
                        </div>
                        <div class="flex-grow"></div>
                        <div class="self-end flex space-x-2">
                            @if($editingEntryId && $editingEntryType === 'Expense')
                                <button type="button" wire:click="cancelEdit" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                    Cancel Edit
                                </button>
                            @endif
                            <button type="submit" class="bg-red-500 text-white font-extrabold rounded-xl p-2">
                                {{ $editingEntryId && $editingEntryType === 'Expense' ? 'Update Expense' : 'Add Expense' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-4 w-full rounded-2xl lg:w-3/5 bg-slate-700">
            <p class="font-fantasque font-bold text-2xl text-slate-100 mb-4">Tracker</p>
            <div class="flex sticky top-24 flex-col items-center min-w-full">
                <!-- Add search bar -->
                <div class="w-full mb-4">
                    <input type="text" wire:model.live.debounce.150ms="search" placeholder="Search entries..." class="w-full p-2 rounded-xl bg-slate-800 text-slate-100 border border-slate-600">
                </div>

                <!-- Scrollable container for table and links -->
                <div class="overflow-x-auto w-full rounded-t-xl scrollbar-thin">
                    <!-- Budget Table -->
                    <table class="w-full min-w-max border-collapse table-auto bg-slate-950 font-fantasque text-sm rounded-t-xl">
                        <style>
                            .zebra-row:nth-child(even) {
                                background-color: rgba(203, 213, 225, 0.08);
                            }
                        </style>
                        <thead class="bg-slate-800 text-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Date Time</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-right border-l border-r">Amount</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($entries['data']) == 0)
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-left md:text-center text-2xl font-bold text-slate-200">No entries found.</td>
                                </tr>
                            @else
                                @foreach($entries['data'] as $entry)
                                    <tr class="zebra-row text-slate-300 {{ $entry['type'] == 'Expense' ? 'expense-row' : 'income-row' }}">
                                        <td class="font-bold px-4 py-2">{{ $entry['date'] }}</td>
                                        <td class="font-bold px-4 py-2 {{ $entry['type'] == 'Expense' ? 'text-red-600' : 'text-green-600' }}">{{ $entry['type'] }}</td>
                                        <td class="font-bold px-4 py-2 order-gray-400">{{ $entry['category'] }}</td>
                                        <td class="font-bold px-4 py-2">{{ $entry['description'] }}</td>
                                        <td class="font-bold px-4 py-2 text-right border-r border-l {{ $entry['type'] == 'Expense' ? 'text-red-300' : 'text-green-300' }}">{{ $entry['amount'] }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <button wire:click="editEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="bg-blue-500 text-white rounded p-2 mr-2">Edit</button>
                                            <button wire:click="deleteEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="bg-red-500 text-white rounded p-2">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="border-t border-black">
                                <th colspan="4" class="px-4 py-2 font-light text-right text-green-300 bg-slate-800">Total Earnings:</th>
                                <th class="px-4 py-2 text-right text-green-300 bg-slate-800 border-r border-l">{{ $totalEarnings }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-red-300 bg-slate-800">Total Expenses:</th>
                                <th class="px-4 py-2 text-right text-red-300 bg-slate-800 border-r border-l">{{ $totalExpenses }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-white bg-slate-900">Page {{ $entries['current_page'] }} Total Balance:</th>
                                <th class="px-4 py-2 text-right text-slate-300 bg-slate-900 border-r border-l">{{ $currentPageBalance }}</th>
                                <th class="bg-slate-900"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-slate-300 bg-slate-900">All Pages Total Balance:</th>
                                <th class="px-4 py-2 text-right text-slate-300 bg-slate-900 border-r border-l">{{ $totalBalance }}</th>
                                <th class="bg-slate-900"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(isset($entries['links']))
                    <div class="overflow-x-auto p-2 bg-slate-900 w-full whitespace-nowrap rounded-b-xl scrollbar-thin">
                        @foreach($entries['links'] as $link)
                            @php
                                $pageNumber = $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : null;
                            @endphp
                            <button
                                wire:click="gotoPage({{ $pageNumber ?: 'null' }})"
                                class="px-4 py-2 rounded {{ $link['active'] ? 'font-bold bg-slate-500 text-white' : 'font-light bg-slate-600 text-slate-400' }}"
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

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scrollToTop', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

