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

    .avatar-modern {
        width: 96px; height: 96px; border-radius: 50%;
        background: #dbeafe; display: flex; align-items: center;
        justify-content: center; margin: 0 auto 1.6rem;
        font-size: 2.8rem; color: #3b82f6;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.22);
    }

    .user-name-modern { font-size: 1.55rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem; }
    .user-email-modern { font-size: 0.92rem; color: #64748b; margin-bottom: 1.8rem; }

    .info-tag-modern {
        background: #eff6ff; border-radius: 50px;
        padding: 0.95rem 1.4rem; margin-bottom: 1rem;
        display: flex; align-items: center; justify-content: flex-start;
        gap: 1rem; font-size: 0.94rem; color: #3b82f6;
        box-shadow: 0 4px 18px rgba(59, 130, 246, 0.12);
        transition: all 0.3s ease;
    }
    .info-tag-modern:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(59, 130, 246, 0.18); }
    .info-tag-modern i { font-size: 1.25rem; color: #3b82f6; width: 36px; flex-shrink: 0; }
    .tag-label { font-size: 0.84rem; color: #64748b; font-weight: 500; }
    .tag-value { font-size: 1.02rem; font-weight: 700; color: #1e40af; }

    .stat-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.3rem; margin-bottom: 2rem; }
    .stat-card {
        background: white; padding: 1.6rem; border-radius: 1.6rem;
        box-shadow: var(--shadow); text-align: center; transition: all 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); }
    .stat-icon {
        width: 56px; height: 56px; border-radius: 18px; background: var(--light-blue);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 1.7rem; color: var(--primary);
    }
    .stat-number { font-size: 2.1rem; font-weight: 800; color: var(--primary); margin: 0.4rem 0; }
    .stat-main-text { font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 0.3rem; }
    .stat-sub-text { font-size: 0.84rem; color: #64748b; }

    .activity-summary { background: white; border-radius: 1.6rem; overflow: hidden; box-shadow: var(--shadow); }
    .activity-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white; padding: 1.1rem 1.8rem; font-weight: 600;
        font-size: 1.08rem; display: flex; align-items: center; gap: 0.7rem;
    }
    .activity-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        padding: 2rem 1.5rem; text-align: center; gap: 1.2rem;
    }
    .activity-item .icon {
        width: 48px; height: 48px; border-radius: 50%; background: var(--light-blue);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 1.45rem; color: var(--primary);
    }
    .activity-item h2 { font-size: 2.1rem; font-weight: 800; color: var(--primary); margin: 0.4rem 0; }
    .activity-item small { font-size: 0.88rem; color: var(--text-gray); }
    .activity-item .role-text {
        font-size: 1.65rem; font-weight: 700; color: var(--primary);
        text-transform: uppercase; letter-spacing: 1px; margin: 0.4rem 0;
    }

    .role-badge-admin { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .role-badge-user  { background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; }

    @media (max-width: 768px) {
        .stat-cards { grid-template-columns: 1fr; }
        .activity-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-md-5">

    {{-- Header --}}
    <div class="profile-header">
        <h4 class="fw-bold mb-1">Profile Saya</h4>
        <p class="mb-0 opacity-90">Kelola informasi dan aktivitas akun Anda</p>
    </div>

    {{-- Alert success di luar modal (fallback) --}}
    @if(session('success') && !$errors->any())
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-5">

        {{-- ── Sidebar ── --}}
        <div class="col-lg-4">
            <div class="profile-sidebar">
                <div class="avatar-modern">
                    <i class="bi bi-person-fill"></i>
                </div>

                <h5 class="user-name-modern">{{ $user->name }}</h5>
                <p class="user-email-modern">{{ $user->email }}</p>

                {{-- Instansi --}}
                <div class="info-tag-modern">
                    <i class="bi bi-building"></i>
                    <div class="text-start">
                        <span class="tag-label">Instansi</span><br>
                        <strong class="tag-value">
                            {{ $user->instansi ?? 'Belum diisi' }}
                        </strong>
                    </div>
                </div>

                {{-- Bergabung Sejak --}}
                <div class="info-tag-modern">
                    <i class="bi bi-calendar-check"></i>
                    <div class="text-start">
                        <span class="tag-label">Bergabung Sejak</span><br>
                        <strong class="tag-value">{{ $user->created_at->translatedFormat('d F Y') }}</strong>
                    </div>
                </div>

                {{-- Role --}}
                <div class="info-tag-modern {{ $user->role === 'admin' ? 'role-badge-admin' : 'role-badge-user' }}">
                    <i class="bi bi-person-badge"></i>
                    <div class="text-start">
                        <span class="tag-label" style="color: rgba(255,255,255,0.9);">Role</span><br>
                        <strong class="tag-value" style="color: white;">{{ $roleDisplay }}</strong>
                    </div>
                </div>

                {{-- Tombol Edit --}}
                <button type="button" class="btn btn-primary w-100 mt-3 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalEditProfile">
                    <i class="bi bi-pencil-square me-2"></i>Edit Profil
                </button>
            </div>
        </div>

        {{-- ── Konten Kanan ── --}}
        <div class="col-lg-8">
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                    <div class="stat-number">{{ $totalPinjamBarang }}</div>
                    <div class="stat-main-text">Peminjaman Barang</div>
                    <div class="stat-sub-text text-success">{{ $totalKembali }} Dikembalikan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-number">{{ $totalPinjamRuangan }}</div>
                    <div class="stat-main-text">Peminjaman Ruangan</div>
                    <div class="stat-sub-text text-primary">Semua tercatat</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-number">{{ $belumKembali }}</div>
                    <div class="stat-main-text">Belum Dikembalikan</div>
                    <div class="stat-sub-text text-warning">Masih dipinjam</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-house-door"></i></div>
                    <div class="stat-number">{{ $ruanganSedangDigunakan }}</div>
                    <div class="stat-main-text">Ruangan Digunakan</div>
                    <div class="stat-sub-text {{ $ruanganSedangDigunakan > 0 ? 'text-success' : 'text-muted' }}">
                        {{ $ruanganSedangDigunakan > 0 ? 'Sedang aktif hari ini' : 'Tidak ada hari ini' }}
                    </div>
                </div>
            </div>

            <div class="activity-summary">
                <div class="activity-header">
                    <i class="bi bi-graph-up"></i> Ringkasan Aktivitas
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
                        <h2 class="role-text">{{ strtoupper($roleDisplay) }}</h2>
                        <small>Peran</small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Modal Edit Profile ── --}}
<div class="modal fade" id="modalEditProfile" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">

            <div class="modal-header border-0 pb-0"
                 style="background: linear-gradient(135deg, #3b82f6, #1e40af); border-radius: 1rem 1rem 0 0;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-person-gear me-2"></i>Edit Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success rounded-3 mb-3">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}"
                               placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email (read only) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;">Email</label>
                        <input type="email" class="form-control rounded-3 bg-light"
                               value="{{ $user->email }}" disabled>
                        <small class="text-muted">Email tidak dapat diubah.</small>
                    </div>

                    {{-- Instansi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.875rem;">
                            Instansi / Fakultas
                        </label>
                        <input type="text" name="instansi"
                               class="form-control rounded-3 @error('instansi') is-invalid @enderror"
                               value="{{ old('instansi', $user->instansi) }}"
                               placeholder="Contoh: Fakultas Teknik Informatika">
                        @error('instansi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-google me-1"></i>
                            Wajib diisi jika login via Google dan instansi belum tersedia.
                        </small>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any() || session('success'))
        var modal = new bootstrap.Modal(document.getElementById('modalEditProfile'));
        modal.show();
    @endif
});
</script>
@endpush