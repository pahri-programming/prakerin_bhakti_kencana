import './bootstrap';

window.Echo.channel('booking-channel')
    .listen('.booking.updated', (data) => {
        console.log('📢 Booking event diterima:', data);
        alert(`Status booking ${data.booking.id} berubah jadi ${data.booking.status}`);
    });

window.Echo.channel('peminjaman-channel')
    .listen('.peminjaman.updated', (data) => {
        console.log('📦 Peminjaman event diterima:', data);
        alert(`Status peminjaman barang "${data.peminjaman.barang.nama_barang}" kini ${data.peminjaman.status}`);
    });
