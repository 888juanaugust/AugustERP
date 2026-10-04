# Persediaan

Modul sistem referensi `inventory`, 13 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- Stok per gudang; HPP rata-rata per barang (lihat pertanyaan I-10); setiap mutasi membawa tanggal dokumennya dan perubahan mundur menghitung ulang HPP sesudahnya (tidak sebelum periode terbuka pertama).

## Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- PipelineImporHarga: impor daftar harga lewat staging, pratinjau selisih, dan rem pengaman (>20% berubah atau satu harga >50%).

## Layar

- [Permintaan Barang](#permintaan-barang)
- [Pemindahan Barang](#pemindahan-barang)
- [Penyesuaian Persediaan](#penyesuaian-persediaan)
- [Perintah Stok Opname](#perintah-stok-opname)
- [Hasil Stok Opname](#hasil-stok-opname)
- [Barang & Jasa](#barang-jasa)
- [Gudang](#gudang)
- [Satuan Barang](#satuan-barang)
- [Kategori Barang](#kategori-barang)
- [Merek Barang](#merek-barang)
- [Pemenuhan Pesanan](#pemenuhan-pesanan)
- [Barang per Gudang](#barang-per-gudang)
- [Barang Stok Minimum](#barang-stok-minimum)

## Permintaan Barang

Rute di sistem referensi: `vendor__purchase-requisition` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Tipe Permintaan · Keterangan · Status · Total Hrg Estimasi

**Saringan:** Tanggal: Semua · Status: Semua · Sudah dicetak: Semua · Tipe Permintaan: Semua

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal | `transDate` | date | ya |  |
| Tipe Permintaan | `returnType` | select |  | Beli Barang, Kirim Barang |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Permintaan Barang |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Tgl Diminta

**Tombol:** Ambil

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Tgl Diminta

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- P-01: permintaan barang internal yang dapat ditarik menjadi PO.

## Pemindahan Barang

Rute di sistem referensi: `inventory__item-transfer` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Tipe Proses · Gudang Tujuan/Dari · Gudang · Keterangan · Status Pengiriman

**Saringan:** Tanggal: Semua · Gudang Tujuan/Dari: Semua · Tipe Proses: Semua · Status Pengiriman: Semua · Gudang: Semua

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Proses | `itemTransferType` | select |  | Kirim Barang, Terima Barang |
| Gudang | `warehouse` | lookup | ya |  |
| Gudang Tujuan | `referenceWarehouse` | lookup | ya |  |
| No. Pemindahan # |  | checkbox | ya |  |
| No. Pemindahan # | `typeAutoNumber` | select | ya | Pemindahan Barang |
| Tanggal | `transDate` | date | ya |  |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Keterangan · Kategori Barang

**Tombol:** Ambil

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Keterangan · Kategori Barang

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- I-07: pemindahan antar gudang (lintas cabang), dengan atau tanpa tahap dalam perjalanan.

### Pertanyaan terbuka

- I-07: apakah Pemindahan Barang punya tahap in-transit?

## Penyesuaian Persediaan

Rute di sistem referensi: `inventory__item-adjustment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Keterangan

**Saringan:** Tanggal: Semua

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal | `transDate` | date | ya |  |
| No Penyesuaian # |  | checkbox | ya |  |
| No Penyesuaian # | `typeAutoNumber` | select | ya | Penyesuaian Persediaan |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Tipe · Kuantitas · Satuan · Biaya Satuan · Total Biaya · Gudang

**Tombol:** Ambil · Rincian

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Tipe · Kuantitas · Satuan · Biaya Satuan · Total Biaya · Gudang

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- I-06: penyesuaian kuantitas dan/atau nilai per gudang ke akun penyesuaian; juga dipakai untuk saldo awal persediaan (I-09).

## Perintah Stok Opname

Rute di sistem referensi: `inventory__stock-opname-order` · Jenis: layar

### Daftar

**Kolom:** Tanggal · Nomor # · Tanggal Mulai · Gudang · Status · Keterangan · Penanggung Jawab

**Saringan:** Tanggal: Semua · Status: Semua

### Formulir baru

**Judul:** Data Baru · Perintah Stok Opname

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal SPK | `transDate` | date |  |  |
| No. SPK |  | checkbox | ya |  |
| No. SPK | `typeAutoNumber` | select | ya | Perintah Stok Opname |
| Tanggal Mulai | `startDate` | date | ya |  |
| Penanggung Jawab | `personCharged` | text | ya |  |
| Dikerjakan oleh | `userList` | lookup | ya |  |
| Keterangan | `description` | textarea |  |  |
| Gudang | `warehouse` | lookup | ya |  |
| Kategori Barang | `itemCategoryList` | lookup |  |  |
| Pemasok Barang | `vendorList` | lookup |  |  |
| Merek Barang | `brandList` | lookup |  |  |

### Perilaku yang direplikasi

- I-08: perintah opname per gudang/kategori dengan daftar hitung.

## Hasil Stok Opname

Rute di sistem referensi: `inventory__stock-opname-result` · Jenis: layar

### Daftar

**Kolom:** # · Tanggal · Nomor # · Perintah Opname · Keterangan

**Saringan:** Tanggal: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal Opname | `transDate` | date | ya |  |
| Perintah Opname | `order` | lookup | ya |  |
| No. Opname |  | checkbox | ya |  |
| No. Opname | `typeAutoNumber` | select | ya | (1 data) |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Kode # · Nama Barang · Kuantitas · Satuan

**Tombol:** Ambil

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Kode # · Nama Barang · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- I-08: hasil hitung dibandingkan sistem, selisih diposting sebagai penyesuaian.

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- PemisahanTugas: penghitung bukan penyetuju selisih.

## Barang & Jasa

Rute di sistem referensi: `inventory__item` · Jenis: layar

### Daftar

**Kolom:** Kode Barang · Jenis Barang · Satuan · Kts (Gdng Pengguna) · Nama Barang · Stok dapat dijual · Harga Beli · Harga Jual · Batas Minimum Stok

**Saringan:** Non Aktif: Tidak · Jenis Barang: Grup, Jasa, Persed... · Kategori Barang: Semua · Merek Barang: Semua

### Formulir baru

**Judul:** Data Baru · Informasi Barang & Jasa · Informasi Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Barang | `name` | text | ya |  |
| Jenis Barang | `itemType` | select |  | (4 data) |
| Kode Barang |  | checkbox | ya |  |
| Kode Barang | `typeAutoNumber` | select | ya | (1 data) |
| UPC/Barcode | `upcNo` | text |  |  |
| Cari/Pilih... | `unit1` | lookup |  |  |
| Merek Barang | `itemBrand` | lookup |  |  |

#### Tab: Umum

_(tidak ada isian terbaca)_

#### Tab: Penjualan / Pembelian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Default Diskon (%) | `defaultDiscount` | text |  |  |
| Def. Hrg. Jual Satuan #1 |  | number |  |  |
| Minimum Jual |  | number |  |  |
| Menerapkan Harga / Diskon Grosir | `useWholesalePrice` | checkbox |  |  |
| Substitusi dengan | `substituted` | checkbox |  |  |
| Pemasok Utama | `preferedVendor` | lookup |  |  |
| Satuan Beli | `vendorUnit` | lookup |  |  |
| Harga Beli |  | number |  |  |
| Minimum Beli |  | number |  |  |
| Batas Minimum Stok |  | number |  |  |
| Ref Kode Pajak | `itemTax` | lookup |  |  |
| PPN | `tax1` | lookup |  |  |
| PPh | `tax3` | lookup |  |  |

#### Tab: Stok

_(tidak ada isian terbaca)_

**Kolom rincian:** Tanggal · Kuantitas · Satuan · Biaya Satuan · Gudang

#### Tab: Akun

_(tidak ada isian terbaca)_

#### Tab: Gambar

_(tidak ada isian terbaca)_

#### Tab: Lain-lain

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Dipakai di Cabang | `branch` | select |  | (4 data) |
| Catatan | `notes` | textarea |  |  |
| Panjang (cm) |  | number |  |  |
| Lebar (cm) |  | number |  |  |
| Tinggi (cm) |  | number |  |  |
| Berat (gr) |  | number |  |  |

### Perilaku yang direplikasi

- I-01: barang dapat dibuat di layar, bukan hanya impor.
- I-02: jenis barang persediaan, non-persediaan, jasa, grup/rakitan (I-13).
- I-04: beberapa satuan dengan rasio konversi dan harga per satuan.
- I-11: stok minimum per barang (per gudang).
- I-12: nomor seri/batch dan kedaluwarsa bila diaktifkan.
- Harga jual per kategori harga, harga beli, akun bawaan, kode pajak, foto, dimensi.

## Gudang

Rute di sistem referensi: `inventory__warehouse` · Jenis: layar

### Daftar

**Kolom:** Nama · Alamat

**Saringan:** Non Aktif: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| Deskripsi | `description` | textarea |  |  |
| Penanggung Jawab | `pic` | text |  |  |
| Gunakan sebagai penyimpanan Barang Rusak | `scrapWarehouse` | checkbox |  |  |

#### Tab: Gudang

_(tidak ada isian terbaca)_

#### Tab: Pengguna

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Semua Pengguna | `usedAllUser` | checkbox |  |  |

### Perilaku yang direplikasi

- I-05: gudang dengan alamat dan cabang, ditetapkan ke pengguna.

## Satuan Barang

Rute di sistem referensi: `inventory__unit` · Jenis: layar

### Daftar

**Kolom:** Nama

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Pajak

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| Ref Kode Pajak | `unitTax` | lookup |  |  |

#### Tab: Satuan Barang

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- I-04: satuan bernama; konversi diatur di barang.

## Kategori Barang

Rute di sistem referensi: `inventory__item-category` · Jenis: layar

### Daftar

**Kolom:** Nama · Kategori Default

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| Ya | `isDefault` | checkbox |  |  |
| Sub Kategori | `sub` | checkbox |  |  |

#### Tab: Kategori Barang

_(tidak ada isian terbaca)_

#### Tab: Akun

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- I-03: kategori barang bertingkat dengan akun bawaan (persediaan, penjualan, HPP, retur).

## Merek Barang

Rute di sistem referensi: `inventory__item-brand` · Jenis: layar

### Daftar

**Kolom:** Nama

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |

#### Tab: Merek Barang

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Merek barang sebagai data master (YUHOLI, OSBORN, dan lainnya).

## Pemenuhan Pesanan

Rute di sistem referensi: `inventory__backorder-inquiry` · Jenis: layar

### Daftar

**Kolom:** Pelanggan · No Pesanan # · Tanggal · Tgl Pengiriman · Terkirim · Dapat Dikirim

**Tombol:** Perlu Pesan

### Perilaku yang direplikasi

- R-08: pesanan belum terkirim/belum terpenuhi per barang (backorder).

## Barang per Gudang

Rute di sistem referensi: `inventory__stock-warehouse` · Jenis: layar

### Daftar

**Kolom:** Gudang · Kuantitas Multi Satuan · Stok dapat dijual · Alamat

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari/Pilih Barang | `item` | lookup |  |  |

### Perilaku yang direplikasi

- R-14: kuantitas dan nilai per barang per gudang.

## Barang Stok Minimum

Rute di sistem referensi: `inventory__minimum-stock-item` · Jenis: layar

### Daftar

**Kolom:** Pemasok · Nama Barang · Kode Barang · Satuan · Stok tersedia · Dipesan · Diminta · Batas Minimum Stok

**Tombol:** Pesan · Minta

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari/Pilih Pemasok... | `vendorId` | lookup |  |  |
| Cari/Pilih Gudang... | `warehouseId` | lookup |  |  |
| Cari Nama/Kode Barang... | `search` | text |  |  |

### Perilaku yang direplikasi

- I-11: barang di bawah stok minimum; dasar saran pemesanan ulang.

