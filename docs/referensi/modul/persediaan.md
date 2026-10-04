# Persediaan

## Permintaan Barang

Rute: `vendor__purchase-requisition`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Tipe Permintaan · Keterangan · Status · Total Hrg Estimasi

**Saringan:** Tanggal: Semua · Status: Semua · Sudah dicetak: Semua · Tipe Permintaan: Semua

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal | date | ya |  |
| Tipe Permintaan | select |  | Beli Barang, Kirim Barang |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Permintaan Barang |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Tipe Permintaan | select |  | Beli Barang, Kirim Barang |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Permintaan Barang |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Tgl Diminta

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Tipe Permintaan | select |  | Beli Barang, Kirim Barang |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Permintaan Barang |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Tgl Diminta

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Tipe Permintaan | select |  | Beli Barang, Kirim Barang |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Permintaan Barang |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Pemindahan Barang

Rute: `inventory__item-transfer`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Tipe Proses · Gudang Tujuan/Dari · Gudang · Keterangan · Status Pengiriman

**Saringan:** Tanggal: Semua · Gudang Tujuan/Dari: Semua · Tipe Proses: Semua · Status Pengiriman: Semua · Gudang: Semua

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Proses | select |  | Kirim Barang, Terima Barang |
| Gudang | lookup | ya |  |
| Gudang Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pemindahan Barang |
| Tanggal | date | ya |  |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Proses | select |  | Kirim Barang, Terima Barang |
| Gudang | lookup | ya |  |
| Gudang Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pemindahan Barang |
| Tanggal | date | ya |  |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Keterangan · Kategori Barang

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Proses | select |  | Kirim Barang, Terima Barang |
| Gudang | lookup | ya |  |
| Gudang Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pemindahan Barang |
| Tanggal | date | ya |  |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Keterangan · Kategori Barang

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Proses | select |  | Kirim Barang, Terima Barang |
| Gudang | lookup | ya |  |
| Gudang Tujuan | lookup | ya |  |
| No. Pemindahan # | checkbox | ya |  |
| No. Pemindahan # | select | ya | Pemindahan Barang |
| Tanggal | date | ya |  |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Penyesuaian Persediaan

Rute: `inventory__item-adjustment`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Keterangan

**Saringan:** Tanggal: Semua

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal | date | ya |  |
| No Penyesuaian # | checkbox | ya |  |
| No Penyesuaian # | select | ya | Penyesuaian Persediaan |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| No Penyesuaian # | checkbox | ya |  |
| No Penyesuaian # | select | ya | Penyesuaian Persediaan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Tipe · Kuantitas · Satuan · Biaya Satuan · Total Biaya · Gudang

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| No Penyesuaian # | checkbox | ya |  |
| No Penyesuaian # | select | ya | Penyesuaian Persediaan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Tipe · Kuantitas · Satuan · Biaya Satuan · Total Biaya · Gudang

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| No Penyesuaian # | checkbox | ya |  |
| No Penyesuaian # | select | ya | Penyesuaian Persediaan |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil · Rincian

## Perintah Stok Opname

Rute: `inventory__stock-opname-order`

**Judul layar:** Data Baru · Perintah Stok Opname

**Kolom daftar:** Tanggal · Nomor # · Tanggal Mulai · Gudang · Status · Keterangan · Penanggung Jawab

**Saringan:** Tanggal: Semua · Status: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal SPK | date |  |  |
| No. SPK | checkbox | ya |  |
| No. SPK | select | ya | Perintah Stok Opname |
| Tanggal Mulai | date | ya |  |
| Penanggung Jawab | text | ya |  |
| Dikerjakan oleh | lookup | ya |  |
| Keterangan | textarea |  |  |
| Gudang | lookup | ya |  |
| Kategori Barang | lookup |  |  |
| Pemasok Barang | lookup |  |  |
| Merek Barang | lookup |  |  |

### Formulir baru

**Judul:** Data Baru · Perintah Stok Opname

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal SPK | date |  |  |
| No. SPK | checkbox | ya |  |
| No. SPK | select | ya | Perintah Stok Opname |
| Tanggal Mulai | date | ya |  |
| Penanggung Jawab | text | ya |  |
| Dikerjakan oleh | lookup | ya |  |
| Keterangan | textarea |  |  |
| Gudang | lookup | ya |  |
| Kategori Barang | lookup |  |  |
| Pemasok Barang | lookup |  |  |
| Merek Barang | lookup |  |  |

## Hasil Stok Opname

Rute: `inventory__stock-opname-result`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** # · Tanggal · Nomor # · Perintah Opname · Keterangan

**Saringan:** Tanggal: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal Opname | date | ya |  |
| Perintah Opname | lookup | ya |  |
| No. Opname | checkbox | ya |  |
| No. Opname | select | ya | (1 data) |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal Opname | date | ya |  |
| Perintah Opname | lookup | ya |  |
| No. Opname | checkbox | ya |  |
| No. Opname | select | ya | (1 data) |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Kode # · Nama Barang · Kuantitas · Satuan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal Opname | date | ya |  |
| Perintah Opname | lookup | ya |  |
| No. Opname | checkbox | ya |  |
| No. Opname | select | ya | (1 data) |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Kode # · Nama Barang · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal Opname | date | ya |  |
| Perintah Opname | lookup | ya |  |
| No. Opname | checkbox | ya |  |
| No. Opname | select | ya | (1 data) |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Barang & Jasa

Rute: `inventory__item`

**Judul layar:** Data Baru · Info Lainnya · Dimensi & Berat

**Kolom daftar:** Kode Barang · Jenis Barang · Satuan · Kts (Gdng Pengguna) · Nama Barang · Stok dapat dijual · Harga Beli · Harga Jual · Batas Minimum Stok

**Saringan:** Non Aktif: Tidak · Jenis Barang: Grup, Jasa, Persed... · Kategori Barang: Semua · Merek Barang: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Dipakai di Cabang | select |  | (4 data) |
| Catatan | textarea |  |  |
| Panjang (cm) | number |  |  |
| Lebar (cm) | number |  |  |
| Tinggi (cm) | number |  |  |
| Berat (gr) | number |  |  |

### Formulir baru

**Judul:** Data Baru · Informasi Barang & Jasa · Informasi Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Barang | text | ya |  |
| Jenis Barang | select |  | (4 data) |
| Kode Barang | checkbox | ya |  |
| Kode Barang | select | ya | (1 data) |
| UPC/Barcode | text |  |  |
| Cari/Pilih... | lookup |  |  |
| Merek Barang | lookup |  |  |

#### Tab: Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Barang | text | ya |  |
| Jenis Barang | select |  | (4 data) |
| Kode Barang | checkbox | ya |  |
| Kode Barang | select | ya | (1 data) |
| UPC/Barcode | text |  |  |
| Cari/Pilih... | lookup |  |  |
| Merek Barang | lookup |  |  |

#### Tab: Penjualan / Pembelian

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Default Diskon (%) | text |  |  |
| Def. Hrg. Jual Satuan #1 | number |  |  |
| Minimum Jual | number |  |  |
| Menerapkan Harga / Diskon Grosir | checkbox |  |  |
| Substitusi dengan | checkbox |  |  |
| Pemasok Utama | lookup |  |  |
| Satuan Beli | lookup |  |  |
| Harga Beli | number |  |  |
| Minimum Beli | number |  |  |
| Batas Minimum Stok | number |  |  |
| Ref Kode Pajak | lookup |  |  |
| PPN | lookup |  |  |
| PPh | lookup |  |  |

#### Tab: Stok

_(tidak ada isian)_

**Kolom rincian:** Tanggal · Kuantitas · Satuan · Biaya Satuan · Gudang

#### Tab: Akun

_(tidak ada isian)_

#### Tab: Gambar

_(tidak ada isian)_

#### Tab: Lain-lain

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipakai di Cabang | select |  | (4 data) |
| Catatan | textarea |  |  |
| Panjang (cm) | number |  |  |
| Lebar (cm) | number |  |  |
| Tinggi (cm) | number |  |  |
| Berat (gr) | number |  |  |

## Gudang

Rute: `inventory__warehouse`

**Judul layar:** Data Baru · Akses Pengguna

**Kolom daftar:** Nama · Alamat

**Saringan:** Non Aktif: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Semua Pengguna | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Deskripsi | textarea |  |  |
| Penanggung Jawab | text |  |  |
| Gunakan sebagai penyimpanan Barang Rusak | checkbox |  |  |

#### Tab: Gudang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Deskripsi | textarea |  |  |
| Penanggung Jawab | text |  |  |
| Gunakan sebagai penyimpanan Barang Rusak | checkbox |  |  |

#### Tab: Pengguna

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Semua Pengguna | checkbox |  |  |

## Satuan Barang

Rute: `inventory__unit`

**Judul layar:** Data Baru · Info Umum · Info Pajak

**Kolom daftar:** Nama

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama | text | ya |  |
| Ref Kode Pajak | lookup |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Pajak

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Ref Kode Pajak | lookup |  |  |

#### Tab: Satuan Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Ref Kode Pajak | lookup |  |  |

## Kategori Barang

Rute: `inventory__item-category`

**Judul layar:** Data Baru · Info Akun

**Kolom daftar:** Nama · Kategori Default

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

#### Tab: Kategori Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

#### Tab: Akun

_(tidak ada isian)_

## Merek Barang

Rute: `inventory__item-brand`

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

#### Tab: Merek Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |

## Pemenuhan Pesanan

Rute: `inventory__backorder-inquiry`

**Kolom daftar:** Pelanggan · No Pesanan # · Tanggal · Tgl Pengiriman · Terkirim · Dapat Dikirim

**Tombol:** Perlu Pesan

## Barang per Gudang

Rute: `inventory__stock-warehouse`

**Kolom daftar:** Gudang · Kuantitas Multi Satuan · Stok dapat dijual · Alamat

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari/Pilih Barang | lookup |  |  |

## Barang Stok Minimum

Rute: `inventory__minimum-stock-item`

**Kolom daftar:** Pemasok · Nama Barang · Kode Barang · Satuan · Stok tersedia · Dipesan · Diminta · Batas Minimum Stok

**Tombol:** Pesan · Minta

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari/Pilih Pemasok... | lookup |  |  |
| Cari/Pilih Gudang... | lookup |  |  |
| Cari Nama/Kode Barang... | text |  |  |

