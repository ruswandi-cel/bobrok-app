<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Time Machine - BOBROK TEAM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .gallery-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 15px; }
        .gallery-item { background: #15191d; border-radius: 8px; overflow: hidden; }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; }
        .gallery-info { padding: 10px; font-size: 12px; }
    </style>
</head>
<body>
    <div style="padding: 20px; border-bottom: 1px solid #2c3136;">
        <a href="index.php" style="color:#adb5bd; text-decoration:none;">← Kembali</a>
        <h2 style="margin: 10px 0 0 0;">🎞️ Time Machine</h2>
    </div>

    <div class="gallery-grid">
        <?php
        $q = $conn->query("SELECT * FROM galeri ORDER BY tanggal_trip DESC");
        while($f = $q->fetch(PDO::FETCH_ASSOC)):
        ?>
        <div class="gallery-item">
            <img src="../assets/uploads/galeri/<?= $f['foto'] ?>" alt="Moment">
            <div class="gallery-info">
                <strong><?= $f['judul'] ?></strong><br>
                <small style="color:#6c757d;"><?= date('M Y', strtotime($f['tanggal_trip'])) ?></small>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>