<?php
include '../../includes/db.php';

if (isset($_POST['tambah'])) {

    // 🔒 Sanitasi input
    $nama = trim($_POST['nama_barang']);
    $kondisi = $_POST['kondisi'];

    // Validasi sederhana
    if (empty($nama)) {
        echo "<script>alert('Nama barang tidak boleh kosong!');</script>";
    } else {

        try {
            $sql = "INSERT INTO inventaris (nama_barang, kondisi, status) 
                    VALUES (:nama, :kondisi, 'Gudang')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':kondisi' => $kondisi
            ]);

            echo "<script>
                    alert('✅ Barang berhasil ditambahkan!');
                    window.location='checklist.php';
                  </script>";

        } catch (PDOException $e) {
            echo "<script>alert('❌ Gagal tambah barang!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alat - BOBROK HUB</title>

    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-card {
            width: 380px;
        }

        .form-card h3 {
            margin-bottom: 10px;
        }

        .form-card p {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: #0b0e11;
            border: 1px solid #2c3136;
            color: white;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-save {
            width: 100%;
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card form-card">
    <h3>➕ Tambah Inventaris</h3>
    <p>Tambahkan perlengkapan baru untuk trip BOBROK TEAM.</p>

    <form method="POST">

        <label>Nama Barang</label>
        <input type="text" name="nama_barang" placeholder="Contoh: Kompor Portable" required>

        <label>Kondisi Awal</label>
        <select name="kondisi">
            <option value="Bagus">✅ Bagus / Siap Pakai</option>
            <option value="Rusak">❌ Perlu Perbaikan</option>
        </select>

        <button type="submit" name="tambah" class="btn btn-primary btn-save">
            💾 Simpan Barang
        </button>

        <a href="checklist.php" class="btn-cancel">← Batal</a>
    </form>
</div>

</body>
</html>