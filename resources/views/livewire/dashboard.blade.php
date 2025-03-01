<div class="flex flex-col items-center pb-4 px-4 mx-auto mt-20 max-w-7xl lg:px-0">
    <div class="flex flex-col w-full space-y-4">
        <p class="font-bold text-2xl text-slate-100 font-fantasque text-center">Tracker</p>
        <div class="pb-4 px-4 w-full rounded-2xl bg-slate-700">
            <div class="flex flex-col space-y-4 my-4">
                <div class="self-center">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-24 object-cover">
                </div>
                <p class="text-center font-fantasque text-slate-400 mt-2">{{ $this->user->name }}</p>
            </div>

            <div class="flex flex-col items-center min-w-full">
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
                                <th class="px-4 py-2 text-right">Amount</th>
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
                                        <td class="font-bold px-4 py-2 text-right {{ $entry['type'] == 'Expense' ? 'text-red-300' : 'text-green-300' }}">{{ $entry['amount'] }}</td>
                                        <td class="p-4 flex">
                                            <div class="inline-flex mx-auto">
                                                <button wire:click="editEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="bg-blue-500 text-white rounded-l-xl p-2 mr-1">Edit</button>
                                                <button wire:click="deleteEntry({{ $entry['id'] }}, '{{ $entry['type'] }}')" class="bg-red-500 text-white rounded-r-xl p-2">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-green-300 bg-slate-800">Total Earnings:</th>
                                <th class="px-4 py-2 text-right text-green-300 bg-slate-800">{{ $totalEarnings }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-red-300 bg-slate-800">Total Expenses:</th>
                                <th class="px-4 py-2 text-right text-red-300 bg-slate-800">{{ $totalExpenses }}</th>
                                <th class="bg-slate-800"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-white bg-slate-900">Page {{ $entries['current_page'] }} Total Balance:</th>
                                <th class="px-4 py-2 text-right text-slate-300 bg-slate-900">{{ $currentPageBalance }}</th>
                                <th class="bg-slate-900"></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="px-4 py-2 font-light text-right text-slate-300 bg-slate-900">All Pages Total Balance:</th>
                                <th class="px-4 py-2 text-right text-slate-300 bg-slate-900">{{ $totalBalance }}</th>
                                <th class="bg-slate-900"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(isset($entries['links']))
                    <div class="overflow-x-auto p-4 bg-slate-900 w-full whitespace-nowrap rounded-b-2xl scrollbar-thin">
                        @foreach($entries['links'] as $link)
                            @php
                                $pageNumber = $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : null;
                            @endphp
                            <button
                                wire:click="gotoPage({{ $pageNumber ?: 'null' }})"
                                class="px-4 py-2 rounded-lg hover:text-white {{ $link['active'] ? 'font-bold bg-slate-700 text-white' : 'font-light bg-slate-800 text-slate-400' }}"
                                {{ $link['url'] ? '' : 'disabled' }}
                            >
                                {!! $link['label'] !!}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Earning and Expense Forms -->
        <div class="flex flex-col justify-start self-start p-4 mb-4 space-y-4 w-full rounded-2xl bg-slate-700 lg:mb-0 lg:mr-4">
            <div class="sticky top-24"
                x-data="{ shouldFocus: @entangle('shouldFocusDate') }"
                x-init="$watch('shouldFocus', value => {
                     if (value === 'earning') {
                         $nextTick(() => $refs.earningDateInput.focus());
                     } else if (value === 'expense') {
                         $nextTick(() => $refs.expenseDateInput.focus());
                     }
                })">
                <div class="flex flex-col {{ $editingEntryType === null ? '' : 'space-y-4' }}">
                    <!-- Earning Form -->
                    <div class="flex flex-row space-x-4">
                        <button wire:click="addEntry('Earning')" class="w-1/2 bg-slate-800 text-green-500 hover:text-green-300 font-fantasque font-bold rounded-xl p-2">
                            {{ $editingEntryId && $editingEntryType === 'Earning' ? 'Update Earning' : 'Add Earning' }}
                        </button>

                        <button wire:click="addEntry('Expense')" type="submit" class="w-1/2 bg-slate-800 text-red-500 hover:text-red-300 font-fantasque font-bold rounded-xl p-2">
                            {{ $editingEntryId && $editingEntryType === 'Earning' ? 'Update Expense' : 'Add Expense' }}
                        </button>
                    </div>

                    <div class="{{ $editingEntryType === null ? 'hidden' : '' }}">
                        <!-- Earning Form -->
                        <form wire:submit.prevent="submitEarning" class="flex flex-col p-4 space-y-4 w-full rounded-xl bg-slate-800 {{ $editingEntryType === 'Expense' ? 'hidden' : '' }}">
                            <p class="text-lg font-bold text-green-500">Earning</p>
                            <div class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 lg:space-x-4">
                                <div class="w-full lg:w-1/2 space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Date:</label>
                                        <div class="flex items-center">
                                            <input type="datetime-local" wire:model="earningForm.date" x-ref="earningDateInput" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Amount:</label>
                                        <input type="decimal" wire:model="earningForm.amount" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Category:</label>
                                        <select wire:model="earningForm.category" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                            @foreach($earningCategories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full lg:w-1/2 space-y-2">
                                    <label class="text-slate-300 font-bold">Description:</label>
                                    <textarea wire:model="earningForm.description" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required rows=8></textarea>
                                </div>
                            </div>
                            <div class="self-end flex space-x-2">
                                @if($editingEntryId && $editingEntryType === 'Earning')
                                    <button type="button" wire:click="cancelEdit" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                        Cancel Edit
                                    </button>
                                @endif
                                @if($editingEntryId === null)
                                    <button type="button" wire:click="cancelAdd" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                        Cancel
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
                            <div class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 lg:space-x-4">
                                <div class="w-full lg:w-1/2 space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Date:</label>
                                        <div class="flex items-center">
                                            <input type="datetime-local" wire:model="expenseForm.date" x-ref="expenseDateInput" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Amount:</label>
                                        <input type="decimal" wire:model="expenseForm.amount" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-slate-300 font-bold">Category:</label>
                                        <select wire:model="expenseForm.category" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full">
                                            @foreach($expenseCategories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full lg:w-1/2 space-y-2">
                                    <label class="text-slate-300 font-bold">Description:</label>
                                    <textarea wire:model="expenseForm.description" class="border border-gray-200 bg-slate-200 rounded-xl p-2 w-full" required rows=8></textarea>
                                </div>
                            </div>
                            <div class="self-end flex space-x-2">
                                @if($editingEntryId && $editingEntryType === 'Expense')
                                    <button type="button" wire:click="cancelEdit" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                        Cancel Edit
                                    </button>
                                @endif
                                @if($editingEntryId === null)
                                    <button type="button" wire:click="cancelAdd" class="bg-gray-500 text-white font-extrabold rounded-xl p-2">
                                        Cancel
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
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scrollToTop', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scrollToBottom', () => {
            setTimeout(() => {
                window.scrollTo({
                    top: document.documentElement.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
        });
    });
</script>

