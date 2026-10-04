# Pengaturan

Modul sistem referensi `setting`, 8 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- X-02 Preferensi sistem referensi adalah sumber kebenaran untuk saklar perilaku; setiap saklar WebTransaction yang dipertahankan muncul di layar Preferensi yang sama.
- X-03/X-04 Hak akses sistem referensi: grup akses per menu (lihat/tambah/ubah/hapus/cetak) ditambah hak khusus, pengguna dibatasi ke cabang dan gudang tertentu; enam grup sistem di-seed agar matriks peran WebTransaction tereproduksi sel demi sel.

## Layar

- [Preferensi](#preferensi)
- [Akses Grup](#akses-grup)
- [Pengguna](#pengguna)
- [Penomoran](#penomoran)
- [Desain Cetakan](#desain-cetakan)
- [Penyetuju Transaksi](#penyetuju-transaksi)
- [Add-on store (vendor service)](#add-on-store-vendor-service)
- [Financing program (vendor service)](#financing-program-vendor-service)

## Preferensi

Rute di sistem referensi: `company__preferences` · Jenis: preferensi

### Daftar

**Tombol:** Simpan

### Tab: Perusahaan

| Isian | Jenis | Nilai |
|---|---|---|
| Nama | text |  |
| Telepon | text |  |
| Faksimili | text |  |
| Email | text |  |
| Tgl Mulai Data | date |  |
| Periode Akuntansi | select |  |

### Tab: Fitur

| Isian | Jenis | Nilai |
|---|---|---|
| Multi Cabang (Pelajari lebih lanjut) | checkbox | ✔ aktif |
| Multi Mata Uang | checkbox | ✔ aktif |
| Pajak | checkbox | ✔ aktif |
| Persetujuan (Approval) | checkbox | ✔ aktif |
| Pencatatan Aset | checkbox | ✔ aktif |
| Anggaran dan Target | checkbox | ✔ aktif |
| Departemen | checkbox | ✘ mati |
| Proyek | checkbox | ✘ mati |
| Kategori Keuangan | checkbox | ✘ mati |
| Pinjaman Karyawan | checkbox | ✘ mati |

### Tab: Pajak

| Isian | Jenis | Nilai |
|---|---|---|
| Nama Perusahaan | text |  |
| Tgl Pengukuhan PKP | date |  |
| No Pengukuhan PKP | text |  |
| Tipe Usaha | text |  |
| NPWP Perusahaan | text |  |
| KLU | text |  |
| NITKU | text |  |

### Tab: Penjualan

| Isian | Jenis | Nilai |
|---|---|---|
| Harga Beli/Biaya masuk terakhir | radio | ✔ aktif |
| BPP Faktur Penjualan | radio | ✘ mati |
| Dibebankan ke akun HPP barang | radio | ✔ aktif |
| Dibebankan ke akun | radio | ✘ mati |
| Perbarui biaya barang saat simpan ulang Retur Penjualan | checkbox | ✔ aktif |
| Pelanggan baru selalu termasuk pajak | checkbox | ✔ aktif |

### Tab: Pembelian

| Isian | Jenis | Nilai |
|---|---|---|
| Diperbarui oleh Tagihan | date |  |
| Tidak diperbarui oleh Tagihan | date |  |
| Akun Kas Penampungan Pembayaran Sementara | lookup |  |

### Tab: Pembatasan

| Isian | Jenis | Nilai |
|---|---|---|
| Tidak dibatasi | radio | ✔ aktif |
| Dibatasi semua | radio | ✘ mati |
| Akses terbatas hanya pada waktu | radio | ✘ mati |

### Tab: Lampiran

| Isian | Jenis | Nilai |
|---|---|---|
| Penawaran Penjualan | checkbox | ✘ mati |
| Pesanan Penjualan | checkbox | ✘ mati |
| Pengiriman Pesanan | checkbox | ✘ mati |
| Faktur Penjualan | checkbox | ✘ mati |
| Penerimaan Penjualan | checkbox | ✘ mati |
| Retur Penjualan | checkbox | ✘ mati |
| Tukar Faktur | checkbox | ✘ mati |
| Pelanggan | checkbox | ✘ mati |
| Penyesuaian Harga/Diskon | checkbox | ✘ mati |

### Tab: Atribut Tambahan

| Isian | Jenis | Nilai |
|---|---|---|
| Kolom 1 | text |  |
| Kolom 2 | text |  |
| Kolom 3 | text |  |
| Kolom 4 | text |  |
| Kolom 5 | text |  |
| Kolom 6 | text |  |
| Kolom 7 | text |  |
| Kolom 8 | text |  |
| Kolom 9 | text |  |
| Kolom 10 | text |  |
| Kolom 11 | text |  |
| Kolom 12 | text |  |
| Kolom 13 | text |  |
| Kolom 14 | text |  |
| Kolom 15 | text |  |
| Kolom 1 | text |  |
| Kolom 2 | text |  |
| Kolom 3 | text |  |
| Kolom 4 | text |  |
| Kolom 5 | text |  |
| Kolom 6 | text |  |
| Kolom 7 | text |  |
| Kolom 8 | text |  |
| Kolom 9 | text |  |
| Kolom 10 | text |  |
| Kolom 1 | date |  |
| Kolom 2 | date |  |

### Tab: Akun Perkiraan

| Isian | Jenis | Nilai |
|---|---|---|
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |
| [5101] Beban Pokok Penjualan | lookup |  |
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |
| Cari/Pilih... | lookup |  |

### Tab: Lain-lain

| Isian | Jenis | Nilai |
|---|---|---|
| Format Desimal | select |  |
| Opsi Desimal | select |  |
| Opsi Desimal | select |  |
| Tampilan Tanggal | date |  |
| Rentang Umur | number | 90 |
| Tanggal Faktur | radio | ✔ aktif |
| Jatuh Tempo | radio | ✘ mati |
| Rentang Umur | number | 30 |
| Komisi dihitung dari | select |  |

### Perilaku yang direplikasi

- X-02: tab dan saklar seperti tersetel di database yang dipindai (lihat docs/referensi/preferensi.md) menjadi nilai bawaan August's ERP.
- I-14: stok minus diizinkan/ditolak adalah preferensi, bukan aturan tetap.
- I-10: metode HPP (rata-rata) dan urutan pembiayaan transaksi sehari diambil dari preferensi persediaan.

### Pertanyaan terbuka

- I-10: apakah HPP dirata-rata per barang seluruh perusahaan atau per gudang, dan apakah edisi ini menawarkan FIFO?
- Dalam urutan apa transaksi sehari dibiayai: urutan input, atau masuk sebelum keluar?

## Akses Grup

Rute di sistem referensi: `company__access-privilege` · Jenis: layar

### Daftar

**Kolom:** Nama Grup · Daftar Pengguna

**Tombol:** Salin Hak

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Grup | `name` | text | ya |  |
| Mengikuti Pembatasan di Preferensi | `restrictionType` | radio |  |  |
| Terbatas pada waktu | `restrictionType` | radio |  |  |
| Daftar Pengguna | `userList` | lookup |  |  |

#### Tab: Umum

_(tidak ada isian terbaca)_

#### Tab: Hak Akses

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Hak Akses |  | text |  |  |

### Perilaku yang direplikasi

- X-03: hak per menu lihat/tambah/ubah/hapus/cetak ditambah hak khusus (lihat harga pokok, ubah harga jual, lihat data kredit, buka periode tertutup, dan lainnya yang terbaca di layar ini).

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- PemisahanTugas: pengaju klaim/retur/opname tidak boleh memverifikasinya sendiri; penyetuju kredit bukan penjual yang berkomisi. Tidak ada di sistem referensi; dipertahankan, bawaan nyala, mematikannya tercatat di audit.

### Pertanyaan terbuka

- X-03: hak khusus apa saja yang ada di luar lihat/tambah/ubah/hapus/cetak?

## Pengguna

Rute di sistem referensi: `company__user-company` · Jenis: layar

### Daftar

**Kolom:** Nama · No Handphone · Email · 2FA Auth · Jenis Akses

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| No Handphone/Email | `account` | text | ya |  |
| Operator | `rbOperator` | radio |  |  |
| Administrator | `rbAdministrator` | radio |  |  |
| Akses Grup | `userRoleAccessList` | lookup |  |  |
| Akses Cabang | `userBranchList` | lookup |  |  |

#### Tab: Pengguna

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- X-04: setiap pengguna dibatasi ke cabang dan gudang yang ditetapkan; peran WebTransaction (sales, marketing, inventori, gudang, finance, owner) tinggal sebagai fungsi kerja (kursi komisi, ikatan gudang), bukan matriks akses.

## Penomoran

Rute di sistem referensi: `company__auto-number` · Jenis: layar

### Daftar

**Kolom:** Nama · Tipe Transaksi · Daftar Pengguna

**Saringan:** Tipe Transaksi: Semua

### Formulir baru

**Judul:** Data Baru · Info Penomoran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| Tipe Transaksi | `transactionType` | select |  | Aset Tetap, Barang & Jasa, Bukti Potong PPh Ps.15, Bukti Potong PPh Ps.21, Bukti Potong PPh Ps.4(2), Bukti Potong PPh23, Check-in, Disposisi Aset Tetap, Draf Transaksi, Faktur Pembelian, Faktur Penjualan, Harga Pemasok, Hasil Stok Opname, Jurnal Umum, Karyawan, Klaim Pemasok, Nomor Bukti Kas/Bank, Pelanggan, Pemasok, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Pencatatan Gaji, Penerimaan Barang, Pengiriman Pesanan, Penyesuaian Harga/Diskon, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Perubahan Aset Tetap, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, SPT PPN/PPNBM, Transfer Anggaran, Transfer Bank, Tukar Faktur |
| Tipe Penomoran | `autoNumberType` | select |  | Tidak Reset, Reset setiap hari, Reset setiap bulan, Reset setiap tahun |
| Jumlah Digit Counter |  | number | ya |  |
| Komponen Penomoran | `detailOpt` | select |  | Tahun, Tahun [Singkat], Bulan, Bulan [Romawi], Hari, Counter, Teks Pemisah |

#### Tab: Penomoran

_(tidak ada isian terbaca)_

#### Tab: Daftar Pengguna

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Semua Pengguna | `usedAllUser` | checkbox |  |  |

### Perilaku yang direplikasi

- X-06: format nomor dokumen dapat diatur per jenis dokumen (token, awalan, reset per periode) menggantikan format tetap PREFIX-CABANG-YYYYMM-NNNN.

### Pertanyaan terbuka

- X-06: token dan periode reset apa saja yang ditawarkan Penomoran?

## Desain Cetakan

Rute di sistem referensi: `company__print-layout` · Jenis: layar

### Daftar

**Kolom:** Nama Desain · Tipe Transaksi · Daftar Pengguna

**Saringan:** Tipe Transaksi: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Desain | `name` | text | ya |  |
| Tipe | `transactionType` | select | ya | Silakan Pilih, Anggaran, Daftar Penagihan, Faktur Pembelian, Faktur Penjualan, Hasil Stok Opname, Jurnal Umum, Klaim Pemasok, Pembayaran, Pembayaran Pembelian, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Penerimaan, Penerimaan Barang, Penerimaan Penjualan, Pengiriman Pesanan, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, Slip Gaji, Target Penjualan, Transfer Bank, Tukar Faktur, Uang Muka Pembelian, Uang Muka Penjualan |
| Semua Pengguna | `usedAllUser` | checkbox |  |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- X-07: tata letak cetakan per dokumen dapat didesain pengguna, menggantikan tampilan cetak HTML tetap.

## Penyetuju Transaksi

Rute di sistem referensi: `company__user-approval` · Jenis: layar

### Daftar

**Kolom:** Tipe Transaksi · Nilai · Disetujui Oleh · Pembuat Transaksi · Cabang

**Saringan:** Tipe Transaksi: Semua · Cabang: Semua

### Formulir baru

**Judul:** Data Baru · Kriteria Pengajuan · Kriteria Penyetuju

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tipe Transaksi | `transactionType` | select |  | Hasil Stok Opname, Penyesuaian Harga/Diskon |
| Pembuat Transaksi | `userRequireApprovalList` | lookup |  |  |
| Akses Grup | `userRoleAccessList` | lookup |  |  |
| Pengguna | `userList` | lookup |  |  |
| Dengan Syarat | `approvalUserType` | select |  | Ada Salah Satu Pengguna Setuju, Disetujui Minimal Dua Pengguna, Semua Pengguna Harus Setuju (Berurutan), Semua Pengguna Harus Setuju (Tidak Berurutan) |

#### Tab: Penyetuju Transaksi

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- X-05: aturan persetujuan dikonfigurasi per dokumen dan penyetuju, bukan dikodekan tetap.

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- PersetujuanMarketingWajib: setiap pesanan menunggu persetujuan marketing yang memegang pelanggan. Menjadi aturan persetujuan yang di-seed.

## Add-on store (vendor service)

Rute di sistem referensi: `company__application` · Jenis: layar

### Perilaku yang direplikasi

- Pasar aplikasi tambahan sistem referensi; tidak direplikasi.

## Financing program (vendor service)

Rute di sistem referensi: `company__capital-program` · Jenis: layar

### Perilaku yang direplikasi

- Program pembiayaan sistem referensi; tidak direplikasi.

