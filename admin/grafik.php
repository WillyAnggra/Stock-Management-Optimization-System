<?php
include '../koneksi/config.php';
include "header.php";

// Ambil iterasi terakhir
$query_last_iteration = "SELECT MAX(Iterasi_ke) as IterasiTerakhir FROM tbl_iterasi";
$result_last_iteration = mysqli_query($conn, $query_last_iteration);
$row_last_iteration = mysqli_fetch_assoc($result_last_iteration);
$iterasi_terakhir = $row_last_iteration['IterasiTerakhir'];

// Ambil data berdasarkan iterasi terakhir
$query_clusters = "SELECT nama_produk, stok_produk, produk_terjual, cluster FROM tbl_iterasi WHERE Iterasi_ke = $iterasi_terakhir";
$result_clusters = mysqli_query($conn, $query_clusters);

$data_points = [];
$centroids = [
    1 => ['x' => 0, 'y' => 0, 'count' => 0],
    2 => ['x' => 0, 'y' => 0, 'count' => 0],
    3 => ['x' => 0, 'y' => 0, 'count' => 0],
];

// Persiapkan data untuk scatter plot
while ($row = mysqli_fetch_assoc($result_clusters)) {
    $cluster = $row['cluster'];
    $nama_produk = $row['nama_produk'];

    // Kunci unik untuk menghindari duplikasi
    $key = $nama_produk . '-' . $cluster;

    if (!isset($data_points[$key])) {
        $data_points[$key] = [
            'nama' => $nama_produk,
            'stok' => (float) $row['stok_produk'],
            'terjual' => (float) $row['produk_terjual'],
            'cluster' => (int) $cluster,
        ];

        // Hitung centroid berdasarkan data
        $centroids[$cluster]['x'] += $row['stok_produk'];
        $centroids[$cluster]['y'] += $row['produk_terjual'];
        $centroids[$cluster]['count']++;
    }
}

// Hitung posisi centroid final
foreach ($centroids as $key => $centroid) {
    if ($centroid['count'] > 0) {
        $centroids[$key]['x'] /= $centroid['count'];
        $centroids[$key]['y'] /= $centroid['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Hasil K-Means - Shoes Holic</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Grafik Hasil K-Means - Shoes Holic</h2>
        <hr>
        <canvas id="kmeansChart" width="800" height="400"></canvas>
    </div>

    <script>
        // Data dari PHP
        const dataPoints = <?= json_encode(array_values($data_points)) ?>;
        const centroids = <?= json_encode($centroids) ?>;

        // Format data untuk scatter plot
        const colors = ["#FF6384", "#36A2EB", "#FFCE56"]; // Warna untuk cluster
        const clusterNames = ["C1 (Laris)", "C2 (Sedang)", "C3 (Tidak Laris)"];
        const datasets = [1, 2, 3].map(cluster => ({
            label: `Cluster ${cluster}`,
            data: dataPoints.filter(point => point.cluster === cluster).map(point => ({
                x: point.stok,
                y: point.terjual,
                label: point.nama, // Tambahkan nama produk
            })),
            backgroundColor: colors[cluster - 1],
            pointRadius: 8, // Ukuran titik data
        }));

        // Centroid dataset
        datasets.push({
            label: 'Centroids',
            data: Object.entries(centroids).map(([key, centroid]) => ({
                x: centroid.x,
                y: centroid.y,
                label: clusterNames[key - 1], // Beri label centroid
            })),
            backgroundColor: "#000",
            borderColor: "#FFD700",
            borderWidth: 3, // Tambahkan garis tepi untuk menonjolkan centroid
            pointStyle: "star", // Bentuk centroid menjadi bintang
            pointRadius: 12, // Ukuran centroid lebih besar
        });

        // Konfigurasi Chart.js
        const ctx = document.getElementById('kmeansChart').getContext('2d');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: datasets,
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top', // Legenda di bagian atas
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                // Tooltip untuk produk
                                if (context.dataset.label.startsWith('Cluster')) {
                                    return `${context.raw.label} (${context.raw.x}, ${context.raw.y})`;
                                }
                                // Tooltip untuk centroid
                                return `${context.raw.label} (${context.raw.x.toFixed(2)}, ${context.raw.y.toFixed(2)})`;
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Stok',
                            font: {
                                size: 16,
                            },
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Produk Terjual',
                            font: {
                                size: 16,
                            },
                        }
                    }
                },
            }
        });
    </script>

</body>

</html>

<?php include "footer.php"; ?>