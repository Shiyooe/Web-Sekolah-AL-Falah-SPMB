<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
require_once 'koneksi.php';

// Query untuk Area Chart - Pendaftaran per bulan
$query_area = "SELECT 
    DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan,
    DATE_FORMAT(tgl_daftar, '%M %Y') as bulan_nama,
    COUNT(*) as total
FROM pendaftaran
GROUP BY DATE_FORMAT(tgl_daftar, '%Y-%m')
ORDER BY bulan";
$result_area = mysqli_query($conn, $query_area);

$area_labels = [];
$area_data = [];
while ($row = mysqli_fetch_assoc($result_area)) {
    $area_labels[] = $row['bulan_nama'];
    $area_data[] = $row['total'];
}

// Query untuk Bar Chart - Jumlah peminat per jurusan
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

// ========== PERBAIKAN PIE CHART ==========
// Query untuk mendapatkan statistik pembayaran
$stats = [];

// 1. Total Pendaftar
$query_total = "SELECT COUNT(*) as total FROM pendaftaran";
$result_total = mysqli_query($conn, $query_total);
$stats['Total Pendaftar'] = mysqli_fetch_assoc($result_total)['total'];

// 2. Sudah Bayar (semua yang punya record di tabel pembayaran)
$query_sudah_bayar = "SELECT COUNT(DISTINCT id_pendaftaran) as total FROM pembayaran";
$result_sudah_bayar = mysqli_query($conn, $query_sudah_bayar);
$stats['Sudah Bayar'] = mysqli_fetch_assoc($result_sudah_bayar)['total'];

// 3. Lunas
$query_lunas = "SELECT COUNT(*) as total FROM pembayaran WHERE status_pembayaran = 'Lunas'";
$result_lunas = mysqli_query($conn, $query_lunas);
$stats['Lunas'] = mysqli_fetch_assoc($result_lunas)['total'];

// 4. Belum Bayar
$query_belum_bayar = "SELECT COUNT(*) as total 
    FROM pendaftaran p 
    LEFT JOIN pembayaran pb ON p.id_pendaftaran = pb.id_pendaftaran 
    WHERE pb.id_pembayaran IS NULL";
$result_belum_bayar = mysqli_query($conn, $query_belum_bayar);
$stats['Belum Bayar'] = mysqli_fetch_assoc($result_belum_bayar)['total'];

// Prepare data untuk chart
$pie_labels = [];
$pie_data = [];
$pie_colors = [];

$color_map = [
    'Total Pendaftar' => '#007bff',  // Biru
    'Sudah Bayar' => '#ffc107',      // Kuning/Warning
    'Lunas' => '#28a745',            // Hijau
    'Belum Bayar' => '#dc3545'       // Merah
];

foreach ($stats as $label => $value) {
    $pie_labels[] = $label;
    $pie_data[] = $value;
    $pie_colors[] = $color_map[$label];
}
// ========== END PERBAIKAN PIE CHART ==========

// Get last update time
$query_last_update = "SELECT MAX(tgl_daftar) as last_update FROM pendaftaran";
$result_last_update = mysqli_query($conn, $query_last_update);
$last_update = mysqli_fetch_assoc($result_last_update)['last_update'];
$last_update_formatted = $last_update ? date('l, F d, Y \a\t g:i A', strtotime($last_update)) : 'No data';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Charts - SPMB Alfalah</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="icon" type="x-icon" href="img/Alfalah.png">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Charts</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Charts</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                <i class="fas fa-info-circle text-primary"></i> 
                                Chart.js digunakan untuk menampilkan visualisasi data SPMB Alfalah. 
                                Grafik di bawah ini menampilkan data real-time dari database sistem pendaftaran.
                                Untuk informasi lebih lanjut tentang Chart.js, kunjungi
                                <a target="_blank" href="https://www.chartjs.org/docs/latest/">dokumentasi resmi Chart.js</a>.
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Area Chart - Trend Pendaftaran per Bulan
                            </div>
                            <div class="card-body"><canvas id="myAreaChart" width="100%" height="30"></canvas></div>
                            <div class="card-footer small text-muted">Last updated: <?php echo $last_update_formatted; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart - Peminat per Jurusan
                                    </div>
                                    <div class="card-body"><canvas id="myBarChart" width="100%" height="50"></canvas></div>
                                    <div class="card-footer small text-muted">Last updated: <?php echo $last_update_formatted; ?></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Pie Chart - Status Pembayaran
                                    </div>
                                    <div class="card-body"><canvas id="myPieChart" width="100%" height="50"></canvas></div>
                                    <div class="card-footer small text-muted">Last updated: <?php echo $last_update_formatted; ?></div>
                                </div>
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
            // Set default font family and color
            Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';

            // Area Chart - Pendaftaran per Bulan
            var ctx1 = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($area_labels); ?>,
                    datasets: [{
                        label: "Jumlah Pendaftaran",
                        lineTension: 0.3,
                        backgroundColor: "rgba(2,117,216,0.2)",
                        borderColor: "rgba(2,117,216,1)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(2,117,216,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: "rgba(2,117,216,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: <?php echo json_encode($area_data); ?>,
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 10
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                                beginAtZero: true
                            },
                            gridLines: {
                                color: "rgba(0, 0, 0, .125)",
                            }
                        }],
                    },
                    legend: {
                        display: true
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return 'Pendaftaran: ' + tooltipItem.yLabel + ' siswa';
                            }
                        }
                    }
                }
            });

            // Bar Chart - Peminat per Jurusan
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
                                maxTicksLimit: 10
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                                beginAtZero: true
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                    },
                    legend: {
                        display: true
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return 'Peminat: ' + tooltipItem.yLabel + ' siswa';
                            }
                        }
                    }
                }
            });

            // Pie Chart - Status Pembayaran
            var ctx3 = document.getElementById("myPieChart");
            var myPieChart = new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($pie_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($pie_data); ?>,
                        backgroundColor: <?php echo json_encode($pie_colors); ?>,
                    }],
                },
                options: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index];
                                
                                // Hitung total dengan benar
                                var total = 0;
                                for (var i = 0; i < data.datasets[0].data.length; i++) {
                                    total += parseInt(data.datasets[0].data[i]) || 0;
                                }
                                
                                // Cegah division by zero
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                
                                return label + ': ' + value + ' siswa (' + percentage + '%)';
                            }
                        }
                    }
                }
            });
        </script>
    </body>
</html>