<?php
include "../koneksi/config.php";

// Handle penghapusan data
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM tbl_data WHERE id='$id'");
    header("location:data.php");
}

$search_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$search_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

$query = "
    SELECT * FROM tbl_data 
    WHERE ('$search_tahun' = '' OR tahun = '$search_tahun')
      AND ('$search_bulan' = '' OR bulan = '$search_bulan')
    ORDER BY CAST(tahun AS UNSIGNED) DESC, 
             FIELD(bulan, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'),
             id";

$data = mysqli_query($conn, $query);

// Menghitung rowspan untuk tahun dan bulan secara terpisah
$rows = []; // Inisialisasi array kosong sebelum fetch data

$data = mysqli_query($conn, $query);

// Mengisi array $rows jika ada data
if ($data && mysqli_num_rows($data) > 0) {
    while ($row = mysqli_fetch_array($data)) {
        $rows[] = $row;
    }
}

// Menghitung rowspan
$rowspan_tahun = [];
$rowspan_bulan = [];

// Hitung jumlah rowspan berdasarkan kombinasi tahun-bulan
foreach ($rows as $row) {
    // Hitung rowspan untuk tahun
    $rowspan_tahun[$row['tahun']] = isset($rowspan_tahun[$row['tahun']])
        ? $rowspan_tahun[$row['tahun']] + 1
        : 1;

    // Hitung rowspan untuk kombinasi tahun dan bulan
    $key_bulan = $row['tahun'] . '-' . $row['bulan'];
    $rowspan_bulan[$key_bulan] = isset($rowspan_bulan[$key_bulan])
        ? $rowspan_bulan[$key_bulan] + 1
        : 1;
}

include "header.php";
?>

<h2 class="mb-4">DATA PRODUK</h2>
<hr>
<div class="shadow p-5">
    <!-- Form Pencarian -->
    <form method="GET" action="data.php" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <!-- Dropdown untuk memilih bulan -->
                <select name="bulan" class="form-control">
                    <option value="">-- Pilih Bulan --</option>
                    <?php
                    $bulan_list = [
                        'Januari',
                        'Februari',
                        'Maret',
                        'April',
                        'Mei',
                        'Juni',
                        'Juli',
                        'Agustus',
                        'September',
                        'Oktober',
                        'November',
                        'Desember'
                    ];
                    foreach ($bulan_list as $bulan) {
                        $selected = (isset($_GET['bulan']) && $_GET['bulan'] == $bulan) ? 'selected' : '';
                        echo "<option value='$bulan' $selected>$bulan</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <!-- Dropdown untuk memilih tahun -->
                <select name="tahun" class="form-control">
                    <option value="">-- Pilih Tahun --</option>
                    <?php
                    // Buat daftar tahun secara dinamis
                    $tahun_awal = 2020;
                    $tahun_akhir = date("Y"); // Tahun sekarang
                    for ($tahun = $tahun_awal; $tahun <= $tahun_akhir; $tahun++) {
                        $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $tahun) ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark">Cari</button>
            </div>
        </div>
    </form>

    <a href="data-add.php" class="btn btn-dark mb-3"><span class="fa fa-plus"> Tambah Data</span></a>
    <hr>

    <!-- Tabel Data -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Tahun</th>
                <th class="text-center">Bulan</th>
                <th class="text-center">Nama Produk</th>
                <th class="text-center">Stok Produk</th>
                <th class="text-center">Produk Terjual</th>
                <th class="text-center">Opsi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Tampilkan data
            if (count($rows) > 0) {
                $current_tahun = "";
                $current_bulan_key = "";
                $no = 1;

                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td class='text-center'>{$no}</td>";

                    // Tampilkan tahun dengan rowspan jika berubah
                    if ($row['tahun'] != $current_tahun) {
                        echo "<td class='text-center' rowspan='{$rowspan_tahun[$row['tahun']]}'>{$row['tahun']}</td>";
                        $current_tahun = $row['tahun'];
                    }

                    // Tampilkan bulan dengan rowspan jika berubah
                    $key_bulan = $row['tahun'] . '-' . $row['bulan'];
                    if ($key_bulan != $current_bulan_key) {
                        echo "<td class='text-center' rowspan='{$rowspan_bulan[$key_bulan]}'>{$row['bulan']}</td>";
                        $current_bulan_key = $key_bulan;
                    }

                    echo "<td class='text-center'>{$row['nama_produk']}</td>";
                    echo "<td class='text-center'>{$row['stok_produk']}</td>";
                    echo "<td class='text-center'>{$row['produk_terjual']}</td>";
                    echo "<td class='text-center'>
                        <a href='data-update.php?id={$row['id']}' class='btn btn-dark'><span class='fa fa-pencil'></span></a>
                        <a href='data.php?id={$row['id']}&aksi=hapus' class='btn btn-danger'><span class='fa fa-trash'></span></a>
                      </td>";
                    echo "</tr>";
                    $no++;
                }

            } else {
                echo "<tr><td colspan='7' class='text-center'>Tidak Ada Data</td></tr>";
            }

            ?>
        </tbody>
    </table>
</div>