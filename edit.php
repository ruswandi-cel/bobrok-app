<?php
include '../../includes/auth.php';
cekLogin();

include '../../includes/db.php';

// VALIDASI ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid!");
}

$id = (int) $_GET['id'];

// AMBIL DATA USER
$stmt = $conn->prepare("SELECT * FROM anggota WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Anggota tidak ditemukan!");
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    $status = $_POST['status'];

    // VALIDASI
    if ($nama == '') {
        echo "<script>alert('Nama tidak boleh kosong!');</script>";
    } elseif (!in_array($status, ['online', 'offline'])) {
        echo "<script>alert('Status tidak valid!');</script>";
    } else {
        $sql = "UPDATE anggota SET nama = ?, status = ? WHERE id = ?";
        $update = $conn->prepare($sql);

        if ($update->execute([$nama, $status, $id])) {
            echo "<script>alert('Data Berhasil Diupdate!'); window.location='list.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal update data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota</title>
    <style>
        body { font-family: sans-serif; background: #0b0e11; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-card { background: #15191d; padding: 30px; border-radius: 12px; width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; background: #0b0e11; border: 1px solid #2c3136; color: white; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0056b3; }
        a { display: block; text-align: center; margin-top: 15px; color: #adb5bd; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="form-card">
        <h3>Edit @<?= htmlspecialchars($user['username']) ?></h3>

        <form method="POST">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
            
            <label>Status</label>
            <select name="status">
                <option value="online" <?= $user['status'] == 'online' ? 'selected' : '' ?>>Online</option>
                <option value="offline" <?= $user['status'] == 'offline' ? 'selected' : '' ?>>Offline</option>
            </select>
            
            <button type="submit" name="update">Simpan Perubahan</button>
            <a href="list.php">Batal</a>
        </form>
    </div>
</body>
</html>