<?php 
include '../../includes/db.php'; 

// Ambil daftar anggota untuk pilihan "Anggota yang Berangkat"
$query_anggota = $conn->query("SELECT nama FROM anggota ORDER BY nama ASC");
$daftar_anggota = $query_anggota->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_trip'];
    $budget = $_POST['budget'];
    $tanggal = $_POST['tanggal_trip'];
    $tikum = $_POST['titik_kumpul'];
    $jam = $_POST['jam_kumpul'];
    $safety = $_POST['pesan_safety'];
    
    // Gabungkan anggota yang dipilih menjadi string (Pisah dengan koma)
    $peserta = isset($_POST['peserta']) ? implode(', ', $_POST['peserta']) : 'Semua Anggota';

    // 1. Set semua trip lama jadi 'selesai'
    $conn->query("UPDATE trip SET status = 'selesai' WHERE status = 'aktif'");

    // 2. Insert trip baru dengan 6 poin utama
    $sql = "INSERT INTO trip (nama_trip, budget_awal, tanggal_trip, titik_kumpul, jam_kumpul, pesan_safety, peserta_trip, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif')";
    
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$nama, $budget, $tanggal, $tikum, $jam, $safety, $peserta])) {
        echo "<script>alert('Trip Baru Dimulai! Hati-hati di jalan!'); window.location='../index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulai Trip Baru - BOBROK HUB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0b0e11; color: #fff; padding: 20px; margin: 0; }
        .card { background: #15191d; padding: 30px; border-radius: 12px; max-width: 450px; margin: auto; border-top: 5px solid #007bff; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { margin-top: 0; text-align: center; color: #007bff; font-size: 20px; }
        label { font-size: 12px; color: #adb5bd; display: block; margin-top: 15px; font-weight: bold; text-transform: uppercase; }
        input, textarea { width: 100%; padding: 12px; margin-top: 5px; background: #0b0e11; border: 1px solid #2c3136; color: white; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .checkbox-group { background: #0b0e11; padding: 10px; border-radius: 8px; border: 1px solid #2c3136; margin-top: 5px; max-height: 150px; overflow-y: auto; }
        .checkbox-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 14px; }
        .checkbox-item input { width: auto; margin: 0; cursor: pointer; }
        button { width: 100%; padding: 15px; background: #007bff; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 25px; font-size: 16px; transition: 0.3s; }
        button:hover { background: #0056b3; transform: translateY(-2px); }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #6c757d; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>🚀 RENCANA TRIP BARU</h2>
        <form method="POST">
            <label>1. Total Anggaran Trip (Rp)</label>
            <input type="number" name="budget" placeholder="Input budget operasional" required>

            <label>2. Lokasi Tujuan</label>
            <input type="text" name="nama_trip" placeholder="Contoh: Bukit Cita-Cita" required>
            
            <label>3. Tanggal Keberangkatan</label>
            <input type="date" name="tanggal_trip" required>

            <label>4. Anggota yang Berangkat</label>
            <div class="checkbox-group">
                <?php foreach($daftar_anggota as $agt): ?>
                <div class="checkbox-item">
                    <input type="checkbox" name="peserta[]" value="<?= $agt['nama'] ?>">
                    <span><?= $agt['nama'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <label>5. Titik Kumpul & Jam</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="titik_kumpul" placeholder="Lokasi Kumpul" style="flex: 2;" required>
                <input type="time" name="jam_kumpul" style="flex: 1;" required>
            </div>

            <label>6. Ucapan Keselamatan</label>
            <textarea name="pesan_safety" rows="2" placeholder="Tulis ucapan keselamatan untuk tim..."></textarea>

            <button type="submit" name="submit">MULAI TRIP SEKARANG!</button>
            <a href="../index.php" class="back-link">← Batal & Kembali</a>
        </form>
    </div>
</body>
</html>