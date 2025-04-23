<?php
session_start(); // Start session
include "../koneksi/config.php";
include "header.php";

// Menampilkan hasil dari session jika ada
if (isset($_SESSION['centroids']) && isset($_SESSION['iteration_results'])) {
    $centroids = $_SESSION['centroids'];
    $clusters = $_SESSION['iteration_results'];
}

// Reset data (if requested)
if (isset($_POST['reset'])) {
    // Delete old data in iteration and centroid tables
    mysqli_query($conn, "DELETE FROM tbl_iterasi");
    mysqli_query($conn, "DELETE FROM tbl_centroids");
}

// Fetch initial centroids from the database (if any)
$centroids = [];
$result_centroids = mysqli_query($conn, "SELECT * FROM tbl_centroids ORDER BY iterasi_ke ASC, cluster ASC");
while ($row = mysqli_fetch_assoc($result_centroids)) {
    $centroids[] = [(float) $row['x'], (float) $row['y']];
}

// Fetch the last iteration only if the submit button is pressed
$iterasi_terakhir = 0;
if (isset($_POST['submit'])) {
    // Check if iterations already exist, if not set to 1
    $result_iterasi = mysqli_query($conn, "SELECT MAX(iterasi_ke) AS iterasi_terakhir FROM tbl_iterasi");
    if ($row = mysqli_fetch_assoc($result_iterasi)) {
        $iterasi_terakhir = $row['iterasi_terakhir'] ? $row['iterasi_terakhir'] : 1; // Start from iteration 1 if none exist
    }
} else {
    // If submit button not pressed, set iteration to 0 (no iteration)
    $iterasi_terakhir = 0;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-Means Clustering - Shoes Holic</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">K-Means Clustering - Shoes Holic</h2>
        <hr>

        <!-- Reset Button -->
        <form method="POST" class="mb-4">
            <button type="submit" name="reset" class="btn btn-danger">Reset Data</button>
        </form>

        <!-- Input Centroid Form -->
        <form action="" method="POST">
            <h4>Masukkan Centroid Awal:</h4>
            <div class="form-row">
                <div class="col-md-2">C1 (Laris):</div>
                <div class="col-md-2"><input type="number" name="C1x" class="form-control" required></div>
                <div class="col-md-2"><input type="number" name="C1y" class="form-control" required></div>
            </div>
            <div class="form-row mt-2">
                <div class="col-md-2">C2 (Sedang):</div>
                <div class="col-md-2"><input type="number" name="C2x" class="form-control" required></div>
                <div class="col-md-2"><input type="number" name="C2y" class="form-control" required></div>
            </div>
            <div class="form-row mt-2">
                <div class="col-md-2">C3 (Tidak Laris):</div>
                <div class="col-md-2"><input type="number" name="C3x" class="form-control" required></div>
                <div class="col-md-2"><input type="number" name="C3y" class="form-control" required></div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary mt-3">Proses</button>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            // Clear iteration and centroid tables before inserting new data
            mysqli_query($conn, "DELETE FROM tbl_iterasi");
            mysqli_query($conn, "DELETE FROM tbl_centroids");

            // Insert new centroids
            $centroids = [
                [(float) $_POST['C1x'], (float) $_POST['C1y']],
                [(float) $_POST['C2x'], (float) $_POST['C2y']],
                [(float) $_POST['C3x'], (float) $_POST['C3y']],
            ];
            foreach ($centroids as $cluster => $centroid) {
                $query = "INSERT INTO tbl_centroids (iterasi_ke, cluster, x, y) VALUES (1, " . ($cluster + 1) . ", {$centroid[0]}, {$centroid[1]})";
                mysqli_query($conn, $query);
            }
            $iterasi_terakhir = 1;
        }

        // Process Iterations if submit button is pressed or iteration exists
        if (isset($_POST['submit']) || $iterasi_terakhir > 0) {
            // Fetch product data
            $query_data = "SELECT id, nama_produk, stok_produk, produk_terjual FROM tbl_data";
            $result_data = mysqli_query($conn, $query_data);
            $data = [];
            while ($row = mysqli_fetch_assoc($result_data)) {
                $data[] = [
                    'id' => $row['id'],
                    'nama_produk' => $row['nama_produk'],
                    'stok' => (float) $row['stok_produk'],
                    'terjual' => (float) $row['produk_terjual'],
                ];
            }

            // Process Iteration
            echo "<h4 class='mt-4'>Proses Iterasi</h4>";
            do {
                $clusters = [[], [], []];

                // Display current centroids at the top (before iteration table)
                echo "<h5>Centroid Iterasi $iterasi_terakhir</h5>";
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Cluster</th><th>X (Stok)</th><th>Y (Terjual)</th></tr></thead><tbody>';
                foreach ($centroids as $cluster => $centroid) {
                    echo "<tr>
                        <td>Cluster " . ($cluster + 1) . "</td>
                        <td>" . round($centroid[0], 2) . "</td>
                        <td>" . round($centroid[1], 2) . "</td>
                    </tr>";
                }
                echo '</tbody></table>';

                // Display iteration table
                echo "<h5 class='mt-4'>Iterasi $iterasi_terakhir</h5>";
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Nama Produk</th><th>Stok</th><th>Terjual</th><th>Jarak ke C1</th><th>Jarak ke C2</th><th>Jarak ke C3</th><th>Cluster</th></tr></thead><tbody>';

                foreach ($data as $key => $produk) {
                    $distances = [];
                    foreach ($centroids as $centroid) {
                        $distances[] = sqrt(pow($produk['stok'] - $centroid[0], 2) + pow($produk['terjual'] - $centroid[1], 2));
                    }
                    $closest_centroid = array_search(min($distances), $distances);
                    $clusters[$closest_centroid][] = $produk;
                    $data[$key]['cluster'] = $closest_centroid + 1;

                    // Display results
                    echo "<tr>
                        <td>{$produk['nama_produk']}</td>
                        <td>{$produk['stok']}</td>
                        <td>{$produk['terjual']}</td>
                        <td>" . round($distances[0], 2) . "</td>
                        <td>" . round($distances[1], 2) . "</td>
                        <td>" . round($distances[2], 2) . "</td>
                        <td>Cluster " . ($closest_centroid + 1) . "</td>
                    </tr>";

                    // Save iteration results to the database
                    $query = "INSERT INTO tbl_iterasi (iterasi_ke, nama_produk, stok_produk, produk_terjual, jarak_c1, jarak_c2, jarak_c3, cluster)
                          VALUES ($iterasi_terakhir, '{$produk['nama_produk']}', {$produk['stok']}, {$produk['terjual']}, {$distances[0]}, {$distances[1]}, {$distances[2]}, " . ($closest_centroid + 1) . ")";
                    mysqli_query($conn, $query);
                }
                echo '</tbody></table>';

                // Update centroids
                $new_centroids = [];
                foreach ($clusters as $cluster) {
                    $sum_x = $sum_y = 0;
                    foreach ($cluster as $point) {
                        $sum_x += $point['stok'];
                        $sum_y += $point['terjual'];
                    }
                    $new_centroids[] = [
                        count($cluster) ? $sum_x / count($cluster) : 0,
                        count($cluster) ? $sum_y / count($cluster) : 0,
                    ];
                }

                // Insert new centroids into the database
                $iterasi_terakhir++;
                foreach ($new_centroids as $cluster => $centroid) {
                    $query = "INSERT INTO tbl_centroids (iterasi_ke, cluster, x, y) VALUES ($iterasi_terakhir, " . ($cluster + 1) . ", {$centroid[0]}, {$centroid[1]})";
                    mysqli_query($conn, $query);
                }

                $converged = ($centroids == $new_centroids);
                $centroids = $new_centroids;
            } while (!$converged && $iterasi_terakhir <= 10);

            // Display final conclusions
            echo "<h4 class='mt-5'>Kesimpulan Akhir</h4>";
            $cluster_names = ['Laris', 'Sedang', 'Tidak Laris'];
            foreach ($clusters as $index => $cluster) {
                // Tampilkan nama cluster
                echo "<h5>Cluster " . ($index + 1) . " ({$cluster_names[$index]})</h5>";

                // Tabel untuk setiap cluster
                echo '<table class="table table-bordered">';
                echo '<thead><tr><th>Tahun</th><th>Bulan</th><th>Nama Produk</th><th>Stok Produk</th><th>Produk Terjual</th></tr></thead>';
                echo '<tbody>';

                // Menampilkan produk dalam cluster
                foreach ($cluster as $produk) {
                    // Ambil tahun dan bulan dari tabel tbl_data berdasarkan id produk
                    $query_tahun_bulan = "SELECT tahun, bulan FROM tbl_data WHERE id = {$produk['id']}";
                    $result_tahun_bulan = mysqli_query($conn, $query_tahun_bulan);
                    if ($row = mysqli_fetch_assoc($result_tahun_bulan)) {
                        $tahun = $row['tahun'];
                        $bulan = $row['bulan'];
                    } else {
                        $tahun = date("Y"); // Default jika data tidak ditemukan
                        $bulan = date("m"); // Default jika data tidak ditemukan
                    }

                    // Tampilkan data produk dalam tabel
                    echo "<tr>
                <td>{$tahun}</td>
                <td>{$bulan}</td>
                <td>{$produk['nama_produk']}</td>
                <td>{$produk['stok']}</td>
                <td>{$produk['terjual']}</td>
              </tr>";
                }

                echo '</tbody></table>';
            }
        }
        ?>
    </div>
</body>

</html>