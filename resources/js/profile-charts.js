import Chart from 'chart.js/auto';

function renderProfileCharts(statsData) {
    const ctxGenres = document.getElementById('genresChart');
    const ctxStudios = document.getElementById('studiosChart');

    if (!ctxGenres || !ctxStudios || !statsData) {
        return;
    }

    const existingGenres = Chart.getChart(ctxGenres);
    if (existingGenres) {
        existingGenres.destroy();
    }

    const existingStudios = Chart.getChart(ctxStudios);
    if (existingStudios) {
        existingStudios.destroy();
    }

    new Chart(ctxGenres, {
        type: 'doughnut',
        data: {
            labels: statsData.genres.labels,
            datasets: [{
                data: statsData.genres.data,
                backgroundColor: ['#0d6efd', '#6f42c1', '#d63384', '#fd7e14', '#198754'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#ffffff',
                        boxWidth: 12,
                        font: {
                            size: 11,
                        },
                    },
                },
            },
        },
    });

    new Chart(ctxStudios, {
        type: 'bar',
        data: {
            labels: statsData.studios.labels,
            datasets: [{
                label: 'Titoli Visti',
                data: statsData.studios.data,
                backgroundColor: '#0dcaf0',
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        color: '#ffffff',
                    },
                    grid: {
                        display: false,
                    },
                },
                y: {
                    ticks: {
                        color: '#ffffff',
                        precision: 0,
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.1)',
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
}

window.renderProfileCharts = renderProfileCharts;

document.addEventListener('livewire:init', () => {
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            const statsWrapper = document.getElementById('profile-stats-charts');
            if (!statsWrapper) {
                return;
            }

            const serializedData = statsWrapper.dataset.chart;
            if (!serializedData) {
                return;
            }

            renderProfileCharts(JSON.parse(serializedData));
        });
    });
});
