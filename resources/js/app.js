import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const notifButton = document.querySelector('#notif-read-all');

    if (notifButton) {
        notifButton.addEventListener('click', () => {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Hapus semua notif dari tampilan
                        document.querySelectorAll('.notif-item').forEach(el => el.remove());
                    }
                });
        });
    }
});


// window.Echo.channel('booking-channel')
//     .listen('.booking.updated', (data) => {
//         console.log('📢 Booking event diterima:', data);
//         alert(`Status booking ${data.booking.id} berubah jadi ${data.booking.status}`);
//     });

// window.Echo.channel('peminjaman-channel')
//     .listen('.peminjaman.updated', (data) => {
//         console.log('📦 Peminjaman event diterima:', data);
//         alert(`Status peminjaman barang "${data.peminjaman.barang.nama_barang}" kini ${data.peminjaman.status}`);
//     });
