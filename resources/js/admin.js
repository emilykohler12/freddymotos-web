import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach((canvas) => {
        try {
            const config = JSON.parse(canvas.dataset.chart);
            new Chart(canvas, config);
        } catch (e) {
            console.error('No se pudo dibujar el gráfico', e);
        }
    });
});
