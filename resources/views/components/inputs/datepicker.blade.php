{{-- resources/views/components/inputs/datepicker.blade.php --}}

@props([
    'id' => 'datepicker-' . uniqid(),
    'name' => null,
    'value' => '',
    'placeholder' => 'Select a date',
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
            dateValue: '{{ $value }}',
            selectedDate: null,
            year: 0,
            month: 0,
            dates: [],
            blankdays: [],
            dayNames: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            modalMode: {{ $modalMode ? 'true' : 'false' }},
            
            init() {
                let today = new Date();
                
                if (this.dateValue) {
                    // Parse the initial value
                    let parts = this.dateValue.split('-');
                    if (parts.length === 3) {
                        let year = parseInt(parts[0]);
                        let month = parseInt(parts[1]) - 1;
                        let day = parseInt(parts[2]);
                        
                        if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
                            today = new Date(year, month, day);
                            this.selectedDate = new Date(year, month, day);
                        }
                    }
                }
                
                // Set input value based on the dateValue
                if (this.selectedDate) {
                    this.$refs.input.value = this.formatDate(this.selectedDate);
                }
                
                this.year = today.getFullYear();
                this.month = today.getMonth();
                this.generateDatePicker();
            },
            
            isSelectedDate(date) {
                if (!this.selectedDate) return false;
                
                let d = new Date(this.year, this.month, date);
                return d.getDate() === this.selectedDate.getDate() && 
                       d.getMonth() === this.selectedDate.getMonth() &&
                       d.getFullYear() === this.selectedDate.getFullYear();
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
                this.selectedDate = selectedDate;
                this.$refs.input.value = this.formatDate(selectedDate);
                
                // Update the dateValue
                this.dateValue = this.formatDateForDatabase(selectedDate);
                
                // Dispatch events for Livewire or other form handling
                this.$refs.input.dispatchEvent(new Event('input'));
                this.$refs.input.dispatchEvent(new Event('change'));
                
                this.showDatepicker = false;
            },
            
            clearDate() {
                this.selectedDate = null;
                this.dateValue = '';
                this.$refs.input.value = '';
                
                // Dispatch events for Livewire or other form handling
                this.$refs.input.dispatchEvent(new Event('input'));
                this.$refs.input.dispatchEvent(new Event('change'));
                
                this.showDatepicker = false;
            },
            
            setToday() {
                const today = new Date();
                this.selectedDate = today;
                this.year = today.getFullYear();
                this.month = today.getMonth();
                this.$refs.input.value = this.formatDate(today);
                
                // Update the dateValue
                this.dateValue = this.formatDateForDatabase(today);
                
                // Dispatch events for Livewire or other form handling
                this.$refs.input.dispatchEvent(new Event('input'));
                this.$refs.input.dispatchEvent(new Event('change'));
                
                this.generateDatePicker();
                this.showDatepicker = false;
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
                this.showDatepicker = !this.showDatepicker;
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
    >
        <input 
            {{ $attributes->merge(['class' => 'w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500']) }}
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            x-ref="input"
            x-on:click="toggleDatepicker()"
            x-on:keydown.escape="showDatepicker = false"
            readonly
        />

        <div 
            x-show="showDatepicker"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:click.away="showDatepicker = false"
            class="absolute z-50 bg-blue-900 bg-opacity-95 rounded-md shadow-lg p-4 w-64 border border-zinc-700 mt-1"
            style="display: none;"
        >
            <div class="flex justify-between items-center mb-4">
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
                                'bg-indigo-600 text-white font-medium': isSelectedDate(date),
                                'text-zinc-300 hover:bg-zinc-800': !isSelectedDate(date) && !isToday(date),
                                'text-indigo-400 font-medium': !isSelectedDate(date) && isToday(date)
                            }"
                        ></button>
                    </div>
                </template>
            </div>

            <div class="mt-4 flex justify-between">
                <button 
                    type="button"
                    class="text-xs text-blue-400 hover:text-blue-300 font-medium"
                    x-on:click="setToday()"
                >
                    Today
                </button>
                <button 
                    type="button"
                    class="text-xs text-zinc-400 hover:text-zinc-300 font-medium"
                    x-on:click="clearDate()"
                >
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>