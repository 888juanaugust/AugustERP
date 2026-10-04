# Penjualan

Modul sistem referensi `sales`, 16 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- Rantai dokumen sistem referensi: Penawaran → Pesanan → Pengiriman (boleh sebagian, boleh beberapa) → Faktur (dari satu/beberapa pengiriman, atau langsung) → Penerimaan. Status pemenuhan (menunggu/sebagian/terproses/ditutup) diturunkan dari kuantitas.
- Harga jual diketik bebas (dengan hak), diskon per baris dan per dokumen, Biaya Lainnya ke akun mana pun, PPN termasuk/belum termasuk per dokumen.

## Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- HargaHanyaDariResolver: harga jual hanya dari resolver harga per pelanggan; harga ketik manual dimatikan.
- TagihSebelumKirim: faktur diterbitkan sebelum pengiriman (alur WebTransaction).
- ReservasiStok: stok dicadangkan saat pesanan disetujui, bukan hanya saat dikirim.
- PecahGudang: satu pesanan dipecah per gudang pengirim.
- BekuKredit dan PeringatanPiutang: pemberitahuan umur 120 hari dan beku transaksi setelah 150 hari (S-16).
- PortalPembeli: pelanggan memesan sendiri lewat portal dan membaca faktur serta surat jalannya.

## Layar

- [Penawaran Penjualan](#penawaran-penjualan)
- [Pesanan Penjualan](#pesanan-penjualan)
- [Pengiriman Pesanan](#pengiriman-pesanan)
- [Uang Muka Penjualan](#uang-muka-penjualan)
- [Faktur Penjualan](#faktur-penjualan)
- [Penerimaan Penjualan](#penerimaan-penjualan)
- [Retur Penjualan](#retur-penjualan)
- [Tukar Faktur](#tukar-faktur)
- [Kategori Pelanggan](#kategori-pelanggan)
- [Kategori Penjualan](#kategori-penjualan)
- [Pelanggan](#pelanggan)
- [Penyesuaian Harga/Diskon](#penyesuaian-hargadiskon)
- [Komisi Penjual](#komisi-penjual)
- [Target Penjualan](#target-penjualan)
- [SmartLink e-Commerce](#smartlink-e-commerce)
- [Check In](#check-in)

## Penawaran Penjualan

Rute di sistem referensi: `#accurate__customer__sales-quotation` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Dipesan oleh: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Dipesan oleh | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Penawaran Penjualan |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Salesman · Keterangan · Kts Terproses

**Tombol:** Ambil · Proses · %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Salesman · Keterangan · Kts Terproses

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |

#### Tab: Biaya Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Biaya Lainnya | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

### Perilaku yang direplikasi

- S-01: penawaran dengan harga dan diskon ketik, dapat diubah/dihapus, satu penawaran dapat menjadi beberapa pesanan (Ambil dari penawaran).

## Pesanan Penjualan

Rute di sistem referensi: `#accurate__customer__sales-order` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Total

**Saringan:** Tanggal: Semua · Dipesan oleh: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Dipesan oleh | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Pesanan # |  | checkbox | ya |  |
| No Pesanan # | `typeAutoNumber` | select | ya | Pesanan Penjualan |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Penjual · Gudang · Keterangan · Kts Terproses

**Tombol:** Ambil · Proses · %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Penjual · Gudang · Keterangan · Kts Terproses

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| No. PO | `poNumber` | text |  |  |
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

- S-02: pesanan dengan No. PO pelanggan, tanggal kirim, metode pengiriman, FOB, gudang per baris; pengiriman sebagian; tutup pesanan; ubah/hapus sesuai hak.

## Pengiriman Pesanan

Rute di sistem referensi: `#accurate__customer__delivery-order` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Pengiriman · Keterangan · Status

**Saringan:** Tanggal: Semua · Kirim ke: Semua · Pengiriman: Semua · Status: Semua

**Tombol:** Ambil · Faktur

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kirim ke | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| Pengiriman | `shipment` | lookup |  |  |
| No Pengiriman # |  | checkbox | ya |  |
| No Pengiriman # | `typeAutoNumber` | select | ya | Pengiriman Pesanan |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

**Tombol:** Ambil · Faktur

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| No. PO | `poNumber` | text |  |  |
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| FOB | `fob` | lookup |  |  |

### Perilaku yang direplikasi

- S-03: dokumen pengiriman tersendiri dengan nomor, menarik dari pesanan (Ambil), sebagian atau beberapa kali; stok keluar pada pengiriman; tombol Faktur membuat faktur dari pengiriman.

### Pertanyaan terbuka

- S-03/S-04: akun apa yang dipakai jurnal Pengiriman Pesanan (Barang Terkirim?) dan faktur yang menyusulnya?

## Uang Muka Penjualan

Rute di sistem referensi: `#accurate__customer__sales-downpayment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Umur (hr) · Total

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Proses

### Formulir baru

**Judul:** Data Baru · Uang Muka

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pelanggan | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Faktur # |  | checkbox | ya |  |
| No Faktur # | `typeAutoNumber` | select | ya | Faktur Penjualan, Uang Muka Penjualan |
| Uang Muka |  | number | ya |  |
| No. PO | `poNumber` | text |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |

**Tombol:** Proses

#### Tab: Uang Muka

_(tidak ada isian terbaca)_

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |

#### Tab: Informasi Pembayaran

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-06: faktur uang muka dengan PPN dan faktur pajaknya sendiri, dipotong pada faktur penjualan.

## Faktur Penjualan

Rute di sistem referensi: `#accurate__customer__sales-invoice` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Keterangan · Status · Umur (hr) · Total

**Saringan:** Pelanggan: Semua · Status: Semua · Sudah dicetak: Semua

**Tombol:** Ambil · Proses · %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pelanggan | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Faktur # |  | checkbox | ya |  |
| No Faktur # | `typeAutoNumber` | select | ya | Faktur Penjualan |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Penjual · Keterangan

**Tombol:** Ambil · Proses · %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Penjual · Keterangan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Syarat Pembayaran | `paymentTerm` | lookup |  |  |
| No. PO | `poNumber` | text |  |  |
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

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

#### Tab: Informasi Pembayaran

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-04: faktur dengan baris sendiri, dari satu/beberapa pengiriman atau langsung; tanggal mundur; diskon baris dan dokumen; Biaya Lainnya; PPN termasuk/belum.
- S-05: faktur tercatat dapat diubah/dihapus sesuai hak akses, kunci periode, dan pemblokir (sudah dilunasi, sudah direkonsiliasi, sudah ada faktur pajak).

## Penerimaan Penjualan

Rute di sistem referensi: `#accurate__customer__sales-receipt` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · No. Cek · Tanggal Cek · Pelanggan · Bank · Keterangan · Pakai Kredit · Nilai Pembayaran

**Saringan:** Tanggal: Semua · Metode Bayar: Semua · Tanggal Cek: Semua · Bank: Semua · Terima dari: Semua

### Formulir baru

**Judul:** Data Baru · Faktur

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Terima dari | `customer` | lookup | ya |  |
| Bank | `bank` | lookup | ya |  |
| Metode Bayar | `paymentMethod` | select |  | Tunai, Cek/Giro, Transfer Bank, EDC, Kartu Debit, Kartu Kredit, QRIS, Payment Link, Virtual Account, Dompet Digital, Non Tunai Lainnya |
| Nilai Pembayaran |  | number |  |  |
| No Bukti # |  | checkbox | ya |  |
| No Bukti # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Tgl Bayar | `transDate` | date | ya |  |
| Faktur | `searchDetailInvoice` | lookup | ya |  |

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran · Akun Diskon

#### Tab: Faktur

_(tidak ada isian terbaca)_

**Kolom rincian:** No. Faktur · Tgl Faktur · Total Faktur · Terhutang · Bayar · Diskon · Pembayaran · Akun Diskon

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- S-07: penerimaan dengan nomor dokumen, melunasi banyak faktur sekaligus, diskon pelunasan, penghapusan selisih (write-off), giro; faktur berstatus lunas hanya lewat alokasi penerimaan.

## Retur Penjualan

Rute di sistem referensi: `#accurate__customer__sales-return` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Pelanggan · Keterangan · Total

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Tipe Pengembalian: Semua · Sudah dicetak: Semua

**Tombol:** %

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Pelanggan | `customer` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| Retur dari | `returnType` | select | ya | Faktur, Pengiriman, Tanpa Faktur, Uang Muka |
| Retur dari | `invoice` | lookup | ya |  |
| No Retur # |  | checkbox | ya |  |
| No Retur # | `typeAutoNumber` | select | ya | Retur Penjualan |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

**Tombol:** %

#### Tab: Rincian Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Satuan · Diskon % · Total Harga · Gudang · Keterangan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Dari Alamat | `toAddress` | textarea |  |  |
| Keterangan | `description` | textarea |  |  |
| Kena Pajak | `taxable` | checkbox |  |  |
| Total termasuk Pajak | `inclusiveTax` | checkbox |  |  |

#### Tab: Biaya Lainnya

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Biaya · Kode # · Jumlah

### Perilaku yang direplikasi

- S-08: retur merujuk faktur (atau tanpa faktur), barang kembali ke gudang, nota kredit otomatis.

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- PemisahanTugas: retur diajukan sales, diposting Inventori (barang-sudah-kembali), tidak oleh pengajunya; final setelah diposting.

## Tukar Faktur

Rute di sistem referensi: `#accurate__customer__exchange-invoice` · Jenis: layar

### Daftar

**Kolom:** Pelanggan · Tanggal · Tgl Tukar · Nomor # · Status · Total Faktur

**Saringan:** Tanggal: Semua · Pelanggan: Semua · Tgl Tukar: Semua · Status: Semua

### Formulir baru

**Judul:** Data Baru · Rincian Faktur

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Mata Uang | `currency` | select | ya | (1 data) |
| Tgl Tukar | `collectDate` | date | ya |  |
| Jatuh Tempo | `dueDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Tukar Faktur |

**Kolom rincian:** Pelanggan · No. Faktur · Tgl Faktur · Jatuh Tempo

**Tombol:** Ambil

#### Tab: Rincian Faktur

_(tidak ada isian terbaca)_

**Kolom rincian:** Pelanggan · No. Faktur · Tgl Faktur · Jatuh Tempo

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- Tanda terima tukar faktur: beberapa faktur diserahkan ke pelanggan untuk dijadwalkan pembayarannya.

## Kategori Pelanggan

Rute di sistem referensi: `#accurate__customer__customer-category` · Jenis: layar

### Daftar

**Kolom:** Nama Kategori · Kategori Default

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Kategori | `name` | text | ya |  |
| Ya | `isDefault` | checkbox |  |  |
| Sub Kategori | `sub` | checkbox |  |  |

#### Tab: Kategori Pelanggan

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-10: kategori pelanggan bebas (menggantikan jenis usaha tetap bengkel/toko/distributor).

## Kategori Penjualan

Rute di sistem referensi: `#accurate__customer__price-category` · Jenis: layar

### Daftar

**Kolom:** Keterangan · Nama Kategori

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Kategori | `name` | text | ya |  |
| Keterangan | `notes` | textarea |  |  |

#### Tab: Kategori Penjualan

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-12: kategori/level harga jual per pelanggan; harga per barang per kategori.

## Pelanggan

Rute di sistem referensi: `#accurate__customer__customer` · Jenis: layar

### Daftar

**Kolom:** Nama · Kontak Utama · ID Pelanggan · Kategori · Kategori Penjualan · Kategori Diskon · Alamat Pajak · Cabang · Alamat Utama · Syarat Pembayaran

**Saringan:** Non Aktif: Tidak · Kategori: Semua · Cabang: GUDANG B, Kantor P...

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| ID Pelanggan |  | checkbox | ya |  |
| ID Pelanggan | `typeAutoNumber` | select | ya | (1 data) |
| Kategori | `category` | lookup |  |  |
| No. Telp. Bisnis | `workPhone` | text |  |  |
| Handphone | `mobilePhone` | text |  |  |
| No. WhatsApp | `bbmPin` | text |  |  |
| Email | `email` | text |  |  |
| Faximili | `fax` | text |  |  |
| Website | `website` | text |  |  |
| Dipakai di Cabang | `branch` | select |  | (4 data) |

#### Tab: Umum

_(tidak ada isian terbaca)_

#### Tab: Kontak

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Lengkap · Posisi Jabatan · Email · Handphone

#### Tab: Pengiriman

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Sama dengan alamat penagihan | `shipSameAsBill` | checkbox |  |  |
| Sama dengan alamat penagihan | `shipStreet` | textarea |  |  |
| Sama dengan alamat penagihan | `shipCity` | text |  |  |
| Sama dengan alamat penagihan | `shipZipCode` | text |  |  |
| Sama dengan alamat penagihan | `shipProvince` | text |  |  |
| Sama dengan alamat penagihan | `shipCountry` | text |  |  |

**Kolom rincian:** Alamat

#### Tab: Penjualan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kategori Diskon | `discountCategory` | lookup |  |  |
| Default Penjual | `salesmanList` | lookup |  |  |
| Default Diskon (%) | `defaultSalesDisc` | text |  |  |
| Default Deskripsi | `defaultInvoiceDesc` | text |  |  |
| Piutang | `customerReceivableAccountList` | lookup |  |  |
| Uang muka | `customerDownPaymentAccountList` | lookup |  |  |
| Penjualan | `salesAccount` | lookup |  |  |
| Diskon Barang | `itemDiscountAccount` | lookup |  |  |
| Beban Pokok Penjualan | `costOfGoodsSoldAccount` | lookup |  |  |
| Retur Penjualan | `salesReturnAccount` | lookup |  |  |
| Diskon Penjualan | `salesDiscountAccount` | lookup |  |  |

#### Tab: Pajak

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Default Total Faktur sudah termasuk Pajak | `defaultIncTax` | checkbox |  |  |
| Tipe ID Pajak | `wpType` | select |  | NIK, NPWP, Passpor, Lainnya |
| Nomor Wajib Pajak | `wpNumber` | text |  |  |
| Nama Wajib Pajak | `wpName` | text |  |  |
| NITKU | `nitku` | text |  |  |
| Kode Negara | `countryTax` | lookup |  |  |
| Tipe Transaksi | `documentCode` | select |  | Faktur Pajak, Dokumen Tertentu, Ekspor, Digunggung |
| Sama dengan alamat penagihan | `taxSameAsBill` | checkbox |  |  |
| Sama dengan alamat penagihan | `taxStreet` | textarea |  |  |
| Sama dengan alamat penagihan | `taxCity` | text |  |  |
| Sama dengan alamat penagihan | `taxZipCode` | text |  |  |
| Sama dengan alamat penagihan | `taxProvince` | text |  |  |
| Sama dengan alamat penagihan | `taxCountry` | text |  |  |

#### Tab: Saldo Piutang

_(tidak ada isian terbaca)_

**Kolom rincian:** Tanggal · Jumlah · Mata Uang · Syarat Pembayaran · Nomor # · Keterangan

#### Tab: Lain-lain

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Per Pelanggan | `groupCustomerLimit` | radio |  |  |
| Jika ada faktur dengan umur lebih dari | `customerLimitAge` | checkbox |  |  |
| Jika total piutang & pesanan melebihi | `customerLimitAmount` | checkbox |  |  |
| Tergabung ke Pelanggan Induk | `groupCustomerLimit` | radio |  |  |
| Gudang Default | `defaultWarehouse` | lookup |  |  |
| Catatan | `notes` | textarea |  |  |

### Perilaku yang direplikasi

- S-09: pelanggan dengan beberapa alamat (tagihan dan kirim), kategori, syarat pembayaran bawaan, batas kredit, penjual, NPWP/NIK/ID TKU untuk faktur pajak, diskon bawaan, termasuk pajak bawaan, saldo awal piutang.

## Penyesuaian Harga/Diskon

Rute di sistem referensi: `#accurate__inventory__sellingprice-adjustment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Mulai Berlaku · Kategori Penjualan · Keterangan · Tanggal Berakhir · Tipe Penyesuaian · #

**Saringan:** Tanggal: Semua · Non Aktif: Semua · Kategori Penjualan: Semua · Tipe Penyesuaian: Semua

**Tombol:** Rincian

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kategori Penjualan | `priceCategory` | lookup | ya |  |
| Tipe Penyesuaian | `salesAdjustmentType` | select |  | Harga, Diskon (%) |
| Mulai Berlaku | `transDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Penyesuaian Harga/Diskon |
| Rincian Barang | `searchDetailItem` | lookup | ya |  |

**Kolom rincian:** No. · Nama Barang · Kode Barang · Satuan · Harga Baru

**Tombol:** Rincian

### Perilaku yang direplikasi

- S-13: penyesuaian harga jual dan diskon massal per kategori harga, per barang, berlaku per tanggal (versi daftar harga).

## Komisi Penjual

Rute di sistem referensi: `#accurate__company__salesman-commission` · Jenis: layar

### Daftar

**Kolom:** Catatan · Nama · Periode Berlaku

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Selamanya | `activePeriod` | radio |  |  |
| Periode Tertentu | `activePeriod` | radio |  |  |
| Nama perhitungan komisi | `name` | text | ya |  |
| Semua | `rbsalesmantarget` | radio |  |  |
| Tertentu | `rbsalesmantarget` | radio |  |  |
| Pertama | `forLevel1` | checkbox |  |  |
| Kedua | `forLevel2` | checkbox |  |  |
| Ketiga | `forLevel3` | checkbox |  |  |
| Keempat | `forLevel4` | checkbox |  |  |
| Kelima | `forLevel5` | checkbox |  |  |
| Tanpa batasan dan syarat | `rbrequirement` | radio |  |  |
| Nilai Penjualan antara | `rbrequirement` | radio |  |  |
| Nilai Penjualan antara |  | number |  |  |
| s/d |  | number |  |  |
| Kuantitas penjualan antara | `rbrequirement` | radio |  |  |
| Kuantitas penjualan antara |  | number |  |  |
| s/d |  | number |  |  |
| Kuantitas terjual per | `rbrequirement` | radio |  |  |
| Kuantitas terjual per |  | number |  |  |
| Akan mendapat komisi | `gain` | select | ya | Persentase, Nilai Tetap |
| Akan mendapat komisi |  | number | ya |  |
| % dari | `gainCalculatedBy` | select |  | Nilai Penjualan, Laba Kotor |

#### Tab: Komisi Penjual

_(tidak ada isian terbaca)_

#### Tab: Lain-lain

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Skema komisi penjual sistem referensi (persentase per penjual/barang).

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- Komisi: komisi WebTransaction atas faktur lunas dengan target dan tarif yang ditetapkan Finance/Owner tetap berlaku sebagai skema tambahan.

## Target Penjualan

Rute di sistem referensi: `#accurate__budget-target__sales-target` · Jenis: layar

### Daftar

**Kolom:** Dari Tanggal · S/d Tanggal · Tahun · Nama · Cabang

**Saringan:** Tipe Target: Per Kategori Baran...

### Formulir baru

**Judul:** Data Baru · Rincian Barang

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Target | `name` | text | ya |  |
| Tipe Target | `salutation` | select |  | Per Barang, Per Kategori Barang, Per Penjual, Per Bulan |
| Penjualan Cabang | `branch` | select |  | (4 data) |
| Dari Tanggal | `fromDate` | date |  |  |
| S/d Tanggal | `toDate` | date | ya |  |
| Rincian Barang | `searchDetailItem` | lookup |  |  |

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Nilai

#### Tab: Target Per Barang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Barang · Kode # · Kuantitas · Nilai

#### Tab: Catatan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Catatan | `notes` | textarea |  |  |
| Penganalisa | `analystName` | text |  |  |

### Perilaku yang direplikasi

- Target penjualan per penjual per periode, dibandingkan realisasi di dasbor.

## SmartLink e-Commerce

Rute di sistem referensi: `#accurate__customer__ecommerce-setting` · Jenis: layar

### Daftar

**Kolom:** Nama e-Commerce · Nama Toko

**Saringan:** Nama e-Commerce: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Toko | `ecommerceType` | select | ya | (7 data) |
| Nama Toko | `shopName` | text | ya |  |
| Penjualan atas Cabang | `branch` | lookup | ya |  |
| Kas/Bank Saldo e-Commerce | `paymentBankAccount` | lookup |  |  |
| Ya. Nilai penjualan sudah termasuk nilai PPN | `taxable` | checkbox |  |  |
| Selesai | `importDoneTransaction` | radio |  |  |
| Semua Status | `importDoneTransaction` | radio |  |  |

#### Tab: Umum

_(tidak ada isian terbaca)_

#### Tab: Ongkir dan Fee

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Masukkan Biaya Kurir ke Faktur Penjualan | `importCourierCost` | checkbox |  |  |

#### Tab: Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Gudang Penjualan | `warehouse` | lookup | ya |  |

### Perilaku yang direplikasi

- Integrasi marketplace sistem referensi; tidak direplikasi.

## Check In

Rute di sistem referensi: `#accurate__customer__sales-check-in` · Jenis: layar

### Daftar

**Kolom:** Tanggal · Nomor # · Nama Pelanggan (Saat Check In) · Sales · Transaksi

**Saringan:** Tanggal: Semua · Sales: Semua

### Formulir baru

_(tidak ada isian terbaca)_

**Kolom rincian:** Tanggal · Nomor # · Nama Pelanggan (Saat Check In) · Sales · Transaksi

### Perilaku yang direplikasi

- Check-in kunjungan penjual ke pelanggan (lokasi, waktu).

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- KunjunganToko: log kunjungan toko WebTransaction dengan catatan dan pesanan dari kunjungan.

