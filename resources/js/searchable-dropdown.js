// resources/js/searchable-dropdown.js

class SearchableDropdown {
    constructor(element) {
        this.element = element;
        this.input = element.querySelector('[data-input]');
        this.dropdown = element.querySelector('[data-dropdown]');
        this.list = element.querySelector('[data-list]');
        this.noResults = element.querySelector('[data-no-results]');
        this.hiddenInput = element.querySelector('[data-hidden]');
        
        if (!this.input || !this.dropdown || !this.list) {
            console.warn('SearchableDropdown: Required elements not found');
            return;
        }
        
        this.originalItems = Array.from(this.list.children);
        this.currentIndex = -1;
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInitialValue();
    }
    
    loadInitialValue() {
        // Load initial value from hidden input if exists
        if (this.hiddenInput && this.hiddenInput.value) {
            const initialItem = this.originalItems.find(item => {
                const link = item.querySelector('a');
                return link && link.getAttribute('data-value') === this.hiddenInput.value;
            });
            
            if (initialItem) {
                const link = initialItem.querySelector('a');
                this.input.value = link.textContent.trim();
            }
        }
    }
    
    bindEvents() {
        // Create bound functions to enable proper cleanup
        this.boundFocusHandler = () => this.openDropdown();
        this.boundInputHandler = (e) => {
            this.openDropdown();
            this.filterItems(e.target.value);
            this.currentIndex = -1; // Reset selection
        };
        this.boundClickHandler = (e) => {
            e.preventDefault();
            const link = e.target.closest('[data-value]');
            if (link) {
                this.selectItem(link);
            }
        };
        this.boundKeydownHandler = (e) => this.handleKeyboard(e);
        this.boundGlobalClickHandler = (e) => {
            if (!this.element.contains(e.target)) {
                this.closeDropdown();
            }
        };
        this.boundClearHandler = (e) => {
            e.preventDefault();
            this.clearSelection();
        };
        
        // Show dropdown on focus
        this.input.addEventListener('focus', this.boundFocusHandler);
        
        // Filter items on input
        this.input.addEventListener('input', this.boundInputHandler);
        
        // Handle selection
        this.dropdown.addEventListener('click', this.boundClickHandler);
        
        // Handle keyboard navigation
        this.input.addEventListener('keydown', this.boundKeydownHandler);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', this.boundGlobalClickHandler);
        
        // Handle clear button if exists
        const clearBtn = this.element.querySelector('[data-clear]');
        if (clearBtn) {
            clearBtn.addEventListener('click', this.boundClearHandler);
        }
    }
    
    openDropdown() {
        this.dropdown.classList.remove('hidden');
        this.isOpen = true;
        this.filterItems(this.input.value);
    }
    
    closeDropdown() {
        this.dropdown.classList.add('hidden');
        this.isOpen = false;
        this.currentIndex = -1;
        this.clearHighlight();
    }
    
    filterItems(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        this.originalItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                const text = link.textContent.toLowerCase();
                const value = link.getAttribute('data-value').toLowerCase();
                const matches = text.includes(term) || value.includes(term);
                
                item.style.display = matches ? 'block' : 'none';
                if (matches) visibleCount++;
            }
        });
        
        // Show/hide no results message
        if (this.noResults) {
            if (visibleCount === 0 && term !== '') {
                this.noResults.classList.remove('hidden');
            } else {
                this.noResults.classList.add('hidden');
            }
        }
    }
    
    selectItem(link) {
        const value = link.getAttribute('data-value');
        const text = link.textContent.trim();
        
        this.input.value = text;
        
        // Update hidden input if exists
        if (this.hiddenInput) {
            this.hiddenInput.value = value;
            
            // Trigger change event for Livewire
            this.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        this.closeDropdown();
        
        // Dispatch custom event
        this.element.dispatchEvent(new CustomEvent('dropdown:selected', {
            detail: { value, text },
            bubbles: true
        }));
    }
    
    clearSelection() {
        this.input.value = '';
        if (this.hiddenInput) {
            this.hiddenInput.value = '';
            this.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        this.filterItems('');
        this.input.focus();
        
        // Dispatch custom event
        this.element.dispatchEvent(new CustomEvent('dropdown:cleared', {
            bubbles: true
        }));
    }
    
    handleKeyboard(e) {
        if (!this.isOpen && ['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
            this.openDropdown();
        }
        
        const visibleItems = this.getVisibleItems();
        
        switch(e.key) {
            case 'Escape':
                e.preventDefault();
                this.closeDropdown();
                break;
                
            case 'ArrowDown':
                e.preventDefault();
                this.currentIndex = Math.min(this.currentIndex + 1, visibleItems.length - 1);
                this.updateHighlight(visibleItems);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.currentIndex = Math.max(this.currentIndex - 1, -1);
                this.updateHighlight(visibleItems);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.currentIndex >= 0 && visibleItems[this.currentIndex]) {
                    const link = visibleItems[this.currentIndex].querySelector('a');
                    if (link) {
                        this.selectItem(link);
                    }
                }
                break;
                
            case 'Tab':
                this.closeDropdown();
                break;
        }
    }
    
    getVisibleItems() {
        return this.originalItems.filter(item => item.style.display !== 'none');
    }
    
    updateHighlight(visibleItems) {
        this.clearHighlight();
        
        if (this.currentIndex >= 0 && visibleItems[this.currentIndex]) {
            const link = visibleItems[this.currentIndex].querySelector('a');
            if (link) {
                link.classList.add('bg-blue-100', 'dark:bg-blue-600');
                
                // Scroll into view if needed
                link.scrollIntoView({ block: 'nearest' });
            }
        }
    }
    
    clearHighlight() {
        this.originalItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                link.classList.remove('bg-blue-100', 'dark:bg-blue-600');
            }
        });
    }
    
    // Public methods for external control
    setValue(value, text = null) {
        if (this.hiddenInput) {
            this.hiddenInput.value = value;
        }
        
        if (text) {
            this.input.value = text;
        } else {
            // Find text from options
            const item = this.originalItems.find(item => {
                const link = item.querySelector('a');
                return link && link.getAttribute('data-value') === value;
            });
            
            if (item) {
                const link = item.querySelector('a');
                this.input.value = link.textContent.trim();
            }
        }
    }
    
    getValue() {
        return this.hiddenInput ? this.hiddenInput.value : null;
    }
    
    getText() {
        return this.input.value;
    }
    
    disable() {
        this.input.disabled = true;
        this.element.classList.add('opacity-50', 'pointer-events-none');
    }
    
    enable() {
        this.input.disabled = false;
        this.element.classList.remove('opacity-50', 'pointer-events-none');
    }
    
    // Destroy method to clean up event listeners
    destroy() {
        // Remove all event listeners
        if (this.input) {
            this.input.removeEventListener('focus', this.boundFocusHandler);
            this.input.removeEventListener('input', this.boundInputHandler);
            this.input.removeEventListener('keydown', this.boundKeydownHandler);
        }
        
        if (this.dropdown) {
            this.dropdown.removeEventListener('click', this.boundClickHandler);
        }
        
        const clearBtn = this.element.querySelector('[data-clear]');
        if (clearBtn) {
            clearBtn.removeEventListener('click', this.boundClearHandler);
        }
        
        // Remove global click listener
        document.removeEventListener('click', this.boundGlobalClickHandler);
        
        // Clear references
        this.element.searchableDropdown = null;
    }
}

// Auto-initialize all searchable dropdowns
function initSearchableDropdowns() {
    const dropdowns = document.querySelectorAll('[data-searchable-dropdown]');
    
    dropdowns.forEach(dropdown => {
        // Always destroy and recreate to avoid stale references
        if (dropdown.searchableDropdown) {
            dropdown.searchableDropdown.destroy();
        }
        dropdown.searchableDropdown = new SearchableDropdown(dropdown);
    });
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', initSearchableDropdowns);

// Re-initialize after Livewire updates
document.addEventListener('livewire:navigated', initSearchableDropdowns);

// Critical: Re-initialize after Livewire morphs the DOM
if (typeof Livewire !== 'undefined') {
    // Livewire 3.x events
    Livewire.hook('morph.updated', () => {
        // Use setTimeout to ensure DOM is fully updated
        setTimeout(initSearchableDropdowns, 0);
    });
    
    Livewire.hook('component.init', () => {
        setTimeout(initSearchableDropdowns, 0);
    });
}

// Export for manual usage
export { SearchableDropdown, initSearchableDropdowns };

// Make initSearchableDropdowns available globally for debugging and manual calls
window.initSearchableDropdowns = initSearchableDropdowns;