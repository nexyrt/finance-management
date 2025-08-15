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

Livewire.on('open-preview-delayed', (data) => {
    setTimeout(() => {
        window.open(data[0].url, '_blank');
    }, data[0].delay);
});
