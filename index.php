<?php 
include '../includes/db.php'; 
// Di sini tidak pakai auth.php karena anggota tidak perlu login admin
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOBROK HUB - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .hero-section { padding: 30px 20px; text-align: center; background: linear-gradient(180deg, #15191d 0%, #0b0e11 100%); }
        .stat-card { border-left: 4px solid #007bff; }
        .quick-chat-box { background: #15191d; border-radius: 12px; padding: 15px; margin-top: 20px; max-height: 150px; overflow-y: auto; font-size: 13px; }
        .pesan-item { margin-bottom: 8px; border-bottom: 1px solid #2c3136; padding-bottom: 5px; }
        
        /* Style Tambahan untuk List Belanja agar rapi */
        #list-belanja-container { margin-top: 12px; border-top: 1px solid #2c3136; padding-top: 10px; }
        .item-belanja { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; color: #adb5bd; }
    </style>
</head>
<body>

    <div class="hero-section">
        <img src="../assets/img/logo.png" width="80" alt="Bobrok Logo">
        <h2 style="margin: 10px 0 5px 0;">BOBROK <span style="color:#007bff;">TEAM</span></h2>
        <p style="color:#6c757d; font-size: 12px;">EST. 2017 - Safety & Brotherhood</p>
    </div>

    <div style="padding: 0 20px;">
        <div class="card stat-card" id="budget-info">
            <small style="color:#adb5bd;">SISA BUDGET TRIP</small>
            <div style="font-size: 28px; font-weight: bold; margin: 5px 0;" id="display-sisa">Rp 0</div>
            <div class="progress-container">
                <div class="progress-bar" id="bar-sisa" style="width: 0%"></div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px;">
                <span id="display-nama-trip">Memuat...</span>
                <span id="display-persen">0%</span>
            </div>

            <div id="list-belanja-container">
                </div>
        </div>

        <div class="menu-grid">
            <a href="tracking.php" class="menu-item btn" style="background:#15191d; border: 1px solid #2c3136; padding: 25px 10px;">
                <div style="font-size: 24px;">📍</div>
                <div style="font-size: 14px; margin-top: 5px;">Radar Live</div>
            </a>
            <a href="kas.php" class="menu-item btn" style="background:#15191d; border: 1px solid #2c3136; padding: 25px 10px;">
                <div style="font-size: 24px;">💰</div>
                <div style="font-size: 14px; margin-top: 5px;">Kas Besar</div>
            </a>
            <a href="galeri.php" class="menu-item btn" style="background:#15191d; border: 1px solid #2c3136; padding: 25px 10px;">
                <div style="font-size: 24px;">🎞️</div>
                <div style="font-size: 14px; margin-top: 5px;">Galeri</div>
            </a>
            <a href="event.php" class="menu-item btn" style="background:#15191d; border: 1px solid #2c3136; padding: 25px 10px;">
                <div style="font-size: 24px;">📅</div>
                <div style="font-size: 14px; margin-top: 5px;">Info Trip</div>
            </a>
        </div>

        <div class="quick-chat-box" id="chat-feed">
            <p style="color:#495057; text-align:center;">Menghubungkan ke sinyal radio...</p>
        </div>
    </div>

    <script>
        // Update Budget & List Belanja dari API
        fetch('../api/kas/get-budget.php')
            .then(res => res.json())
            .then(data => {
                if(!data.error) {
                    document.getElementById('display-sisa').innerText = 'Rp ' + data.sisa.toLocaleString();
                    document.getElementById('display-nama-trip').innerText = data.nama_trip;
                    document.getElementById('display-persen').innerText = (100 - data.persen_terpakai) + '% Sisa';
                    document.getElementById('bar-sisa').style.width = (100 - data.persen_terpakai) + '%';

                    // Update List Belanja (Item & Nominal)
                    let belanjaHtml = '';
                    if(data.riwayat && data.riwayat.length > 0) {
                        data.riwayat.forEach(item => {
                            belanjaHtml += `
                            <div class="item-belanja">
                                <span>🛒 ${item.keterangan}</span>
                                <span style="color: #ff4757; font-weight: bold;">- Rp ${parseInt(item.nominal).toLocaleString()}</span>
                            </div>`;
                        });
                    } else {
                        belanjaHtml = '<small style="color: #495057; font-size: 10px;">Belum ada pengeluaran</small>';
                    }
                    document.getElementById('list-belanja-container').innerHTML = belanjaHtml;
                }
            });

        // Update Chat dari API
        function loadChat() {
            fetch('../api/chat/get-chat.php')
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(c => {
                        html += `<div class="pesan-item"><strong>${c.nama}:</strong> ${c.pesan}</div>`;
                    });
                    document.getElementById('chat-feed').innerHTML = html;
                });
        }
        setInterval(loadChat, 5000);
        loadChat();
    </script>
</body>
</html>