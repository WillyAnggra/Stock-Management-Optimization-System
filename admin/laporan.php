<?php
include "../koneksi/config.php";
include "header.php";

// Set timezone ke waktu lokal Anda
date_default_timezone_set('Asia/Jakarta');

// Dapatkan tanggal otomatis sesuai hari ini
$tanggalCetak = date("d F Y");  // Format: 09 September 2024
$waktuCetak = date("H:i:s");    // Format waktu: 04:10:32

// Ambil data dari form input tanda tangan (jika ada)
$namaPenandatangan = isset($_POST['nama']) ? $_POST['nama'] : '...';
$jabatanPenandatangan = isset($_POST['jabatan']) ? $_POST['jabatan'] : '...';
$tempatPenandatangan = isset($_POST['tempat']) ? $_POST['tempat'] : '...';

// Ambil parameter pencarian
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$cluster = isset($_GET['cluster']) ? $_GET['cluster'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan K-Means Clustering - Shoes Holic</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        @media print {

            nav,
            .btn,
            footer,
            form,
            .tbl,
            .form-group {
                display: none !important;
            }
        }

        .signature-container {
            text-align: center;
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-content {
            width: 300px;
        }

        .signature-content .location-date {
            margin-bottom: 10px;
        }

        .signature-content .position {
            font-weight: bold;
            margin-bottom: 70px;
        }

        .signature-content .name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-content .signature-line {
            border-top: 1px solid black;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="row header-section">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <img src="../assets/images/masuk.png" alt="Logo" style="width: 150px; margin-right: 20px;">
                    <div>
                        <h4>SHOES HOLIC</h4>
                        <p>Telpon: 08116613156</p>
                        <p>Jl. Pemuda No.7, Olo, Kec. Padang Bar., Kota Padang, Sumatera Barat.</p>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 text-center">
                <h4>Laporan Hasil K-Means Clustering</h4>
            </div>
        </div>
        <hr>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-4">
            <div class="form-group">
                <label for="bulan">Cari Bulan:</label>
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Semua Bulan</option>
                    <option value="Januari" <?= $bulan == 'Januari' ? 'selected' : '' ?>>Januari</option>
                    <option value="Februari" <?= $bulan == 'Februari' ? 'selected' : '' ?>>Februari</option>
                    <option value="Maret" <?= $bulan == 'Maret' ? 'selected' : '' ?>>Maret</option>
                    <option value="April" <?= $bulan == 'April' ? 'selected' : '' ?>>April</option>
                    <option value="Mei" <?= $bulan == 'Mei' ? 'selected' : '' ?>>Mei</option>
                    <option value="Juni" <?= $bulan == 'Juni' ? 'selected' : '' ?>>Juni</option>
                    <option value="Juli" <?= $bulan == 'Juli' ? 'selected' : '' ?>>Juli</option>
                    <option value="Agustus" <?= $bulan == 'Agustus' ? 'selected' : '' ?>>Agustus</option>
                    <option value="September" <?= $bulan == 'September' ? 'selected' : '' ?>>September</option>
                    <option value="Oktober" <?= $bulan == 'Oktober' ? 'selected' : '' ?>>Oktober</option>
                    <option value="November" <?= $bulan == 'November' ? 'selected' : '' ?>>November</option>
                    <option value="Desember" <?= $bulan == 'Desember' ? 'selected' : '' ?>>Desember</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun">Cari Tahun:</label>
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Semua Tahun</option>
                    <option value="2024" <?= $tahun == '2024' ? 'selected' : '' ?>>2024</option>
                    <option value="2023" <?= $tahun == '2023' ? 'selected' : '' ?>>2023</option>
                    <option value="2022" <?= $tahun == '2022' ? 'selected' : '' ?>>2022</option>
                    <option value="2021" <?= $tahun == '2021' ? 'selected' : '' ?>>2021</option>
                    <option value="2020" <?= $tahun == '2020' ? 'selected' : '' ?>>2020</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cluster">Cari Cluster:</label>
                <select name="cluster" id="cluster" class="form-control">
                    <option value="">Semua Cluster</option>
                    <option value="1" <?= $cluster == '1' ? 'selected' : '' ?>>C1 (Laris)</option>
                    <option value="2" <?= $cluster == '2' ? 'selected' : '' ?>>C2 (Sedang)</option>
                    <option value="3" <?= $cluster == '3' ? 'selected' : '' ?>>C3 (Tidak Laris)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="laporan.php" class="btn btn-secondary">Reset</a>
        </form>

        <form method="POST" class="mb-4">
            <button type="button" class="btn btn-dark mt-3" onclick="window.print()">Cetak Laporan</button>
        </form>

        <?php
        // Ambil data kesimpulan terakhir dari tbl_iterasi
        $query_last_iteration = "SELECT MAX(Iterasi_ke) as IterasiTerakhir FROM tbl_iterasi";
        $result_last_iteration = mysqli_query($conn, $query_last_iteration);
        $row_last_iteration = mysqli_fetch_assoc($result_last_iteration);
        $iterasi_terakhir = $row_last_iteration['IterasiTerakhir'];

        // Ambil data berdasarkan iterasi terakhir
        // Ambil data berdasarkan iterasi terakhir dan filter cluster jika ada
        $query_clusters = "
        SELECT d.nama_produk, d.stok_produk, d.produk_terjual, d.tahun, d.bulan, i.cluster
        FROM tbl_data d
        JOIN tbl_iterasi i ON d.nama_produk = i.nama_produk
        WHERE i.Iterasi_ke = (SELECT MAX(Iterasi_ke) FROM tbl_iterasi)"; // Menampilkan data dari iterasi terakhir
        
        // Tambahkan filter bulan jika ada
        if (!empty($bulan)) {
            $query_clusters .= " AND d.bulan = '$bulan'";
        }

        // Tambahkan filter tahun jika ada
        if (!empty($tahun)) {
            $query_clusters .= " AND d.tahun = '$tahun'";
        }

        // Tambahkan filter cluster jika ada
        if (!empty($cluster)) {
            $query_clusters .= " AND i.cluster = $cluster";
        }

        // Query yang sudah disaring
        $result_clusters = mysqli_query($conn, $query_clusters);

        // Menyaring data agar unik berdasarkan kombinasi nama_produk dan cluster
        $unique_rows = [];
        while ($row = mysqli_fetch_assoc($result_clusters)) {
            // Gabungkan nama_produk dan cluster sebagai kunci unik
            $key = $row['nama_produk'] . '-' . $row['cluster'];
            if (!isset($unique_rows[$key])) {
                $unique_rows[$key] = $row;
            }
        }
        ?>

        <!-- Tabel Kesimpulan -->
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Nama Produk</th>
                    <th>Stok Produk</th>
                    <th>Produk Terjual</th>
                    <th>Cluster</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unique_rows as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tahun']); ?></td>
                        <td><?php echo htmlspecialchars($row['bulan']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                        <td><?php echo htmlspecialchars($row['stok_produk']); ?></td>
                        <td><?php echo htmlspecialchars($row['produk_terjual']); ?></td>
                        <td>
                            <?php
                            switch ($row['cluster']) {
                                case 1:
                                    echo 'C1 (Laris)';
                                    break;
                                case 2:
                                    echo 'C2 (Sedang)';
                                    break;
                                case 3:
                                    echo 'C3 (Tidak Laris)';
                                    break;
                                default:
                                    echo 'Unknown';
                                    break;
                            }
                            ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form method="POST" class="mb-4">
            <!-- Form untuk Input Tempat, Nama, dan Jabatan -->
            <div class="form-group mt-3">
                <label for="tempat">Tempat:</label>
                <input type="text" name="tempat" id="tempat" class="form-control" placeholder="Masukkan Tempat"
                    value="<?php echo htmlspecialchars($tempatPenandatangan); ?>" required>
            </div>
            <div class="form-group mt-3">
                <label for="nama">Nama Penandatangan:</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama"
                    value="<?php echo htmlspecialchars($namaPenandatangan); ?>" required>
            </div>
            <div class="form-group mt-2">
                <label for="jabatan">Jabatan Penandatangan:</label>
                <input type="text" name="jabatan" id="jabatan" class="form-control" placeholder="Masukkan Jabatan"
                    value="<?php echo htmlspecialchars($jabatanPenandatangan); ?>" required>
            </div>

            <button type="submit" name="simpan" class="btn btn-success mt-3">Simpan Data Penandatangan</button>
        </form>

        <!-- Tanda Tangan -->
        <div class="signature-container">
            <div class="signature-content">
                <p class="location-date"><?php echo htmlspecialchars($tempatPenandatangan); ?>,
                    <?php echo $tanggalCetak; ?>
                </p>
                <p class="position"><?php echo htmlspecialchars($jabatanPenandatangan); ?></p>
                <p class="name"><?php echo htmlspecialchars($namaPenandatangan); ?></p>
                <div class="signature-line"></div> <!-- Garis tempat tanda tangan -->
            </div>
        </div>

    </div>
    <br><br><br>
    <div class="row">
        <div class="col-12">
            <p>Di cetak pada: <?php echo $tanggalCetak; ?> Jam: <?php echo $waktuCetak; ?></p>
        </div>
    </div>

</body>

</html>