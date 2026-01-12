@props([
    'goalForm',
    'editingGoalId',
])

<div
    x-data="{ show: @entangle('showGoalModal') }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    style="display: none"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
        @click="show = false"
    ></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block w-full max-w-2xl overflow-hidden text-left align-middle transition-all transform bg-slate-800 rounded-2xl shadow-xl"
            @click.away="show = false"
        >
            <!-- Close Button -->
            <button
                @click="show = false"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-200 transition-colors z-10"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-700">
                <h3 class="text-xl font-bold text-blue-500" id="modal-title">
                    {{ $editingGoalId ? 'Update Goal' : 'Create New Goal' }}
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form wire:submit.prevent="submitGoal" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Goal Name -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Goal Name:</label>
                                <input
                                    type="text"
                                    wire:model="goalForm.name"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., Vacation Fund"
                                    required
                                />
                            </div>

                            <!-- Target Amount -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Target Amount:</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="goalForm.target_amount"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="0.00"
                                    required
                                />
                            </div>

                            <!-- Current Amount -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Current Amount:</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="goalForm.current_amount"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="0.00"
                                />
                            </div>

                            <!-- Deadline -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Deadline (Optional):</label>
                                <input
                                    type="date"
                                    wire:model="goalForm.deadline"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>

                            <!-- Category -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Category (Optional):</label>
                                <input
                                    type="text"
                                    wire:model="goalForm.category"
                                    list="category-suggestions"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., Travel, Emergency, Purchase"
                                />
                                <datalist id="category-suggestions">
                                    @foreach ($this->categories as $category)
                                        <option value="{{ $category }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-2">
                            <label class="text-slate-300 font-bold text-sm">Description (Optional):</label>
                            <textarea
                                wire:model="goalForm.description"
                                class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                rows="12"
                                placeholder="Describe your goal..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                        <button
                            type="button"
                            @click="show = false"
                            class="px-4 py-2 bg-slate-700 text-slate-300 hover:bg-slate-600 font-bold rounded-xl transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 font-bold rounded-xl transition-colors"
                        >
                            {{ $editingGoalId ? 'Update' : 'Create' }} Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
