<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
require_once 'koneksi.php';

// Handle hapus data
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_pendaftaran = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Hapus data (cascade akan otomatis menghapus data di tabel jurusan, pembayaran, dan wali)
    $query_delete = "DELETE FROM pendaftaran WHERE id_pendaftaran = '$id_pendaftaran'";
    
    if (mysqli_query($conn, $query_delete)) {
        $_SESSION['success_message'] = "Data pendaftaran berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data: " . mysqli_error($conn);
    }
    
    header("Location: index.php");
    exit();
}

// Query untuk mendapatkan statistik
// Total Pendaftar
$query_total_pendaftar = "SELECT COUNT(*) as total FROM pendaftaran";
$result_total_pendaftar = mysqli_query($conn, $query_total_pendaftar);
$total_pendaftar = mysqli_fetch_assoc($result_total_pendaftar)['total'];

// Sudah Bayar (Pending + Belum Lunas + Lunas)
$query_sudah_bayar = "SELECT COUNT(*) as total FROM pembayaran";
$result_sudah_bayar = mysqli_query($conn, $query_sudah_bayar);
$sudah_bayar = mysqli_fetch_assoc($result_sudah_bayar)['total'];

// Lunas
$query_lunas = "SELECT COUNT(*) as total FROM pembayaran WHERE status_pembayaran = 'Lunas'";
$result_lunas = mysqli_query($conn, $query_lunas);
$lunas = mysqli_fetch_assoc($result_lunas)['total'];

// Belum Bayar
$query_belum_bayar = "SELECT COUNT(*) as total FROM pendaftaran p LEFT JOIN pembayaran pb ON p.id_pendaftaran = pb.id_pendaftaran WHERE pb.id_pembayaran IS NULL";
$result_belum_bayar = mysqli_query($conn, $query_belum_bayar);
$belum_bayar = mysqli_fetch_assoc($result_belum_bayar)['total'];

// Query untuk datatable dengan status pembayaran
$query_table = "SELECT 
    p.id_pendaftaran,
    p.nama, 
    p.nisn, 
    p.jenis_kelamin, 
    TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as umur,
    p.asal_sekolah,
    p.tgl_daftar,
    COALESCE(pb.status_pembayaran, 'Belum Bayar') as status_pembayaran
FROM pendaftaran p
LEFT JOIN pembayaran pb ON p.id_pendaftaran = pb.id_pendaftaran
ORDER BY p.tgl_daftar DESC";
$result_table = mysqli_query($conn, $query_table);

// Query untuk chart data
$query_chart = "SELECT 
    DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan,
    COUNT(*) as total
FROM pendaftaran
GROUP BY DATE_FORMAT(tgl_daftar, '%Y-%m')
ORDER BY bulan";
$result_chart = mysqli_query($conn, $query_chart);

$chart_labels = [];
$chart_data = [];
while ($row = mysqli_fetch_assoc($result_chart)) {
    $chart_labels[] = $row['bulan'];
    $chart_data[] = $row['total'];
}

// Query untuk bar chart (jurusan)
$query_bar = "SELECT 
    jurusan1 as jurusan,
    COUNT(*) as total
FROM jurusan
GROUP BY jurusan1
UNION ALL
SELECT 
    jurusan2 as jurusan,
    COUNT(*) as total
FROM jurusan
WHERE jurusan2 IS NOT NULL
GROUP BY jurusan2";
$result_bar = mysqli_query($conn, $query_bar);

$bar_labels = [];
$bar_data = [];
$jurusan_counts = [];

while ($row = mysqli_fetch_assoc($result_bar)) {
    if (isset($jurusan_counts[$row['jurusan']])) {
        $jurusan_counts[$row['jurusan']] += $row['total'];
    } else {
        $jurusan_counts[$row['jurusan']] = $row['total'];
    }
}

foreach ($jurusan_counts as $jurusan => $total) {
    $bar_labels[] = $jurusan;
    $bar_data[] = $total;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SPMB Alfalah</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="icon" type="x-icon" href="img/Alfalah.png">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            .btn-delete {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            .status-badge {
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.75rem;
                font-weight: 600;
            }
            .status-lunas {
                background-color: #28a745;
                color: white;
            }
            .status-pending {
                background-color: #007bff;
                color: white;
            }
            .status-belum-lunas {
                background-color: #ffc107;
                color: #000;
            }
            .status-belum-bayar {
                background-color: #dc3545;
                color: white;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">SPMB Alfalah</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
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
                                            <a class="nav-link" href="log/500.html">500 Page</a>                                        </nav>
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
                            <a class="nav-link" href="backup/kontribusi.html">
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
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        
                        <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); endif; ?>
                        
                        <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); endif; ?>
                        
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <i class="fas fa-users me-2"></i>Total Pendaftar
                                        <h3 class="mb-0 mt-2"><?php echo $total_pendaftar; ?></h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#datatablesSimple">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4">
                                    <div class="card-body">
                                        <i class="fas fa-credit-card me-2"></i>Sudah Bayar
                                        <h3 class="mb-0 mt-2"><?php echo $sudah_bayar; ?></h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="bayar.php">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <i class="fas fa-check-circle me-2"></i>Lunas
                                        <h3 class="mb-0 mt-2"><?php echo $lunas; ?></h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="tables.php">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Belum Bayar
                                        <h3 class="mb-0 mt-2"><?php echo $belum_bayar; ?></h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#datatablesSimple">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Area Chart SPMB ALFALAH
                                    </div>
                                    <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart SPMB ALFALAH
                                    </div>
                                    <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable SPMB ALFALAH
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Umur</th>
                                            <th>Asal Sekolah</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_table)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                                            <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                                            <td><?php echo htmlspecialchars($row['umur']); ?> tahun</td>
                                            <td><?php echo htmlspecialchars($row['asal_sekolah']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tgl_daftar'])); ?></td>
                                            <td>
                                                <?php 
                                                $status = $row['status_pembayaran'];
                                                $badge_class = 'status-badge ';
                                                if ($status == 'Lunas') {
                                                    $badge_class .= 'status-lunas';
                                                } elseif ($status == 'Pending') {
                                                    $badge_class .= 'status-pending';
                                                } elseif ($status == 'Belum Lunas') {
                                                    $badge_class .= 'status-belum-lunas';
                                                } else {
                                                    $badge_class .= 'status-belum-bayar';
                                                }
                                                ?>
                                                <span class="<?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($row['status_pembayaran'] == 'Belum Bayar'): ?>
                                                <button class="btn btn-danger btn-delete btn-sm" onclick="confirmDelete(<?php echo $row['id_pendaftaran']; ?>, '<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Umur</th>
                                            <th>Asal Sekolah</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Admin Website 2025/2026</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script>
            // Fungsi konfirmasi hapus
            function confirmDelete(id, nama) {
                if (confirm('Apakah Anda yakin ingin menghapus pendaftaran atas nama "' + nama + '"?\n\nData ini akan dihapus permanen dari database!')) {
                    window.location.href = 'index.php?action=delete&id=' + id;
                }
            }
            
            // Area Chart
            Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';

            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chart_labels); ?>,
                    datasets: [{
                        label: "Pendaftaran",
                        lineTension: 0.3,
                        backgroundColor: "rgba(2,117,216,0.2)",
                        borderColor: "rgba(2,117,216,1)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(2,117,216,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(2,117,216,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: <?php echo json_encode($chart_data); ?>,
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'month'
                            },
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5
                            },
                            gridLines: {
                                color: "rgba(0, 0, 0, .125)",
                            }
                        }],
                    },
                    legend: {
                        display: false
                    }
                }
            });

            // Bar Chart
            var ctx2 = document.getElementById("myBarChart");
            var myBarChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($bar_labels); ?>,
                    datasets: [{
                        label: "Jumlah Peminat",
                        backgroundColor: "rgba(2,117,216,1)",
                        borderColor: "rgba(2,117,216,1)",
                        data: <?php echo json_encode($bar_data); ?>,
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 6
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                    },
                    legend: {
                        display: false
                    }
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const datatablesSimple = document.getElementById('datatablesSimple');
                if (datatablesSimple) {
                    new simpleDatatables.DataTable(datatablesSimple);
                }
            });
            // Tambahkan script ini di index.php sebelum </body>

// Function untuk refresh data statistik
function refreshStatistics() {
    fetch('api/get_statistics.php')
        .then(response => response.json())
        .then(data => {
            // Update card statistik
            document.querySelector('.card.bg-primary h3').textContent = data.total_pendaftar;
            document.querySelector('.card.bg-warning h3').textContent = data.sudah_bayar;
            document.querySelector('.card.bg-success h3').textContent = data.lunas;
            document.querySelector('.card.bg-danger h3').textContent = data.belum_bayar;
        })
        .catch(error => console.error('Error refreshing statistics:', error));
}

// Function untuk refresh datatable
function refreshDataTable() {
    fetch('api/get_registrations.php')
        .then(response => response.json())
        .then(data => {
            // Rebuild datatable
            const datatable = document.getElementById('datatablesSimple');
            if (datatable) {
                // Hapus data lama
                const tbody = datatable.querySelector('tbody');
                tbody.innerHTML = '';
                
                // Tambah data baru
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    
                    // Tentukan class badge
                    let badgeClass = 'status-badge ';
                    if (row.status_pembayaran === 'Lunas') {
                        badgeClass += 'status-lunas';
                    } else if (row.status_pembayaran === 'Pending') {
                        badgeClass += 'status-pending';
                    } else if (row.status_pembayaran === 'Belum Lunas') {
                        badgeClass += 'status-belum-lunas';
                    } else {
                        badgeClass += 'status-belum-bayar';
                    }
                    
                    // Format tanggal
                    const date = new Date(row.tgl_daftar);
                    const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
                    
                    tr.innerHTML = `
                        <td>${row.nama}</td>
                        <td>${row.nisn}</td>
                        <td>${row.jenis_kelamin}</td>
                        <td>${row.umur} tahun</td>
                        <td>${row.asal_sekolah}</td>
                        <td>${formattedDate}</td>
                        <td><span class="${badgeClass}">${row.status_pembayaran}</span></td>
                        <td>
                            ${row.status_pembayaran === 'Belum Bayar' ? 
                                `<button class="btn btn-danger btn-delete btn-sm" onclick="confirmDelete(${row.id_pendaftaran}, '${row.nama}')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>` : 
                                '<span class="text-muted">-</span>'
                            }
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                
                // Reinitialize datatable
                new simpleDatatables.DataTable(datatable, {
                    searchable: true,
                    fixedHeight: true
                });
            }
        })
        .catch(error => console.error('Error refreshing datatable:', error));
}

// Function untuk refresh charts
function refreshCharts() {
    fetch('api/get_chart_data.php')
        .then(response => response.json())
        .then(data => {
            // Update area chart
            myLineChart.data.labels = data.area_labels;
            myLineChart.data.datasets[0].data = data.area_data;
            myLineChart.update();
            
            // Update bar chart
            myBarChart.data.labels = data.bar_labels;
            myBarChart.data.datasets[0].data = data.bar_data;
            myBarChart.update();
        })
        .catch(error => console.error('Error refreshing charts:', error));
}

// Auto refresh setiap 5 detik (5000 ms)
setInterval(() => {
    refreshStatistics();
    refreshDataTable();
    refreshCharts();
}, 360000);

// Indicator untuk menunjukkan last update time
function updateLastRefreshTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID');
    
    // Tambahkan element jika belum ada
    let indicator = document.getElementById('last-refresh-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'last-refresh-indicator';
        indicator.className = 'alert alert-info position-fixed bottom-0 end-0 m-3';
        indicator.style.cssText = 'z-index: 9999; opacity: 0.8;';
        document.body.appendChild(indicator);
    }
    
    indicator.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> Last update: ${timeString}`;
    
    // Fade out after 2 seconds
    setTimeout(() => {
        indicator.style.opacity = '0';
        setTimeout(() => {
            indicator.style.opacity = '0.8';
        }, 460000);
    }, 360000);
}

// Update indicator setiap refresh
setInterval(updateLastRefreshTime, 360000);
        </script>
        
    </body>
</html>