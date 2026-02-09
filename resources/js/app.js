import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts;

import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import Day.js and locale support
import dayjs from 'dayjs';
import 'dayjs/locale/id'; // Indonesian
import 'dayjs/locale/zh-cn'; // Chinese Simplified

// Set Day.js locale based on Laravel locale
const locale = document.documentElement.lang || 'id';
const dayjsLocale = locale === 'zh' ? 'zh-cn' : locale;
dayjs.locale(dayjsLocale);

// Update Day.js locale when language changes
document.addEventListener('livewire:navigated', () => {
    const currentLocale = document.documentElement.lang || 'id';
    const currentDayjsLocale = currentLocale === 'zh' ? 'zh-cn' : currentLocale;
    dayjs.locale(currentDayjsLocale);
});

window.dayjs = dayjs;

// Initialize Quill editor with Livewire compatibility
function initializeQuill() {
    const editorElement = document.querySelector('#editor');

    if (editorElement && !editorElement.hasAttribute('data-quill-initialized')) {
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote', 'code-block'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        // Mark as initialized to prevent duplicate initialization
        editorElement.setAttribute('data-quill-initialized', 'true');

        // Optional: Store quill instance globally for Livewire integration
        window.quillEditor = quill;
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', initializeQuill);

// Re-initialize after Livewire updates (if using Livewire)
document.addEventListener('livewire:navigated', initializeQuill);

// Prevent default drag and drop behavior globally to avoid file opening in new tab
// This is especially important for files dragged from WhatsApp Web or other sources
document.addEventListener('DOMContentLoaded', () => {
    // Helper function to check if element is an upload component
    function isUploadComponent(element) {
        if (!element) return false;

        // Check multiple selectors for upload components
        const uploadSelectors = [
            '[x-data*="tallstackui_formUpload"]',
            'input[type="file"]',
            '[data-upload-zone]',
            '[x-ref="files"]',
            '[dusk="tallstackui_upload_floating"]',
            '[dusk="tallstackui_file_select"]'
        ];

        // Check if element itself or any parent matches
        for (const selector of uploadSelectors) {
            if (element.closest(selector)) {
                return true;
            }
        }

        return false;
    }

    // Allow dragover on upload components (required for drop to work)
    document.addEventListener('dragover', (e) => {
        const isUpload = isUploadComponent(e.target);

        // Always prevent default to avoid browser opening files
        e.preventDefault();

        if (isUpload) {
            e.dataTransfer.dropEffect = 'copy'; // Show copy cursor
        } else {
            e.dataTransfer.dropEffect = 'none'; // Show "no drop" cursor
        }
    }, false);

    // Prevent default drop on entire document EXCEPT on upload components
    document.addEventListener('drop', (e) => {
        const isUpload = isUploadComponent(e.target);

        if (!isUpload) {
            // Prevent file opening on non-upload areas
            e.preventDefault();
            e.stopPropagation();
        }
        // For upload components: let TallStackUI handle the drop
    }, false);
});

// Re-attach handlers after Livewire navigation
document.addEventListener('livewire:navigated', () => {
    // Handlers are already attached globally, no need to re-attach
});

// Enhanced drag and drop support for TallStackUI upload components
// This ensures files from WhatsApp Web and other sources work properly
document.addEventListener('DOMContentLoaded', () => {
    // Find and enhance all upload components
    function enhanceUploadComponents() {
        const uploadComponents = document.querySelectorAll('[x-data*="tallstackui_formUpload"]');

        uploadComponents.forEach(component => {
            const fileInput = component.querySelector('input[type="file"]');

            if (!fileInput) return;

            // Mark as enhanced to avoid double-binding
            if (component.hasAttribute('data-upload-enhanced')) return;
            component.setAttribute('data-upload-enhanced', 'true');

            // Prevent default on the wrapper itself
            component.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                component.classList.add('dragging');
            });

            component.addEventListener('dragleave', (e) => {
                e.preventDefault();
                component.classList.remove('dragging');
            });

            component.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                component.classList.remove('dragging');

                // Get files from drop event
                const files = e.dataTransfer?.files;

                if (files && files.length > 0) {
                    // Create a new FileList-like object
                    const dataTransfer = new DataTransfer();

                    // Add all dropped files to the input
                    Array.from(files).forEach(file => {
                        dataTransfer.items.add(file);
                    });

                    // Assign files to input element
                    fileInput.files = dataTransfer.files;

                    // Trigger change event to notify Livewire/Alpine
                    const changeEvent = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(changeEvent);

                    console.log('Files dropped and uploaded:', files.length, 'file(s)');
                }
            });
        });
    }

    // Enhance on load
    enhanceUploadComponents();

    // Re-enhance after Livewire updates
    document.addEventListener('livewire:navigated', enhanceUploadComponents);

    // Re-enhance after Livewire component updates (for modals)
    Livewire.hook('morph.updated', () => {
        enhanceUploadComponents();
    });

    // Also enhance after a short delay for modals that appear dynamically
    document.addEventListener('livewire:navigated', () => {
        setTimeout(enhanceUploadComponents, 100);
    });
});

// Livewire.on('open-preview-delayed', (data) => {
//     setTimeout(() => {
//         window.open(data[0].url, '_blank');
//     }, data[0].delay);
// });