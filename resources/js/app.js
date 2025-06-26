import 'flowbite';
import '../../vendor/masmerise/livewire-toaster/resources/js';
import './currency-component.js';
import { initSearchableDropdowns } from './searchable-dropdown';

// Initialize on app load
document.addEventListener('DOMContentLoaded', () => {
    initSearchableDropdowns();
});

// Re-initialize after Livewire navigation/updates
document.addEventListener('livewire:navigated', () => {
    setTimeout(initSearchableDropdowns, 100);
});

// For Livewire 3.x - Critical for fixing the dropdown issue
if (typeof Livewire !== 'undefined') {
    // Re-initialize after each Livewire update
    Livewire.hook('morph.updated', ({ el, component }) => {
        // Small delay to ensure DOM is fully updated
        setTimeout(() => {
            initSearchableDropdowns();
        }, 50);
    });
    
    // Also handle component initialization
    Livewire.hook('component.init', ({ component }) => {
        setTimeout(() => {
            initSearchableDropdowns();
        }, 50);
    });
    
    // Handle element updates
    Livewire.hook('element.updated', ({ el, component }) => {
        // Check if the updated element contains searchable dropdowns
        if (el.querySelector('[data-searchable-dropdown]') || el.hasAttribute('data-searchable-dropdown')) {
            setTimeout(() => {
                initSearchableDropdowns();
            }, 50);
        }
    });
}

