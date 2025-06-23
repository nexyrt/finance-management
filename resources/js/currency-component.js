// resources/js/currency-component.js

document.addEventListener('alpine:init', () => {
    Alpine.data('currencyInput', (options = {}) => ({
        // Options dengan default values
        name: options.name || 'amount',
        initialValue: options.value || 0,
        placeholder: options.placeholder || '12.500.000',
        wireModel: options.wireModel || null,
        
        // Internal state
        rawValue: 0,
        maxValue: 999999999999999, // 15 digit maksimum untuk DECIMAL(15,2)
        
        init() {
            // Set initial value
            this.rawValue = this.initialValue;
            
            // Format input saat load
            this.$nextTick(() => {
                if (this.$refs.input && this.$refs.input.value) {
                    let numbers = this.$refs.input.value.replace(/[^0-9]/g, '');
                    this.rawValue = numbers ? parseInt(numbers) : 0;
                    
                    // Validasi maksimum value
                    if (this.rawValue > this.maxValue) {
                        this.rawValue = this.maxValue;
                    }
                    
                    this.$refs.input.value = this.formatCurrency(this.rawValue);
                }
                this.updateAll();
            });
        },
        
        handleInput(event) {
            let numbers = event.target.value.replace(/[^0-9]/g, '');
            let numericValue = numbers ? parseInt(numbers) : 0;
            
            // Validasi maksimum value
            if (numericValue > this.maxValue) {
                numericValue = this.maxValue;
                
                // Tampilkan peringatan visual
                event.target.classList.add('border-red-500', 'bg-red-50');
                setTimeout(() => {
                    event.target.classList.remove('border-red-500', 'bg-red-50');
                }, 2000);
            }
            
            this.rawValue = numericValue;
            event.target.value = this.formatCurrency(this.rawValue);
            this.updateAll();
        },
        
        restrictInput(event) {
            // Izinkan Ctrl+A, Ctrl+V, Ctrl+C, Ctrl+X
            if (event.ctrlKey && [65, 86, 67, 88].includes(event.keyCode)) {
                return;
            }
            
            // Izinkan backspace, tab, delete, arrow keys, home, end
            if ([8, 9, 46, 37, 38, 39, 40, 35, 36].includes(event.keyCode)) {
                return;
            }
            
            // Hanya izinkan angka (0-9)
            if ((event.keyCode < 48 || event.keyCode > 57) && 
                (event.keyCode < 96 || event.keyCode > 105)) {
                event.preventDefault();
            }
        },
        
        handlePaste(event) {
            event.preventDefault();
            let paste = (event.clipboardData || window.clipboardData).getData('text');
            let numbers = paste.replace(/[^0-9]/g, '');
            
            if (numbers) {
                let numericValue = parseInt(numbers);
                
                // Validasi maksimum value
                if (numericValue > this.maxValue) {
                    numericValue = this.maxValue;
                    
                    // Tampilkan peringatan visual
                    event.target.classList.add('border-red-500', 'bg-red-50');
                    setTimeout(() => {
                        event.target.classList.remove('border-red-500', 'bg-red-50');
                    }, 2000);
                }
                
                this.rawValue = numericValue;
                event.target.value = this.formatCurrency(this.rawValue);
                this.updateAll();
            }
        },
        
        formatCurrency(value) {
            if (!value || value === 0) return '';
            return parseInt(value).toLocaleString('id-ID');
        },
        
        updateAll() {
            // Update hidden input
            if (this.$refs.hiddenInput) {
                this.$refs.hiddenInput.value = this.rawValue;
            }
            
            // Update Livewire model jika ada
            if (this.wireModel && this.$wire) {
                this.$wire.set(this.wireModel, this.rawValue);
            }
        },
        
        // Public methods
        setValue(value) {
            // Validasi maksimum value saat set programmatically
            if (value > this.maxValue) {
                value = this.maxValue;
            }
            
            this.rawValue = value;
            if (this.$refs.input) {
                this.$refs.input.value = this.formatCurrency(value);
            }
            this.updateAll();
        },
        
        getValue() {
            return this.rawValue;
        },
        
        // Helper method untuk mendapatkan maksimum value yang diformat
        getMaxValueFormatted() {
            return this.formatCurrency(this.maxValue);
        }
    }));
});