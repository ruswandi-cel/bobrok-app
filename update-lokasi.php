<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// VALIDASI METHOD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Method tidak diizinkan"
    ]);
    exit;
}

// VALIDASI INPUT
if (!isset($_POST['id_anggota'], $_POST['lat'], $_POST['lng'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter tidak lengkap"
    ]);
    exit;
}

$id  = (int) $_POST['id_anggota'];
$lat = (float) $_POST['lat'];
$lng = (float) $_POST['lng'];

try {

    $sql = "UPDATE anggota 
            SET latitude = :lat, 
                longitude = :lng, 
                terakhir_online = NOW() 
            WHERE id = :id";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':id'  => $id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Lokasi terupdate",
        "data" => [
            "id" => $id,
            "lat" => $lat,
            "lng" => $lng
        ]
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

}