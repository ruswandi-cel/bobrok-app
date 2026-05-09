<?php
include '../../includes/db.php';

// ==========================
// VALIDASI ID
// ==========================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: input-belanja.php");
    exit;
}

$id_edit = (int) $_GET['id'];

// ==========================
// AMBIL DATA LAMA
// ==========================
$st = $conn->prepare("SELECT * FROM belanja WHERE id_belanja = ?");
$st->execute([$id_edit]);
$data = $st->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='input-belanja.php';</script>";
    exit;
}

// ==========================
// PROSES UPDATE
// ==========================
if (isset($_POST['update'])) {

    $ket = trim($_POST['keterangan']);
    $nom = (int) $_POST['nominal'];

    if ($ket == "" || $nom <= 0) {
        echo "<script>alert('Input tidak valid!');</script>";
    } else {

        $upd = $conn->prepare("
            UPDATE belanja 
            SET keterangan = ?, nominal = ? 
            WHERE id_belanja = ?
        ");

        if ($upd->execute([$ket, $nom, $id_edit])) {
            echo "<script>
                alert('Data Berhasil Diperbarui!');
                window.location='input-belanja.php';
            </script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Belanja - BOBROK HUB</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #0b0e11;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* CARD */
.card {
    background: #15191d;
    padding: 30px;
    border-radius: 16px;
    width: 100%;
    max-width: 400px;
    border-top: 5px solid #ffc107;
    box-shadow: 0 10px 30px rgba(0,0,0,0.6);
}

h2 {
    margin-bottom: 20px;
    color: #ffc107;
    text-align: center;
}

/* FORM */
label {
    font-size: 12px;
    color: #adb5bd;
    display: block;
    margin-top: 10px;
}

input {
    width: 100%;
    padding: 14px;
    margin-top: 5px;
    background: #0b0e11;
    border: 1px solid #2c3136;
    color: white;
    border-radius: 8px;
    font-size: 15px;
}

input:focus {
    border-color: #ffc107;
    outline: none;
}

/* BUTTON */
button {
    width: 100%;
    padding: 15px;
    background: #ffc107;
    border: none;
    color: #000;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    margin-top: 25px;
    transition: 0.3s;
}

button:hover {
    background: #ffca2c;
    transform: translateY(-2px);
}

/* BACK */
.back-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #6c757d;
    text-decoration: none;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="card">
    <h2>✏️ Edit Pengeluaran</h2>

    <form method="POST">
        <label>KETERANGAN</label>
        <input 
            type="text" 
            name="keterangan" 
            value="<?= htmlspecialchars($data['keterangan']) ?>" 
            required
        >

        <label>NOMINAL (Rp)</label>
        <input 
            type="number" 
            name="nominal" 
            value="<?= (int)$data['nominal'] ?>" 
            required
        >

        <button type="submit" name="update">
            💾 SIMPAN PERUBAHAN
        </button>

        <a href="input-belanja.php" class="back-link">
            ← Batal & Kembali
        </a>
    </form>
</div>

</body>
</html>