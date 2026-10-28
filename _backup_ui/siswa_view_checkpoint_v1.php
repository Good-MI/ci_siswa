<?php
// Master class options grouped by major
$classList = [
    'RPL' => ['10 RPL', '11 RPL', '12 RPL'],
    'Broadcast' => ['10 Broadcast', '11 Broadcast', '12 Broadcast'],
    'Animation' => ['10 Animation', '11 Animation', '12 Animation'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Budi Luhur - Student Data</title>
    <!-- CDN CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CDN Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CDN CSS DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --page-bg: #f1f5f9;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --accent-blue: #2563eb;
            --accent-blue-hover: #1d4ed8;
            --danger-soft: #fef2f2;
            --danger-color: #ef4444;
            --danger-text: #dc2626;
        }

        body {
            background-color: var(--page-bg);
            color: var(--text-primary);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background-color: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
        }
        .brand-title {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .brand-subtitle {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Card */
        .main-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Search wrapper */
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 280px;
        }
        .search-wrapper .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            font-size: 0.9rem;
        }
        .search-wrapper .search-input {
            padding-left: 36px;
            padding-right: 14px;
            padding-top: 8px;
            padding-bottom: 8px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            font-size: 0.9rem;
            color: var(--text-primary);
            transition: all 0.15s ease-in-out;
        }
        .search-wrapper .search-input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--accent-blue) !important;
            border-color: var(--accent-blue) !important;
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.15s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--accent-blue-hover) !important;
            border-color: var(--accent-blue-hover) !important;
        }
        .btn-outline-secondary {
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
            background-color: #ffffff !important;
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.15s ease-in-out;
        }
        .btn-outline-secondary:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: var(--text-primary) !important;
        }

        .btn-danger {
            background-color: var(--danger-color) !important;
            border-color: var(--danger-color) !important;
            color: #ffffff !important;
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.15s ease-in-out;
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: var(--danger-text) !important;
            border-color: var(--danger-text) !important;
        }

        .delete-icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: var(--danger-soft);
            color: var(--danger-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Action Icon Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            cursor: pointer;
            font-size: 1.05rem;
        }
        .btn-action-edit:hover {
            color: var(--accent-blue);
            background-color: #eff6ff;
        }
        .btn-action-delete:hover {
            color: var(--danger-text);
            background-color: var(--danger-soft);
        }

        /* Class Pill Badges per Major */
        .badge-class {
            font-weight: 600;
            font-size: 0.775rem;
            padding: 5px 12px;
            border-radius: 50rem;
            letter-spacing: 0.02em;
            display: inline-block;
        }
        .badge-rpl {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-broadcast {
            background-color: #f3e8ff;
            color: #7e22ce;
        }
        .badge-animation {
            background-color: #ffedd5;
            color: #c2410c;
        }
        .badge-default {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* Table Styling */
        #tabelSiswa {
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0 !important;
            border-bottom: none !important;
        }

        #tabelSiswa thead th {
            background-color: #f8fafc !important;
            color: var(--text-muted) !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            border-top: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-left: none !important;
            border-right: none !important;
            padding: 12px 16px !important;
        }

        #tabelSiswa tbody td {
            border-top: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-left: none !important;
            border-right: none !important;
            padding: 14px 16px !important;
            color: var(--text-primary);
            font-size: 0.925rem;
        }

        #tabelSiswa tbody tr {
            transition: background-color 0.15s ease-in-out;
        }

        #tabelSiswa tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Menghilangkan ikon panah sorting bawaan DataTables */
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc:after {
            display: none !important;
        }
        
        /* Menghapus padding bekas ikon panah di header tabel */
        table.dataTable thead th {
            background-image: none !important;
            padding-right: 12px !important;
            cursor: default !important;
        }

        /* Pagination & Info Controls */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .page-item .page-link {
            border: none !important;
            border-radius: 6px !important;
            color: var(--text-muted) !important;
            background-color: transparent !important;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
            box-shadow: none !important;
        }
        .page-item .page-link:hover {
            color: var(--accent-blue) !important;
            background-color: #f1f5f9 !important;
        }
        .page-item.active .page-link {
            color: #ffffff !important;
            background-color: var(--accent-blue) !important;
        }
        .page-item.disabled .page-link {
            color: #cbd5e1 !important;
            background-color: transparent !important;
        }
        .dataTables_info {
            color: var(--text-muted) !important;
            font-size: 0.875rem;
            padding-top: 0 !important;
        }

        /* Modals & Forms */
        .modal-content {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .modal-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
        }
        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 14px 20px;
        }
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.925rem;
            color: var(--text-primary);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            padding: 6px;
            font-size: 0.875rem;
        }
        .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--text-primary);
        }
        .dropdown-item.text-danger:hover {
            background-color: var(--danger-soft);
            color: var(--danger-text) !important;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <span class="d-flex align-items-center justify-content-center text-primary fs-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <div>
                    <div class="brand-title">SMK Budi Luhur</div>
                    <div class="brand-subtitle">Student Information System</div>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="user-name">Admin</span>
            </div>
        </div>
    </nav>

    <!-- Container Utama -->
    <div class="container my-5">
        <!-- Flash Message Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><?= esc(session()->getFlashdata('success')); ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div><?= esc(session()->getFlashdata('error')); ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card main-card">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title fw-bold m-0" style="color: var(--text-primary);">Registered Students</h5>
            </div>
            <div class="card-body p-4">
                
                <!-- Action Bar: Add, Sort & Search -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <!-- Left group: Add & Sort -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Tombol Tambah Data -->
                        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-plus-lg"></i>
                            <span>Add Student Data</span>
                        </button>

                        <!-- Dropdown Sorting Custom -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2" type="button" id="dropdownSorting" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-sort-down"></i>
                                <span>Sort Data</span>
                            </button>
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="dropdownSorting">
                                <li><h6 class="dropdown-header">Alphabetical (Name)</h6></li>
                                <li><a class="dropdown-item" href="#" id="sortNamaAZ"><i class="bi bi-sort-alpha-down me-2"></i>Name: A - Z (Ascending)</a></li>
                                <li><a class="dropdown-item" href="#" id="sortNamaZA"><i class="bi bi-sort-alpha-down-alt me-2"></i>Name: Z - A (Descending)</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><h6 class="dropdown-header">Numerical (NIS)</h6></li>
                                <li><a class="dropdown-item" href="#" id="sortNisAsc"><i class="bi bi-sort-numeric-down me-2"></i>NIS: Lowest - Highest (1 - 9)</a></li>
                                <li><a class="dropdown-item" href="#" id="sortNisDesc"><i class="bi bi-sort-numeric-down-alt me-2"></i>NIS: Highest - Lowest (9 - 1)</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><a class="dropdown-item text-danger" href="#" id="sortReset"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Sorting (Default)</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right group: Custom Search -->
                    <div class="search-wrapper position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="customSearch" class="form-control search-input" placeholder="Search NIS, name, class...">
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table id="tabelSiswa" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 6%;">No</th>
                                <th scope="col" style="width: 20%;">NIS</th>
                                <th scope="col" style="width: 36%;">Name</th>
                                <th scope="col" style="width: 24%;">Class</th>
                                <th scope="col" style="width: 14%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($siswa)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($siswa as $row): ?>
                                    <?php
                                    $majorBadge = 'badge-default';
                                    if (stripos($row['kelas'], 'RPL') !== false) {
                                        $majorBadge = 'badge-rpl';
                                    } elseif (stripos($row['kelas'], 'Broadcast') !== false || stripos($row['kelas'], 'BC') !== false) {
                                        $majorBadge = 'badge-broadcast';
                                    } elseif (stripos($row['kelas'], 'Animation') !== false || stripos($row['kelas'], 'ANM') !== false) {
                                        $majorBadge = 'badge-animation';
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= esc($row['nis']); ?></td>
                                        <td><?= esc($row['nama']); ?></td>
                                        <td>
                                            <span class="badge badge-class <?= $majorBadge; ?>"><?= esc($row['kelas']); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <!-- Tombol Edit -->
                                                <button type="button" 
                                                        class="btn-action btn-action-edit" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEdit<?= esc($row['id']); ?>"
                                                        title="Edit"
                                                        aria-label="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <a href="<?= base_url('home/hapus/' . esc($row['id'])); ?>" 
                                                   class="btn-action btn-action-delete btn-delete-trigger"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#modalDelete"
                                                   data-id="<?= esc($row['id']); ?>"
                                                   data-nama="<?= esc($row['nama']); ?>"
                                                   data-nis="<?= esc($row['nis']); ?>"
                                                   data-href="<?= base_url('home/hapus/' . esc($row['id'])); ?>"
                                                   title="Delete"
                                                   aria-label="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Shared Modal Konfirmasi Hapus Data Siswa -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content text-center p-3">
                <div class="modal-body pt-3 pb-2">
                    <div class="delete-icon-circle mx-auto">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-2" id="modalDeleteLabel" style="color: var(--text-primary);">Delete student?</h5>
                    <p class="text-muted small mb-0">
                        You are about to delete <strong id="deleteStudentNama" class="text-dark"></strong> (NIS <span id="deleteStudentNis" class="text-dark fw-semibold"></span>). This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center gap-2 pt-3 pb-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" id="btnCancelDelete">Cancel</button>
                    <a href="#" id="btnConfirmDelete" class="btn btn-danger d-inline-flex align-items-center gap-2 px-4">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Edit Data Siswa (Rendered outside table for valid HTML) -->
    <?php if (!empty($siswa)): ?>
        <?php foreach ($siswa as $row): ?>
            <?php
            $currentMajor = '';
            if (stripos($row['kelas'], 'RPL') !== false) {
                $currentMajor = 'RPL';
            } elseif (stripos($row['kelas'], 'Broadcast') !== false || stripos($row['kelas'], 'BC') !== false) {
                $currentMajor = 'Broadcast';
            } elseif (stripos($row['kelas'], 'Animation') !== false || stripos($row['kelas'], 'ANM') !== false) {
                $currentMajor = 'Animation';
            }
            ?>
            <div class="modal fade" id="modalEdit<?= esc($row['id']); ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= esc($row['id']); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalEditLabel<?= esc($row['id']); ?>" style="color: var(--text-primary);">Edit Student Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= base_url('home/edit/' . esc($row['id'])); ?>" method="POST">
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label">NIS</label>
                                    <input type="text" class="form-control bg-light text-muted" value="<?= esc($row['nis']); ?>" readonly disabled>
                                    <div class="form-text text-muted small">NIS will change automatically if the major is changed.</div>
                                    <div class="form-text text-warning d-none major-change-warning small mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Major changed: NIS will be regenerated when you save.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="nama" class="form-control" value="<?= esc($row['nama']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Class</label>
                                    <select class="form-select edit-kelas-select" data-original-major="<?= esc($currentMajor); ?>" name="kelas" required>
                                        <option value="" disabled>Select class...</option>
                                        <?php foreach ($classList as $group => $classes): ?>
                                            <optgroup label="<?= esc($group); ?>">
                                                <?php foreach ($classes as $cls): ?>
                                                    <option value="<?= esc($cls); ?>" <?= ($row['kelas'] === $cls) ? 'selected' : ''; ?>>
                                                        <?= esc($cls); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer bg-light-subtle">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modal Form Tambah Data Siswa -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel" style="color: var(--text-primary);">Add Student Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('home/tambah'); ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">NIS (Auto-generated)</label>
                            <input type="text" class="form-control bg-light text-muted" value="Auto-generated by system" readonly disabled>
                            <div class="form-text text-muted small">NIS will be automatically generated upon saving based on the chosen major.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Full Name</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Enter Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Class</label>
                            <select class="form-select" name="kelas" id="kelas" required>
                                <option value="" disabled selected>Select class...</option>
                                <?php foreach ($classList as $group => $classes): ?>
                                    <optgroup label="<?= esc($group); ?>">
                                        <?php foreach ($classes as $cls): ?>
                                            <option value="<?= esc($cls); ?>"><?= esc($cls); ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light-subtle">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CDN JS jQuery, Bootstrap 5, & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Inisialisasi DataTables & Custom Dropdown Sorting Script -->
    <script>
        $(document).ready(function() {
            var table = $('#tabelSiswa').DataTable({
                "dom": 'rt<"d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-2 gap-2"ip>',
                "pageLength": 5,
                "bLengthChange": false, // Menghilangkan menu "Tampilkan X data"
                "columnDefs": [
                    { "orderable": false, "targets": "_all" } // Mematikan efek klik sorting di header tabel
                ],
                "language": {
                    "zeroRecords": "No matching records found",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "paginate": {
                        "first": '<i class="bi bi-chevron-double-left"></i>',
                        "last": '<i class="bi bi-chevron-double-right"></i>',
                        "next": '<i class="bi bi-chevron-right"></i>',
                        "previous": '<i class="bi bi-chevron-left"></i>'
                    }
                }
            });

            // Hubungkan Custom Search Input dengan DataTables
            $('#customSearch').on('keyup input', function() {
                table.search(this.value).draw();
            });

            // Helper untuk mengecek jurusan dari nama kelas
            function getMajorFromClass(className) {
                if (!className) return '';
                if (className.indexOf('RPL') !== -1) return 'RPL';
                if (className.indexOf('Broadcast') !== -1 || className.indexOf('BC') !== -1) return 'Broadcast';
                if (className.indexOf('Animation') !== -1 || className.indexOf('ANM') !== -1) return 'Animation';
                return '';
            }

            // Peringatan perubahan jurusan di Modal Edit
            $(document).on('change', '.edit-kelas-select', function() {
                var originalMajor = $(this).data('original-major');
                var newClass = $(this).val();
                var newMajor = getMajorFromClass(newClass);
                var warningEl = $(this).closest('.modal-content').find('.major-change-warning');

                if (originalMajor && newMajor && originalMajor !== newMajor) {
                    warningEl.removeClass('d-none');
                } else {
                    warningEl.addClass('d-none');
                }
            });

            // Event delegation untuk Delete Trigger agar href tidak langsung navigasi saat JS aktif
            $(document).on('click', '.btn-delete-trigger', function(e) {
                e.preventDefault();
            });

            // Handle data attributes saat modalDelete dibuka
            var modalDeleteEl = document.getElementById('modalDelete');
            if (modalDeleteEl) {
                modalDeleteEl.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    if (!button) return;

                    var nama = button.getAttribute('data-nama') || '';
                    var nis = button.getAttribute('data-nis') || '';
                    var href = button.getAttribute('data-href') || button.getAttribute('href') || '#';

                    // Gunakan textContent untuk menghindari XSS
                    var namaEl = document.getElementById('deleteStudentNama');
                    var nisEl = document.getElementById('deleteStudentNis');
                    var confirmBtn = document.getElementById('btnConfirmDelete');

                    if (namaEl) namaEl.textContent = nama;
                    if (nisEl) nisEl.textContent = nis;
                    if (confirmBtn) confirmBtn.setAttribute('href', href);
                });

                // Fokus pada tombol Cancel saat modal terbuka (safer default)
                modalDeleteEl.addEventListener('shown.bs.modal', function() {
                    var cancelBtn = document.getElementById('btnCancelDelete');
                    if (cancelBtn) cancelBtn.focus();
                });
            }

            // 1. Sorting Abjad / Teks (Nama - Kolom index 2)
            $('#sortNamaAZ').on('click', function(e) {
                e.preventDefault();
                table.order([2, 'asc']).draw();
            });

            $('#sortNamaZA').on('click', function(e) {
                e.preventDefault();
                table.order([2, 'desc']).draw();
            });

            // 2. Sorting Angka / Numerik (NIS - Kolom index 1)
            $('#sortNisAsc').on('click', function(e) {
                e.preventDefault();
                table.order([1, 'asc']).draw();
            });

            $('#sortNisDesc').on('click', function(e) {
                e.preventDefault();
                table.order([1, 'desc']).draw();
            });

            // 3. Reset Urutan Default (No - Kolom index 0)
            $('#sortReset').on('click', function(e) {
                e.preventDefault();
                table.order([0, 'asc']).draw();
            });
        });
    </script>
</body>
</html>