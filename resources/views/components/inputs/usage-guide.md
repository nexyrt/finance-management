# Flux UI Form Components Guide

This guide covers how to use the updated form components with optional labels.

## Installation

1. Copy the component files to your project:
   - `resources/views/components/inputs/datepicker.blade.php`
   - `resources/views/components/inputs/daterangepicker.blade.php`
   - `resources/views/components/inputs/select.blade.php`

2. Make sure Alpine.js is available in your layout.

3. Add these sections to your layout:
   ```php
   @stack('styles')
   <!-- At the end of the body -->
   @stack('scripts')
   ```

## Basic Usage

### Datepicker

```php
<!-- Without label -->
<x-inputs.datepicker name="event_date" />

<!-- With label -->
<x-inputs.datepicker name="event_date" label="Event Date" />

<!-- With Livewire -->
<x-inputs.datepicker wire:model="eventDate" label="Event Date" />
```

### Date Range Picker

```php
<!-- Without label -->
<x-inputs.daterangepicker 
    startName="check_in" 
    endName="check_out" 
/>

<!-- With label -->
<x-inputs.daterangepicker 
    startName="check_in" 
    endName="check_out" 
    label="Booking Period" 
/>

<!-- With Livewire -->
<x-inputs.daterangepicker 
    startName="startDate" 
    endName="endDate" 
    wire:model="startDate" 
    label="Date Range" 
/>
```

### Select Dropdown

```php
<!-- Without label -->
<x-inputs.select 
    :options="[
        ['value' => 'option1', 'label' => 'Option 1'],
        ['value' => 'option2', 'label' => 'Option 2']
    ]"
/>

<!-- With label -->
<x-inputs.select 
    :options="$roomTypes"
    label="Room Type"
/>

<!-- With Livewire -->
<x-inputs.select 
    wire:model="selectedOption" 
    :options="$options"
    label="Select an Option" 
/>
```

## Form Example

```php
<form method="POST" action="{{ route('bookings.store') }}">
    @csrf
    
    <!-- Select room type with label -->
    <div class="mb-4">
        <x-inputs.select 
            name="room_type" 
            :options="$roomTypes"
            label="Room Type"
        />
        @error('room_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Date range for booking with label -->
    <div class="mb-4">
        <x-inputs.daterangepicker 
            startName="check_in" 
            endName="check_out"
            label="Stay Period"
        />
        @error('check_in') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @error('check_out') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Arrival date with label -->
    <div class="mb-4">
        <x-inputs.datepicker 
            name="arrival_date" 
            label="Arrival Date"
        />
        @error('arrival_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
        Create Booking
    </button>
</form>
```

## Livewire Component Example

```php
// In your Livewire component class
public $roomType;
public $startDate;
public $endDate;
public $arrivalDate;

// In your Livewire component template
<div>
    <!-- All components with labels -->
    <div class="mb-4">
        <x-inputs.select 
            wire:model="roomType" 
            :options="$roomTypeOptions"
            label="Room Type"
        />
        @error('roomType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-4">
        <x-inputs.daterangepicker 
            startName="startDate" 
            endName="endDate" 
            wire:model="startDate"
            label="Stay Period"
        />
        @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <div class="mb-4">
        <x-inputs.datepicker 
            wire:model="arrivalDate" 
            label="Arrival Date"
        />
        @error('arrivalDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
```

## Benefits of Using Labels

1. **Improved Accessibility**: Labels help screen readers identify form fields
2. **Better User Experience**: Clear labeling makes forms easier to understand
3. **Consistent Design**: Standardizes the appearance of form fields
4. **Optional Implementation**: Labels can be omitted when not needed

All components work consistently with or without labels, so you can use whichever format best suits your specific UI requirements.

## Styling

The labels use the `text-zinc-300` color class to match the dark theme of your application. You can modify this in the component files if you need a different appearance.