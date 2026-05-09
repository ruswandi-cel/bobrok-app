<?php
include '../../includes/db.php';
header('Content-Type: application/json');

if (isset($_POST['id_barang']) && isset($_POST['status'])) {
    $id = $_POST['id_barang'];
    $status = $_POST['status'];

    $sql = "UPDATE inventaris SET status = ? WHERE id_barang = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$status, $id])) {
        echo json_encode(["status" => "success"]);
    }
}