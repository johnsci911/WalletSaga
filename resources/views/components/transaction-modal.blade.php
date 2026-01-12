@props([
    'show' => false,
    'type' => '',
    //'Earning'or'Expense''form',
    'categories',
    'editingEntryId',
    'editingEntryType',
    'modalState',
    'submitAction',
])

@php
    $isEarning = $type === 'Earning';
    $color = $isEarning ? 'green' : 'red';
    $formModel = $isEarning ? 'earningForm' : 'expenseForm';
@endphp

<div
    x-data="{ show: @entangle($modalState) }"
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
                <h3 class="text-xl font-bold text-{{ $color }}-500" id="modal-title">
                    {{ $editingEntryId && $editingEntryType === $type ? 'Update' : 'Add' }} {{ $type }}
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form wire:submit.prevent="{{ $submitAction }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Date -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Date:</label>
                                <input
                                    type="datetime-local"
                                    wire:model="{{ $formModel }}.date"
                                    x-ref="{{ strtolower($type) }}DateInput"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-{{ $color }}-500 focus:border-transparent"
                                    required
                                />
                            </div>

                            <!-- Amount -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Amount:</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="{{ $formModel }}.amount"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-{{ $color }}-500 focus:border-transparent"
                                    placeholder="0.00"
                                    required
                                />
                            </div>

                            <!-- Category -->
                            <div class="space-y-2">
                                <label class="text-slate-300 font-bold text-sm">Category:</label>
                                <select
                                    wire:model="{{ $formModel }}.category"
                                    class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-{{ $color }}-500 focus:border-transparent"
                                    required
                                >
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-2">
                            <label class="text-slate-300 font-bold text-sm">Description:</label>
                            <textarea
                                wire:model="{{ $formModel }}.description"
                                class="border border-slate-600 bg-slate-700 text-slate-100 rounded-xl p-2.5 w-full focus:ring-2 focus:ring-{{ $color }}-500 focus:border-transparent resize-none"
                                rows="8"
                                placeholder="Enter description..."
                                required
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
                            class="px-4 py-2 bg-{{ $color }}-500 text-white hover:bg-{{ $color }}-600 font-bold rounded-xl transition-colors"
                        >
                            {{ $editingEntryId && $editingEntryType === $type ? 'Update' : 'Add' }} {{ $type }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
