<?php
include '../../includes/db.php';
header('Content-Type: application/json');

try {

    // Ambil semua anggota yang punya lokasi
    $query = $conn->query("
        SELECT 
            id, 
            nama, 
            no_urut, 
            latitude, 
            longitude, 
            terakhir_online
        FROM anggota 
        WHERE latitude IS NOT NULL 
        ORDER BY no_urut ASC
    ");

    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    $leader = null;
    $latestTime = 0;

    foreach ($data as $row) {

        // ========================
        // STATUS ONLINE (<= 10 detik)
        // ========================
        $isOnline = false;
        if ($row['terakhir_online']) {
            $last = strtotime($row['terakhir_online']);
            if (time() - $last <= 10) {
                $isOnline = true;
            }
        }

        // ========================
        // DETEKSI LEADER (paling update)
        // ========================
        if ($row['terakhir_online']) {
            $time = strtotime($row['terakhir_online']);
            if ($time > $latestTime) {
                $latestTime = $time;
                $leader = $row['id'];
            }
        }

        $result[] = [
            "id" => $row['id'],
            "nama" => $row['nama'],
            "no_urut" => $row['no_urut'],
            "latitude" => (float)$row['latitude'],
            "longitude" => (float)$row['longitude'],
            "online" => $isOnline,
            "is_leader" => false // nanti diupdate
        ];
    }

    // ========================
    // SET LEADER
    // ========================
    foreach ($result as &$r) {
        if ($r['id'] == $leader) {
            $r['is_leader'] = true;
        }
    }

    echo json_encode([
        "status" => "success",
        "total" => count($result),
        "data" => $result
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}