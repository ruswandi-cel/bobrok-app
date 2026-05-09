<?php 
include '../includes/db.php'; 

// Ambil trip aktif
$query = $conn->query("SELECT * FROM trip WHERE status='aktif' LIMIT 1");
$trip = $query->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tracking Live - BOBROK HUB</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
:root {
    --bg:#0b0e11;
    --panel:#15191d;
    --accent:#00aaff;
    --success:#00ff88;
    --danger:#ff4757;
}

body { margin:0; font-family:sans-serif; background:var(--bg); color:white; }

.container { display:flex; height:100vh; }

/* SIDEBAR */
.sidebar {
    width:260px;
    background:var(--panel);
    border-right:1px solid #222;
    overflow-y:auto;
}

.sidebar h3 {
    padding:15px;
    margin:0;
    border-bottom:1px solid #222;
}

.member {
    padding:10px 15px;
    border-bottom:1px solid #1f1f1f;
    display:flex;
    gap:10px;
}

.badge {
    width:25px;
    height:25px;
    border-radius:50%;
    background:var(--accent);
    text-align:center;
    line-height:25px;
    font-size:12px;
    font-weight:bold;
}

.online { color:var(--success); font-size:11px; }
.offline { color:#777; font-size:11px; }

/* MAP */
.map-container { flex:1; position:relative; }
#map { height:100%; }

/* TOP */
.topbar {
    position:absolute;
    top:10px;
    left:50%;
    transform:translateX(-50%);
    background:rgba(0,0,0,0.7);
    padding:10px 20px;
    border-radius:10px;
    z-index:999;
    text-align:center;
}

/* INFO */
.info-box{
    position:absolute;
    bottom:110px;
    left:20px;
    background:rgba(0,0,0,0.7);
    padding:12px;
    border-radius:10px;
    font-size:12px;
}

/* RADAR */
.radar-btn {
    position:absolute;
    bottom:20px;
    left:50%;
    transform:translateX(-50%);
    padding:15px 20px;
    border:none;
    border-radius:10px;
    background:var(--accent);
    color:white;
    font-weight:bold;
    cursor:pointer;
}

.active { background:var(--success) !important; }

.marker {
    border-radius:50%;
    border:2px solid white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
}

/* CHAT */
.chat-panel {
    width:280px;
    background:var(--panel);
    border-left:1px solid #222;
    display:flex;
    flex-direction:column;
}

.chat-panel h3 {
    padding:15px;
    margin:0;
    border-bottom:1px solid #222;
    flex-shrink:0;
}

.chat-messages {
    flex:1;
    overflow-y:auto;
    padding:10px;
    display:flex;
    flex-direction:column;
    gap:8px;
}

.chat-bubble {
    background:#1f2428;
    border-radius:8px;
    padding:8px 10px;
    font-size:12px;
    word-break:break-word;
}

.chat-bubble .sender {
    color:var(--accent);
    font-weight:bold;
    margin-bottom:3px;
}

.chat-bubble .time {
    color:#555;
    font-size:10px;
    margin-top:3px;
}

.chat-input {
    display:flex;
    border-top:1px solid #222;
    flex-shrink:0;
}

.chat-input input {
    flex:1;
    background:#1f2428;
    border:none;
    padding:10px;
    color:white;
    font-size:13px;
    outline:none;
}

.chat-input button {
    background:var(--accent);
    border:none;
    padding:10px 14px;
    color:white;
    cursor:pointer;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>ANGGOTA (LIVE)</h3>
    <div id="member-list"></div>
</div>

<!-- MAP -->
<div class="map-container">

    <div class="topbar">
        <?php if($trip): ?>
            <?= $trip['titik_kumpul'] ?> → <?= $trip['nama_trip'] ?><br>
            <small style="color:red;">Trip Aktif</small>
        <?php else: ?>
            Tidak ada trip aktif
        <?php endif; ?>
    </div>

    <div id="map"></div>

    <div class="info-box">
        🚗 Tracking aktif<br>
        🛰️ Real-time posisi anggota
    </div>

    <button id="radarBtn" class="radar-btn" onclick="toggleRadar()">
        🚀 AKTIFKAN RADAR
    </button>

</div>

<!-- CHAT PANEL -->
<div class="chat-panel">
    <h3>💬 CHAT TRIP</h3>
    <div class="chat-messages" id="chat-messages"></div>
    <div class="chat-input">
        <input type="text" id="chat-input" placeholder="Ketik pesan..." onkeydown="if(event.key==='Enter') kirimPesan()">
        <button onclick="kirimPesan()">➤</button>
    </div>
</div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let markers = {};
let routeLine;

// INIT MAP
var map = L.map('map').setView([-6.2,106.8], 11);

L.tileLayer('https://{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', {
    maxZoom:20,
    subdomains:['mt0','mt1','mt2','mt3']
}).addTo(map);

// DARK MODE MAP
document.querySelector('.leaflet-tile-pane').style.filter =
"invert(100%) hue-rotate(180deg)";

// UPDATE DATA
function updateRadar(){
    fetch('../api/tracking/get-semua-lokasi.php')
    .then(res=>res.json())
    .then(data=>{

        data.sort((a,b)=>a.no_urut - b.no_urut);

        let html='';
        let coords=[];

        data.forEach((user,i)=>{

            // STATUS ONLINE
            let online = "offline";
            if(user.terakhir_online){
                let diff = (new Date() - new Date(user.terakhir_online))/1000;
                if(diff < 15) online = "online";
            }

            let jarak = (i * 0.5).toFixed(1); // dummy

            html += `
            <div class="member">
                <div class="badge">${user.no_urut}</div>
                <div>
                    <b>${user.nama}</b><br>
                    <span>0 km/jam • ${jarak} km</span><br>
                    <span class="${online}">● ${online}</span>
                </div>
            </div>`;

            if(user.latitude && user.longitude){

                let pos = [parseFloat(user.latitude), parseFloat(user.longitude)];
                coords.push(pos);

                let isLeader = (i === 0);

                let icon = L.divIcon({
                    html: `
                    <div style="
                        width:${isLeader ? '40px':'30px'};
                        height:${isLeader ? '40px':'30px'};
                        background:${isLeader ? '#00ff88':'#00aaff'};
                    " class="marker">
                        ${user.no_urut}
                    </div>`
                });

                if(markers[user.id]){
                    markers[user.id].setLatLng(pos);
                } else {
                    markers[user.id] = L.marker(pos,{icon})
                        .addTo(map)
                        .bindPopup(user.nama);
                }
            }

        });

        // ROUTE LINE
        if(coords.length > 1){
            if(routeLine){
                routeLine.setLatLngs(coords);
            } else {
                routeLine = L.polyline(coords,{
                    color:'#00ff88',
                    weight:5
                }).addTo(map);
            }
        }

        // CENTER KE LEADER
        if(coords.length){
            map.setView(coords[0], 12);
        }

        document.getElementById('member-list').innerHTML = html;

    });
}

// RADAR SYSTEM
let radarInterval = null;

function toggleRadar(){
    let btn = document.getElementById('radarBtn');

    if(!radarInterval){

        // PERMISSION FIX
        navigator.geolocation.getCurrentPosition(()=>{},()=>{
            alert("GPS tidak diizinkan!");
        });

        btn.innerHTML = "🛰️ RADAR AKTIF";
        btn.classList.add('active');

        radarInterval = setInterval(()=>{
            navigator.geolocation.getCurrentPosition(pos=>{

                let fd = new FormData();
                fd.append('id_anggota',1);
                fd.append('lat',pos.coords.latitude);
                fd.append('lng',pos.coords.longitude);

                fetch('../api/tracking/update-lokasi.php',{
                    method:'POST',
                    body:fd
                });

            });
        },5000);

    } else {

        clearInterval(radarInterval);
        radarInterval = null;

        btn.innerHTML = "🚀 AKTIFKAN RADAR";
        btn.classList.remove('active');
    }
}

// CHAT
let lastChatId = 0;

function loadChat(){
    fetch('../api/chat/get-chat.php')
    .then(res=>res.json())
    .then(data=>{
        let box = document.getElementById('chat-messages');
        let atBottom = box.scrollHeight - box.scrollTop <= box.clientHeight + 20;
        box.innerHTML = data.map(c=>{
            let t = new Date(c.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
            return `<div class="chat-bubble">
                <div class="sender">${c.nama}</div>
                <div>${c.pesan}</div>
                <div class="time">${t}</div>
            </div>`;
        }).join('');
        if(atBottom) box.scrollTop = box.scrollHeight;
    });
}

function kirimPesan(){
    let input = document.getElementById('chat-input');
    let pesan = input.value.trim();
    if(!pesan) return;

    let fd = new FormData();
    fd.append('id_anggota', 1);
    fd.append('pesan', pesan);

    fetch('../api/chat/kirim-pesan.php',{method:'POST',body:fd})
    .then(()=>{ input.value=''; loadChat(); });
}

setInterval(loadChat, 5000);
loadChat();

// AUTO REFRESH
setInterval(updateRadar,5000);
updateRadar();

</script>

</body>
</html>