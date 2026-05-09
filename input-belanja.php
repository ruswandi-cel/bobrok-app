<?php
include '../../includes/db.php';

// 1. Ambil Trip Aktif
$st = $conn->query("SELECT id_trip, nama_trip FROM trip WHERE status = 'aktif' LIMIT 1");
$trip = $st->fetch(PDO::FETCH_ASSOC);

// 2. HAPUS DATA
if (isset($_GET['hapus'])) {
    $id_h = (int) $_GET['hapus'];

    $del = $conn->prepare("DELETE FROM belanja WHERE id_belanja = ?");
    if ($del->execute([$id_h])) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='input-belanja.php';</script>";
        exit;
    }
}

// 3. TAMBAH DATA
if (isset($_POST['simpan'])) {

    if (!$trip) {
        echo "<script>alert('Tidak ada trip aktif!'); window.location='set-budget.php';</script>";
        exit;
    }

    $ket = trim($_POST['keterangan']);
    $nom = (int) $_POST['nominal'];

    if (empty($ket) || $nom <= 0) {
        echo "<script>alert('Input tidak valid!');</script>";
    } else {
        $ins = $conn->prepare("
            INSERT INTO belanja (id_trip, keterangan, nominal, waktu) 
            VALUES (?, ?, ?, NOW())
        ");

        if ($ins->execute([$trip['id_trip'], $ket, $nom])) {
            echo "<script>alert('Belanja berhasil dicatat!'); window.location='input-belanja.php';</script>";
            exit;
        }
    }
}

// 4. AMBIL DATA BELANJA
$list_belanja = [];

if ($trip) {
    $qb = $conn->prepare("
        SELECT * FROM belanja 
        WHERE id_trip = ? 
        ORDER BY waktu DESC
    ");
    $qb->execute([$trip['id_trip']]);
    $list_belanja = $qb->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Input Belanja - BOBROK HUB</title>

<link rel="stylesheet" href="../../assets/css/style.css">

<style>
.container {
    max-width: 500px;
    margin: auto;
    padding: 20px;
}

.card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid var(--border);
    margin-bottom: 20px;
}

.trip-info {
    background: rgba(46, 204, 113, 0.1);
    color: var(--success);
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 15px;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 12px;
    background: #0b0e11;
    border: 1px solid #2c3136;
    color: white;
    border-radius: 8px;
}

.btn {
    width: 100%;
    padding: 12px;
    background: var(--success);
    border: none;
    color: white;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
}

.table-box {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #2c3136;
}

th {
    background: #15191d;
    color: var(--text-muted);
}

.btn-edit { color: #f1c40f; text-decoration: none; }
.btn-del { color: #ff4757; text-decoration: none; }
</style>
</head>

<body>

<div class="container">

    <!-- FORM -->
    <div class="card">
        <h3>🛒 Input Belanja</h3>

        <div class="trip-info">
            Trip: <b><?= htmlspecialchars($trip['nama_trip'] ?? 'Tidak Ada') ?></b>
        </div>

        <form method="POST">
            <input type="text" name="keterangan" placeholder="Contoh: Beli Beras" required>
            <input type="number" name="nominal" placeholder="Contoh: 50000" required>
            <button type="submit" name="simpan" class="btn">Simpan</button>
        </form>

        <a href="../index.php" style="display:block; text-align:center; margin-top:10px; color:#888;">← Dashboard</a>
    </div>

    <!-- TABLE -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Nominal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($list_belanja)): ?>
                    <?php foreach ($list_belanja as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['keterangan']) ?></td>
                        <td>Rp <?= number_format($b['nominal'], 0, ',', '.') ?></td>
                        <td>
                            <a href="edit-belanja.php?id=<?= $b['id_belanja'] ?>" class="btn-edit">Edit</a>
                            |
                            <a href="?hapus=<?= $b['id_belanja'] ?>" 
                               class="btn-del"
                               onclick="return confirm('Hapus data ini?')">
                               Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#777;">
                            Belum ada pengeluaran
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>