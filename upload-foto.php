<?php
include '../../includes/auth.php';
cekLogin();

include '../../includes/db.php';

if (isset($_POST['upload'])) {

    $judul = trim($_POST['judul']);
    $tgl_trip = $_POST['tanggal_trip'];

    // VALIDASI INPUT
    if ($judul == '') {
        echo "<script>alert('Judul tidak boleh kosong!');</script>";
    } else {

        // DATA FILE
        $file = $_FILES['foto'];
        $error = $file['error'];

        if ($error === 0) {

            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $max_size = 2 * 1024 * 1024; // 2MB

            $file_name = $file['name'];
            $tmp_name = $file['tmp_name'];
            $file_size = $file['size'];

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // VALIDASI EXTENSION
            if (!in_array($ext, $allowed_ext)) {
                echo "<script>alert('Format file tidak didukung!');</script>";
                exit;
            }

            // VALIDASI SIZE
            if ($file_size > $max_size) {
                echo "<script>alert('Ukuran file maksimal 2MB!');</script>";
                exit;
            }

            // VALIDASI MIME TYPE (ANTI FILE BERBAHAYA)
            $mime = mime_content_type($tmp_name);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                echo "<script>alert('File tidak valid!');</script>";
                exit;
            }

            // GENERATE NAMA AMAN
            $newName = "BOBROK_" . time() . "_" . rand(100,999) . "." . $ext;
            $dest = "../../assets/uploads/galeri/" . $newName;

            // UPLOAD
            if (move_uploaded_file($tmp_name, $dest)) {

                $stmt = $conn->prepare("INSERT INTO galeri (judul, foto, tanggal_trip) VALUES (?, ?, ?)");

                if ($stmt->execute([$judul, $newName, $tgl_trip])) {
                    echo "<script>alert('Foto berhasil disimpan!'); window.location='../index.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal simpan ke database!');</script>";
                }

            } else {
                echo "<script>alert('Gagal upload file!');</script>";
            }

        } else {
            echo "<script>alert('Terjadi error saat upload!');</script>";
        }
    }
}
?>