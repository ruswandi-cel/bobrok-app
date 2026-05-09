<?php
include '../../includes/db.php';
header('Content-Type: application/json');

// 1. Ambil Trip yang sedang AKTIF
$query = $conn->query("SELECT * FROM trip WHERE status = 'aktif' LIMIT 1");
$trip = $query->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    echo json_encode(["error" => "Tidak ada trip aktif"]);
    exit;
}

$id_trip = $trip['id_trip'];

// 2. Hitung Total Pengeluaran
$sqlTotal = $conn->prepare("SELECT SUM(nominal) as total FROM belanja WHERE id_trip = ?");
$sqlTotal->execute([$id_trip]);
$totalBelanja = $sqlTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 3. AMBIL RIWAYAT BELANJA (Terbaru)
// Kita ambil 3-5 item terakhir saja supaya dashboard tetap rapi
$sqlHistory = $conn->prepare("SELECT keterangan, nominal, waktu FROM belanja WHERE id_trip = ? ORDER BY waktu DESC LIMIT 5");
$sqlHistory->execute([$id_trip]);
$riwayat = $sqlHistory->fetchAll(PDO::FETCH_ASSOC);

$sisa = $trip['budget_awal'] - $totalBelanja;
$persentase = ($trip['budget_awal'] > 0) ? ($totalBelanja / $trip['budget_awal']) * 100 : 0;

// 4. Kirim Data Lengkap
echo json_encode([
    "id_trip" => $id_trip,
    "nama_trip" => $trip['nama_trip'],
    "budget_awal" => (int)$trip['budget_awal'],
    "terpakai" => (int)$totalBelanja,
    "sisa" => (int)$sisa,
    "persen_terpakai" => round($persentase, 2),
    "riwayat" => $riwayat // Data ini yang akan dibaca oleh JavaScript di index.php
]);