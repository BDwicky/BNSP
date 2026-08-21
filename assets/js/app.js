/**
 * SMART INVENTORY PRO - CLIENT JAVASCRIPT
 * Interaktivitas, Panduan Asesor, Konfirmasi Aksi, & Visualisasi Chart
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Tab Panduan Asesor (9 Langkah Kerja)
    const stepBtns = document.querySelectorAll('.step-btn');
    const stepContents = document.querySelectorAll('.step-content');

    stepBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetStep = btn.getAttribute('data-step');

            // Set active class pada button
            stepBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Tampilkan content yang sesuai
            stepContents.forEach(content => {
                if (content.id === `step-content-${targetStep}`) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        });
    });

    // 2. Konfirmasi Penghapusan Data (Security UX)
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemName = btn.getAttribute('data-name') || 'item ini';
            const confirmMsg = `Apakah Anda yakin ingin menghapus data "${itemName}"?\nTindakan ini tidak dapat dibatalkan.`;
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    });

    // 3. Auto Dismiss Alert Notifikasi
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // 4. Inisialisasi Chart.js jika ada canvas di halaman dashboard
    const chartCanvas = document.getElementById('inventoryChart');
    if (chartCanvas && typeof Chart !== 'undefined') {
        const labels = JSON.parse(chartCanvas.getAttribute('data-labels') || '[]');
        const dataValues = JSON.parse(chartCanvas.getAttribute('data-values') || '[]');

        new Chart(chartCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#06b6d4',
                        '#8b5cf6',
                        '#ec4899'
                    ],
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12
                            },
                            padding: 15
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
