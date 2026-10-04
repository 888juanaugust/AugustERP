# Aset Tetap

## Aset Tetap

Rute: `#referensi__fixed-asset__fixed-asset`

**Judul layar:** Data Baru · Akun Pengeluaran

**Kolom daftar:** # · Nomor # · Nama Aset · Tanggal Beli · Kuantitas · Total Aset

**Saringan:** Kategori Aset: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama | text | ya |  |
| Tanggal Beli | date | ya |  |
| Tanggal Pakai | date | ya |  |
| Kode Aset | checkbox | ya |  |
| Kode Aset | select | ya | Aset Tetap |
| Akun Pengeluaran | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tanggal Beli | date | ya |  |
| Tanggal Pakai | date | ya |  |
| Kode Aset | checkbox | ya |  |
| Kode Aset | select | ya | Aset Tetap |
| Ya | checkbox |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Akun Aset | lookup | ya |  |
| Akun Akumulasi Penyusutan | lookup | ya |  |
| Akun Beban Penyusutan | lookup | ya |  |
| Kuantitas | number | ya |  |
| Umur Aset | number | ya |  |
| Bulan | number |  |  |
| Nilai Sisa | number |  |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tanggal Beli | date | ya |  |
| Tanggal Pakai | date | ya |  |
| Kode Aset | checkbox | ya |  |
| Kode Aset | select | ya | Aset Tetap |
| Ya | checkbox |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Akun Aset | lookup | ya |  |
| Akun Akumulasi Penyusutan | lookup | ya |  |
| Akun Beban Penyusutan | lookup | ya |  |
| Kuantitas | number | ya |  |
| Umur Aset | number | ya |  |
| Bulan | number |  |  |
| Nilai Sisa | number |  |  |

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tanggal Beli | date | ya |  |
| Tanggal Pakai | date | ya |  |
| Kode Aset | checkbox | ya |  |
| Kode Aset | select | ya | Aset Tetap |
| Kategori Aset | lookup | ya |  |
| Lokasi Awal Aset | lookup |  |  |
| Catatan | textarea |  |  |
| Ya | checkbox |  |  |

#### Tab: Akun Pengeluaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tanggal Beli | date | ya |  |
| Tanggal Pakai | date | ya |  |
| Kode Aset | checkbox | ya |  |
| Kode Aset | select | ya | Aset Tetap |
| Akun Pengeluaran | lookup | ya |  |

**Kolom rincian:** Kode # · Deskripsi · Tanggal · Jumlah

## Kategori Aset

Rute: `#referensi__fixed-asset__fa-type`

**Judul layar:** Data Baru · Info Umum

**Kolom daftar:** Nama

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama | text | ya |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |

## Kategori Aset Tetap Pajak

Rute: `#referensi__fixed-asset__fiscal-fa-type`

**Judul layar:** Data Baru · Info Umum

**Kolom daftar:** Tarif Penyusutan (%) · Nama · Perkiraan Umur (tahun) · Metode Penyusutan

**Saringan:** Metode Penyusutan: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama | text |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Perkiraan Umur | number |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Perkiraan Umur | number |  |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Perkiraan Umur | number |  |  |

## Perubahan Aset Tetap

Rute: `#referensi__fixed-asset__fixed-asset-edited`

**Judul layar:** Data Baru · Info Lainnya

**Kolom daftar:** Nomor # · Tanggal · Keterangan · Aset Tetap

**Saringan:** Tanggal: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Jenis Perubahan | select |  | Data, Revaluasi |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Perubahan Aset Tetap |
| Tanggal | date |  |  |
| Ya | checkbox |  |  |
| Cabang | lookup | ya |  |
| Akun Aset | lookup |  |  |
| Ya | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Perubahan | select |  | Data, Revaluasi |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Perubahan Aset Tetap |
| Tanggal | date |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Nilai Sisa | number |  |  |
| Keterangan Perubahan | textarea |  |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Perubahan | select |  | Data, Revaluasi |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Perubahan Aset Tetap |
| Tanggal | date |  |  |
| Metode Penyusutan | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Nilai Sisa | number |  |  |
| Keterangan Perubahan | textarea |  |  |

#### Tab: Pengeluaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Perubahan | select |  | Data, Revaluasi |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Perubahan Aset Tetap |
| Tanggal | date |  |  |
| Pengeluaran | lookup |  |  |

**Kolom rincian:** Kode # · Deskripsi · Jumlah

#### Tab: Info Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Perubahan | select |  | Data, Revaluasi |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Perubahan Aset Tetap |
| Tanggal | date |  |  |
| Ya | checkbox |  |  |
| Cabang | lookup | ya |  |
| Akun Aset | lookup |  |  |
| Ya | checkbox |  |  |

## Disposisi Aset Tetap

Rute: `#referensi__fixed-asset__fixed-asset-disposed`

**Judul layar:** Data Baru · Informasi Umum

**Kolom daftar:** Nomor # · Tanggal · Keterangan · Aset Tetap

**Saringan:** Tanggal: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Disposisi Aset Tetap |
| Tanggal | date | ya |  |
| Kuantitas | number | ya |  |
| Akun Laba Rugi | lookup | ya |  |
| Lokasi Aset | lookup |  |  |
| Catatan | textarea |  |  |
| Ya | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Aset | lookup | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Disposisi Aset Tetap |
| Tanggal | date | ya |  |
| Kuantitas | number | ya |  |
| Akun Laba Rugi | lookup | ya |  |
| Lokasi Aset | lookup |  |  |
| Catatan | textarea |  |  |
| Ya | checkbox |  |  |

## Pindah Aset

Rute: `#referensi__fixed-asset__asset-transfer`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Keterangan · Alamat Asal · Alamat Tujuan

**Saringan:** Tanggal: Semua · Alamat Asal: Semua · Alamat Tujuan: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal | date | ya |  |
| Alamat Asal | lookup | ya |  |
| Alamat Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pindah Aset |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Detail Aset

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Alamat Asal | lookup | ya |  |
| Alamat Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pindah Aset |
| Detail Aset | lookup | ya |  |

**Kolom rincian:** Kode Aset · Deskripsi Aset · Kuantitas · Keterangan

#### Tab: Detail Aset

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Alamat Asal | lookup | ya |  |
| Alamat Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pindah Aset |
| Detail Aset | lookup | ya |  |

**Kolom rincian:** Kode Aset · Deskripsi Aset · Kuantitas · Keterangan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Alamat Asal | lookup | ya |  |
| Alamat Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pindah Aset |
| Keterangan | textarea |  |  |

## Aset per Lokasi

Rute: `#referensi__fixed-asset__asset-location`

**Kolom daftar:** Nama · Alamat · Kuantitas

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari/Pilih Aset... | lookup |  |  |

