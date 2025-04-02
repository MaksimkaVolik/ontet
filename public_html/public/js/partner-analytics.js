document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    fetch(`/api/partner/analytics?from=${dateFrom}&to=${dateTo}`)
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Клики',
                        data: data.clicks,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                    }, {
                        label: 'Конверсии',
                        data: data.conversions,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)'
                    }, {
                        label: 'Доход',
                        data: data.revenue,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        });

    // Экспорт в PDF
    document.getElementById('exportPdf').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.text('Отчет по партнерской программе', 10, 10);
        doc.save('partner-report.pdf');
    });
});