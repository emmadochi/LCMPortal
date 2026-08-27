<?php
use App\Utilities\AssetHelper;

$churches = $churches ?? [];
$units = $units ?? [];
$eventTypes = $eventTypes ?? [];
$members = $members ?? [];
$existingMarks = $existingMarks ?? [];
$unit_id = (int)($unit_id ?? 0);
$event_date = $event_date ?? date('Y-m-d');
$event_type = $event_type ?? 'sunday_service';
$church_id = (int)($church_id ?? 0);
$churchFilter = $churchFilter ?? null;
$markUrl = AssetHelper::url('attendance/mark');
$eventTypeLabel = $eventTypes[$event_type] ?? ucfirst(str_replace('_', ' ', $event_type));
?>

<style>
:root {
    --att-primary: #4f46e5;
    --att-success: #10b981;
    --att-danger: #f43f5e;
    --att-warning: #f59e0b;
    --att-dark: #0f172a;
    --att-border: #e2e8f0;
    --att-radius: 16px;
}

.att-dashboard {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.att-header-card {
    background: #ffffff;
    border-radius: var(--att-radius);
    padding: 24px 28px;
    border: 1px solid var(--att-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.att-panel {
    background: #ffffff;
    border-radius: var(--att-radius);
    border: 1px solid var(--att-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}

.att-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    flex-wrap: wrap;
    gap: 12px;
}

.att-panel-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--att-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.att-table {
    width: 100%;
    border-collapse: collapse;
}

.att-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 14px 20px;
    border-bottom: 1px solid var(--att-border);
}

.att-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.att-table tbody tr:hover {
    background: #f8fafc;
}

.att-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--att-dark);
    vertical-align: middle;
}

.member-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #4338ca;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.mark-btn-group {
    display: inline-flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 30px;
    border: 1px solid #e2e8f0;
}

.mark-btn {
    border: none;
    background: transparent;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.mark-btn[data-status="present"].active {
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.mark-btn[data-status="absent"].active {
    background: #f43f5e;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(244, 63, 94, 0.3);
}

.att-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
}
.stat-pill-present { background: #dcfce7; color: #15803d; }
.stat-pill-absent { background: #fee2e2; color: #b91c1c; }
.stat-pill-total { background: #e0e7ff; color: #4338ca; }
.stat-pill-ft { background: #fef3c7; color: #b45309; }
</style>

<div class="container-fluid p-0 att-dashboard">
    <!-- Header Section -->
    <div class="att-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('attendance') ?>" class="text-decoration-none text-muted">Attendance</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Mark Roll-Call</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-list-check text-primary"></i> Service Attendance Roll-Call
                </h3>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= AssetHelper::url('attendance') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                </a>
                <a href="<?= AssetHelper::url('attendance/create') ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bx bx-user-plus me-1"></i> Single Check-In
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Scope Bar -->
    <div class="att-panel p-4 mb-4">
        <form method="GET" action="<?= $markUrl ?>" id="load-form" class="row g-3 align-items-end">
            <?php if (!empty($churches)): ?>
            <div class="col-lg-3 col-md-6">
                <label for="church_id_select" class="form-label small fw-bold text-muted text-uppercase">Church Branch</label>
                <select class="form-select" id="church_id_select" name="church_id" onchange="document.getElementById('load-form').submit();">
                    <option value="">All Churches / General</option>
                    <?php foreach ($churches as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $church_id === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-lg-3 col-md-6">
                <label for="unit_id" class="form-label small fw-bold text-muted text-uppercase">Department / Scope <span class="text-danger">*</span></label>
                <select class="form-select" id="unit_id" name="unit_id" required onchange="document.getElementById('load-form').submit();">
                    <?php foreach ($units as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= $unit_id === (int)$u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label for="event_type" class="form-label small fw-bold text-muted text-uppercase">Service Type <span class="text-danger">*</span></label>
                <select class="form-select" id="event_type" name="event_type" required onchange="document.getElementById('load-form').submit();">
                    <?php foreach ($eventTypes as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $event_type === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label for="event_date" class="form-label small fw-bold text-muted text-uppercase">Service Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="event_date" name="event_date" value="<?= htmlspecialchars($event_date) ?>" required onchange="document.getElementById('load-form').submit();">
            </div>

            <div class="col-lg-1 col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-3" title="Reload Roster">
                    <i class="bx bx-refresh fs-5"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Roster Form -->
    <?php if ($event_date && $event_type && !empty($members)): ?>
        <form method="POST" action="<?= AssetHelper::url('attendance/mark') ?>" id="submit-form">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <input type="hidden" name="unit_id" value="<?= $unit_id ?>">
            <input type="hidden" name="event_date" value="<?= htmlspecialchars($event_date) ?>">
            <input type="hidden" name="event_type" value="<?= htmlspecialchars($event_type) ?>">
            <?php if ($church_id): ?>
                <input type="hidden" name="church_id" value="<?= $church_id ?>">
            <?php endif; ?>

            <div class="att-panel">
                <div class="att-panel-header">
                    <div>
                        <h5 class="att-panel-title">
                            <i class="bx bx-user-check text-success fs-4"></i> <?= htmlspecialchars($eventTypeLabel) ?> Roster — <?= date('l, F j, Y', strtotime($event_date)) ?>
                        </h5>
                        <div class="small text-muted mt-1">
                            Recording for: <strong class="text-dark"><?= htmlspecialchars($churchFilter ? $churchFilter['name'] : 'Church-wide') ?></strong>
                            (<?= count($members) ?> Roster Members)
                        </div>
                    </div>

                    <!-- Quick Batch Actions & Live Counters -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="att-stat-pill stat-pill-total">
                            Total: <span id="counterTotal"><?= count($members) ?></span>
                        </div>
                        <div class="att-stat-pill stat-pill-present">
                            Present: <span id="counterPresent">0</span>
                        </div>
                        <div class="att-stat-pill stat-pill-absent">
                            Absent: <span id="counterAbsent">0</span>
                        </div>

                        <div class="btn-group btn-group-sm ms-2">
                            <button type="button" class="btn btn-outline-success btn-sm" id="btnMarkAllPresent">
                                <i class="bx bx-check-double me-1"></i> Mark All Present
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnMarkAllAbsent">
                                <i class="bx bx-x me-1"></i> Mark All Absent
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Search Box & Description -->
                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 450px;">
                        <span class="text-muted small text-uppercase fw-bold"><i class="bx bx-search"></i></span>
                        <input type="text" id="memberSearchInput" class="form-control form-control-sm" placeholder="Filter member name or email...">
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 450px;">
                        <label for="service_description" class="small text-muted text-nowrap">Service Notes:</label>
                        <input type="text" class="form-control form-control-sm" id="service_description" name="service_description" 
                               maxlength="255" placeholder="e.g. 1st Service, Healing Service, Revival"
                               value="<?= htmlspecialchars($service_description ?? '') ?>">
                    </div>
                </div>

                <!-- Member Roll Call Table -->
                <div class="att-panel-body p-0">
                    <div class="table-responsive">
                        <table class="att-table mb-0" id="rosterTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Congregant / Member</th>
                                    <th>Contact Email / Phone</th>
                                    <th class="text-center">Age Group</th>
                                    <th class="text-center" style="width: 220px;">Attendance Status</th>
                                    <th class="text-center" style="width: 160px;">First-Time Visitor?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $i => $member): ?>
                                    <?php
                                    $uid = (int)$member['id'];
                                    $mark = $existingMarks[$uid] ?? null;
                                    $current = is_array($mark) ? ($mark['status'] ?? 'present') : ($mark ?: 'present');
                                    $isFirstTimer = is_array($mark) ? (int)($mark['is_first_timer'] ?? 0) : 0;
                                    $memberName = trim($member['first_name'] . ' ' . $member['last_name']);
                                    $initials = strtoupper(substr($member['first_name'] ?? 'M', 0, 1) . substr($member['last_name'] ?? '', 0, 1));
                                    $ageGroup = $member['age_group'] ?? '';
                                    ?>
                                    <tr class="member-row">
                                        <td class="text-muted small"><?= $i + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="member-avatar">
                                                    <?= htmlspecialchars($initials) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark member-name"><?= htmlspecialchars($memberName) ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars($member['role'] ?? 'Member') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= htmlspecialchars($member['email'] ?? 'No email') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($member['phone'] ?? '') ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-secondary border">
                                                <?= ucfirst($ageGroup ?: 'Adult') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="marks[<?= $uid ?>]" id="mark_<?= $uid ?>" value="<?= htmlspecialchars($current) ?>" class="status-input">
                                            <div class="mark-btn-group">
                                                <button type="button" class="mark-btn <?= $current === 'present' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="present">
                                                    <i class="bx bx-check me-1"></i> Present
                                                </button>
                                                <button type="button" class="mark-btn <?= $current === 'absent' ? 'active' : '' ?>" data-user="<?= $uid ?>" data-status="absent">
                                                    <i class="bx bx-x me-1"></i> Absent
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input type="checkbox" class="form-check-input first-timer-cb" name="first_timer[<?= $uid ?>]" value="1" id="ft_<?= $uid ?>" <?= $isFirstTimer ? 'checked' : '' ?>>
                                                <label class="form-check-label small text-muted ms-1" for="ft_<?= $uid ?>">First-Timer</label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Submission Bar -->
                <div class="p-3 bg-light border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <a href="<?= AssetHelper::url('attendance') ?><?= $church_id ? '?church_id=' . $church_id : '' ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow">
                        <i class="bx bx-check-circle me-2"></i> Save & Submit Service Attendance
                    </button>
                </div>
            </div>
        </form>
    <?php elseif ($event_date && $event_type && empty($members)): ?>
        <div class="att-panel p-5 text-center">
            <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="bx bx-user-x text-muted fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark">No members found for this unit / branch</h5>
            <p class="text-muted small mb-3">Add or assign members to this department or branch church to start marking service roll-call.</p>
            <a href="<?= AssetHelper::url('members/create') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bx bx-plus me-1"></i> Add New Member
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // 1. Live Counters Update
    function updateCounters() {
        const rows = document.querySelectorAll('.member-row');
        let present = 0, absent = 0;
        rows.forEach(r => {
            const input = r.querySelector('.status-input');
            if (input && input.value === 'present') present++;
            if (input && input.value === 'absent') absent++;
        });
        const elPres = document.getElementById('counterPresent');
        const elAbs = document.getElementById('counterAbsent');
        if (elPres) elPres.textContent = present;
        if (elAbs) elAbs.textContent = absent;
    }
    updateCounters();

    // 2. Button Toggle Handlers
    document.querySelectorAll('.mark-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const user = this.getAttribute('data-user');
            const status = this.getAttribute('data-status');
            const row = this.closest('tr');
            row.querySelectorAll('.mark-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const input = document.getElementById('mark_' + user);
            if (input) input.value = status;
            updateCounters();
        });
    });

    // 3. Mark All Present
    const btnAllPresent = document.getElementById('btnMarkAllPresent');
    if (btnAllPresent) {
        btnAllPresent.addEventListener('click', function() {
            document.querySelectorAll('.member-row').forEach(row => {
                const btnPres = row.querySelector('.mark-btn[data-status="present"]');
                const btnAbs = row.querySelector('.mark-btn[data-status="absent"]');
                const input = row.querySelector('.status-input');
                if (btnPres && btnAbs && input) {
                    btnAbs.classList.remove('active');
                    btnPres.classList.add('active');
                    input.value = 'present';
                }
            });
            updateCounters();
        });
    }

    // 4. Mark All Absent
    const btnAllAbsent = document.getElementById('btnMarkAllAbsent');
    if (btnAllAbsent) {
        btnAllAbsent.addEventListener('click', function() {
            document.querySelectorAll('.member-row').forEach(row => {
                const btnPres = row.querySelector('.mark-btn[data-status="present"]');
                const btnAbs = row.querySelector('.mark-btn[data-status="absent"]');
                const input = row.querySelector('.status-input');
                if (btnPres && btnAbs && input) {
                    btnPres.classList.remove('active');
                    btnAbs.classList.add('active');
                    input.value = 'absent';
                }
            });
            updateCounters();
        });
    }

    // 5. Live Search Filter
    const searchInput = document.getElementById('memberSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.member-row').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
