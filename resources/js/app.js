import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts;

import Chart from 'chart.js/auto';
window.Chart = Chart;

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
// This is especially important for files dragged from WhatsApp Web
document.addEventListener('DOMContentLoaded', () => {
    // Prevent default dragover on entire document
    document.addEventListener('dragover', (e) => {
        // Check if dragging over an upload component or file input
        const isOverUpload = e.target.closest('[x-data*="tallstackui_formUpload"]') ||
                            e.target.closest('input[type="file"]');

        if (isOverUpload) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, false);

    // Prevent default drop on entire document
    document.addEventListener('drop', (e) => {
        // Check if dropping on an upload component or file input
        const isOverUpload = e.target.closest('[x-data*="tallstackui_formUpload"]') ||
                            e.target.closest('input[type="file"]');

        if (isOverUpload) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            // Prevent file opening on any other area
            e.preventDefault();
        }
    }, false);
});

// Livewire.on('open-preview-delayed', (data) => {
//     setTimeout(() => {
//         window.open(data[0].url, '_blank');
//     }, data[0].delay);
// });