<?php
$conn = mysqli_connect("localhost", "root", "", "kasir_db");

if (!$conn) {
    die("Koneksi database gagal");
}