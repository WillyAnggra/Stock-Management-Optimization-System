<?php
error_reporting(0);
session_start();
include "../koneksi/config.php";
include "../koneksi/cek.php";
?>

<!doctype html>
<html lang="en">

<head>
    <title>MENENTUKAN KELARISAN ALGORITMA K-MEANS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css-sidebar/css/style.css">
    <style>
        #sidebar {
            background-color: #000000;
        }

        .txt {
            border: 1px solid;
        }

        .logo-icon {
            width: 50px;
            /* Sesuaikan ukuran sesuai kebutuhan Anda */
            height: 50px;
            /* Sesuaikan ukuran sesuai kebutuhan Anda */
            vertical-align: middle;
            /* Untuk memastikan gambar berada di tengah tombol */
            border-radius: 40px;
            margin: -40px;
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex align-items-stretch">
        <nav id="sidebar" class="active">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <img src="../assets/images/logo.png" alt="Menu" class="logo-icon">
                    <span class="sr-only">Toggle Menu</span>
                </button>
            </div>
            <div class="p-4">
                <ul class="list-unstyled components mb-5">
                    <li class="active">
                        <a href="index.php"><span class="fa fa-home mr-3"></span>Home</a>
                    </li>
                    <li>
                        <a href="data.php"><span class="bi bi-table mr-3"></span>Data</a>
                    </li>
                    <li>
                        <a href="analisa-kmeans.php"><span class="bi bi-gear-fill mr-3"></span>Analisa K-Means</a>
                    </li>
                    <li>
                        <a href="laporan.php"><span class="bi bi-printer-fill mr-3"></span>Laporan</a>
                    </li>
                    <li>
                        <a href="grafik.php"><span class="bi bi-bar-chart-fill mr-3"></span>Grafik Hasil</a>
                    </li>
                    <li>
                        <a href="logout.php"><span class="bi bi-box-arrow-right mr-3"></span>Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Page Content  -->
        <div id="content" class="p-5 p-md-5 pt-5">
            <div class="container">
                <!-- Page content goes here -->

                <script src="../assets/css-sidebar/js/jquery.min.js"></script>
                <script src="../assets/css-sidebar/js/popper.js"></script>
                <script src="../assets/css-sidebar/js/bootstrap.min.js"></script>
                <script src="../assets/css-sidebar/js/main.js"></script>
</body>

</html>