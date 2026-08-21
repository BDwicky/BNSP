/**
 * SMART INVENTORY PRO - CLIENT JAVASCRIPT
 * Interaktivitas, Toast Notifications, SweetAlert2 Confirmations, & Visualisasi Chart
 */

// 1. Inisialisasi Toast Mixin menggunakan SweetAlert2
const Toast = (typeof Swal !== 'undefined') ? Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
}) : null;

/**
 * Helper untuk memicu Toast Notification
 */
function showToast(type, message) {
    if (Toast) {
        let icon = type;
        if (type === 'error' || type === 'danger') icon = 'error';
        if (type === 'warning') icon = 'warning';
        if (type === 'info') icon = 'info';
        if (type === 'success') icon = 'success';

        Toast.fire({
            icon: icon,
            title: message
        });
    } else {
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // 1.1 Mobile Navigation Toggle
    const navToggle = document.getElementById('mobileNavToggle');
    const navMenu = document.getElementById('navbarMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('show');
            const icon = navToggle.querySelector('i');
            if (icon) {
                if (navMenu.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // 2. Deteksi Flash Message dari Server dan Tampilkan sebagai Toast
    const flashEl = document.getElementById('flash-data');
    if (flashEl) {
        const type = flashEl.getAttribute('data-type') || 'info';
        const message = flashEl.getAttribute('data-message') || '';
        if (message) {
            showToast(type, message);
        }
    }

    // 3. Konfirmasi Penghapusan Data (SweetAlert2 Model Toast/Modal)
    const deleteForms = document.querySelectorAll('form[action*="product_delete"]');
    deleteForms.forEach(form => {
        const btn = form.querySelector('.btn-delete-confirm');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const itemName = btn.getAttribute('data-name') || 'produk ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Konfirmasi Hapus Data',
                        html: `Apakah Anda yakin ingin menghapus <strong>"${itemName}"</strong>?<br><span style="color: #94a3b8; font-size: 0.85rem;">Data yang dihapus tidak dapat dipulihkan.</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#334155',
                        confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: '#1e293b',
                        color: '#f8fafc',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?`)) {
                        form.submit();
                    }
                }
            });
        }
    });

    // 4. Inisialisasi Tab Panduan Asesor (9 Langkah Kerja)
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

    // 5. Inisialisasi Chart.js jika ada canvas di halaman dashboard
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
