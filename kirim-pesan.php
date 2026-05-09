<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// Ambil data dari POST (ID Anggota dan Isi Pesan)
if (isset($_POST['id_anggota']) && isset($_POST['pesan'])) {
    $id = $_POST['id_anggota'];
    $pesan = $_POST['pesan'];

    $sql = "INSERT INTO chat (id_anggota, pesan) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$id, $pesan])) {
        echo json_encode(["status" => "success", "message" => "Pesan terkirim"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal kirim pesan"]);
    }
}
?>