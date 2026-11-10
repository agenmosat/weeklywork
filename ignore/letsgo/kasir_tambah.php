<?php
$judul = 'Contoh Form Kasir';
$halaman = 'kasir';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <span class="badge text-bg-warning mb-2">Contoh Form</span>
    <h1 class="h3 mb-1">Form Tambah Kasir</h1>
    <p class="text-secondary mb-0">
      Ini contoh tampilan form. Proses simpan belum disediakan.
    </p>
  </div>
  <a class="btn btn-outline-secondary" href="kasir.php">Kembali ke Daftar Kasir</a>
</div>

<div class="alert alert-info">
  <strong>Tugasmu:</strong> tambahkan tombol simpan, validasi, dan proses
  <code>INSERT</code> ke tabel <code>kasir</code>. Contoh lengkap ada di
  <code>produk_tambah.php</code>.
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="kasir_tambah.php">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="nama_kasir">Nama Kasir</label>
          <input class="form-control" type="text" id="nama_kasir" name="nama_kasir"
                 maxlength="50" placeholder="Contoh: Rina" required>
          <div class="form-text">Wajib diisi, maksimal 50 karakter.</div>
        </div>

        <div class="col-md-3">
          <label class="form-label" for="status">Status</label>
          <select class="form-select" id="status" name="status">
            <option value="aktif" selected>Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label" for="tanggal_keluar">Tanggal Keluar</label>
          <input class="form-control" type="date" id="tanggal_keluar" name="tanggal_keluar">
          <div class="form-text">Kosongkan jika masih aktif.</div>
        </div>
      </div>

      <div class="alert alert-secondary mt-4 mb-0">
        Tombol <strong>Simpan</strong> belum dibuat. Siswa melengkapi bagian ini
        sesuai brief di <a href="panduan.php#tugas-kasir">panduan kasir</a>.
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
