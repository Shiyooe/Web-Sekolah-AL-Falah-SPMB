<?php
// Koneksi ke database
require_once '../config/koneksi3.php';

session_start();

// Query untuk mengambil data pembayaran dengan JOIN ke tabel pendaftaran
$query = "SELECT 
            p.id_pendaftaran,
            p.nama,
            p.nisn,
            pb.id_pembayaran,
            pb.Tanggal_pembayaran,
            pb.jumlah_pembayaran,
            pb.status_pembayaran,
            pb.bukti_pembayaran,
            pb.total_cicilan
          FROM pembayaran pb
          INNER JOIN pendaftaran p ON pb.id_pendaftaran = p.id_pendaftaran
          ORDER BY pb.Tanggal_pembayaran DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Data Pembayaran - Admin SPMB Al-Falah</title>
    
    <!-- SB Admin CSS -->
    <link href="../admin/css/styles.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/Alfalah.png">
    
    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header Section */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin: -1rem -1.5rem 2rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
        }

        .page-header .breadcrumb {
            background: transparent;
            margin: 0.5rem 0 0 0;
            padding: 0;
        }

        .page-header .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .page-header .breadcrumb-item.active {
            color: white;
        }

        /* Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-card h4 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-card h6 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-card small {
            font-size: 0.875rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .card-footer {
            background: rgba(0,0,0,0.05);
            border: none;
            padding: 0.75rem 1.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .text-gray {
            color: white !important;
        }

        /* Main Card */
        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .main-card .card-header {
            background: white;
            border-bottom: 2px solid var(--light-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .main-card .card-body {
            padding: 1.5rem;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fc;
            color: var(--dark-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
            padding: 1rem 0.75rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            color: var(--dark-color);
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
            transform: scale(1.01);
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.85rem;
            font-weight: 600;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .status-lunas {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .status-belum {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            color: white;
        }

        /* Image Styles */
        .bukti-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid #e3e6f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .bukti-img:hover {
            transform: scale(1.15);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }

        /* Button Styles */
        .btn-detail {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }

        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.25rem 1.5rem;
        }

        .modal-header .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Riwayat Styles */
        .riwayat-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .riwayat-container::-webkit-scrollbar {
            width: 8px;
        }

        .riwayat-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .riwayat-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .riwayat-item {
            background: white;
            border-left: 4px solid var(--primary-color);
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .riwayat-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1.25rem;
        }

        .alert-info {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: #006064;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .stat-card h4 {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.85rem;
            }

            .bukti-img {
                width: 50px;
                height: 50px;
            }
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .sb-topnav, 
            .sb-sidenav, 
            footer,
            .page-header,
            .stat-card {
                display: none !important;
            }
            
            #layoutSidenav_content {
                margin-left: 0 !important;
            }

            .main-card {
                box-shadow: none;
            }

            .table {
                font-size: 10pt;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d3e2;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #858796;
        }

        /* Loading Animation */
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }

        /* Image in Modal */
        #modalImage {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Summary Alert */
        .summary-alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid var(--primary-color);
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">SPMB Alfalah</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
            <i class="fas fa-bars"></i>
        </button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Layouts
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="layout-static.php">Static Navigation</a>
                                <a class="nav-link" href="layout-sidenav-light.php">Light Sidenav</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Pages
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Authentication
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="login.php">Login</a>
                                        <a class="nav-link" href="register.php">Register</a>
                                        <a class="nav-link" href="password.php">Forgot Password</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Error
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="log/401.html">401 Page</a>
                                        <a class="nav-link" href="log/404.html">404 Page</a>
                                        <a class="nav-link" href="log/500.html">500 Page</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading">Addons</div>
                        <a class="nav-link" href="charts.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            Charts
                        </a>
                        <a class="nav-link" href="tables.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Tables
                        </a>
                        <a class="nav-link" href="Backup/kontribusi.html">
                            <div class="sb-nav-link-icon"><i class="bi bi-brush"></i></div>
                            Who Have Contributed
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </div>                
            </nav>
        </div>
        
        <!-- Main Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="container-fluid">
                            <h1><i class="bi bi-cash-coin"></i> Data Pembayaran Siswa</h1>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Pembayaran</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Card Statistik -->
                    <div class="row mb-4">
                        <?php
                        // Hitung statistik
                        $total_pembayaran = mysqli_num_rows($result);
                        mysqli_data_seek($result, 0);
                        
                        $lunas = 0;
                        $belum_lunas = 0;
                        $total_uang = 0;
                        
                        while ($stat = mysqli_fetch_assoc($result)) {
                            if ($stat['status_pembayaran'] == 'Lunas') $lunas++;
                            else $belum_lunas++;
                            $total_uang += $stat['jumlah_pembayaran'];
                        }
                        mysqli_data_seek($result, 0);
                        ?>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card bg-gradient-primary">
                                <div class="card-body">
                                    <h4><?= $total_pembayaran ?></h4>
                                    <small>Total Pembayaran</small>
                                </div>
                                <div class="card-footer">
                                    <i class="fas fa-receipt fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card bg-gradient-success">
                                <div class="card-body">
                                    <h4><?= $lunas ?></h4>
                                    <small>Pembayaran Lunas</small>
                                </div>
                                <div class="card-footer">
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card bg-gradient-warning">
                                <div class="card-body">
                                    <h4><?= $belum_lunas ?></h4>
                                    <small>Belum Lunas</small>
                                </div>
                                <div class="card-footer">
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card bg-gradient-info">
                                <div class="card-body">
                                    <h6>Rp <?= number_format($total_uang, 0, ',', '.') ?></h6>
                                    <small>Total Pemasukan</small>
                                </div>
                                <div class="card-footer">
                                    <i class="fas fa-wallet fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabel Data -->
                    <div class="card main-card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <i class="fas fa-table me-2"></i>
                                    Daftar Pembayaran Siswa
                                </div>
                                <div class="col-auto no-print">
                                    <button class="btn btn-print" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Cetak Laporan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Siswa</th>
                                            <th>NISN</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Jumlah Bayar</th>
                                            <th style="width: 100px;">Cicilan</th>
                                            <th style="width: 120px;">Status</th>
                                            <th class="no-print" style="width: 100px;">Bukti</th>
                                            <th class="no-print" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)): 
                                            $tanggal = date('d/m/Y', strtotime($row['Tanggal_pembayaran']));
                                            $jumlah = 'Rp ' . number_format($row['jumlah_pembayaran'], 0, ',', '.');
                                            $statusClass = ($row['status_pembayaran'] == 'Lunas') ? 'status-lunas' : 'status-belum';
                                        ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?= $no++; ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['nama']); ?></strong>
                                            </td>
                                            <td><code><?= htmlspecialchars($row['nisn']); ?></code></td>
                                            <td>
                                                <i class="bi bi-calendar3 text-muted me-1"></i>
                                                <?= $tanggal; ?>
                                            </td>
                                            <td class="fw-bold text-success"><?= $jumlah; ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    <i class="bi bi-arrow-repeat me-1"></i><?= $row['total_cicilan'] ?? 1; ?>x
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $statusClass; ?>">
                                                    <?php if($row['status_pembayaran'] == 'Lunas'): ?>
                                                        <i class="bi bi-check-circle me-1"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-clock me-1"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($row['status_pembayaran']); ?>
                                                </span>
                                            </td>
                                            <td class="no-print text-center">
                                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                                    <img src="<?= htmlspecialchars($row['bukti_pembayaran']); ?>" 
                                                         alt="Bukti" 
                                                         class="bukti-img"
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#modalBukti"
                                                         onclick="showImage('<?= htmlspecialchars($row['bukti_pembayaran']); ?>')"
                                                         onerror="this.src='img/no-image.png'">
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="bi bi-image"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="no-print">
                                                <button class="btn btn-primary btn-detail btn-sm w-100" 
                                                        onclick="showRiwayat(<?= $row['id_pembayaran']; ?>, '<?= htmlspecialchars($row['nama']); ?>')">
                                                    <i class="bi bi-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5>Tidak Ada Data</h5>
                                <p class="text-muted">Belum ada data pembayaran yang tersedia saat ini.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; SMK Al-Falah Bandung 2025</div>
                        <div>
                            <a href="#" class="text-decoration-none">Privacy Policy</a>
                            &middot;
                            <a href="#" class="text-decoration-none">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar bukti pembayaran -->
    <div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuktiLabel">
                        <i class="bi bi-image-fill me-2"></i>Bukti Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="modalImage" src="" alt="Bukti Pembayaran" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan riwayat pembayaran -->
    <div class="modal fade" id="modalRiwayat" tabindex="-1" aria-labelledby="modalRiwayatLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRiwayatLabel">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran - <span id="namaSiswa" class="fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="riwayatContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-3">Memuat riwayat pembayaran...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <!-- SB Admin Scripts -->
    <script src="../admin/js/scripts.js"></script>
    
    <script>
        // Fungsi untuk menampilkan gambar di modal
        function showImage(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
        }

        // Fungsi untuk menampilkan riwayat pembayaran
        function showRiwayat(idPembayaran, namaSiswa) {
            document.getElementById('namaSiswa').textContent = namaSiswa;
            
            const modal = new bootstrap.Modal(document.getElementById('modalRiwayat'));
            modal.show();
            
            document.getElementById('riwayatContent').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Memuat riwayat pembayaran...</p>
                </div>
            `;
            
            fetch('get_riwayat.php?id=' + idPembayaran)
                .then(response => response.json())
                .then(data => {
                    console.log('Data received:', data);
                    let html = '';
                    
                    if (data.success && data.riwayat && data.riwayat.length > 0) {
                        html = '<div class="riwayat-container">';
                        
                        data.riwayat.forEach((item, index) => {
                            html += `
                                <div class="riwayat-item">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-2">
                                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                                <strong>Pembayaran #${index + 1}</strong>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                ${item.tanggal_bayar}
                                            </p>
                                            ${item.catatan ? `<p class="mb-0 small"><i class="bi bi-chat-left-text me-1"></i>${item.catatan}</p>` : ''}
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-success fs-6 px-3 py-2">
                                                <i class="bi bi-cash me-1"></i>${item.jumlah_bayar_format}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted mb-2">
                                                <i class="bi bi-image me-1"></i>BUKTI PEMBAYARAN
                                            </label>
                                            <div class="position-relative">
                                                <img src="${item.bukti_bayar}" 
                                                     alt="Bukti Pembayaran ${index + 1}"
                                                     class="img-thumbnail w-100"
                                                     style="cursor: pointer; border-radius: 10px; max-height: 200px; object-fit: cover;"
                                                     onclick="showImage('${item.bukti_bayar}')"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#modalBukti"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <div style="display:none;" class="alert alert-danger small mb-0 mt-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Gambar tidak dapat dimuat
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold small text-muted mb-2">
                                                <i class="bi bi-info-circle me-1"></i>INFORMASI PEMBAYARAN
                                            </label>
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <small class="text-muted d-block mb-1">Tanggal Upload</small>
                                                            <strong>${item.created_at}</strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted d-block mb-1">Nominal</small>
                                                            <strong class="text-success">${item.jumlah_bayar_format}</strong>
                                                        </div>
                                                        ${item.catatan ? `
                                                        <div class="col-12">
                                                            <small class="text-muted d-block mb-1">Catatan</small>
                                                            <p class="mb-0">${item.catatan}</p>
                                                        </div>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        html += '</div>';
                        
                        // Summary Card
                        html += `
                            <div class="alert summary-alert mt-4 mb-0">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-3">
                                            <i class="bi bi-clipboard-check text-primary me-2"></i>
                                            <strong>Ringkasan Pembayaran</strong>
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded p-2 me-3">
                                                        <i class="bi bi-cash-stack text-white"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Total Pembayaran</small>
                                                        <strong class="fs-5 text-primary">${data.total_bayar_format}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info rounded p-2 me-3">
                                                        <i class="bi bi-arrow-repeat text-white"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Total Cicilan</small>
                                                        <strong class="fs-5">${data.total_cicilan}x Pembayaran</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted d-block mb-2">Status Pembayaran</small>
                                        <span class="badge ${data.status === 'Lunas' ? 'status-lunas' : 'status-belum'} fs-5 px-4 py-2">
                                            <i class="bi ${data.status === 'Lunas' ? 'bi-check-circle' : 'bi-clock'} me-2"></i>
                                            ${data.status}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        html = `
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5>Tidak Ada Riwayat</h5>
                                <p class="text-muted">Belum ada riwayat pembayaran untuk siswa ini.</p>
                            </div>
                        `;
                    }
                    
                    document.getElementById('riwayatContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('riwayatContent').innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle-fill fs-1 mb-3 d-block"></i>
                            <h5>Gagal Memuat Data</h5>
                            <p class="mb-0">Terjadi kesalahan saat memuat riwayat pembayaran.</p>
                            <small class="d-block mt-2 text-muted">${error.message}</small>
                        </div>
                    `;
                    console.error('Error:', error);
                });
        }

        // Smooth scroll to top when modal closes
        document.getElementById('modalRiwayat').addEventListener('hidden.bs.modal', function () {
            document.getElementById('riwayatContent').scrollTop = 0;
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>