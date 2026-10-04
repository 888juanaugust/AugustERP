# Laporan sistem referensi

## Daftar Laporan

Rute: `#referensi__report__report`

**Judul layar:** Memorize · Keuangan · Neraca (Standar) · Buku Besar · Buku Besar (BIAYA OPERASIONAL) · Buku Besar (tribus) · Diskon Penjualan Final · Histori Buku Besar · Keseluruhan Jurnal (cek) · NERACA SALDO · Neraca Percobaan (tribus) · RINCIAN JURNAL PEMBAYARAN · Rincian Buku Besar · Rincian Buku Besar (BEBAN OPERASIONAL RANGKUMAN)) · Rincian Buku Besar (BEBAN OPERASIONAL) · Rincian Buku Besar (cek) · Rincian Buku Besar (tribus) · Kas & Bank · Histori Bank · Piutang · Faktur Belum Lunas · Faktur Belum Lunas (UPDATE PIUTANG UNTUK KOMISI) · Faktur Belum Lunas (UPDATE SPREADSHEET) · Faktur Belum Lunas per Pelanggan (PIUTANG MONTHLY) · PIUTANG JAWA TIMUR · Pembayaran Per Pelanggan Per Faktur untuk Hitung Komisi · Piutang per Pelanggan · Rata-rata Pembayaran Pelanggan (CEK PEMBAYARAN TOKO) · Rincian Penerimaan Penjualan (DAILY UPDATE PIUTANG) · Rincian Umur Piutang (DAILY)

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Lain-lain | text |  |  |

## SPT PPN / PPNBM

Rute: `#referensi__report__spt-masa`

**Judul layar:** Data Baru · Pajak Keluaran

**Kolom daftar:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pelanggan

**Tombol:** eFaktur

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Pajak | date | ya |  |
| s/d | date | ya |  |
| Tipe | select |  | Pajak Masukan, Pajak Keluaran, Pajak Masukan dan Keluaran |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | SPT PPN/PPNBM |
| Pajak Keluaran | select |  | [Semua Tipe Transaksi], Faktur Pajak, Dokumen Tertentu, Ekspor, Digunggung |
| Pajak Keluaran | text |  |  |

### Tab daftar: Pajak Keluaran

**Kolom:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pelanggan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Pajak | date | ya |  |
| s/d | date | ya |  |
| Tipe | select |  | Pajak Masukan, Pajak Keluaran, Pajak Masukan dan Keluaran |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | SPT PPN/PPNBM |
| Pajak Keluaran | select |  | [Semua Tipe Transaksi], Faktur Pajak, Dokumen Tertentu, Ekspor, Digunggung |
| Pajak Keluaran | text |  |  |


### Tab daftar: Pajak Masukan

**Kolom:** No. Faktur Pajak · No Faktur # · Tgl Pajak · Tipe Transaksi · Detail Transaksi · DPP · PPN · Pemasok

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tgl Pajak | date | ya |  |
| s/d | date | ya |  |
| Tipe | select |  | Pajak Masukan, Pajak Keluaran, Pajak Masukan dan Keluaran |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | SPT PPN/PPNBM |
| Pajak Masukan | select |  | [Semua Tipe Transaksi], Faktur Pajak, Impor, Perolehan Dalam Negeri, Tidak dikreditkan, Digunggung |
| Pajak Masukan | text |  |  |


## Analisa AI

Rute: `#referensi__report-insight-analysis`

## SPT PPh Ps.21

Rute: `#referensi__report__formulir-1721-induk`

**Tombol:** Cetak Formulir 1721 · e-PPh 2126

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Periode | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |
| Jenis SPT | select |  | Normal, Perbaikan |
| Tgl. Cetak | date |  |  |
| 1721-I Satu Masa | number |  |  |
| 1721-I Satu Tahun | number |  |  |
| 1721-II | number |  |  |
| 1721-V | checkbox |  |  |
| 1721-III | number |  |  |
| 1721-IV | number |  |  |
| Termasuk SSP atau Pbk | number |  |  |
| Termasuk Surat Kuasa | checkbox |  |  |
| Nama Pemotong | text |  |  |
| NPWP Pemotong | text |  |  |
| Termasuk Kelebihan SPT Sebelumnya | checkbox |  |  |
| Termasuk Kompensasi Kelebihan | checkbox |  |  |

### Tab daftar: Formulir 1721

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Periode | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |
| Jenis SPT | select |  | Normal, Perbaikan |
| Tgl. Cetak | date |  |  |
| 1721-I Satu Masa | number |  |  |
| 1721-I Satu Tahun | number |  |  |
| 1721-II | number |  |  |
| 1721-V | checkbox |  |  |
| 1721-III | number |  |  |
| 1721-IV | number |  |  |
| Termasuk SSP atau Pbk | number |  |  |
| Termasuk Surat Kuasa | checkbox |  |  |
| Nama Pemotong | text |  |  |
| NPWP Pemotong | text |  |  |
| Termasuk Kelebihan SPT Sebelumnya | checkbox |  |  |
| Termasuk Kompensasi Kelebihan | checkbox |  |  |


### Tab daftar: Formulir 1721-I

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Periode | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |
| Periode Pajak | select |  | Satu Masa Pajak, Satu Tahun Pajak |


### Tab daftar: Formulir 1721-II

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Periode | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |


### Tab daftar: Formulir 1721-IV

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Periode | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Periode | select |  | 2026, 2025, 2024, 2023, 2022, 2021 |


## Bukti Potong PPh Ps.21

Rute: `#referensi__report__formulir-1721-bukti-potong`

**Tombol:** e-PPh 2126

### Tab daftar: Formulir 1721-VI


