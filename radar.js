// Simpan marker dalam objek agar bisa diupdate (tidak numpuk)
let markers = {};

function updateRadar() {
  fetch("../api/tracking/get-semua-lokasi.php")
    .then((response) => response.json())
    .then((data) => {
      data.forEach((user) => {
        const lat = parseFloat(user.latitude);
        const lng = parseFloat(user.longitude);
        const pos = [lat, lng];

        if (markers[user.id]) {
          // Jika marker sudah ada, geser posisinya saja (berjalan)
          markers[user.id].setLatLng(pos);
        } else {
          // Jika anggota baru muncul, buatkan marker baru
          const customIcon = L.divIcon({
            className: "custom-div-icon",
            html: `<div style="background-color:#007bff; color:white; border-radius:50%; width:25px; height:25px; display:flex; align-items:center; justify-content:center; border:2px solid white; font-weight:bold; font-size:10px;">${user.no_urut}</div>`,
            iconSize: [25, 25],
            iconAnchor: [12, 12],
          });

          markers[user.id] = L.marker(pos, { icon: customIcon })
            .addTo(map)
            .bindPopup(`<b>${user.nama}</b>`);
        }
      });
    })
    .catch((err) => console.error("Gagal ambil lokasi:", err));
}

// Ubah jadi 5-10 detik saja biar pergerakannya lebih terasa "Live"
setInterval(updateRadar, 5000);
