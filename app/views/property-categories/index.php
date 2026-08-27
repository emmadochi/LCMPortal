<?php
use App\Utilities\AssetHelper;
use App\Utilities\Security;

$csrfToken = Security::generateCSRFToken();
$totalCategories = count($categories ?? []);
$totalAssets = 0;
foreach ($categories ?? [] as $cat) {
    $totalAssets += (int)($cat['property_count'] ?? 0);
}
?>

<style>
:root {
    --fin-emerald: #10b981;
    --fin-indigo: #4f46e5;
    --fin-amber: #f59e0b;
    --fin-rose: #f43f5e;
    --fin-dark: #0f172a;
    --fin-surface: #ffffff;
    --fin-border: #e2e8f0;
    --fin-sub: #64748b;
    --fin-radius: 16px;
}

.cat-dashboard {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--fin-dark);
}

.fin-header-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    padding: 22px 28px;
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.fin-metric-card {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    padding: 20px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    height: 100%;
}
.fin-metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
.fin-metric-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
}
.fin-accent-cat { background: linear-gradient(90deg, #4f46e5, #818cf8); }
.fin-accent-asset { background: linear-gradient(90deg, #10b981, #34d399); }
.fin-accent-tpl { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.fin-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.fin-icon-cat { background: #eef2ff; color: #4f46e5; }
.fin-icon-asset { background: #ecfdf5; color: #10b981; }
.fin-icon-tpl { background: #fffbeb; color: #f59e0b; }

.fin-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--fin-sub);
    margin-bottom: 4px;
}
.fin-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--fin-dark);
    line-height: 1.2;
}

.fin-panel {
    background: #ffffff;
    border-radius: var(--fin-radius);
    border: 1px solid var(--fin-border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 24px;
}
.fin-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
}
.fin-panel-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--fin-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.fin-table {
    width: 100%;
    border-collapse: collapse;
}
.fin-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--fin-border);
}
.fin-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.fin-table tbody tr:hover {
    background: #f8fafc;
}
.fin-table td {
    padding: 14px 20px;
    font-size: 0.88rem;
    color: var(--fin-dark);
    vertical-align: middle;
}

.template-chip {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 3px;
}
.template-chip:hover {
    border-color: #4f46e5;
    background: #eef2ff;
    color: #4f46e5;
    transform: translateY(-2px);
}

.category-card {
    border: 1px solid var(--fin-border);
    border-radius: 14px;
    background: #ffffff;
    padding: 20px;
    transition: all 0.2s ease;
    height: 100%;
}
.category-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    transform: translateY(-3px);
}
</style>

<div class="container-fluid p-0 cat-dashboard">
    <!-- Header Section -->
    <div class="fin-header-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('/') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= AssetHelper::url('properties') ?>" class="text-decoration-none text-muted">Properties</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Asset Categories</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-folder text-primary"></i> Property & Asset Categories
                </h3>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= AssetHelper::url('properties') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bx bx-cube me-1"></i> View All Properties
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bx bx-plus me-1"></i> Create Category
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-cat"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fin-label">Total Categories</div>
                    <div class="fin-icon-box fin-icon-cat">
                        <i class="bx bx-category"></i>
                    </div>
                </div>
                <div class="fin-value text-primary"><?= $totalCategories ?></div>
                <div class="small text-muted mt-1">Classification groups for church assets</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-asset"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fin-label">Total Linked Assets</div>
                    <div class="fin-icon-box fin-icon-asset">
                        <i class="bx bx-cube-alt"></i>
                    </div>
                </div>
                <div class="fin-value text-success"><?= $totalAssets ?></div>
                <div class="small text-muted mt-1">Registered items under all categories</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="fin-metric-card">
                <div class="fin-metric-accent fin-accent-tpl"></div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fin-label">Quick Setup</div>
                    <div class="fin-icon-box fin-icon-tpl">
                        <i class="bx bx-bulb"></i>
                    </div>
                </div>
                <div class="fin-value text-dark" style="font-size: 1.15rem; font-weight: 700; margin-top: 4px;">Common Church Assets</div>
                <div class="small text-muted mt-1">Click templates below to add instantly</div>
            </div>
        </div>
    </div>

    <!-- Recommended Church Categories Template Tray -->
    <div class="fin-panel p-4 mb-4" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bx bxs-magic-wand text-primary fs-5"></i>
            <h6 class="mb-0 fw-bold text-dark">Suggested Church Category Templates</h6>
            <span class="badge bg-soft-primary text-primary small">Click any chip to add</span>
        </div>
        <p class="small text-muted mb-3">Quickly add standard church inventory classifications with pre-filled descriptions:</p>
        <div class="d-flex flex-wrap gap-1">
            <span class="template-chip" onclick="applyTemplate('Musical Instruments & Audio Gear', 'Keyboards, drums, sound mixers, amplifiers, microphones, and audio interfaces.')">
                <i class="bx bx-music text-primary"></i> 🎸 Musical Instruments & Audio
            </span>
            <span class="template-chip" onclick="applyTemplate('Media, Video & Lighting', 'Cameras, livestream rigs, studio monitors, LED stage lights, and projectors.')">
                <i class="bx bx-camera text-info"></i> 📽️ Media & Video Production
            </span>
            <span class="template-chip" onclick="applyTemplate('Church Transportation & Vehicles', 'Buses, vans, utility vehicles, and protocol cars.')">
                <i class="bx bx-car text-success"></i> 🚗 Transportation & Vehicles
            </span>
            <span class="template-chip" onclick="applyTemplate('Sanctuary & Hall Furniture', 'Pulpits, altar chairs, congregational seating, and event tables.')">
                <i class="bx bx-chair text-warning"></i> 🪑 Sanctuary Furniture
            </span>
            <span class="template-chip" onclick="applyTemplate('Power Systems & Generators', 'Diesel generators, solar inverters, battery banks, and stabilizers.')">
                <i class="bx bx-bolt-circle text-danger"></i> ⚡ Power & Generators
            </span>
            <span class="template-chip" onclick="applyTemplate('Office & IT Hardware', 'Desktops, laptops, network routers, printers, and admin workstations.')">
                <i class="bx bx-laptop text-primary"></i> 💻 Office & IT Equipment
            </span>
            <span class="template-chip" onclick="applyTemplate('HVAC & Cooling Equipment', 'Standing AC units, ceiling cassette air conditioners, and ventilation fans.')">
                <i class="bx bx-wind text-info"></i> ❄️ HVAC & Air Conditioning
            </span>
        </div>
    </div>

    <!-- Categories List Panel -->
    <div class="fin-panel">
        <div class="fin-panel-header">
            <div class="d-flex align-items-center gap-3">
                <h5 class="fin-panel-title">
                    <i class="bx bx-category text-primary fs-5"></i> Registered Asset Categories (<?= count($categories ?? []) ?>)
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                    <input type="text" id="categorySearchInput" class="form-control border-start-0" placeholder="Filter categories..." onkeyup="filterCategories()">
                </div>
            </div>
        </div>
        <div class="fin-panel-body p-0">
            <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                        <i class="bx bx-folder-open text-muted font-size-24"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No categories registered yet</h5>
                    <p class="text-muted small mb-3">Click one of the suggested templates above or create a custom classification.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="bx bx-plus me-1"></i> Create First Category
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fin-table mb-0" id="categoryTable">
                        <thead>
                            <tr>
                                <th class="ps-4">Category Name</th>
                                <th>Description</th>
                                <th class="text-center">Total Assets</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr class="category-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.9rem; background: #eef2ff;">
                                                <i class="bx bx-folder"></i>
                                            </div>
                                            <span class="fw-bold text-dark category-name"><?= htmlspecialchars($category['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small category-desc"><?= htmlspecialchars($category['description'] ?: 'No description provided') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-bold">
                                            <?= (int)$category['property_count'] ?> Assets
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($category['creator_first_name']): ?>
                                            <span class="small fw-medium text-dark"><?= htmlspecialchars($category['creator_first_name'] . ' ' . $category['creator_last_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($category['created_at'])) ?></td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="<?= AssetHelper::url('property-categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-light rounded-pill px-3" title="Edit">
                                                <i class="bx bx-edit text-primary me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="<?= AssetHelper::url('property-categories/' . $category['id'] . '/delete') ?>" 
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');" class="d-inline">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 text-danger" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title text-white fw-bold" id="createCategoryModalLabel">
                    <i class="bx bx-folder-plus me-1"></i> Create Property Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= AssetHelper::url('property-categories') ?>" method="POST">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="modal_cat_name" class="form-label fw-bold small text-muted text-uppercase">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_cat_name" name="name" required placeholder="e.g. Musical & Audio Gear">
                    </div>
                    <div class="mb-3">
                        <label for="modal_cat_desc" class="form-label fw-bold small text-muted text-uppercase">Description</label>
                        <textarea class="form-control" id="modal_cat_desc" name="description" rows="3" placeholder="Optional description for items classified under this category"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bx bx-check me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyTemplate(name, desc) {
    document.getElementById('modal_cat_name').value = name;
    document.getElementById('modal_cat_desc').value = desc;
    var modalEl = document.getElementById('createCategoryModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function filterCategories() {
    var input = document.getElementById("categorySearchInput").value.toLowerCase();
    var rows = document.querySelectorAll(".category-row");
    rows.forEach(function(row) {
        var name = row.querySelector(".category-name").textContent.toLowerCase();
        var desc = row.querySelector(".category-desc").textContent.toLowerCase();
        if (name.includes(input) || desc.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>
