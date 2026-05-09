<?php
include '../../includes/db.php';

// 1. Ambil data trip yang masih aktif
$st = $conn->query("SELECT * FROM trip WHERE status = 'aktif' LIMIT 1");
$trip = $st->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    echo "Tidak ada trip aktif yang bisa ditutup.";
    exit;
}

// 2. Hitung total belanja trip ini
$id_t = $trip['id_trip'];
$qB = $conn->prepare("SELECT SUM(nominal) as total FROM belanja WHERE id_trip = ?");
$qB->execute([$id_t]);
$resB = $qB->fetch(PDO::FETCH_ASSOC);
$total_belanja = $resB['total'] ?? 0;

// 3. Hitung Sisa
$sisa = $trip['budget_awal'] - $total_belanja;

// 4. Proses Tutup Trip & Pindah Dana
try {
    $conn->beginTransaction();

    // Set trip jadi selesai
    $conn->prepare("UPDATE trip SET status = 'selesai' WHERE id_trip = ?")->execute([$id_t]);

    // Jika ada sisa, masukkan ke kas besar
    if ($sisa > 0) {
        $msg = "Sisa dana dari trip: " . $trip['nama_trip'];
        $insKas = $conn->prepare("INSERT INTO kas_besar (jumlah, keterangan) VALUES (?, ?)");
        $insKas->execute([$sisa, $msg]);
    }

    $conn->commit();
    echo "<script>alert('Trip Ditutup! Sisa Rp " . number_format($sisa) . " telah dipindah ke Kas Besar.'); window.location='../index.php';</script>";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Gagal menutup trip: " . $e->getMessage();
}
?>