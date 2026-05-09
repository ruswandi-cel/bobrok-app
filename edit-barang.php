<?php
include '../../includes/auth.php';
cekLogin();

include '../../includes/db.php';

// ========================
// VALIDASI ID
// ========================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: checklist.php");
    exit;
}

$id = (int) $_GET['id'];

// ========================
// AMBIL DATA BARANG
// ========================
$stmt = $conn->prepare("SELECT * FROM inventaris WHERE id_barang = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "Barang tidak ditemukan!";
    exit;
}

// ========================
// PROSES UPDATE
// ========================
if (isset($_POST['update'])) {

    $nama = trim($_POST['nama_barang']);

    if ($nama == '') {
        echo "<script>alert('Nama barang tidak boleh kosong!');</script>";
    } else {

        $sql = "UPDATE inventaris SET nama_barang = ? WHERE id_barang = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$nama, $id])) {
            echo "<script>alert('Nama barang diperbarui!'); window.location='checklist.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal update!');</script>";
        }
    }
}
?>