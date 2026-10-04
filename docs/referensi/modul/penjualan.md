# Penjualan

## Penawaran Penjualan

Rute: `#referensi__customer__sales-quotation`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Dipesan oleh: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penawaran Penjualan |
| Biaya Lainnya | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penawaran Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Salesman · Keterangan · Kts Terproses

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penawaran Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Salesman · Keterangan · Kts Terproses

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penawaran Penjualan |
| Syarat Pembayaran | lookup |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penawaran Penjualan |
| Biaya Lainnya | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

**Tombol formulir:** Ambil · Proses · %

## Pesanan Penjualan

Rute: `#referensi__customer__sales-order`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Dipesan oleh: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| No Pesanan # | checkbox | ya |  |
| No Pesanan # | select | ya | Pesanan Penjualan |
| Biaya Lainnya | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| No Pesanan # | checkbox | ya |  |
| No Pesanan # | select | ya | Pesanan Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Penjual · Gudang · Keterangan · Kts Terproses

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| No Pesanan # | checkbox | ya |  |
| No Pesanan # | select | ya | Pesanan Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Penjual · Gudang · Keterangan · Kts Terproses

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| No Pesanan # | checkbox | ya |  |
| No Pesanan # | select | ya | Pesanan Penjualan |
| Syarat Pembayaran | lookup |  |  |
| No. PO | text |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |
| Tgl Pengiriman | date |  |  |
| Pengiriman | lookup |  |  |
| FOB | lookup |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Dipesan oleh | lookup | ya |  |
| Tanggal | date | ya |  |
| No Pesanan # | checkbox | ya |  |
| No Pesanan # | select | ya | Pesanan Penjualan |
| Biaya Lainnya | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

**Tombol formulir:** Ambil · Proses · %

## Pengiriman Pesanan

Rute: `#referensi__customer__delivery-order`

**Judul layar:** Data Baru · Info lainnya · Info Tambahan

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Pengiriman · Keterangan · Status

**Saringan:** Tanggal: Semua · Kirim ke: Semua · Pengiriman: Semua · Status: Semua

**Tombol:** Ambil · Faktur

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Kirim ke | lookup | ya |  |
| Tanggal | date | ya |  |
| Pengiriman | lookup |  |  |
| No Pengiriman # | checkbox | ya |  |
| No Pengiriman # | select | ya | Pengiriman Pesanan |
| No. PO | text |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| FOB | lookup |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kirim ke | lookup | ya |  |
| Tanggal | date | ya |  |
| Pengiriman | lookup |  |  |
| No Pengiriman # | checkbox | ya |  |
| No Pengiriman # | select | ya | Pengiriman Pesanan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kirim ke | lookup | ya |  |
| Tanggal | date | ya |  |
| Pengiriman | lookup |  |  |
| No Pengiriman # | checkbox | ya |  |
| No Pengiriman # | select | ya | Pengiriman Pesanan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kirim ke | lookup | ya |  |
| Tanggal | date | ya |  |
| Pengiriman | lookup |  |  |
| No Pengiriman # | checkbox | ya |  |
| No Pengiriman # | select | ya | Pengiriman Pesanan |
| No. PO | text |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| FOB | lookup |  |  |

**Tombol formulir:** Ambil · Faktur

## Uang Muka Penjualan

Rute: `#referensi__customer__sales-downpayment`

**Judul layar:** Data Baru · Pembayaran melalui SmartLink e-Payment

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Umur (hr) · Total

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Proses

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan, Uang Muka Penjualan |

### Formulir baru

**Judul:** Data Baru · Uang Muka

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan, Uang Muka Penjualan |
| Uang Muka | number | ya |  |
| No. PO | text |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |

#### Tab: Uang Muka

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan, Uang Muka Penjualan |
| Uang Muka | number | ya |  |
| No. PO | text |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan, Uang Muka Penjualan |
| Syarat Pembayaran | lookup |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |

#### Tab: Informasi Pembayaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan, Uang Muka Penjualan |

**Tombol formulir:** Proses

## Faktur Penjualan

Rute: `#referensi__customer__sales-invoice`

**Judul layar:** Data Baru · Pembayaran melalui SmartLink e-Payment

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Umur (hr) · Total

**Saringan:** Pelanggan: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Penjual · Keterangan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Penjual · Keterangan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |
| Syarat Pembayaran | lookup |  |  |
| No. PO | text |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |
| Tgl Pengiriman | date |  |  |
| Pengiriman | lookup |  |  |
| FOB | lookup |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |
| Biaya Lainnya | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

#### Tab: Informasi Pembayaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| No Faktur # | checkbox | ya |  |
| No Faktur # | select | ya | Faktur Penjualan |

**Tombol formulir:** Ambil · Proses · %

## Penerimaan Penjualan

Rute: `#referensi__customer__sales-receipt`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · No. Cek · Tanggal Cek · Pelanggan · Bank · Keterangan · Pakai Kredit · Nilai Pembayaran

**Saringan:** Tanggal: Semua · Metode Bayar: Semua · Tanggal Cek: Semua · Bank: Semua · Terima dari: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Terima dari | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran · Akun Diskon

#### Tab: Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran · Akun Diskon

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Keterangan | textarea |  |  |

## Retur Penjualan

Rute: `#referensi__customer__sales-return`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · Tanggal · Pelanggan · Keterangan · Total

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Tipe Pengembalian: Semua · Sudah dicetak: Semua

**Tombol:** %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Penjualan |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Penjualan |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Penjualan |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Penjualan |
| Dari Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pelanggan | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Penjualan |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

**Tombol formulir:** %

## Tukar Faktur

Rute: `#referensi__customer__exchange-invoice`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Pelanggan · Tanggal · Tgl Tukar · Nomor # · Status · Total Faktur

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Tgl Tukar: Semua · Status: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Mata Uang | select | ya | (1 data) |
| Tgl Tukar | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Tukar Faktur |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Mata Uang | select | ya | (1 data) |
| Tgl Tukar | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Tukar Faktur |

**Kolom rincian:** Pelanggan · No. Faktur · Tgl Faktur · Jatuh Tempo

#### Tab: Rincian Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Mata Uang | select | ya | (1 data) |
| Tgl Tukar | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Tukar Faktur |

**Kolom rincian:** Pelanggan · No. Faktur · Tgl Faktur · Jatuh Tempo

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Mata Uang | select | ya | (1 data) |
| Tgl Tukar | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Tukar Faktur |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Kategori Pelanggan

Rute: `#referensi__customer__customer-category`

**Judul layar:** Data Baru · Info Umum

**Kolom daftar:** Nama Kategori · Kategori Default

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama Kategori | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Kategori | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

#### Tab: Kategori Pelanggan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Kategori | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

## Kategori Penjualan

Rute: `#referensi__customer__price-category`

**Judul layar:** Data Baru · Info Umum

**Kolom daftar:** Keterangan · Nama Kategori

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama Kategori | text | ya |  |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Kategori | text | ya |  |
| Keterangan | textarea |  |  |

#### Tab: Kategori Penjualan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Kategori | text | ya |  |
| Keterangan | textarea |  |  |

## Pelanggan

Rute: `#referensi__customer__customer`

**Judul layar:** Data Baru · Pembatasan Piutang Pelanggan · Lain-lain

**Kolom daftar:** Nama · Kontak Utama · ID Pelanggan · Kategori · Kategori Penjualan · Kategori Diskon · Alamat Pajak · Cabang · Alamat Utama · Syarat Pembayaran

**Saringan:** Non Aktif: Tidak · Kategori: Semua · Cabang: GUDANG B, Kantor P...

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Per Pelanggan | radio |  |  |
| Jika ada faktur dengan umur lebih dari | checkbox |  |  |
| Jika total piutang & pesanan melebihi | checkbox |  |  |
| Tergabung ke Pelanggan Induk | radio |  |  |
| Gudang Default | lookup |  |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| ID Pelanggan | checkbox | ya |  |
| ID Pelanggan | select | ya | (1 data) |
| Kategori | lookup |  |  |
| No. Telp. Bisnis | text |  |  |
| Handphone | text |  |  |
| No. WhatsApp | text |  |  |
| Email | text |  |  |
| Faximili | text |  |  |
| Website | text |  |  |
| Dipakai di Cabang | select |  | (4 data) |

#### Tab: Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| ID Pelanggan | checkbox | ya |  |
| ID Pelanggan | select | ya | (1 data) |
| Kategori | lookup |  |  |
| No. Telp. Bisnis | text |  |  |
| Handphone | text |  |  |
| No. WhatsApp | text |  |  |
| Email | text |  |  |
| Faximili | text |  |  |
| Website | text |  |  |
| Dipakai di Cabang | select |  | (4 data) |

#### Tab: Kontak

_(tidak ada isian)_

**Kolom rincian:** Nama Lengkap · Posisi Jabatan · Email · Handphone

#### Tab: Pengiriman

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Sama dengan alamat penagihan | checkbox |  |  |
| Sama dengan alamat penagihan | textarea |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |

**Kolom rincian:** Alamat

#### Tab: Penjualan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kategori Diskon | lookup |  |  |
| Default Penjual | lookup |  |  |
| Default Diskon (%) | text |  |  |
| Default Deskripsi | text |  |  |
| Piutang | lookup |  |  |
| Uang muka | lookup |  |  |
| Penjualan | lookup |  |  |
| Diskon Barang | lookup |  |  |
| Beban Pokok Penjualan | lookup |  |  |
| Retur Penjualan | lookup |  |  |
| Diskon Penjualan | lookup |  |  |

#### Tab: Pajak

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Default Total Faktur sudah termasuk Pajak | checkbox |  |  |
| Tipe ID Pajak | select |  | NIK, NPWP, Passpor, Lainnya |
| Nomor Wajib Pajak | text |  |  |
| Nama Wajib Pajak | text |  |  |
| NITKU | text |  |  |
| Kode Negara | lookup |  |  |
| Tipe Transaksi | select |  | Faktur Pajak, Dokumen Tertentu, Ekspor, Digunggung |
| Sama dengan alamat penagihan | checkbox |  |  |
| Sama dengan alamat penagihan | textarea |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |
| Sama dengan alamat penagihan | text |  |  |

#### Tab: Saldo Piutang

_(tidak ada isian)_

**Kolom rincian:** Tanggal · Jumlah · Mata Uang · Syarat Pembayaran · Nomor # · Keterangan

#### Tab: Lain-lain

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Per Pelanggan | radio |  |  |
| Jika ada faktur dengan umur lebih dari | checkbox |  |  |
| Jika total piutang & pesanan melebihi | checkbox |  |  |
| Tergabung ke Pelanggan Induk | radio |  |  |
| Gudang Default | lookup |  |  |
| Catatan | textarea |  |  |

## Penyesuaian Harga/Diskon

Rute: `#referensi__inventory__sellingprice-adjustment`

**Judul layar:** Data Baru

**Kolom daftar:** Nomor # · Mulai Berlaku · Kategori Penjualan · Keterangan · Tanggal Berakhir · Tipe Penyesuaian · #

**Saringan:** Tanggal: Semua · Non Aktif: Semua · Kategori Penjualan: Semua · Tipe Penyesuaian: Semua

**Tombol:** Rincian

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Kategori Penjualan | lookup | ya |  |
| Tipe Penyesuaian | select |  | Harga, Diskon (%) |
| Mulai Berlaku | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penyesuaian Harga/Diskon |
| Rincian Barang | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kategori Penjualan | lookup | ya |  |
| Tipe Penyesuaian | select |  | Harga, Diskon (%) |
| Mulai Berlaku | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Penyesuaian Harga/Diskon |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** No. · Nama Barang · Kode Barang · Satuan · Harga Baru

**Tombol formulir:** Rincian

## Komisi Penjual

Rute: `#referensi__company__salesman-commission`

**Judul layar:** Data Baru

**Kolom daftar:** Catatan · Nama · Periode Berlaku

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Selamanya | radio |  |  |
| Periode Tertentu | radio |  |  |
| Nama perhitungan komisi | text | ya |  |
| Semua | radio |  |  |
| Tertentu | radio |  |  |
| Pertama | checkbox |  |  |
| Kedua | checkbox |  |  |
| Ketiga | checkbox |  |  |
| Keempat | checkbox |  |  |
| Kelima | checkbox |  |  |
| Tanpa batasan dan syarat | radio |  |  |
| Nilai Penjualan antara | radio |  |  |
| Nilai Penjualan antara | number |  |  |
| s/d | number |  |  |
| Kuantitas penjualan antara | radio |  |  |
| Kuantitas penjualan antara | number |  |  |
| s/d | number |  |  |
| Kuantitas terjual per | radio |  |  |
| Kuantitas terjual per | number |  |  |
| Akan mendapat komisi | select | ya | Persentase, Nilai Tetap |
| Akan mendapat komisi | number | ya |  |
| % dari | select |  | Nilai Penjualan, Laba Kotor |

#### Tab: Komisi Penjual

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Selamanya | radio |  |  |
| Periode Tertentu | radio |  |  |
| Nama perhitungan komisi | text | ya |  |
| Semua | radio |  |  |
| Tertentu | radio |  |  |
| Pertama | checkbox |  |  |
| Kedua | checkbox |  |  |
| Ketiga | checkbox |  |  |
| Keempat | checkbox |  |  |
| Kelima | checkbox |  |  |
| Tanpa batasan dan syarat | radio |  |  |
| Nilai Penjualan antara | radio |  |  |
| Nilai Penjualan antara | number |  |  |
| s/d | number |  |  |
| Kuantitas penjualan antara | radio |  |  |
| Kuantitas penjualan antara | number |  |  |
| s/d | number |  |  |
| Kuantitas terjual per | radio |  |  |
| Kuantitas terjual per | number |  |  |
| Akan mendapat komisi | select | ya | Persentase, Nilai Tetap |
| Akan mendapat komisi | number | ya |  |
| % dari | select |  | Nilai Penjualan, Laba Kotor |

#### Tab: Lain-lain

_(tidak ada isian)_

## Target Penjualan

Rute: `#referensi__budget-target__sales-target`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Dari Tanggal · S/d Tanggal · Tahun · Nama · Cabang

**Saringan:** Tipe Target: Per Kategori Baran...

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Nama Target | text | ya |  |
| Tipe Target | select |  | Per Barang, Per Kategori Barang, Per Penjual, Per Bulan |
| Penjualan Cabang | select |  | (4 data) |
| Dari Tanggal | date |  |  |
| S/d Tanggal | date | ya |  |
| Catatan | textarea |  |  |
| Penganalisa | text |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Target | text | ya |  |
| Tipe Target | select |  | Per Barang, Per Kategori Barang, Per Penjual, Per Bulan |
| Penjualan Cabang | select |  | (4 data) |
| Dari Tanggal | date |  |  |
| S/d Tanggal | date | ya |  |
| Rincian Barang | lookup |  |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Nilai

#### Tab: Target Per Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Target | text | ya |  |
| Tipe Target | select |  | Per Barang, Per Kategori Barang, Per Penjual, Per Bulan |
| Penjualan Cabang | select |  | (4 data) |
| Dari Tanggal | date |  |  |
| S/d Tanggal | date | ya |  |
| Rincian Barang | lookup |  |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Nilai

#### Tab: Catatan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Target | text | ya |  |
| Tipe Target | select |  | Per Barang, Per Kategori Barang, Per Penjual, Per Bulan |
| Penjualan Cabang | select |  | (4 data) |
| Dari Tanggal | date |  |  |
| S/d Tanggal | date | ya |  |
| Catatan | textarea |  |  |
| Penganalisa | text |  |  |

## SmartLink e-Commerce

Rute: `#referensi__customer__ecommerce-setting`

**Judul layar:** Data Baru

**Kolom daftar:** Nama e-Commerce · Nama Toko

**Saringan:** Nama e-Commerce: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Gudang Penjualan | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Toko | select | ya | (7 data) |
| Nama Toko | text | ya |  |
| Penjualan atas Cabang | lookup | ya |  |
| Kas/Bank Saldo e-Commerce | lookup |  |  |
| Ya. Nilai penjualan sudah termasuk nilai PPN | checkbox |  |  |
| Selesai | radio |  |  |
| Semua Status | radio |  |  |

#### Tab: Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Toko | select | ya | (7 data) |
| Nama Toko | text | ya |  |
| Penjualan atas Cabang | lookup | ya |  |
| Kas/Bank Saldo e-Commerce | lookup |  |  |
| Ya. Nilai penjualan sudah termasuk nilai PPN | checkbox |  |  |
| Selesai | radio |  |  |
| Semua Status | radio |  |  |

#### Tab: Ongkir dan Fee

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Masukkan Biaya Kurir ke Faktur Penjualan | checkbox |  |  |

#### Tab: Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Gudang Penjualan | lookup | ya |  |

## Check In

Rute: `#referensi__customer__sales-check-in`

**Kolom daftar:** Tanggal · Nomor # · Nama Pelanggan (Saat Check In) · Sales · Transaksi

**Saringan:** Tanggal: Semua · Sales: Semua

### Formulir baru

_(tidak ada isian)_

**Kolom rincian:** Tanggal · Nomor # · Nama Pelanggan (Saat Check In) · Sales · Transaksi

