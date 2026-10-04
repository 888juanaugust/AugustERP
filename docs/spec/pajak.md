# Pajak

Modul sistem referensi `smartlink-tax`, 3 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- T-02: ekspor faktur pajak keluaran ke format impor Coretax (XML TaxInvoiceBulk) dan e-Faktur lama (CSV) tetap tersedia; NSFP yang kembali disimpan pada faktur.

## Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- EksporCoretax: ekspor XML Coretax WebTransaction (CoretaxXmlWriter) dipertahankan sebagai jalur ekspor.

## Layar

- [e-Faktur CTAS](#e-faktur-ctas)
- [Email Faktur Pajak](#email-faktur-pajak)
- [e-Faktur Legacy](#e-faktur-legacy)

## e-Faktur CTAS

Rute di sistem referensi: `company__efaktur-ctas` · Jenis: layar

### Daftar

**Kolom:** Tgl Pajak · Nomor Transaksi · No Faktur Pajak · DPP · PPN · Dokumen/Transaksi · Status · Koreksi · NPWP/NIK · Nama · Informasi

**Tombol:** Unggah 0 Data

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| - | `ppnType` | select |  | PPN Keluaran, PPN Masukan, Preopulated Pajak Masukan |
| - | `ppnDocumentType` | select |  | Semua, Faktur Pajak, Digunggung |
| - | `ppnStatus` | select |  | Semua, Draf / Gagal, Draf, Diproses, Terkirim, Berhasil, Gagal, Abaikan |
| - | `branch` | select |  | [Semua Cabang], Kantor Pusat, GUDANG B, MAKASSAR |
| - | `dayFrom` | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | `dayTo` | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | `ppnMonth` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| - | `ppnYear` | select |  | 2025, 2026, 2027 |
| - | `searchEfaktur` | text |  |  |

### Perilaku yang direplikasi

- T-02: pemilihan faktur, pembuatan berkas Coretax, pencatatan nomor seri faktur pajak kembali.

## Email Faktur Pajak

Rute di sistem referensi: `customer__efaktur-send` · Jenis: layar

### Daftar

**Tombol:** Email Faktur Pajak

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari/Pilih Pelanggan... | `customer` | lookup |  |  |
| Cari/Pilih Faktur... | `invoice` | lookup |  |  |

### Perilaku yang direplikasi

- Pengiriman faktur pajak ke pelanggan lewat email; pengiriman email di luar lingkup awal.

## e-Faktur Legacy

Rute di sistem referensi: `company__efaktur-online` · Jenis: layar

### Daftar

**Kolom:** Tgl Pajak · No Faktur Pajak · DPP · PPN · Dokumen/Transaksi · Status · Koreksi · NPWP/NIK · Nama · Informasi

**Tombol:** Unggah 0 Data

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| - | `ppnType` | select |  | PPN Keluaran, PPN Masukan, Nota Retur Penjualan, Nota Retur Pembelian, Prepopulated Masukan, Prepopulated PEB, Prepopulated PIB, Prepopulated Cukai, Prepopulated SPPB |
| - | `ppnDocumentType` | select |  | Semua, Faktur Pajak, Dokumen Dipersamakan FP, Dokumen Ekspor (PEB), Digunggung |
| - | `ppnStatus` | select |  | Semua, Draf / Gagal, Draf, Diproses, Berhasil, Gagal, Abaikan |
| - | `branch` | select |  | [Semua Cabang], Kantor Pusat, GUDANG B, MAKASSAR |
| - | `dayFrom` | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | `dayTo` | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | `ppnMonth` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| - | `ppnYear` | select |  | 2020, 2021, 2022, 2023, 2024, 2025 |
| - | `searchEfaktur` | text |  |  |

### Perilaku yang direplikasi

- T-02: format CSV e-Faktur lama, dipertahankan agar pelaporan lama dapat direproduksi.

