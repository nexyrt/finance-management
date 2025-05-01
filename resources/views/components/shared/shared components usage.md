# Custom Button Components Usage Guide

I've created two custom button components to replace the problematic Flux buttons:

1. `<x-button>` - A general-purpose button component
2. `<x-icon-button>` - A specialized button for icon-only actions

These components provide consistent styling and behavior while avoiding the errors you were experiencing with Flux buttons.

## Installation

Place these files in your Laravel project:
- `resources/views/components/button.blade.php`
- `resources/views/components/icon-button.blade.php`

## Basic Button Component

### Basic Usage

```blade
<x-button>
    Click Me
</x-button>
```

### Button Variants

```blade
<x-button variant="primary">Primary Button</x-button>
<x-button variant="secondary">Secondary Button</x-button>
<x-button variant="success">Success Button</x-button>
<x-button variant="danger">Danger Button</x-button>
<x-button variant="warning">Warning Button</x-button>
<x-button variant="info">Info Button</x-button>
<x-button variant="link">Link Button</x-button>
<x-button variant="outline">Outline Button</x-button>
<x-button variant="ghost">Ghost Button</x-button>
```

### Button Sizes

```blade
<x-button size="xs">Extra Small</x-button>
<x-button size="sm">Small</x-button>
<x-button size="md">Medium (Default)</x-button>
<x-button size="lg">Large</x-button>
<x-button size="xl">Extra Large</x-button>
```

### With Icons

```blade
<x-button icon="M12 4v16m8-8H4">
    Add New
</x-button>

<x-button icon="M5 13l4 4L19 7" iconPosition="left">
    Confirm
</x-button>

<x-button icon="M17 8l4 4m0 0l-4 4m4-4H3" iconPosition="right">
    Next
</x-button>
```

### Disabled State

```blade
<x-button disabled>
    Cannot Click
</x-button>
```

### Full Width

```blade
<x-button fullWidth>
    Full Width Button
</x-button>
```

### Form Buttons

```blade
<x-button type="submit">
    Submit Form
</x-button>

<x-button type="reset" variant="secondary">
    Reset Form
</x-button>
```

### With Additional Classes

```blade
<x-button class="shadow-lg">
    Custom Class Button
</x-button>
```

## Icon Button Component

The `<x-icon-button>` component is designed specifically for icon-only buttons, common in tables for actions like edit, delete, and view.

### Basic Usage

```blade
<x-icon-button>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
</x-icon-button>
```

### Button Variants

```blade
<x-icon-button variant="primary">
    <!-- Icon SVG here -->
</x-icon-button>

<x-icon-button variant="success">
    <!-- Icon SVG here -->
</x-icon-button>

<x-icon-button variant="danger">
    <!-- Icon SVG here -->
</x-icon-button>

<x-icon-button variant="warning">
    <!-- Icon SVG here -->
</x-icon-button>

<x-icon-button variant="info">
    <!-- Icon SVG here -->
</x-icon-button>
```

### Combined with Flux Modal Trigger

```blade
<flux:modal.trigger name="edit-client-{{ $client->id }}" wire:click="editClient({{ $client->id }})">
    <x-icon-button variant="warning">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
    </x-icon-button>
</flux:modal.trigger>
```

## Common Action Button Examples

### View Button
```blade
<x-icon-button variant="info">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
</x-icon-button>
```

### Edit Button
```blade
<x-icon-button variant="warning">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
</x-icon-button>
```

### Delete Button
```blade
<x-icon-button variant="danger">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>
</x-icon-button>
```

### Add New Button
```blade
<x-button icon="M12 4v16m8-8H4">
    Add New Client
</x-button>
```

### Submit Form Button
```blade
<x-button type="submit" variant="primary">
    Save Changes
</x-button>
```

### Cancel Button
```blade
<x-button type="button" variant="secondary">
    Cancel
</x-button>
```

## Within Modals

```blade
<div class="mt-6 flex justify-end space-x-3">
    <flux:modal.close>
        <x-button variant="secondary">
            Cancel
        </x-button>
    </flux:modal.close>
    
    <x-button type="submit" variant="primary">
        Save Changes
    </x-button>
</div>
```

These components provide a consistent, error-free alternative to Flux buttons while maintaining the same visual design and functionality.
