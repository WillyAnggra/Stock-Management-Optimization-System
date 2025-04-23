<?php include "header.php"; ?>

<style>
    body {
        margin: 0;
        font-family: "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet";
        color: #333;
    }

    /* Content container */
    .content-container {
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        margin: 50px auto;
        max-width: 1200px;
        /* Menyesuaikan lebar kontainer dengan gambar */
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    /* Gravity text styling */
    #gravityText {
        display: inline-block;
        font-size: 20px;
        color: #9f795d;
        transition: transform 0.1s ease-in-out;
    }


    /* Parallax background for bottom section */
    .parallax-bottom {
        background-image: url('../assets/Images/parallax.jpg');
        /* Gambar bagian bawah */
        min-height: 500px;
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        border-radius: 10px;
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        #gravityText {
            font-size: 18px;
        }

        .content-container {
            padding: 15px;
            margin: 20px;
            max-width: 90%;
            /* Responsif untuk perangkat lebih kecil */
        }
    }
</style>

<!-- Content Section -->
<div class="content-container">
    <p style="font-size: 18px;">
        <?php
        echo '<strong id="gravityText">SELAMAT DATANG DI WEBSITE SHOES HOLIC<br>DATA MINING METODE ALGORITMA K - MEANS</strong>';
        ?>
    </p>
</div>

<!-- Parallax Bottom Section -->
<div class="parallax-bottom"></div>