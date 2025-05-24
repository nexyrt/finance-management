# Custom Input Components Guide

This guide covers how to use the custom input components with optional labels.

## Installation

1. Copy the component files to your project:
   - `resources/views/components/inputs/datepicker.blade.php`
   - `resources/views/components/inputs/daterangepicker.blade.php`
   - `resources/views/components/inputs/select.blade.php`

2. Make sure Alpine.js and Flatpickr are available in your layout.

3. Add these sections to your layout:
   ```php
   @stack('styles')
   <!-- At the end of the body -->
   @stack('scripts')
   <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
   ```

## Basic Usage

### Date Picker

```php
<!-- Single date picker (default mode) -->
<x-inputs.datepicker name="event_date" />

<!-- Date range picker -->
<x-inputs.datepicker 
    name="booking_period" 
    mode="range" 
    placeholder="Pilih rentang tanggal"
/>

<!-- Multiple date picker -->
<x-inputs.datepicker 
    name="holidays" 
    mode="multiple" 
    placeholder="Pilih beberapa tanggal"
/>

<!-- With time enabled -->
<x-inputs.datepicker 
    name="appointment_time" 
    :enable-time="true" 
    date-format="Y-m-d H:i"
    placeholder="Pilih tanggal dan waktu"
/>

<!-- With disabled dates -->
<x-inputs.datepicker 
    name="available_date" 
    :disabled-dates="[
        [
            'from' => '2025-01-01',
            'to' => '2025-01-07'
        ],
        '2025-02-14' // Single date
    ]"
/>

<!-- With min/max dates -->
<x-inputs.datepicker 
    name="future_date" 
    min-date="today"
    max-date="2025-12-31"
/>
```

### Date Range Picker

```php
<!-- Single date picker (default mode) -->
<x-inputs.daterangepicker name="event_date" />

<!-- Date range picker -->
<x-inputs.daterangepicker 
    name="booking_period" 
    mode="range" 
    placeholder="Pilih rentang tanggal"
/>

<!-- Multiple date picker -->
<x-inputs.daterangepicker 
    name="holidays" 
    mode="multiple" 
    placeholder="Pilih beberapa tanggal"
/>

<!-- With time enabled -->
<x-inputs.daterangepicker 
    name="appointment_time" 
    :enable-time="true" 
    date-format="Y-m-d H:i"
    placeholder="Pilih tanggal dan waktu"
/>
```

### Select Dropdown

```php
<!-- Basic select -->
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

<!-- Different sizes -->
<x-inputs.select 
    :options="$options"
    size="sm"
    label="Small Select"
/>

<!-- For use in modals -->
<x-inputs.select 
    :options="$options"
    :modal-mode="true"
    label="Modal Select"
/>
```

## Form Example

```php
<form method="POST" action="{{ route('bookings.store') }}">
    @csrf
    
    <!-- Select room type -->
    <div class="mb-4">
        <x-inputs.select 
            name="room_type" 
            :options="$roomTypes"
            label="Room Type"
        />
        @error('room_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Date range for booking -->
    <div class="mb-4">
        <x-inputs.datepicker 
            name="booking_dates" 
            mode="range"
            placeholder="Pilih rentang tanggal booking"
            min-date="today"
        />
        @error('booking_dates') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Arrival time with time picker -->
    <div class="mb-4">
        <x-inputs.datepicker 
            name="arrival_time" 
            :enable-time="true"
            date-format="Y-m-d H:i"
            placeholder="Pilih waktu kedatangan"
        />
        @error('arrival_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
public $bookingDates;
public $arrivalTime;
public $availableDates;

// In your Livewire component template
<div>
    <!-- Select - auto-binds to $roomType property -->
    <div class="mb-4">
        <x-inputs.select 
            name="roomType" 
            :options="$roomTypeOptions"
            label="Room Type"
        />
        @error('roomType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Date picker - auto-binds to $bookingDates property -->
    <div class="mb-4">
        <x-inputs.datepicker 
            name="bookingDates" 
            mode="range"
            placeholder="Pilih periode booking"
        />
        @error('bookingDates') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    
    <!-- Multiple date selection - auto-binds to $availableDates property -->
    <div class="mb-4">
        <x-inputs.datepicker 
            name="availableDates" 
            mode="multiple"
            placeholder="Pilih tanggal yang tersedia"
        />
        @error('availableDates') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
```

## Component Properties

### DatePicker Props

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `name` | string | 'date' | Property name for Livewire binding |
| `placeholder` | string | 'Pilih tanggal' | Input placeholder text |
| `disabledDates` | array | [] | Array of disabled date ranges or single dates |
| `mode` | string | 'single' | Picker mode: 'single', 'multiple', 'range' |
| `dateFormat` | string | 'Y-m-d' | Date format for display and value |
| `enableTime` | boolean | false | Enable time selection |
| `minDate` | string | null | Minimum selectable date |
| `maxDate` | string | null | Maximum selectable date |

### DateRangePicker Props

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `name` | string | 'date' | Property name for Livewire binding |
| `placeholder` | string | 'Pilih tanggal' | Input placeholder text |
| `disabledDates` | array | [] | Array of disabled date ranges or single dates |
| `mode` | string | 'single' | Picker mode: 'single', 'multiple', 'range' |
| `dateFormat` | string | 'Y-m-d' | Date format for display and value |
| `enableTime` | boolean | false | Enable time selection |
| `minDate` | string | null | Minimum selectable date |
| `maxDate` | string | null | Maximum selectable date |

### Select Props

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `options` | array | [] | Array of options with 'value' and 'label' keys |
| `placeholder` | string | 'Select an option' | Placeholder text |
| `selected` | string | '' | Initially selected value |
| `label` | string | null | Optional label for the select |
| `modalMode` | boolean | false | Use higher z-index for modals |
| `size` | string | 'md' | Size variant: 'sm', 'md', 'xl' |

## Advanced Examples

### Disabled Dates Configuration

```php
<!-- Complex disabled dates pattern -->
<x-inputs.datepicker 
    name="event_date"
    :disabled-dates="[
        // Disable date ranges
        [
            'from' => '2025-12-24',
            'to' => '2025-12-26'
        ],
        // Disable specific dates
        '2025-01-01',
        '2025-07-04'
    ]"
/>
```

### Custom Styling

```php
<!-- Custom CSS classes -->
<x-inputs.datepicker 
    name="styled_date"
    class="custom-datepicker-class"
/>

<x-inputs.select 
    :options="$options"
    class="custom-select-class"
/>
```

## Key Features

1. **Auto Livewire Binding**: Components automatically bind to Livewire properties using the `name` attribute
2. **Consistent Dark Theme**: Matches your zinc-based color scheme
3. **Flexible Configuration**: Multiple modes and options for date pickers
4. **Accessibility**: Proper labeling and keyboard navigation
5. **Reusable**: Works across all Livewire components without additional wire:model attributes

## Important Notes

- **Livewire Integration**: Simply use the `name` attribute to bind to your Livewire property. No need for `wire:model`
- **Property Naming**: The `name` attribute value should match your Livewire component property name exactly
- **Auto Updates**: Changes in the components automatically update your Livewire properties via `@this.set()`

## Styling

The components use your application's dark theme with zinc colors:
- Background: `bg-zinc-600` for inputs, `bg-zinc-800` for dropdowns
- Text: `text-gray-200` for input text, `text-zinc-200` for options
- Borders: `border-zinc-700` for dropdown borders
- Focus states: `focus:ring-blue-500` for better UX