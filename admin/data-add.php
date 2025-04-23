<?php
include "../koneksi/config.php";
if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'simpan') {
        $tahun = $_POST['tahun'];
        $bulan = $_POST['bulan'];
        $nama_produk = $_POST['nama_produk'];
        $stok_produk = $_POST['stok_produk'];
        $produk_terjual = $_POST['produk_terjual'];

        mysqli_query($conn, "INSERT INTO tbl_data(tahun,
                                                  bulan,
                                                  nama_produk,
                                                  stok_produk,
                                                  produk_terjual) 
                             VALUES ('$tahun',
                                     '$bulan',
                                     '$nama_produk',
                                     '$stok_produk',
                                     '$produk_terjual')");
        header("location:data.php");
    }
}
include "header.php";
?>

<h2 class="mb-4">Tambah Data Produk</h2>
<hr>
<div class="shadow p-5">
    <form action="data-add.php?aksi=simpan" method="post">
        <div class="form-group">
            <label>Tahun</label>
            <input type="text" name="tahun" class="txt form-control" required>
        </div>
        <div class="form-group">
            <label>Bulan</label>
            <input type="text" name="bulan" class="txt form-control" required>
        </div>
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" class="txt form-control" required>
        </div>
        <div class="form-group">
            <label>Stok Produk</label>
            <input type="text" name="stok_produk" class="txt form-control" required>
        </div>
        <div class="form-group">
            <label>Produk Terjual</label>
            <input type="text" name="produk_terjual" class="txt form-control" required>
        </div>
        <input type="submit" value="Simpan" class="btn btn-dark">
        <a href="data.php" class="btn btn-danger text-light">Batal</a>
    </form>
</div>
</div>
</div>
</div>