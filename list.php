<?php
include '../../includes/auth.php';
cekLogin();

include '../../includes/db.php';

try {
    // Ambil semua data anggota
    $query = $conn->query("SELECT * FROM anggota ORDER BY nama ASC");
    $anggota = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data anggota!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota - BOBROK HUB</title>
    <style>
        body { font-family: sans-serif; background: #0b0e11; color: #fff; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: #15191d; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #2c3136; }
        th { background: #007bff; color: white; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; }
        .online { background: #28a745; }
        .offline { background: #6c757d; }
        .btn-edit { color: #ffc107; text-decoration: none; font-weight: bold; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #adb5bd; text-decoration: none; }
        .empty { text-align:center; color:#6c757d; padding:20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="btn-back">← Kembali ke Dashboard</a>
        <h2>Manajemen Anggota (<?= count($anggota) ?>)</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($anggota)): ?>
                    <?php foreach($anggota as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                        <td>@<?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 'online' ? 'online' : 'offline' ?>">
                                <?= strtoupper(htmlspecialchars($row['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty">Belum ada anggota</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>