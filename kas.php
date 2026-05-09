<?php 
include '../includes/db.php'; 

// 1. Ambil Total Kas Besar (Saldo Abadi)
$qKas = $conn->query("SELECT SUM(jumlah) as total FROM kas_besar");
$kasBesar = $qKas->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 2. Ambil Riwayat Trip yang sudah selesai
$qTrip = $conn->query("SELECT * FROM trip WHERE status = 'selesai' ORDER BY tanggal_trip DESC");
$historyTrip = $qTrip->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas Besar - BOBROK HUB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .kas-header { 
            background: linear-gradient(45deg, #1d2b3a, #15191d); 
            padding: 40px 20px; 
            text-align: center; 
            border-bottom: 3px solid #28a745;
        }
        .saldo-amount { font-size: 32px; font-weight: bold; color: #28a745; margin-top: 10px; }
        .history-section { padding: 20px; }
        .trip-item { 
            background: #15191d; 
            padding: 15px; 
            border-radius: 10px; 
            margin-bottom: 12px; 
            border: 1px solid #2c3136;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .trip-name { font-weight: bold; display: block; }
        .trip-date { font-size: 12px; color: #6c757d; }
        .trip-budget { color: #007bff; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    <div class="kas-header">
        <a href="index.php" style="color:#adb5bd; text-decoration:none; font-size: 14px; position: absolute; left: 20px; top: 20px;">← Kembali</a>
        <div style="font-size: 14px; color: #adb5bd;">TOTAL SALDO KAS BESAR</div>
        <div class="saldo-amount">Rp <?= number_format($kasBesar, 0, ',', '.') ?></div>
        <small style="color: #6c757d;">Dana abadi untuk keperluan darurat & alat.</small>
    </div>

    <div class="history-section">
        <h3 style="margin-bottom: 20px; font-size: 16px;">📜 Riwayat Perjalanan</h3>
        
        <?php if($historyTrip): ?>
            <?php foreach($historyTrip as $t): ?>
                <div class="trip-item">
                    <div>
                        <span class="trip-name"><?= htmlspecialchars($t['nama_trip']) ?></span>
                        <span class="trip-date"><?= date('d M Y', strtotime($t['tanggal_trip'])) ?></span>
                    </div>
                    <div class="trip-budget">
                        Rp <?= number_format($t['budget_awal'], 0, ',', '.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #495057; margin-top: 30px;">Belum ada riwayat perjalanan yang tercatat.</p>
        <?php endif; ?>
    </div>

</body>
</html>