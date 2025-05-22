{{-- resources/views/components/inputs/daterangepicker.blade.php --}}

@props([
    'id' => 'daterangepicker-' . uniqid(),
    'name' => null,
    'startName' => null,
    'endName' => null,
    'startValue' => '',
    'endValue' => '',
    'placeholder' => 'Select date range',
    'separator' => ' - ',
    'label' => null,
    'modalMode' => false, // Add this prop to handle modal context
])

@once
@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endonce

<div class="w-full">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-zinc-300 mb-1">{{ $label }}</label>
    @endif

    <div 
        x-cloak
        class="relative"
        x-data="{
            showDatepicker: false,
            startValue: '{{ $startValue }}',
            endValue: '{{ $endValue }}',
            startDate: null,
            endDate: null,
            year: 0,
            month: 0,
            dates: [],
            blankdays: [],
            selectionStage: 'start',
            dayNames: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            modalMode: {{ $modalMode ? 'true' : 'false' }},
            
            init() {
                let today = new Date();
                
                // Initialize start date
                if (this.startValue) {
                    let startDate = this.parseDate(this.startValue);
                    if (startDate) {
                        this.startDate = startDate;
                    }
                }
                
                // Initialize end date
                if (this.endValue) {
                    let endDate = this.parseDate(this.endValue);
                    if (endDate) {
                        this.endDate = endDate;
                    }
                }
                
                // Set the display value in the input field
                this.updateInputDisplay();
                
                // Initialize hidden inputs
                if (this.startDate) {
                    this.$refs.startInput.value = this.formatDateForDatabase(this.startDate);
                }
                
                if (this.endDate) {
                    this.$refs.endInput.value = this.formatDateForDatabase(this.endDate);
                }
                
                this.year = today.getFullYear();
                this.month = today.getMonth();
                this.generateDatePicker();
                
                // Fix for Livewire interactions
                this.$watch('startValue', (value) => {
                    if (value) {
                        let startDate = this.parseDate(value);
                        if (startDate) {
                            this.startDate = startDate;
                            this.updateInputDisplay();
                        }
                    }
                });
                
                this.$watch('endValue', (value) => {
                    if (value) {
                        let endDate = this.parseDate(value);
                        if (endDate) {
                            this.endDate = endDate;
                            this.updateInputDisplay();
                        }
                    }
                });
            },
            
            parseDate(dateString) {
                // Parse YYYY-MM-DD format
                let parts = dateString.split('-');
                if (parts.length === 3) {
                    let year = parseInt(parts[0]);
                    let month = parseInt(parts[1]) - 1;
                    let day = parseInt(parts[2]);
                    
                    if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
                        return new Date(year, month, day);
                    }
                }
                return null;
            },
            
            isSelectedDate(date) {
                let d = new Date(this.year, this.month, date);
                
                if (this.startDate && d.getTime() === this.startDate.getTime()) {
                    return true;
                }
                
                if (this.endDate && d.getTime() === this.endDate.getTime()) {
                    return true;
                }
                
                return false;
            },
            
            isInRange(date) {
                if (!this.startDate || !this.endDate) return false;
                
                let d = new Date(this.year, this.month, date);
                return d > this.startDate && d < this.endDate;
            },
            
            isDateSelectable(date) {
                let d = new Date(this.year, this.month, date);
                
                if (this.selectionStage === 'end' && this.startDate) {
                    // If selecting end date, it must be after or equal to start date
                    return d >= this.startDate;
                }
                
                return true;
            },
            
            isToday(date) {
                const today = new Date();
                const d = new Date(this.year, this.month, date);
                
                return d.getDate() === today.getDate() &&
                       d.getMonth() === today.getMonth() &&
                       d.getFullYear() === today.getFullYear();
            },
            
            selectDate(date) {
                let selectedDate = new Date(this.year, this.month, date);
                
                if (this.selectionStage === 'start') {
                    this.startDate = selectedDate;
                    this.selectionStage = 'end';
                    
                    // If end date exists and is before new start date, clear it
                    if (this.endDate && this.endDate < this.startDate) {
                        this.endDate = null;
                    }
                } else {
                    // Selecting end date
                    this.endDate = selectedDate;
                    
                    // If end date is before start date, swap them
                    if (this.endDate < this.startDate) {
                        let temp = this.startDate;
                        this.startDate = this.endDate;
                        this.endDate = temp;
                    }
                    
                    // Reset to start selection for next time
                    this.selectionStage = 'start';
                }
                
                this.updateInputValues();
                this.generateDatePicker();
            },
            
            updateInputValues() {
                // Update hidden input values
                if (this.startDate) {
                    this.$refs.startInput.value = this.formatDateForDatabase(this.startDate);
                } else {
                    this.$refs.startInput.value = '';
                }
                
                if (this.endDate) {
                    this.$refs.endInput.value = this.formatDateForDatabase(this.endDate);
                } else {
                    this.$refs.endInput.value = '';
                }
            },
            
            updateInputDisplay() {
                // Update the visible input display
                if (this.startDate && this.endDate) {
                    this.$refs.input.value = this.formatDate(this.startDate) + ' - ' + this.formatDate(this.endDate);
                } else if (this.startDate) {
                    this.$refs.input.value = this.formatDate(this.startDate) + ' - ';
                } else {
                    this.$refs.input.value = '';
                }
            },
            
            clearDates() {
                this.startDate = null;
                this.endDate = null;
                this.selectionStage = 'start';
                this.updateInputValues();
                this.updateInputDisplay();
                
                // Dispatch events for Livewire
                this.$refs.startInput.dispatchEvent(new Event('input'));
                this.$refs.endInput.dispatchEvent(new Event('input'));
                
                this.showDatepicker = false;
            },
            
            setToday() {
                const today = new Date();
                
                if (this.selectionStage === 'start') {
                    this.startDate = today;
                    this.selectionStage = 'end';
                } else {
                    if (today >= this.startDate) {
                        this.endDate = today;
                        this.selectionStage = 'start';
                    }
                }
                
                this.year = today.getFullYear();
                this.month = today.getMonth();
                
                this.updateInputValues();
                this.generateDatePicker();
            },
            
            applyDateRange() {
                if (this.startDate && this.endDate) {
                    this.updateInputDisplay();
                    
                    // Dispatch events for Livewire
                    this.$refs.startInput.dispatchEvent(new Event('input'));
                    this.$refs.endInput.dispatchEvent(new Event('input'));
                    this.$refs.input.dispatchEvent(new Event('input'));
                    this.$refs.input.dispatchEvent(new Event('change'));
                    
                    this.showDatepicker = false;
                }
            },
            
            formatDate(date) {
                // Format date as DD/MM/YYYY
                let day = date.getDate().toString().padStart(2, '0');
                let month = (date.getMonth() + 1).toString().padStart(2, '0');
                let year = date.getFullYear();
                
                return `${day}/${month}/${year}`;
            },
            
            formatDateForDatabase(date) {
                // Format date as YYYY-MM-DD for database storage
                let day = date.getDate().toString().padStart(2, '0');
                let month = (date.getMonth() + 1).toString().padStart(2, '0');
                let year = date.getFullYear();
                
                return `${year}-${month}-${day}`;
            },
            
            toggleDatepicker() {
                // Always reset the visibility state before toggling
                setTimeout(() => {
                    this.showDatepicker = !this.showDatepicker;
                }, 10);
            },
            
            generateDatePicker() {
                // Get the first day of the month
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                let firstDayOfMonth = new Date(this.year, this.month, 1).getDay();
                
                // Calculate blank days (days from previous month)
                this.blankdays = [];
                for (let i = 0; i < firstDayOfMonth; i++) {
                    this.blankdays.push(i);
                }
                
                // Generate days of the current month
                this.dates = [];
                for (let i = 1; i <= daysInMonth; i++) {
                    this.dates.push(i);
                }
            },
            
            prevMonth() {
                if (this.month === 0) {
                    this.month = 11;
                    this.year--;
                } else {
                    this.month--;
                }
                this.generateDatePicker();
            },
            
            nextMonth() {
                if (this.month === 11) {
                    this.month = 0;
                    this.year++;
                } else {
                    this.month++;
                }
                this.generateDatePicker();
            }
        }"
        @click.outside="showDatepicker = false"
    >
        <input 
            {{ $attributes->merge(['class' => 'w-full px-3 py-2.5 bg-zinc-800 border border-zinc-700 rounded-xl shadow-sm hover:border-zinc-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-zinc-200 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500']) }}
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            x-ref="input"
            x-on:click="toggleDatepicker()"
            x-on:keydown.escape="showDatepicker = false"
            readonly
        />

        <input type="hidden" name="{{ $startName ?? 'start_date' }}" x-ref="startInput">
        <input type="hidden" name="{{ $endName ?? 'end_date' }}" x-ref="endInput">

        <div 
            x-show="showDatepicker"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.away="showDatepicker = false"
            class="absolute z-50 bg-zinc-900 rounded-md shadow-lg p-4 border border-zinc-700 mt-1"
            style="display: none; width: 300px;"
        >
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-medium text-zinc-200">
                    <span x-text="selectionStage === 'start' ? 'Select Start Date' : 'Select End Date'"></span>
                </h3>
                <button 
                    type="button"
                    class="text-xs text-zinc-400 hover:text-zinc-300 font-medium"
                    x-on:click="clearDates()"
                >
                    Clear
                </button>
            </div>

            <div class="mt-1 mb-2 flex items-center space-x-2 text-xs">
                <div class="flex-1 px-2 py-1.5 rounded" :class="{'bg-zinc-800': true}">
                    <div class="font-medium text-zinc-400">Start</div>
                    <div class="text-zinc-200" x-text="startDate ? formatDate(startDate) : 'Not selected'"></div>
                </div>
                <div class="text-zinc-500">→</div>
                <div class="flex-1 px-2 py-1.5 rounded" :class="{'bg-zinc-800': true}">
                    <div class="font-medium text-zinc-400">End</div>
                    <div class="text-zinc-200" x-text="endDate ? formatDate(endDate) : 'Not selected'"></div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-3">
                <button 
                    type="button"
                    class="p-1 transition duration-100 text-zinc-400 hover:text-zinc-200"
                    x-on:click.prevent="prevMonth"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <div class="text-base font-medium text-zinc-200" x-text="monthNames[month] + ' ' + year"></div>
                
                <button 
                    type="button"
                    class="p-1 transition duration-100 text-zinc-400 hover:text-zinc-200"
                    x-on:click.prevent="nextMonth"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- Days of week --}}
            <div class="grid grid-cols-7 mb-2 text-center">
                <template x-for="(day, index) in dayNames" :key="index">
                    <div class="px-0.5">
                        <div class="text-zinc-400 text-xs font-medium" x-text="day"></div>
                    </div>
                </template>
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="blankday in blankdays" :key="blankday">
                    <div class="py-1 text-center text-sm"></div>
                </template>
                
                <template x-for="(date, dateIndex) in dates" :key="dateIndex">
                    <div class="p-0.5 text-center">
                        <button
                            type="button"
                            x-text="date"
                            x-on:click="selectDate(date)"
                            class="h-7 w-7 mx-auto text-center rounded-full text-sm leading-none flex items-center justify-center transition duration-150"
                            :class="{
                                'bg-blue-600 text-white font-medium': isSelectedDate(date),
                                'bg-zinc-700 text-white': isInRange(date),
                                'text-zinc-300 hover:bg-zinc-800': !isSelectedDate(date) && !isInRange(date) && !isToday(date),
                                'text-blue-400 font-medium': !isSelectedDate(date) && !isInRange(date) && isToday(date)
                            }"
                            :disabled="!isDateSelectable(date)"
                        ></button>
                    </div>
                </template>
            </div>

            <div class="mt-4 flex justify-between">
                <button 
                    type="button"
                    class="px-3 py-1 text-xs text-zinc-400 hover:text-zinc-300 font-medium rounded hover:bg-zinc-800"
                    x-on:click="setToday()"
                >
                    Today
                </button>
                <button 
                    type="button"
                    class="px-3 py-1 text-xs text-zinc-200 hover:text-white font-medium bg-blue-600 hover:bg-blue-700 rounded"
                    x-on:click="applyDateRange()"
                    :disabled="!startDate || !endDate"
                    :class="{'opacity-50 cursor-not-allowed': !startDate || !endDate, 'cursor-pointer': startDate && endDate}"
                >
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>