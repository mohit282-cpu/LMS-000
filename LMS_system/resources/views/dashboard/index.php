<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small">Active Users</div>
                    <div class="fs-3 fw-semibold"><?= e($stats['users']) ?></div>
                </div>
                <span class="stat-icon"><i data-lucide="users"></i></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small">Students</div>
                    <div class="fs-3 fw-semibold"><?= e($stats['students']) ?></div>
                </div>
                <span class="stat-icon"><i data-lucide="graduation-cap"></i></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small">Teachers</div>
                    <div class="fs-3 fw-semibold"><?= e($stats['teachers']) ?></div>
                </div>
                <span class="stat-icon"><i data-lucide="presentation"></i></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small">Courses</div>
                    <div class="fs-3 fw-semibold"><?= e($stats['courses']) ?></div>
                </div>
                <span class="stat-icon"><i data-lucide="book-open"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Enrollment Snapshot</h2>
                <span class="badge badge-soft">Live totals</span>
            </div>
            <div class="card-body">
                <canvas id="dashboardChart" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Recent Audit Activity</h2>
                <span class="badge text-bg-primary"><?= e($stats['audit_today']) ?> today</span>
            </div>
            <div class="list-group list-group-flush">
                <?php if ($recentLogs === []): ?>
                    <div class="list-group-item text-secondary">No audit activity recorded yet.</div>
                <?php endif; ?>
                <?php foreach ($recentLogs as $log): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="fw-medium"><?= e($log['action']) ?></div>
                                <div class="small text-secondary"><?= e($log['actor_name'] ?? 'System') ?> · <?= e($log['entity_type']) ?></div>
                            </div>
                            <time class="small text-secondary text-nowrap"><?= e(date('M j, H:i', strtotime($log['created_at']))) ?></time>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const canvas = document.getElementById('dashboardChart');
        if (!canvas || !window.Chart) {
            return;
        }

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: ['Students', 'Teachers', 'Courses'],
                datasets: [{
                    label: 'Total',
                    data: [<?= (int) $stats['students'] ?>, <?= (int) $stats['teachers'] ?>, <?= (int) $stats['courses'] ?>],
                    backgroundColor: ['#0d6efd', '#198754', '#6f42c1'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    })();
</script>

