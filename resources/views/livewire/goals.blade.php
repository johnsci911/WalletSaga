<div class="flex flex-col items-center pb-4 px-4 mx-auto mt-20 max-w-7xl lg:px-0">
    <div class="flex flex-col w-full space-y-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <p class="font-bold text-2xl text-slate-100 font-fantasque">Financial Goals</p>
            <button
                wire:click="addGoal"
                class="mt-4 md:mt-0 px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                + Create New Goal
            </button>
        </div>

        <!-- Goals Grid -->
        @if(count($goals) == 0)
        <div class="flex flex-col items-center justify-center py-16 bg-slate-700 rounded-2xl p-6">
            <svg class="w-16 h-16 text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <p class="text-xl font-bold text-slate-300 mb-2">No Goals Yet</p>
            <p class="text-slate-400">Create your first financial goal to start tracking your progress!</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($goals as $goal)
            <div class="bg-slate-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow {{ $goal['is_completed'] ? 'opacity-75' : '' }} flex flex-col">
                <!-- Goal Content (grows to fill space) -->
                <div class="flex-1">
                    <!-- Goal Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-slate-100 {{ $goal['is_completed'] ? 'line-through' : '' }}">
                                {{ $goal['name'] }}
                            </h3>
                            @if($goal['category'])
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-600 text-slate-300 rounded-full">
                                {{ $goal['category'] }}
                            </span>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        @if($goal['is_completed'])
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                            ✓ Completed
                        </span>
                        @elseif($goal['is_overdue'])
                        <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                            Overdue
                        </span>
                        @endif
                    </div>

                    <!-- Description -->
                    @if($goal['description'])
                    <p class="text-sm text-slate-400 mb-4 line-clamp-2">{{ $goal['description'] }}</p>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-300 font-bold">${{ $goal['current_amount'] }}</span>
                            <span class="text-slate-400">of ${{ $goal['target_amount'] }}</span>
                        </div>
                        <div class="w-full bg-slate-600 rounded-full h-3 overflow-hidden">
                            <div
                                class="h-3 rounded-full transition-all duration-500 {{ $goal['is_completed'] ? 'bg-green-500' : 'bg-blue-500' }}"
                                style="width: {{ $goal['progress_percentage'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-slate-400">{{ $goal['progress_percentage'] }}% complete</span>
                            @if(!$goal['is_completed'])
                            <span class="text-slate-400">${{ $goal['remaining_amount'] }} remaining</span>
                            @endif
                        </div>
                    </div>

                    <!-- Deadline Info -->
                    @if($goal['deadline'])
                    <div class="flex items-center text-sm mb-4 {{ $goal['is_overdue'] ? 'text-red-400' : 'text-slate-400' }}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>
                            @if($goal['is_completed'])
                            Completed on {{ $goal['completed_at'] }}
                            @elseif($goal['is_overdue'])
                            Overdue since {{ $goal['deadline'] }}
                            @else
                            {{ round($goal['days_remaining']) }} days remaining ({{ $goal['deadline'] }})
                            @endif
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Actions (stays at bottom) -->
                <div class="flex flex-col md:flex-row gap-3 pt-4 border-t border-slate-600 mt-auto">
                    @if(!$goal['is_completed'])
                    <button
                        wire:click="toggleComplete({{ $goal['id'] }})"
                        class="flex-1 px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl transition-colors">
                        Mark Complete
                    </button>
                    @else
                    <button
                        wire:click="toggleComplete({{ $goal['id'] }})"
                        class="flex-1 px-3 py-2 bg-slate-600 hover:bg-slate-500 text-white text-sm font-bold rounded-xl transition-colors">
                        Reopen
                    </button>
                    @endif
                    <div class="flex gap-2">
                        <button
                            wire:click="editGoal({{ $goal['id'] }})"
                            class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl transition-colors">
                            Edit
                        </button>
                        <button
                            wire:click="deleteGoal({{ $goal['id'] }})"
                            onclick="return confirm('Are you sure you want to delete this goal?')"
                            class="flex-1 px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-colors">
                            Delete
                        </button>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="mt-6 p-4 bg-slate-900 rounded-2xl">
            <nav role="navigation" aria-label="Pagination Navigation">
                {{-- Mobile --}}
                <div class="flex flex-col items-center gap-3 sm:hidden">
                    <span class="text-sm text-slate-400">
                        Showing {{ $goals->firstItem() }} to {{ $goals->lastItem() }} of {{ $goals->total() }} results
                    </span>
                    <div class="flex gap-2">
                        @if ($goals->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-slate-500 bg-slate-700 border border-slate-600 cursor-default rounded-md">
                            Previous
                        </span>
                        @else
                        <button wire:click="previousPage" class="px-4 py-2 text-sm font-medium text-slate-200 bg-slate-700 border border-slate-600 rounded-md hover:bg-slate-600 transition-colors">
                            Previous
                        </button>
                        @endif

                        @if ($goals->hasMorePages())
                        <button wire:click="nextPage" class="px-4 py-2 text-sm font-medium text-slate-200 bg-slate-700 border border-slate-600 rounded-md hover:bg-slate-600 transition-colors">
                            Next
                        </button>
                        @else
                        <span class="px-4 py-2 text-sm font-medium text-slate-500 bg-slate-700 border border-slate-600 cursor-default rounded-md">
                            Next
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Desktop --}}
                <div class="hidden sm:flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-400">
                            Showing
                            <span class="font-medium text-slate-200">{{ $goals->firstItem() }}</span>
                            to
                            <span class="font-medium text-slate-200">{{ $goals->lastItem() }}</span>
                            of
                            <span class="font-medium text-slate-200">{{ $goals->total() }}</span>
                            results
                        </p>
                    </div>
                    <div class="flex gap-1">
                        {{-- Previous Button --}}
                        @if ($goals->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-500 border border-slate-600 cursor-default rounded-md">
                            &laquo;
                        </span>
                        @else
                        <button wire:click="previousPage" class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600 rounded-md transition-colors">
                            &laquo;
                        </button>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                        $currentPage = $goals->currentPage();
                        $lastPage = $goals->lastPage();
                        $onEachSide = 2; // Show 2 pages on each side of current page

                        $start = max(1, $currentPage - $onEachSide);
                        $end = min($lastPage, $currentPage + $onEachSide);
                        @endphp

                        {{-- First page --}}
                        @if ($start > 1)
                        <button wire:click="gotoPage(1)" class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600 rounded-md transition-colors">
                            1
                        </button>
                        @if ($start > 2)
                        <span class="px-3 py-2 text-sm font-medium text-slate-500">...</span>
                        @endif
                        @endif

                        {{-- Page range --}}
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page==$currentPage)
                            <span class="px-3 py-2 text-sm font-medium bg-blue-600 text-white rounded-md">
                            {{ $page }}
                            </span>
                            @else
                            <button wire:click="gotoPage({{ $page }})" class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600 rounded-md transition-colors">
                                {{ $page }}
                            </button>
                            @endif
                            @endfor

                            {{-- Last page --}}
                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                <span class="px-3 py-2 text-sm font-medium text-slate-500">...</span>
                                @endif
                                <button wire:click="gotoPage({{ $lastPage }})" class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600 rounded-md transition-colors">
                                    {{ $lastPage }}
                                </button>
                                @endif

                                {{-- Next Button --}}
                                @if ($goals->hasMorePages())
                                <button wire:click="nextPage" class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-300 border border-slate-600 hover:bg-slate-600 rounded-md transition-colors">
                                    &raquo;
                                </button>
                                @else
                                <span class="px-3 py-2 text-sm font-medium bg-slate-700 text-slate-500 border border-slate-600 cursor-default rounded-md">
                                    &raquo;
                                </span>
                                @endif
                    </div>
                </div>
            </nav>
        </div>
        @endif
    </div>

    <!-- Goal Modal -->
    <x-goal-modal
        :goalForm="$goalForm"
        :editingGoalId="$editingGoalId" />
</div>