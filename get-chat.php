<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// Ambil 15 pesan terakhir beserta nama pengirimnya
$query = "SELECT chat.*, anggota.nama 
          FROM chat 
          JOIN anggota ON chat.id_anggota = anggota.id 
          ORDER BY chat.created_at DESC 
          LIMIT 15";

$stmt = $conn->query($query);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kita balik urutannya supaya yang paling baru ada di bawah (seperti chat pada umumnya)
echo json_encode(array_reverse($chats));
?>