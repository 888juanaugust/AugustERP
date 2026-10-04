# Laporan

Modul sistem referensi `report`, 5 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- R-01 s.d. R-17: setiap laporan di Daftar Laporan punya parameter (periode, cabang, gudang, pelanggan, pemasok, akun) dan dapat diekspor ke Excel dan PDF; angka laporan selalu dihitung dari jurnal dan ledger, tidak disimpan.

## Pertanyaan terbuka

- Daftar Laporan dipindai sebagai katalog nama laporan; parameter tiap laporan dilengkapi pada Fase laporan dengan membuka satu per satu.

## Layar

- [Daftar Laporan](#daftar-laporan)
- [SPT PPN / PPNBM](#spt-ppn-ppnbm)
- [Analisa AI](#analisa-ai)
- [SPT PPh Ps.21](#spt-pph-ps21)
- [Bukti Potong PPh Ps.21](#bukti-potong-pph-ps21)

## Daftar Laporan

Rute di sistem referensi: `#accurate__report__report` · Jenis: laporan

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Lain-lain | `searchReport` | text |  |  |

### Perilaku yang direplikasi

- Katalog laporan sistem referensi per kelompok: Neraca (R-01, dengan kolom pembanding dan lancar/tidak lancar), Laba Rugi (R-02, per cabang), Neraca Saldo (R-03), Buku Besar (R-04), Arus Kas (R-05), Perubahan Modal (R-06), rincian penjualan per pelanggan/barang/penjual (R-07), pesanan belum dikirim/ditagih (R-08), umur piutang (R-09), rincian pembelian (R-10), PO belum diterima (R-11), umur hutang (R-12), kartu stok (R-13), nilai persediaan per gudang (R-14), mutasi kas & bank (R-15), jadwal penyusutan (R-16).

## SPT PPN / PPNBM

Rute di sistem referensi: `#accurate__report__spt-masa` · Jenis: laporan

### Daftar

**Kolom:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pelanggan

**Tombol:** eFaktur

### Tab: Pajak Keluaran

**Kolom:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pelanggan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tgl Pajak | `fromDate` | date | ya |  |
| s/d | `toDate` | date | ya |  |
| Tipe | `sptMasaType` | select |  | Pajak Masukan, Pajak Keluaran, Pajak Masukan dan Keluaran |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | SPT PPN/PPNBM |
| Pajak Keluaran | `documentCodeOutfilterView` | select |  | [Semua Tipe Transaksi], Faktur Pajak, Dokumen Tertentu, Ekspor, Digunggung |
| Pajak Keluaran | `searchTaxOutNumber` | text |  |  |

**Kolom rincian:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pelanggan

### Tab: Pajak Masukan

**Kolom:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pemasok

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tgl Pajak | `fromDate` | date | ya |  |
| s/d | `toDate` | date | ya |  |
| Tipe | `sptMasaType` | select |  | Pajak Masukan, Pajak Keluaran, Pajak Masukan dan Keluaran |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | SPT PPN/PPNBM |
| Pajak Masukan | `documentCodeInfilterView` | select |  | [Semua Tipe Transaksi], Faktur Pajak, Impor, Perolehan Dalam Negeri, Tidak dikreditkan, Digunggung |
| Pajak Masukan | `searchTaxInNumber` | text |  |  |

**Kolom rincian:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pemasok

### Perilaku yang direplikasi

- Rekap PPN keluaran dan masukan per masa pajak untuk SPT.

## Analisa AI

Rute di sistem referensi: `#accurate__report-insight-analysis` · Jenis: laporan

### Perilaku yang direplikasi

- Fitur analisa AI sistem referensi; tidak direplikasi.

## SPT PPh Ps.21

Rute di sistem referensi: `#accurate__report__formulir-1721-induk` · Jenis: laporan

### Daftar

**Tombol:** Cetak Formulir 1721 · e-PPh 2126

### Tab: Formulir 1721

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Periode | `monthTax` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | `yearTax` | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |
| Jenis SPT | `sptType` | select |  | Normal, Perbaikan |
| Tgl. Cetak | `printDate` | date |  |  |
| 1721-I Satu Masa |  | number |  |  |
| 1721-I Satu Tahun |  | number |  |  |
| 1721-II |  | number |  |  |
| 1721-V | `include1721V` | checkbox |  |  |
| 1721-III |  | number |  |  |
| 1721-IV |  | number |  |  |
| Termasuk SSP atau Pbk |  | number |  |  |
| Termasuk Surat Kuasa | `includeSuratKuasa` | checkbox |  |  |
| Nama Pemotong | `kuasaTaxName` | text |  |  |
| NPWP Pemotong | `kuasaTaxNpwpNo` | text |  |  |
| Termasuk Kelebihan SPT Sebelumnya | `includeOverPayment` | checkbox |  |  |
| Termasuk Kompensasi Kelebihan | `includeWillOverPay` | checkbox |  |  |

### Tab: Formulir 1721-I

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Periode | `monthTax` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | `yearTax` | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |
| Periode Pajak | `formulirType` | select |  | Satu Masa Pajak, Satu Tahun Pajak |

### Tab: Formulir 1721-II

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Periode | `monthTax` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | `yearTax` | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |

### Tab: Formulir 1721-IV

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Periode | `monthTax` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | `yearTax` | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |

### Perilaku yang direplikasi

- Formulir 1721 induk; penggajian di luar lingkup, formulir ini menyusul bila penggajian masuk.

## Bukti Potong PPh Ps.21

Rute di sistem referensi: `#accurate__report__formulir-1721-bukti-potong` · Jenis: laporan

### Daftar

**Tombol:** e-PPh 2126

### Tab: Formulir 1721-VI

### Perilaku yang direplikasi

- Bukti potong 1721; penggajian di luar lingkup.

