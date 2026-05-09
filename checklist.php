<?php
include '../../includes/auth.php';
cekLogin();

include '../../includes/db.php';

// ========================
// AMBIL DATA
// ========================
try {
    // Inventaris
    $query = $conn->query("SELECT * FROM inventaris ORDER BY nama_barang ASC");
    $items = $query->fetchAll(PDO::FETCH_ASSOC);

    // Anggota
    $queryAnggota = $conn->query("SELECT nama FROM anggota ORDER BY nama ASC");
    $listAnggota = $queryAnggota->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gagal mengambil data!");
}

// ========================
// UPDATE ITEM
// ========================
if (isset($_POST['update_item'])) {

    $id = (int) $_POST['id_barang'];
    $status = trim($_POST['status']);
    $kondisi = trim($_POST['kondisi']);

    if (!$id || $status == '' || $kondisi == '') {
        echo "<script>alert('Data tidak valid!');</script>";
    } else {

        $sql = "UPDATE inventaris SET status = ?, kondisi = ? WHERE id_barang = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$status, $kondisi, $id])) {
            echo "<script>alert('Logistik diperbarui!'); window.location='checklist.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal update!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Logistik - BOBROK HUB</title>

    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #0b0e11; color: #fff; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }

        .btn-add {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-back { color: #adb5bd; text-decoration: none; font-size: 14px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .item-card {
            background: #15191d;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #2c3136;
            display: flex;
            flex-direction: column;
        }

        .item-card h3 {
            margin: 0 0 15px 0;
            border-bottom: 1px solid #2c3136;
            padding-bottom: 10px;
        }

        label { font-size: 12px; color: #adb5bd; }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: none;
        }

        select {
            background: #0b0e11;
            color: #fff;
            border: 1px solid #2c3136;
            margin-bottom: 10px;
        }

        .btn-save {
            background: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .kondisi-good { color: #28a745; font-weight: bold; }
        .kondisi-bad { color: #dc3545; font-weight: bold; }

        .card-footer {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #2c3136;
            padding-top: 15px;
        }

        .action-link { text-decoration: none; font-size: 13px; font-weight: bold; }
        .edit-link { color: #ffc107; }
        .delete-link { color: #dc3545; }

        .empty {
            text-align: center;
            color: #6c757d;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="container">

    <a href="../index.php" class="btn-back">← Kembali ke Dashboard</a>

    <div class="header-flex">
        <div>
            <h2>Checklist Logistik Group</h2>
            <p style="color:#adb5bd;">Kelola perabotan camping BOBROK TEAM</p>
        </div>
        <a href="tambah.php" class="btn-add">+ Tambah Barang</a>
    </div>

    <div class="grid">

        <?php if (!empty($items)): ?>
            <?php foreach($items as $item): ?>

            <div class="item-card">

                <h3><?= htmlspecialchars($item['nama_barang']) ?></h3>

                <p>
                    Status:
                    <strong style="color:#007bff;">
                        <?= htmlspecialchars($item['status']) ?>
                    </strong><br>

                    Kondisi:
                    <span class="<?= $item['kondisi'] == 'Bagus' ? 'kondisi-good' : 'kondisi-bad' ?>">
                        <?= strtoupper(htmlspecialchars($item['kondisi'])) ?>
                    </span>
                </p>

                <form method="POST">
                    <input type="hidden" name="id_barang" value="<?= (int)$item['id_barang'] ?>">

                    <label>Update Lokasi / Pembawa:</label>
                    <select name="status">

                        <option value="Gudang" <?= $item['status'] == 'Gudang' ? 'selected' : '' ?>>
                            🏠 Di Gudang
                        </option>

                        <optgroup label="Dibawa Oleh:">
                            <?php foreach($listAnggota as $ag): ?>
                                <option value="Dibawa <?= htmlspecialchars($ag['nama']) ?>"
                                <?= $item['status'] == "Dibawa " . $ag['nama'] ? 'selected' : '' ?>>
                                    👤 <?= htmlspecialchars($ag['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>

                    </select>

                    <label>Update Kondisi:</label>
                    <select name="kondisi">
                        <option value="Bagus" <?= $item['kondisi'] == 'Bagus' ? 'selected' : '' ?>>
                            ✅ Bagus
                        </option>
                        <option value="Rusak" <?= $item['kondisi'] == 'Rusak' ? 'selected' : '' ?>>
                            ❌ Rusak
                        </option>
                    </select>

                    <button type="submit" name="update_item" class="btn-save">
                        Simpan Perubahan
                    </button>
                </form>

                <div class="card-footer">
                    <a href="edit-barang.php?id=<?= (int)$item['id_barang'] ?>" class="action-link edit-link">
                        ✏️ Edit
                    </a>

                    <a href="hapus.php?id=<?= (int)$item['id_barang'] ?>"
                       onclick="return confirm('Yakin hapus <?= htmlspecialchars($item['nama_barang']) ?>?')"
                       class="action-link delete-link">
                        🗑️ Hapus
                    </a>
                </div>

            </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">
                Belum ada data inventaris
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>