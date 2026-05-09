<?php 
include '../includes/db.php'; 

// Ambil data Trip yang sedang aktif
// Kita pastikan mengambil kolom-kolom baru yang kita bahas tadi
$query = $conn->query("SELECT * FROM trip WHERE status = 'aktif' LIMIT 1");
$trip = $query->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Trip - BOBROK HUB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Tambahkan efek Glassmorphism agar lebih menarik sesuai gambar mockup */
        .event-header { 
            background: rgba(21, 25, 29, 0.8); 
            padding: 20px; 
            border-bottom: 2px solid var(--primary); 
            backdrop-filter: blur(10px);
        }
        .detail-box { padding: 20px; padding-bottom: 100px; }
        .info-card { 
            background: var(--card-bg); 
            padding: 15px; 
            border-radius: 12px; 
            margin-bottom: 15px; 
            border: 1px solid var(--border);
            border-left: 4px solid var(--success); 
        }
        .info-label { color: var(--text-muted); font-size: 11px; font-weight: bold; display: block; margin-bottom: 5px; letter-spacing: 1px; }
        .info-value { font-size: 15px; font-weight: bold; color: #fff; display: block; }
        .btn-map { 
            display: block; 
            background: var(--primary); 
            color: white; 
            text-align: center; 
            padding: 15px; 
            border-radius: 12px; 
            text-decoration: none; 
            font-weight: bold; 
            margin-top: 20px;
            box-shadow: 0 4px 15px var(--primary-glow);
        }
        .member-tag {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin: 4px 2px;
            border: 1px solid var(--border);
        }
    </style>
</head>
<body>

    <div class="event-header">
        <a href="index.php" style="color:var(--text-muted); text-decoration:none; font-size: 14px;">← Dashboard</a>
        <h2 style="margin: 10px 0 0 0; letter-spacing: 1px;">📅 DETAIL TRIP</h2>
    </div>

    <div class="detail-box">
        <?php if($trip): ?>
            <div class="info-card" style="border-left-color: var(--primary);">
                <span class="info-label">BUDGET OPERASIONAL</span>
                <span class="info-value">Rp <?= number_format($trip['budget_awal'], 0, ',', '.') ?></span>
            </div>

            <div class="info-card" style="border-left-color: var(--success);">
                <span class="info-label">TUJUAN & WAKTU</span>
                <span class="info-value"><?= strtoupper($trip['nama_trip']) ?></span>
                <span style="font-size: 13px; color: var(--text-muted);">
                    🗓️ <?= date('l, d F Y', strtotime($trip['tanggal_trip'])) ?>
                </span>
            </div>

            <div class="info-card" style="border-left-color: #ffc107;">
                <span class="info-label">TITIK KUMPUL (TIKUM)</span>
                <span class="info-value"><?= $trip['titik_kumpul'] ?? 'Basecamp Bobrok' ?></span>
                <span style="font-size: 13px; color: var(--text-muted);">
                    ⏰ Pukul <?= date('H:i', strtotime($trip['jam_kumpul'] ?? '20:00:00')) ?> WIB
                </span>
            </div>

            <div class="info-card" style="border-left-color: #e83e8c;">
                <span class="info-label">ANGGOTA BERANGKAT</span>
                <div style="margin-top: 10px;">
                    <?php 
                    // Asumsi kolom peserta_trip menyimpan nama yang dipisahkan koma
                    $peserta = explode(',', $trip['peserta_trip'] ?? 'Semua Anggota');
                    foreach($peserta as $p): ?>
                        <span class="member-tag">🏍️ <?= trim($p) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="info-card" style="border-left-color: var(--danger);">
                <span class="info-label">UCAPAN KESELAMATAN</span>
                <p style="font-size: 13px; line-height: 1.6; color: #e9ecef; margin: 8px 0 0 0; font-style: italic;">
                    "<?= $trip['pesan_safety'] ?? 'Tetap utamakan keselamatan, cek kondisi motor sebelum gas!' ?>"
                </p>
            </div>

            <a href="https://www.google.com/maps/search/?api=1&query=camping+ground" target="_blank" class="btn-map">
                🗺️ LIHAT RUTE DI GOOGLE MAPS
            </a>

        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px;">
                <div style="font-size: 50px;">☕</div>
                <h3 style="color: #6c757d;">Belum ada trip aktif.</h3>
                <p style="color: #495057;">Waktunya ngopi dulu, Elliot!</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>