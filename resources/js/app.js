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

window.dayjs = dayjs;

// Prevent default drag and drop behavior globally to avoid file opening in new tab
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('dragover', (e) => {
        const isUpload = e.target.closest('input[type="file"], [data-upload-zone]');
        e.preventDefault();
        e.dataTransfer.dropEffect = isUpload ? 'copy' : 'none';
    }, false);

    document.addEventListener('drop', (e) => {
        if (!e.target.closest('input[type="file"], [data-upload-zone]')) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, false);
});
