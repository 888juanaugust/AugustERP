# Pembelian

## Pesanan Pembelian

Rute: `vendor__purchase-order`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · Tanggal · Pemasok · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Status: Semua

**Tombol:** Ambil · Proses · %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pesanan Pembelian |
| Biaya Lainnya | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pesanan Pembelian |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Kts Terproses

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pesanan Pembelian |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Kts Terproses

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pesanan Pembelian |
| Syarat Pembayaran | lookup |  |  |
| Rekening Bank | lookup |  |  |
| Alamat Kirim | textarea | ya |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |
| Tgl Pengiriman | date |  |  |
| Pengiriman | lookup |  |  |
| FOB | lookup |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pesanan Pembelian |
| Biaya Lainnya | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

**Tombol formulir:** Ambil · Proses · Rincian · %

## Penerimaan Barang

Rute: `vendor__receive-item`

**Judul layar:** Data Baru · Info lainnya · Info Pengiriman

**Kolom daftar:** Nomor # · No Terima # · Tanggal · Pemasok · Keterangan · Status

**Saringan:** Tanggal: Semua · Terima dari: Semua · Status: Semua

**Tombol:** Ambil · Faktur

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Terima dari | lookup | ya |  |
| Tanggal | date | ya |  |
| No Terima # | text | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Penerimaan Barang |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Tgl Kirim | date |  |  |
| Pengiriman | lookup |  |  |
| FOB | lookup |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Tanggal | date | ya |  |
| No Terima # | text | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Penerimaan Barang |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Tanggal | date | ya |  |
| No Terima # | text | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Penerimaan Barang |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Terima dari | lookup | ya |  |
| Tanggal | date | ya |  |
| No Terima # | text | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Penerimaan Barang |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Tgl Kirim | date |  |  |
| Pengiriman | lookup |  |  |
| FOB | lookup |  |  |

**Tombol formulir:** Ambil · Faktur

## Uang Muka Pembelian

Rute: `vendor__purchase-downpayment`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · No Faktur # · Tanggal · Pemasok · Keterangan · Status · Umur (hr) · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Status: Semua

**Tombol:** Proses

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian, Uang Muka Pembelian |
| Rekening Bank | lookup |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Uang Muka

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian, Uang Muka Pembelian |
| Uang Muka | number | ya |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |
| Syarat Pembayaran | lookup |  |  |

#### Tab: Uang Muka

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian, Uang Muka Pembelian |
| Uang Muka | number | ya |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |
| Syarat Pembayaran | lookup |  |  |

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian, Uang Muka Pembelian |
| Rekening Bank | lookup |  |  |
| Alamat | textarea |  |  |
| Keterangan | textarea |  |  |

**Tombol formulir:** Proses

## Faktur Pembelian

Rute: `vendor__purchase-invoice`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · No Faktur # · Tanggal · Pemasok · Keterangan · Status · Umur (hr) · Total

**Saringan:** Pemasok: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian |
| Biaya Lainnya | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian |
| Syarat Pembayaran | lookup |  |  |
| Rekening Bank | lookup |  |  |
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
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| No Form # | checkbox | ya |  |
| No Form # | select | ya | Faktur Pembelian |
| Biaya Lainnya | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

**Tombol formulir:** Ambil · Proses · %

## Pembayaran Pembelian

Rute: `vendor__purchase-payment`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · No. Cek · Tanggal Cek · Pemasok · Bank · Keterangan · Nilai Pembayaran

**Saringan:** Tanggal: Semua · Metode Bayar: Semua · Tanggal Cek: Semua · Bank: Semua · Pembayaran ke: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pembayaran ke | lookup | ya |  |
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
| Pembayaran ke | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran

#### Tab: Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pembayaran ke | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pembayaran ke | lookup | ya |  |
| Bank | lookup | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran | number |  |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | date | ya |  |
| Metode Bayar | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Keterangan | textarea |  |  |

## Retur Pembelian

Rute: `vendor__purchase-return`

**Judul layar:** Data Baru · Biaya Lainnya

**Kolom daftar:** Nomor # · Tanggal · Pemasok · Keterangan · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Sudah dicetak: Semua

**Tombol:** %

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Pembelian |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Pembelian |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon · Total Harga · Keterangan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Pembelian |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon · Total Harga · Keterangan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Pembelian |
| Ke Alamat | textarea |  |  |
| Keterangan | textarea |  |  |
| Kena Pajak | checkbox |  |  |
| Total termasuk Pajak | checkbox |  |  |

#### Tab: Biaya Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Tanggal | date | ya |  |
| Retur dari | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | lookup | ya |  |
| No Retur # | checkbox | ya |  |
| No Retur # | select | ya | Retur Pembelian |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

**Tombol formulir:** %

## Klaim Pemasok

Rute: `vendor__vendor-claim`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Tipe Klaim · Pemasok · Keterangan · Status Pengiriman

**Saringan:** Tanggal: Semua · Status Klaim: Semua · Tipe Klaim: Semua · Pemasok: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tipe Klaim | select |  | Kirim Barang, Terima Barang |
| Pemasok | lookup | ya |  |
| No. Klaim # | checkbox | ya |  |
| No. Klaim # | select | ya | Klaim Pemasok |
| Tanggal | date | ya |  |
| Alamat Pemasok | textarea |  |  |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Klaim | select |  | Kirim Barang, Terima Barang |
| Pemasok | lookup | ya |  |
| No. Klaim # | checkbox | ya |  |
| No. Klaim # | select | ya | Klaim Pemasok |
| Tanggal | date | ya |  |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Klaim | select |  | Kirim Barang, Terima Barang |
| Pemasok | lookup | ya |  |
| No. Klaim # | checkbox | ya |  |
| No. Klaim # | select | ya | Klaim Pemasok |
| Tanggal | date | ya |  |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Klaim | select |  | Kirim Barang, Terima Barang |
| Pemasok | lookup | ya |  |
| No. Klaim # | checkbox | ya |  |
| No. Klaim # | select | ya | Klaim Pemasok |
| Tanggal | date | ya |  |
| Alamat Pemasok | textarea |  |  |
| Keterangan | textarea |  |  |

## Harga Pemasok

Rute: `inventory__vendor-price`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Mulai Berlaku · Pemasok · Keterangan · Tanggal Berakhir

**Saringan:** Tanggal: Semua · Pemasok: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Pemasok | lookup | ya |  |
| Mulai Berlaku | date | ya |  |
| Atur Tanggal Berakhir | checkbox |  |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Harga Pemasok |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Mulai Berlaku | date | ya |  |
| Atur Tanggal Berakhir | checkbox |  |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Harga Pemasok |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Satuan · Harga Baru

#### Tab: Rincian Barang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Mulai Berlaku | date | ya |  |
| Atur Tanggal Berakhir | checkbox |  |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Harga Pemasok |
| Rincian Barang | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Satuan · Harga Baru

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Pemasok | lookup | ya |  |
| Mulai Berlaku | date | ya |  |
| Atur Tanggal Berakhir | checkbox |  |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Harga Pemasok |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Kategori Pemasok

Rute: `vendor__vendor-category`

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

#### Tab: Kategori Pemasok

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama Kategori | text | ya |  |
| Ya | checkbox |  |  |
| Sub Kategori | checkbox |  |  |

## Pemasok

Rute: `vendor__vendor`

**Judul layar:** Data Baru · Lain-lain

**Kolom daftar:** Nama · ID Pemasok · Saldo

**Saringan:** Non Aktif: Semua · Kategori: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Ya, Pemasok memberikan nomor faktur pada tagihan | checkbox |  |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| ID Pemasok | checkbox | ya |  |
| ID Pemasok | select | ya | (1 data) |
| Kategori | lookup |  |  |
| No. Telp. Bisnis | text |  |  |
| Handphone | text |  |  |
| No. WhatsApp | text |  |  |
| Email | text |  |  |
| Faximili | text |  |  |
| Website | text |  |  |
| Ya, Penjual jasa orang pribadi (Dikenakan PPh 21) | checkbox |  |  |
| Dipakai di Cabang | select | ya | (4 data) |
| Tipe Pemasok | select |  | (4 data) |

#### Tab: Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nama | text | ya |  |
| ID Pemasok | checkbox | ya |  |
| ID Pemasok | select | ya | (1 data) |
| Kategori | lookup |  |  |
| No. Telp. Bisnis | text |  |  |
| Handphone | text |  |  |
| No. WhatsApp | text |  |  |
| Email | text |  |  |
| Faximili | text |  |  |
| Website | text |  |  |
| Ya, Penjual jasa orang pribadi (Dikenakan PPh 21) | checkbox |  |  |
| Dipakai di Cabang | select | ya | (4 data) |
| Tipe Pemasok | select |  | (4 data) |

#### Tab: Kontak

_(tidak ada isian)_

**Kolom rincian:** Nama Lengkap · Posisi Jabatan · Email · Handphone

#### Tab: Pembelian

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Default Diskon (%) | text |  |  |
| Default Deskripsi | textarea |  |  |
| Akun Utang | lookup |  |  |
| Akun Uang muka | lookup |  |  |

**Kolom rincian:** No Rekening · Atas Nama · Nama Bank

#### Tab: Pajak

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Default Faktur sudah termasuk Pajak | checkbox |  |  |
| Tipe ID Pajak | select |  | NIK, NPWP, Passpor, Lainnya |
| Nomor Wajib Pajak | text |  |  |
| Nama Wajib Pajak | text |  |  |
| NITKU | text |  |  |
| Tipe Transaksi | select |  | Faktur Pajak, Impor, Perolehan Dalam Negeri, Tidak dikreditkan, Digunggung |
| Alamat pajak sama dengan alamat pembayaran | checkbox |  |  |
| Alamat pajak sama dengan alamat pembayaran | textarea |  |  |
| Alamat pajak sama dengan alamat pembayaran | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | text |  |  |

#### Tab: Saldo Utang

_(tidak ada isian)_

**Kolom rincian:** Tanggal · Jumlah · Mata Uang · Syarat Pembayaran · Nomor # · Keterangan

#### Tab: Lain-lain

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ya, Pemasok memberikan nomor faktur pada tagihan | checkbox |  |  |
| Catatan | textarea |  |  |

## Perintah Pembayaran

Rute: `vendor__transfer-order`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Keterangan · Bank · Status

**Saringan:** Tanggal: Semua · Status: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tgl Batas Transfer | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Perintah Pembayaran |
| Metode Bayar | select |  | Transfer Bank, Virtual Account, Cek/Giro |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Batas Transfer | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Perintah Pembayaran |
| Metode Bayar | select |  | Transfer Bank, Virtual Account, Cek/Giro |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terutang · Bayar · Diskon · Pembayaran · Nama Pemasok

#### Tab: Faktur

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Batas Transfer | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Perintah Pembayaran |
| Metode Bayar | select |  | Transfer Bank, Virtual Account, Cek/Giro |
| Faktur | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terutang · Bayar · Diskon · Pembayaran · Nama Pemasok

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Batas Transfer | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Perintah Pembayaran |
| Metode Bayar | select |  | Transfer Bank, Virtual Account, Cek/Giro |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Transfer Pemasok

Rute: `vendor__multi-vendor-transfer`

**Kolom daftar:** Tgl Batas Transfer · Pemasok · Metode Bayar · Bank · No Rekening Pemasok · A/n Rekening · Nilai Pembayaran · Proses

**Tombol:** Export/Bayar

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari Nomor/Nama bank/Rek.. | text |  |  |

