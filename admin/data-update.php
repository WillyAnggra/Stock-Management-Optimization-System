<?php
include "../koneksi/config.php";
if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'ubah') {
        $id = $_POST['id'];
        $tahun = $_POST['tahun'];
        $bulan = $_POST['bulan'];
        $nama_produk = $_POST['nama_produk'];
        $stok_produk = $_POST['stok_produk'];
        $produk_terjual = $_POST['produk_terjual'];

        mysqli_query($conn, "UPDATE tbl_data SET  tahun='$tahun',
                                                  bulan='$bulan',
                                                  nama_produk='$nama_produk',
                                                  stok_produk='$stok_produk',
                                                  produk_terjual='$produk_terjual'
                                             WHERE id='$id'");
        header("location:data.php");
    }
}

include "header.php";
?>

<h2 class="mb-4">EDIT DATA PRODUK</h2>
<hr>
<div class="shadow p-5">
    <?php
    $data = mysqli_query($conn, "SELECT * FROM tbl_data WHERE id='$_GET[id]'");
    while ($a = mysqli_fetch_array($data)) {
        ?>
        <form action="data-update.php?aksi=ubah" method="post">
            <input name="id" type="hidden" value="<?= $a['id'] ?>">
            <div class="form-group">
                <label>Tahun</label>
                <input type="text" name="tahun" class="txt form-control" required value="<?= $a['tahun'] ?>">
            </div>
            <div class="form-group">
                <label>Bulan</label>
                <input type="text" name="bulan" class="txt form-control" required value="<?= $a['bulan'] ?>">
            </div>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="txt form-control" required value="<?= $a['nama_produk'] ?>">
            </div>
            <div class="form-group">
                <label>Stok Produk</label>
                <input type="text" name="stok_produk" class="txt form-control" required value="<?= $a['stok_produk'] ?>">
            </div>
            <div class="form-group">
                <label>Produk Terjual</label>
                <input type="text" name="produk_terjual" class="txt form-control" required
                    value="<?= $a['produk_terjual'] ?>">
            </div>
            <input type="submit" value="Ubah" class="btn btn-dark">
            <a href="data.php" class="btn btn-danger text-light">Batal</a>
        </form>
    <?php } ?>
</div>
</div>
</div>
</div>