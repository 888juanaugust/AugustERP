# Pembelian

Modul sistem referensi `purchase`, 12 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- Rantai: Permintaan Barang → Pesanan Pembelian → Penerimaan Barang (dari PO, sebagian) → Faktur Pembelian (dari penerimaan atau langsung, memindahkan stok bila langsung) → Pembayaran Pembelian. Diskon baris, kode pajak baris, Biaya Lainnya ke akun mana pun.

## Layar

- [Pesanan Pembelian](#pesanan-pembelian)
- [Penerimaan Barang](#penerimaan-barang)
- [Uang Muka Pembelian](#uang-muka-pembelian)
- [Faktur Pembelian](#faktur-pembelian)
- [Pembayaran Pembelian](#pembayaran-pembelian)
- [Retur Pembelian](#retur-pembelian)
- [Klaim Pemasok](#klaim-pemasok)
- [Harga Pemasok](#harga-pemasok)
- [Kategori Pemasok](#kategori-pemasok)
- [Pemasok](#pemasok)
- [Perintah Pembayaran](#perintah-pembayaran)
- [Transfer Pemasok](#transfer-pemasok)

## Pesanan Pembelian

Rute di sistem referensi: `vendor__purchase-order` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pemasok · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Status: Semua

**Tombol:** Ambil · Proses · %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pemasok | `vendor` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Pesanan Pembelian |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Kts Terproses

**Tombol:** Ambil · Proses · Rincian · %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Kts Terproses

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| Rekening Bank | `vendorBankAccount` | lookup |  |  |
| Alamat Kirim | `toAddress` | textarea | ya |  |
| Keterangan | `description` | textarea |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |
| Tgl Pengiriman | `shipDate` | date |  |  |
| Pengiriman | `shipment` | lookup |  |  |
| FOB | `fob` | lookup |  |  |

#### Tab: Biaya Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Biaya Lainnya | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

### Perilaku yang direplikasi

- P-02: PO dengan diskon baris dan kode pajak; penerimaan dapat menarik dari PO (Ambil).

## Penerimaan Barang

Rute di sistem referensi: `vendor__receive-item` · Jenis: layar

### Daftar

**Kolom:** Nomor # · No Terima # · Tanggal · Pemasok · Keterangan · Status

**Saringan:** Tanggal: Semua · Terima dari: Semua · Status: Semua

**Tombol:** Ambil · Faktur

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Terima dari | `vendor` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Terima # | `receiveNumber` | text | ya |  |
| No Form # |  | checkbox | ya |  |
| No Form # | `typeAutoNumber` | select | ya | Penerimaan Barang |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

**Tombol:** Ambil · Faktur

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| Tgl Kirim | `shipDate` | date |  |  |
| Pengiriman | `shipment` | lookup |  |  |
| FOB | `fob` | lookup |  |  |

### Perilaku yang direplikasi

- P-04: penerimaan per gudang, menarik dari PO, sebagian; dapat diubah/dihapus sesuai hak dan pemblokir.

## Uang Muka Pembelian

Rute di sistem referensi: `vendor__purchase-downpayment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · No Faktur # · Tanggal · Pemasok · Keterangan · Status · Umur (hr) · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Status: Semua

**Tombol:** Proses

### Formulir baru

**Judul:** Data Baru · Uang Muka

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pemasok | `vendor` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Form # |  | checkbox | ya |  |
| No Form # | `typeAutoNumber` | select | ya | Faktur Pembelian, Uang Muka Pembelian |
| Uang Muka |  | number | ya |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |

**Tombol:** Proses

#### Tab: Uang Muka

_(tidak ada isian terbaca)_

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Rekening Bank | `vendorBankAccount` | lookup |  |  |
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- P-03: uang muka ke pemasok, dipotong pada faktur pembelian.

## Faktur Pembelian

Rute di sistem referensi: `vendor__purchase-invoice` · Jenis: layar

### Daftar

**Kolom:** Nomor # · No Faktur # · Tanggal · Pemasok · Keterangan · Status · Umur (hr) · Total

**Saringan:** Pemasok: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pemasok | `vendor` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Form # |  | checkbox | ya |  |
| No Form # | `typeAutoNumber` | select | ya | Faktur Pembelian |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

**Tombol:** Ambil · Proses · %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| Rekening Bank | `vendorBankAccount` | lookup |  |  |
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |
| Tgl Pengiriman | `shipDate` | date |  |  |
| Pengiriman | `shipment` | lookup |  |  |
| FOB | `fob` | lookup |  |  |

#### Tab: Biaya Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Biaya Lainnya | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

### Perilaku yang direplikasi

- P-05: faktur dari satu/beberapa penerimaan atau langsung; diskon; PPN termasuk/belum; faktur pajak masukan.
- P-09: Biaya Lainnya ke akun mana pun, termasuk alokasi ke harga pokok barang (landed cost).

## Pembayaran Pembelian

Rute di sistem referensi: `vendor__purchase-payment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · No. Cek · Tanggal Cek · Pemasok · Bank · Keterangan · Nilai Pembayaran

**Saringan:** Tanggal: Semua · Metode Bayar: Semua · Tanggal Cek: Semua · Bank: Semua · Pembayaran ke: Semua

### Formulir baru

**Judul:** Data Baru · Faktur

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pembayaran ke | `vendor` | lookup | ya |  |
| Bank | `bank` | lookup | ya |  |
| Metode Bayar | `paymentMethod` | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran |  | number |  |  |
| No Bukti # |  | checkbox | ya |  |
| No Bukti # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | `transDate` | date | ya |  |
| Faktur | `searchDetailInvoice` | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran

#### Tab: Faktur

_(tidak ada isian terbaca)_

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- P-06: pembayaran dengan nomor dokumen, banyak faktur, diskon pembayaran, giro keluar.

## Retur Pembelian

Rute di sistem referensi: `vendor__purchase-return` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pemasok · Keterangan · Total

**Saringan:** Tanggal: Semua · Pemasok: Semua · Sudah dicetak: Semua

**Tombol:** %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pemasok | `vendor` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| Retur dari | `returnType` | select | ya | Faktur, Penerimaan, Tanpa Faktur, Uang Muka |
| Retur dari | `invoice` | lookup | ya |  |
| No Retur # |  | checkbox | ya |  |
| No Retur # | `typeAutoNumber` | select | ya | Retur Pembelian |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon · Total Harga · Keterangan

**Tombol:** %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon · Total Harga · Keterangan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ke Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |

#### Tab: Biaya Lainnya

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Biaya · Kode # · Jumlah · Keterangan

### Perilaku yang direplikasi

- P-07: retur ke pemasok merujuk penerimaan/faktur, nota debit otomatis.

## Klaim Pemasok

Rute di sistem referensi: `vendor__vendor-claim` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Tipe Klaim · Pemasok · Keterangan · Status Pengiriman

**Saringan:** Tanggal: Semua · Status Klaim: Semua · Tipe Klaim: Semua · Pemasok: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tipe Klaim | `vendorClaimType` | select |  | Kirim Barang, Terima Barang |
| Pemasok | `vendor` | lookup | ya |  |
| No. Klaim # |  | checkbox | ya |  |
| No. Klaim # | `typeAutoNumber` | select | ya | Klaim Pemasok |
| Tanggal | `transDate` | date | ya |  |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Alamat Pemasok | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- P-11: klaim/nota kredit dari pemasok tanpa barang kembali.

## Harga Pemasok

Rute di sistem referensi: `inventory__vendor-price` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Mulai Berlaku · Pemasok · Keterangan · Tanggal Berakhir

**Saringan:** Tanggal: Semua · Pemasok: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pemasok | `vendor` | lookup | ya |  |
| Mulai Berlaku | `transDate` | date | ya |  |
| Atur Tanggal Berakhir |  | checkbox |  |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Harga Pemasok |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Satuan · Harga Baru

**Tombol:** Ambil

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Satuan · Harga Baru

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- P-10: harga beli per pemasok per barang, dipakai sebagai harga bawaan PO.

## Kategori Pemasok

Rute di sistem referensi: `vendor__vendor-category` · Jenis: layar

### Daftar

**Kolom:** Nama Kategori · Kategori Default

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Kategori | `name` | text | ya |  |
| Ya | `isDefault` | checkbox |  |  |
| Sub Kategori | `sub` | checkbox |  |  |

#### Tab: Kategori Pemasok

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Kategori pemasok bebas.

## Pemasok

Rute di sistem referensi: `vendor__vendor` · Jenis: layar

### Daftar

**Kolom:** Nama · ID Pemasok · Saldo

**Saringan:** Non Aktif: Semua · Kategori: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| ID Pemasok |  | checkbox | ya |  |
| ID Pemasok | `typeAutoNumber` | select | ya | (1 data) |
| Kategori | `category` | lookup |  |  |
| No. Telp. Bisnis | `workPhone` | text |  |  |
| Handphone | `mobilePhone` | text |  |  |
| No. WhatsApp | `waNumber` | text |  |  |
| Email | `email` | text |  |  |
| Faximili | `fax` | text |  |  |
| Website | `website` | text |  |  |
| Ya, Penjual jasa orang pribadi (Dikenakan PPh 21) | `serviceSeller` | checkbox |  |  |
| Dipakai di Cabang | `branch` | select | ya | (4 data) |
| Tipe Pemasok | `vendorType` | select |  | (4 data) |

#### Tab: Umum

_(tidak ada isian terbaca)_

#### Tab: Kontak

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Lengkap · Posisi Jabatan · Email · Handphone

#### Tab: Pembelian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Default Diskon (%) | `defaultPurchaseDisc` | text |  |  |
| Default Deskripsi | `defaultInvoiceDesc` | textarea |  |  |
| Akun Utang | `vendorPayableAccountList` | lookup |  |  |
| Akun Uang muka | `vendorDownPaymentAccountList` | lookup |  |  |

**Kolom rincian:** No Rekening · Atas Nama · Nama Bank

#### Tab: Pajak

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Default Faktur sudah termasuk Pajak | `defaultIncTax` | checkbox |  |  |
| Tipe ID Pajak | `wpType` | select |  | NIK, NPWP, Passpor, Lainnya |
| Nomor Wajib Pajak | `wpNumber` | text |  |  |
| Nama Wajib Pajak | `wpName` | text |  |  |
| NITKU | `nitku` | text |  |  |
| Tipe Transaksi | `documentCode` | select |  | Faktur Pajak, Impor, Perolehan Dalam Negeri, Tidak dikreditkan, Digunggung |
| Alamat pajak sama dengan alamat pembayaran | `taxSameAsBill` | checkbox |  |  |
| Alamat pajak sama dengan alamat pembayaran | `taxStreet` | textarea |  |  |
| Alamat pajak sama dengan alamat pembayaran | `taxCity` | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | `taxZipCode` | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | `taxProvince` | text |  |  |
| Alamat pajak sama dengan alamat pembayaran | `taxCountry` | text |  |  |

#### Tab: Saldo Utang

_(tidak ada isian terbaca)_

**Kolom rincian:** Tanggal · Jumlah · Mata Uang · Syarat Pembayaran · Nomor # · Keterangan

#### Tab: Lain-lain

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ya, Pemasok memberikan nomor faktur pada tagihan | `useBillNumber` | checkbox |  |  |
| Catatan | `notes` | textarea |  |  |

### Perilaku yang direplikasi

- P-08: pemasok dengan syarat pembayaran, status PKP, NPWP, pajak bawaan, rekening bank, beberapa kontak, saldo awal hutang.

## Perintah Pembayaran

Rute di sistem referensi: `vendor__transfer-order` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Keterangan · Bank · Status

**Saringan:** Tanggal: Semua · Status: Semua

### Formulir baru

**Judul:** Data Baru · Faktur

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tgl Batas Transfer | `transDate` | date | ya |  |
| No Bukti # |  | checkbox | ya |  |
| No Bukti # | `typeAutoNumber` | select | ya | Perintah Pembayaran |
| Metode Bayar | `paymentMethod` | select |  | Transfer Bank, Virtual Account, Cek/Giro |
| Faktur | `searchDetailInvoice` | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terutang · Bayar · Diskon · Pembayaran · Nama Pemasok

**Tombol:** Ambil

#### Tab: Faktur

_(tidak ada isian terbaca)_

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terutang · Bayar · Diskon · Pembayaran · Nama Pemasok

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- Perintah pembayaran (transfer order) ke bank untuk beberapa faktur pemasok.

## Transfer Pemasok

Rute di sistem referensi: `vendor__multi-vendor-transfer` · Jenis: layar

### Daftar

**Kolom:** Tgl Batas Transfer · Pemasok · Metode Bayar · Bank · No Rekening Pemasok · A/n Rekening · Nilai Pembayaran · Proses

**Tombol:** Export/Bayar

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari Nomor/Nama bank/Rek.. | `search` | text |  |  |

### Perilaku yang direplikasi

- Pembayaran massal ke banyak pemasok dalam satu dokumen.

