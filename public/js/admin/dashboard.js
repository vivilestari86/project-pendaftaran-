(function () {
    const chartCanvas = document.getElementById('chartTrenMingguan');
    const weeklyData = Array.isArray(window.trenMingguan) ? window.trenMingguan : [];

    if (chartCanvas && window.Chart) {
        const labels = weeklyData.map((item) => item.label);
        const dateLabels = weeklyData.map((item) => item.tanggal);
        const datasets = {
            pendaftar: weeklyData.map((item) => item.pendaftar),
            kelengkapan: weeklyData.map((item) => item.kelengkapan),
        };
        const modeLabels = {
            pendaftar: 'Pendaftar',
            kelengkapan: 'Data Lengkap',
        };

        const gradient = chartCanvas.getContext('2d').createLinearGradient(0, 0, 0, 360);
        gradient.addColorStop(0, 'rgba(47, 109, 246, 0.24)');
        gradient.addColorStop(1, 'rgba(47, 109, 246, 0)');

        const chart = new Chart(chartCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: modeLabels.pendaftar,
                    data: datasets.pendaftar,
                    borderColor: '#2f6df6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    tension: 0.42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        displayColors: false,
                        backgroundColor: '#111827',
                        padding: 12,
                        callbacks: {
                            title(tooltipItems) {
                                const item = tooltipItems[0];
                                return `${item.label} • ${dateLabels[item.dataIndex] ?? ''}`;
                            },
                            label(context) {
                                return `${context.dataset.label}: ${context.parsed.y}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#7a8496', font: { size: 12, weight: '600' } },
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#edf1f7' },
                        ticks: { color: '#94a3b8', precision: 0, stepSize: 1 },
                    },
                },
            },
        });

        document.querySelectorAll('.chart-toggle button').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.chart-toggle button').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');
                const mode = button.dataset.mode || 'pendaftar';
                chart.data.datasets[0].label = modeLabels[mode] || modeLabels.pendaftar;
                chart.data.datasets[0].data = datasets[mode] || datasets.pendaftar;
                chart.update();
            });
        });
    }

    const closeModal = (modal) => modal && modal.classList.remove('is-open');
    const detailModal = document.getElementById('modalDetailPendaftar');
    const editModal = document.getElementById('modalEditPendaftar');
    const allPendaftarModal = document.getElementById('modalAllPendaftar');
    const openAllPendaftarButton = document.getElementById('openAllPendaftarModal');

    document.querySelectorAll('.modal-close').forEach((button) => {
        button.addEventListener('click', () => closeModal(button.closest('.modal-overlay')));
    });

    document.querySelectorAll('.modal-overlay').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll('.pendaftar-item[data-detail-url]').forEach((item) => {
        item.addEventListener('click', async () => {
            if (!detailModal) {
                return;
            }

            const body = document.getElementById('modalDetailBody');
            body.innerHTML = '<div class="modal-loading">Memuat data...</div>';
            detailModal.classList.add('is-open');

            try {
                const response = await fetch(item.dataset.detailUrl, {
                    headers: { Accept: 'application/json' },
                });
                const payload = await response.json();
                const data = payload.data;

                body.innerHTML = `
                    <div class="modal-profile">
                        <img class="modal-avatar" src="${data.foto}" alt="${data.nama}">
                        <div>
                            <p class="modal-profile-name">${data.nama}</p>
                            <p class="modal-profile-id">${data.id_pendaftar}</p>
                        </div>
                    </div>
                    ${[
                        ['Profesi', data.profesi],
                        ['Email', data.email],
                        ['No HP', data.no_hp],
                        ['Wilayah', data.wilayah],
                        ['Status', data.status_kelengkapan],
                        ['Tanggal Daftar', data.tanggal_daftar],
                    ].map(([label, value]) => `
                        <div class="modal-detail-row">
                            <span class="modal-detail-label">${label}</span>
                            <span class="modal-detail-value">${value || '-'}</span>
                        </div>
                    `).join('')}
                `;

                const editButton = detailModal.querySelector('.modal-edit-btn');
                if (editButton) {
                    editButton.dataset.editUrl = item.dataset.editUrl;
                }
            } catch (error) {
                body.innerHTML = '<div class="modal-loading">Data gagal dimuat.</div>';
            }
        });
    });

    if (detailModal && editModal) {
        const editButton = detailModal.querySelector('.modal-edit-btn');
        editButton && editButton.addEventListener('click', () => {
            closeModal(detailModal);
            editModal.classList.add('is-open');
        });
    }

    if (allPendaftarModal && openAllPendaftarButton) {
        openAllPendaftarButton.addEventListener('click', () => {
            allPendaftarModal.classList.add('is-open');
        });
    }
})();
