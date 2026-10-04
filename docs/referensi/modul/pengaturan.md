# Pengaturan

## Akses Grup

Rute: `#referensi__company__access-privilege`

**Judul layar:** Data Baru

**Kolom daftar:** Nama Grup · Daftar Pengguna

**Tombol:** Salin Hak

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Hak Akses | text |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Grup | text | ya |  |
| Mengikuti Pembatasan di Preferensi | radio |  |  |
| Terbatas pada waktu | radio |  |  |
| Daftar Pengguna | lookup |  |  |

#### Tab: Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Grup | text | ya |  |
| Mengikuti Pembatasan di Preferensi | radio |  |  |
| Terbatas pada waktu | radio |  |  |
| Daftar Pengguna | lookup |  |  |

#### Tab: Hak Akses

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Hak Akses | text |  |  |

## Pengguna

Rute: `#referensi__company__user-company`

**Judul layar:** Data Baru

**Kolom daftar:** Nama · No Handphone · Email · 2FA Auth · Jenis Akses

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| No Handphone/Email | text | ya |  |
| Operator | radio |  |  |
| Administrator | radio |  |  |
| Akses Grup | lookup |  |  |
| Akses Cabang | lookup |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| No Handphone/Email | text | ya |  |
| Operator | radio |  |  |
| Administrator | radio |  |  |
| Akses Grup | lookup |  |  |
| Akses Cabang | lookup |  |  |

#### Tab: Pengguna

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| No Handphone/Email | text | ya |  |
| Operator | radio |  |  |
| Administrator | radio |  |  |
| Akses Grup | lookup |  |  |
| Akses Cabang | lookup |  |  |

## Penomoran

Rute: `#referensi__company__auto-number`

**Judul layar:** Data Baru · Akses Pengguna

**Kolom daftar:** Nama · Tipe Transaksi · Daftar Pengguna

**Saringan:** Tipe Transaksi: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Semua Pengguna | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Info Penomoran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tipe Transaksi | select |  | Aset Tetap, Barang & Jasa, Bukti Potong PPh Ps.15, Bukti Potong PPh Ps.21, Bukti Potong PPh Ps.4(2), Bukti Potong PPh23, Check-in, Disposisi Aset Tetap, Draf Transaksi, Faktur Pembelian, Faktur Penjualan, Harga Pemasok, Hasil Stok Opname, Jurnal Umum, Karyawan, Klaim Pemasok, Nomor Bukti Kas/Bank, Pelanggan, Pemasok, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Pencatatan Gaji, Penerimaan Barang, Pengiriman Pesanan, Penyesuaian Harga/Diskon, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Perubahan Aset Tetap, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, SPT PPN/PPNBM, Transfer Anggaran, Transfer Bank, Tukar Faktur |
| Tipe Penomoran | select |  | Tidak Reset, Reset setiap hari, Reset setiap bulan, Reset setiap tahun |
| Jumlah Digit Counter | number | ya |  |
| Komponen Penomoran | select |  | Tahun, Tahun [Singkat], Bulan, Bulan [Romawi], Hari, Counter, Teks Pemisah |

#### Tab: Penomoran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| Tipe Transaksi | select |  | Aset Tetap, Barang & Jasa, Bukti Potong PPh Ps.15, Bukti Potong PPh Ps.21, Bukti Potong PPh Ps.4(2), Bukti Potong PPh23, Check-in, Disposisi Aset Tetap, Draf Transaksi, Faktur Pembelian, Faktur Penjualan, Harga Pemasok, Hasil Stok Opname, Jurnal Umum, Karyawan, Klaim Pemasok, Nomor Bukti Kas/Bank, Pelanggan, Pemasok, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Pencatatan Gaji, Penerimaan Barang, Pengiriman Pesanan, Penyesuaian Harga/Diskon, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Perubahan Aset Tetap, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, SPT PPN/PPNBM, Transfer Anggaran, Transfer Bank, Tukar Faktur |
| Tipe Penomoran | select |  | Tidak Reset, Reset setiap hari, Reset setiap bulan, Reset setiap tahun |
| Jumlah Digit Counter | number | ya |  |
| Komponen Penomoran | select |  | Tahun, Tahun [Singkat], Bulan, Bulan [Romawi], Hari, Counter, Teks Pemisah |

#### Tab: Daftar Pengguna

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Semua Pengguna | checkbox |  |  |

## Desain Cetakan

Rute: `#referensi__company__print-layout`

**Judul layar:** Data Baru

**Kolom daftar:** Nama Desain · Tipe Transaksi · Daftar Pengguna

**Saringan:** Tipe Transaksi: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari... | text |  |  |
| Nama Desain | text | ya |  |
| Tipe | select | ya | Silakan Pilih, Anggaran, Daftar Penagihan, Faktur Pembelian, Faktur Penjualan, Hasil Stok Opname, Jurnal Umum, Klaim Pemasok, Pembayaran, Pembayaran Pembelian, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Penerimaan, Penerimaan Barang, Penerimaan Penjualan, Pengiriman Pesanan, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, Slip Gaji, Target Penjualan, Transfer Bank, Tukar Faktur, Uang Muka Pembelian, Uang Muka Penjualan |
| Semua Pengguna | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Desain | text | ya |  |
| Tipe | select | ya | Silakan Pilih, Anggaran, Daftar Penagihan, Faktur Pembelian, Faktur Penjualan, Hasil Stok Opname, Jurnal Umum, Klaim Pemasok, Pembayaran, Pembayaran Pembelian, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Penerimaan, Penerimaan Barang, Penerimaan Penjualan, Pengiriman Pesanan, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, Slip Gaji, Target Penjualan, Transfer Bank, Tukar Faktur, Uang Muka Pembelian, Uang Muka Penjualan |
| Semua Pengguna | checkbox |  |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Desain | text | ya |  |
| Tipe | select | ya | Silakan Pilih, Anggaran, Daftar Penagihan, Faktur Pembelian, Faktur Penjualan, Hasil Stok Opname, Jurnal Umum, Klaim Pemasok, Pembayaran, Pembayaran Pembelian, Pemindahan Barang, Penawaran Penjualan, Pencatatan Beban, Penerimaan, Penerimaan Barang, Penerimaan Penjualan, Pengiriman Pesanan, Penyesuaian Persediaan, Perintah Pembayaran, Perintah Stok Opname, Permintaan Barang, Pesanan Pembelian, Pesanan Penjualan, Pindah Aset, Retur Pembelian, Retur Penjualan, Slip Gaji, Target Penjualan, Transfer Bank, Tukar Faktur, Uang Muka Pembelian, Uang Muka Penjualan |
| Semua Pengguna | checkbox |  |  |

## Penyetuju Transaksi

Rute: `#referensi__company__user-approval`

**Judul layar:** Data Baru · Kriteria Pengajuan · Kriteria Penyetuju

**Kolom daftar:** Tipe Transaksi · Nilai · Disetujui Oleh · Pembuat Transaksi · Cabang

**Saringan:** Tipe Transaksi: Semua · Cabang: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Transaksi | select |  | Hasil Stok Opname, Penyesuaian Harga/Diskon |
| Pembuat Transaksi | lookup |  |  |
| Akses Grup | lookup |  |  |
| Pengguna | lookup |  |  |
| Dengan Syarat | select |  | Ada Salah Satu Pengguna Setuju, Disetujui Minimal Dua Pengguna, Semua Pengguna Harus Setuju (Berurutan), Semua Pengguna Harus Setuju (Tidak Berurutan) |

### Formulir baru

**Judul:** Data Baru · Kriteria Pengajuan · Kriteria Penyetuju

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Transaksi | select |  | Hasil Stok Opname, Penyesuaian Harga/Diskon |
| Pembuat Transaksi | lookup |  |  |
| Akses Grup | lookup |  |  |
| Pengguna | lookup |  |  |
| Dengan Syarat | select |  | Ada Salah Satu Pengguna Setuju, Disetujui Minimal Dua Pengguna, Semua Pengguna Harus Setuju (Berurutan), Semua Pengguna Harus Setuju (Tidak Berurutan) |

#### Tab: Penyetuju Transaksi

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Transaksi | select |  | Hasil Stok Opname, Penyesuaian Harga/Diskon |
| Pembuat Transaksi | lookup |  |  |
| Akses Grup | lookup |  |  |
| Pengguna | lookup |  |  |
| Dengan Syarat | select |  | Ada Salah Satu Pengguna Setuju, Disetujui Minimal Dua Pengguna, Semua Pengguna Harus Setuju (Berurutan), Semua Pengguna Harus Setuju (Tidak Berurutan) |

## Referensi Store

Rute: `#referensi__company__application`

## Referensi Capital

Rute: `#referensi__company__capital-program`

