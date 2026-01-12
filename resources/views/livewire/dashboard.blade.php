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
                <!-- Add search bar and transaction buttons -->
                <div class="w-full gap-3 mb-4 flex flex-col md:flex-row items-center justify-center">
                    <input type="text" wire:model.live.debounce.150ms="search" placeholder="Search entries..." class="w-full py-3 px-3 rounded-xl bg-slate-800 text-slate-100 border border-slate-600">

                    <!-- Floating Action Buttons -->
                    <div class="flex flex-row flex-wrap justify-center gap-3 flex-shrink-0">
                        <!-- Add Earning Button -->
                        <button
                            wire:click="addEntry('Earning')"
                            class="group relative flex items-center justify-center w-32 py-2 px-3 bg-green-500 hover:bg-green-600 text-white rounded-xl shadow-lg hover:shadow-xl"
                            title="Add Earning">
                            Add Earning
                        </button>

                        <!-- Add Expense Button -->
                        <button
                            wire:click="addEntry('Expense')"
                            class="group relative flex items-center justify-center w-32 py-2 px-3 bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-lg hover:shadow-xl"
                            title="Add Expense">
                            Add Expense
                        </button>
                    </div>
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

                {{-- Pagination --}}
                @if(isset($entries['links']))
                <div class="p-4 bg-slate-900 w-full rounded-b-2xl">
                    <nav role="navigation" aria-label="Pagination Navigation">
                        {{-- Mobile --}}
                        <div class="flex flex-col items-center gap-3 sm:hidden">
                            <span class="text-sm text-slate-400">
                                Page {{ $entries['current_page'] }} of {{ $entries['last_page'] }}
                            </span>
                            <div class="flex gap-2">
                                @foreach($entries['links'] as $link)
                                @if($link['label'] === '&laquo; Previous' || $link['label'] === 'Next &raquo;')
                                @if($link['url'])
                                <button wire:click="gotoPage({{ $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : 'null' }})"
                                    class="px-4 py-2 text-sm font-medium text-slate-200 bg-slate-700 border border-slate-600 rounded-md hover:bg-slate-600 transition-colors">
                                    {{ $link['label'] === '&laquo; Previous' ? 'Previous' : 'Next' }}
                                </button>
                                @else
                                <span class="px-4 py-2 text-sm font-medium text-slate-500 bg-slate-700 border border-slate-600 cursor-default rounded-md">
                                    {{ $link['label'] === '&laquo; Previous' ? 'Previous' : 'Next' }}
                                </span>
                                @endif
                                @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Desktop --}}
                        <div class="hidden sm:flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-400">
                                    Showing
                                    <span class="font-medium text-slate-200">{{ $entries['from'] }}</span>
                                    to
                                    <span class="font-medium text-slate-200">{{ $entries['to'] }}</span>
                                    of
                                    <span class="font-medium text-slate-200">{{ $entries['total'] }}</span>
                                    results
                                </p>
                            </div>
                            <div class="flex gap-1">
                                @php
                                $currentPage = $entries['current_page'];
                                $lastPage = $entries['last_page'];
                                $onEachSide = 2;

                                $start = max(1, $currentPage - $onEachSide);
                                $end = min($lastPage, $currentPage + $onEachSide);
                                @endphp

                                @foreach($entries['links'] as $link)
                                @php
                                $pageNumber = $link['url'] ? ltrim(parse_url($link['url'], PHP_URL_QUERY), 'page=') : null;
                                $isArrow = $link['label'] === '&laquo; Previous' || $link['label'] === 'Next &raquo;';
                                $isEllipsis = $link['label'] === '...';

                                // Skip page numbers outside our range
                                if (!$isArrow && !$isEllipsis && $pageNumber) {
                                $pageNum = (int)$pageNumber;
                                // Show if it's first page, last page, or in range
                                if ($pageNum != 1 && $pageNum != $lastPage && ($pageNum < $start || $pageNum> $end)) {
                                    continue;
                                    }
                                    }
                                    @endphp

                                    @if($link['url'])
                                    <button wire:click="gotoPage({{ $pageNumber ?: 'null' }})"
                                        class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ $link['active'] ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600' }}">
                                        {!! $link['label'] !!}
                                    </button>
                                    @else
                                    <span class="px-3 py-2 text-sm font-medium rounded-md {{ $link['active'] ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-500 border border-slate-600 cursor-default' }}">
                                        {!! $link['label'] !!}
                                    </span>
                                    @endif
                                    @endforeach
                            </div>
                        </div>
                    </nav>
                </div>
                @endif
            </div>
        </div>

        <!-- Transaction Modals -->
        <x-transaction-modal
            type="Earning"
            :form="$earningForm"
            :categories="$earningCategories"
            :editingEntryId="$editingEntryId"
            :editingEntryType="$editingEntryType"
            modalState="showEarningModal"
            submitAction="submitEarning" />

        <x-transaction-modal
            type="Expense"
            :form="$expenseForm"
            :categories="$expenseCategories"
            :editingEntryId="$editingEntryId"
            :editingEntryType="$editingEntryType"
            modalState="showExpenseModal"
            submitAction="submitExpense" />
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scrollToTop', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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