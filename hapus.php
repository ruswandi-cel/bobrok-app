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
// CEK DATA DULU
// ========================
$stmt = $conn->prepare("SELECT nama_barang FROM inventaris WHERE id_barang = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='checklist.php';</script>";
    exit;
}

// ========================
// PROSES DELETE
// ========================
try {

    $stmt = $conn->prepare("DELETE FROM inventaris WHERE id_barang = ?");
    $stmt->execute([$id]);

    echo "<script>
        alert('Barang \"".htmlspecialchars($item['nama_barang'])."\" berhasil dihapus!');
        window.location='checklist.php';
    </script>";

} catch (PDOException $e) {

    echo "<script>alert('Gagal menghapus data!'); window.location='checklist.php';</script>";

}
?>