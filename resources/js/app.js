import './bootstrap';

console.log('Pusher Channels siap!');

    // TEST: Dengar event
    window.Echo.channel('peminjaman')
    .listen('.test', (e) => {
        console.log('TEST EVENT DITERIMA:', e);
    });

    // REAL: Dengar status selesai
    window.Echo.channel('peminjaman')
    .listen('PeminjamanExpired', (e) => {
        console.log('PEMINJAMAN SELESAI:', e);

        // Update badge status
        const row = document.querySelector(`[data-peminjaman-id="${e.id}"]`);
        if (row) {
            const badge = row.querySelector('.status-badge');
            if (badge) {
                badge.innerHTML = '<span class="badge bg-success">Selesai</span>';
            }
        }

        // Toast
        Toastify({
            text: `Peminjaman ${e.barang} selesai! Stok +${e.jumlah}`,
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            duration: 5000
        }).showToast();
    });

    window.Echo.channel('booking')
    .listen('BookingExpired', (e) => {
        console.log('BOOKING SELESAI:', e);

        const row = document.querySelector(`[data-booking-id="${e.id}"]`);
        if (row) {
            const badge = row.querySelector('.badge');
            badge.className = 'badge bg-success px-3 py-2';
            badge.textContent = 'Selesai';
        }

        Toastify({
            text: `Booking ${e.booking.ruang_nama} selesai!`,
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            duration: 5000
        }).showToast();
    });