# Aset Tetap

Modul sistem referensi `asset`, 7 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- A-01: aset dibeli tunai/kredit atau dari faktur pembelian; A-03: metode penyusutan garis lurus dan saldo menurun, umur dan nilai sisa; penyusutan bulanan diposting otomatis.

## Pertanyaan terbuka

- A-03: metode penyusutan apa saja yang tersedia di edisi ini?

## Layar

- [Aset Tetap](#aset-tetap)
- [Kategori Aset](#kategori-aset)
- [Kategori Aset Tetap Pajak](#kategori-aset-tetap-pajak)
- [Perubahan Aset Tetap](#perubahan-aset-tetap)
- [Disposisi Aset Tetap](#disposisi-aset-tetap)
- [Pindah Aset](#pindah-aset)
- [Aset per Lokasi](#aset-per-lokasi)

## Aset Tetap

Rute di sistem referensi: `#referensi__fixed-asset__fixed-asset` · Jenis: layar

### Daftar

**Kolom:** # · Nomor # · Nama Aset · Tanggal Beli · Kuantitas · Total Aset

**Saringan:** Kategori Aset: Semua

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `description` | text | ya |  |
| Tanggal Beli | `transDate` | date | ya |  |
| Tanggal Pakai | `usageDate` | date | ya |  |
| Kode Aset |  | checkbox | ya |  |
| Kode Aset | `typeAutoNumber` | select | ya | Aset Tetap |
| Ya | `intangible` | checkbox |  |  |
| Metode Penyusutan | `depreciationMethod` | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Akun Aset | `assetAccount` | lookup | ya |  |
| Akun Akumulasi Penyusutan | `acmDepreciationAccount` | lookup | ya |  |
| Akun Beban Penyusutan | `depreciationAccount` | lookup | ya |  |
| Kuantitas |  | number | ya |  |
| Umur Aset |  | number | ya |  |
| Bulan |  | number |  |  |
| Nilai Sisa |  | number |  |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kategori Aset | `faType` | lookup | ya |  |
| Lokasi Awal Aset | `location` | lookup |  |  |
| Catatan | `notes` | textarea |  |  |
| Ya | `fiscalFa` | checkbox |  |  |

#### Tab: Akun Pengeluaran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Akun Pengeluaran | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Kode # · Deskripsi · Tanggal · Jumlah

### Perilaku yang direplikasi

- A-01: aset dengan kategori, tanggal perolehan, nilai, akun aset/akumulasi/beban, lokasi, dapat diubah.

## Kategori Aset

Rute di sistem referensi: `#referensi__fixed-asset__fa-type` · Jenis: layar

### Daftar

**Kolom:** Nama

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- A-02: kategori aset dengan akun bawaan dan metode/umur bawaan.

## Kategori Aset Tetap Pajak

Rute di sistem referensi: `#referensi__fixed-asset__fiscal-fa-type` · Jenis: layar

### Daftar

**Kolom:** Tarif Penyusutan (%) · Nama · Perkiraan Umur (tahun) · Metode Penyusutan

**Saringan:** Metode Penyusutan: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text |  |  |
| Metode Penyusutan | `depreciationMethod` | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Perkiraan Umur |  | number |  |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Kelompok aset fiskal (golongan harta) untuk penyusutan pajak.

## Perubahan Aset Tetap

Rute di sistem referensi: `#referensi__fixed-asset__fixed-asset-edited` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Keterangan · Aset Tetap

**Saringan:** Tanggal: Semua

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Jenis Perubahan | `editedType` | select |  | Data, Revaluasi |
| Aset | `fixedAsset` | lookup | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Perubahan Aset Tetap |
| Tanggal | `transDate` | date |  |  |
| Metode Penyusutan | `newDepreciationMethod` | select |  | Tidak Terdepresiasi, Metode Garis Lurus, Metode Saldo Menurun, Metode Angka Tahun |
| Nilai Sisa |  | number |  |  |
| Keterangan Perubahan | `description` | textarea |  |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

#### Tab: Pengeluaran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pengeluaran | `searchDetailAccount` | lookup |  |  |

**Kolom rincian:** Kode # · Deskripsi · Jumlah

#### Tab: Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ya | `newIntangible` | checkbox |  |  |
| Cabang | `branch` | lookup | ya |  |
| Akun Aset | `assetAccount` | lookup |  |  |
| Ya | `newFiscalFa` | checkbox |  |  |

### Perilaku yang direplikasi

- A-05: perubahan/revaluasi nilai aset dan penambahan biaya.

## Disposisi Aset Tetap

Rute di sistem referensi: `#referensi__fixed-asset__fixed-asset-disposed` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Keterangan · Aset Tetap

**Saringan:** Tanggal: Semua

### Formulir baru

**Judul:** Data Baru · Informasi Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Aset | `fixedAsset` | lookup | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Disposisi Aset Tetap |
| Tanggal | `transDate` | date | ya |  |
| Kuantitas |  | number | ya |  |
| Akun Laba Rugi | `gainLossAccount` | lookup | ya |  |
| Lokasi Aset | `location` | lookup |  |  |
| Catatan | `description` | textarea |  |  |
| Ya | `sellingAsset` | checkbox |  |  |

### Perilaku yang direplikasi

- A-04: pelepasan aset dengan laba/rugi pelepasan.

## Pindah Aset

Rute di sistem referensi: `#referensi__fixed-asset__asset-transfer` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Keterangan · Alamat Asal · Alamat Tujuan

**Saringan:** Tanggal: Semua · Alamat Asal: Semua · Alamat Tujuan: Semua

### Formulir baru

**Judul:** Data Baru · Detail Aset

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal | `transDate` | date | ya |  |
| Alamat Asal | `fromLocation` | lookup | ya |  |
| Alamat Tujuan | `toLocation` | lookup | ya |  |
| No. Pemindahan # |  | checkbox | ya |  |
| No. Pemindahan # | `typeAutoNumber` | select | ya | Pindah Aset |
| Detail Aset | `searchDetailAsset` | lookup | ya |  |

**Kolom rincian:** Kode Aset · Deskripsi Aset · Kuantitas · Keterangan

#### Tab: Detail Aset

_(tidak ada isian terbaca)_

**Kolom rincian:** Kode Aset · Deskripsi Aset · Kuantitas · Keterangan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- Pemindahan aset antar lokasi/cabang.

## Aset per Lokasi

Rute di sistem referensi: `#referensi__fixed-asset__asset-location` · Jenis: layar

### Daftar

**Kolom:** Nama · Alamat · Kuantitas

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari/Pilih Aset... | `asset` | lookup |  |  |

### Perilaku yang direplikasi

- Daftar aset per lokasi.

