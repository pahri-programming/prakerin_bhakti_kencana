@extends('layouts.frontend')

@push('styles')
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --light-blue: #dbeafe;
            --soft-gray: #94a3b8;
            --text-gray: #64748b;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 1.8rem 2.2rem;
            border-radius: 1.6rem;
            color: white;
            margin-bottom: 2rem;
        }

        .profile-sidebar {
            background: white;
            border-radius: 1.6rem;
            padding: 2rem 1.8rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        /* Avatar bulat biru muda + glow halus */
        .avatar-modern {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.6rem;
            font-size: 2.8rem;
            color: #3b82f6;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.22);
        }

        /* Nama besar tebal */
        .user-name-modern {
            font-size: 1.55rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        /* Email kecil abu-abu */
        .user-email-modern {
            font-size: 0.92rem;
            color: #64748b;
            margin-bottom: 1.8rem;
        }

        /* Tag biru muda memanjang — PERSIS GAMBAR */
        .info-tag-modern {
            background: #eff6ff;
            border-radius: 50px;
            padding: 0.95rem 1.4rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            font-size: 0.94rem;
            color: #3b82f6;
            box-shadow: 0 4px 18px rgba(59, 130, 246, 0.12);
            transition: all 0.3s ease;
        }

        .info-tag-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.18);
        }

        .info-tag-modern i {
            font-size: 1.25rem;
            color: #3b82f6;
            width: 36px;
            flex-shrink: 0;
        }

        .tag-label {
            font-size: 0.84rem;
            color: #64748b;
            font-weight: 500;
        }

        .tag-value {
            font-size: 1.02rem;
            font-weight: 700;
            color: #1e40af;
        }

        .stat-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.3rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.6rem;
            border-radius: 1.6rem;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.7rem;
            color: var(--primary);
        }

        .stat-number {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0.4rem 0;
        }

        .stat-main-text {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.3rem;
        }

        .stat-sub-text {
            font-size: 0.84rem;
            color: #64748b;
        }

        .activity-summary {
            background: white;
            border-radius: 1.6rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .activity-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.1rem 1.8rem;
            font-weight: 600;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            padding: 2rem 1.5rem;
            text-align: center;
            gap: 1.2rem;
        }

        .activity-item .icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.45rem;
            color: var(--primary);
        }

        .activity-item h2 {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0.4rem 0;
        }

        .activity-item small {
            font-size: 0.88rem;
            color: var(--text-gray);
        }

        /* KHUSUS UNTUK PERAN — SUPAYA TIDAK GEDE */
        .activity-item .role-text {
            font-size: 1.65rem;
            /* Lebih kecil dari angka lain */
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0.4rem 0;
        }

        @media (max-width: 768px) {

            .stat-cards,
            .activity-grid {
                grid-template-columns: 1fr;
            }

            .activity-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-md-5">
        <!-- Header -->
        <div class="profile-header">
            <h4 class="fw-bold mb-1">Profile Saya</h4>
            <p class="mb-0 opacity-90">Kelola informasi dan aktivitas akun Anda</p>
        </div>

        <div class="row g-5">

            <div class="col-lg-4">
                <div class="profile-sidebar">
                    <div class="avatar-modern">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <h5 class="user-name-modern">{{ $user->name }}</h5>
                    <p class="user-email-modern">{{ $user->email }}</p>

                    <div class="info-tag-modern">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <span class="tag-label">Bergabung Sejak</span><br>
                            <strong class="tag-value">{{ $user->created_at->translatedFormat('d F Y') }}</strong>
                        </div>
                    </div>


                    <div class="info-tag-modern">
                        <i class="bi bi-person-badge"></i>
                        <div>
                            <span class="tag-label">Role</span><br>
                            <strong class="tag-value text-uppercase">{{ $user->role ?? 'User' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-number">{{ $totalPinjamBarang }}</div>
                        <div class="stat-main-text">Peminjaman Barang</div>
                        <div class="stat-sub-text text-success">{{ $totalKembali }} Dikembalikan</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="stat-number">{{ $totalPinjamRuangan }}</div>
                        <div class="stat-main-text">Peminjaman Ruangan</div>
                        <div class="stat-sub-text text-primary">Semua tercatat</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-number">{{ $belumKembali }}</div>
                        <div class="stat-main-text">Belum Dikembalikan</div>
                        <div class="stat-sub-text text-warning">Masih dipinjam</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <div class="stat-number">{{ $ruanganSedangDigunakan }}</div>
                        <div class="stat-main-text">Ruangan Digunakan</div>
                        <div class="stat-sub-text {{ $ruanganSedangDigunakan > 0 ? 'text-success' : 'text-muted' }}">
                            {{ $ruanganSedangDigunakan > 0 ? 'Sedang aktif hari ini' : 'Tidak ada hari ini' }}
                        </div>
                    </div>
                </div>

                <div class="activity-summary">
                    <div class="activity-header">
                        Ringkasan Aktivitas
                    </div>
                    <div class="activity-grid">
                        <div class="activity-item">
                            <div class="icon"><i class="bi bi-clipboard-check"></i></div>
                            <h2>{{ $totalPeminjaman }}</h2>
                            <small>Total Peminjaman</small>
                        </div>
                        <div class="activity-item">
                            <div class="icon"><i class="bi bi-check-circle"></i></div>
                            <h2>{{ $totalKembali }}</h2>
                            <small>Total Dikembalikan</small>
                        </div>
                        <div class="activity-item">
                            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                            <h2>{{ $belumKembali }}</h2>
                            <small>Belum Kembali</small>
                        </div>
                        <div class="activity-item">
                            <div class="icon"><i class="bi bi-person-circle"></i></div>
                            <h2 class="role-text">{{ strtoupper($user->role ?? 'user') }}</h2>
                            <small>Peran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
