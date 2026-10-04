# Pajak

## e-Faktur CTAS

Rute: `#referensi__company__efaktur-ctas`

**Judul layar:** JAVA INDO

**Kolom daftar:** Tgl Pajak · Nomor Transaksi · No Faktur Pajak · DPP · PPN · Dokumen/Transaksi · Status · Koreksi · NPWP/NIK · Nama · Informasi

**Tombol:** Unggah 0 Data

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| - | select |  | PPN Keluaran, PPN Masukan, Preopulated Pajak Masukan |
| - | select |  | Semua, Faktur Pajak, Digunggung |
| - | select |  | Semua, Draf / Gagal, Draf, Diproses, Terkirim, Berhasil, Gagal, Abaikan |
| - | select |  | [Semua Cabang], Kantor Pusat, GUDANG B, MAKASSAR |
| - | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| - | select |  | 2025, 2026, 2027 |
| - | text |  |  |

## Email Faktur Pajak

Rute: `#referensi__customer__efaktur-send`

**Tombol:** Email Faktur Pajak

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari/Pilih Pelanggan... | lookup |  |  |
| Cari/Pilih Faktur... | lookup |  |  |

## e-Faktur Legacy

Rute: `#referensi__company__efaktur-online`

**Judul layar:** JAVA INDO

**Kolom daftar:** Tgl Pajak · No Faktur Pajak · DPP · PPN · Dokumen/Transaksi · Status · Koreksi · NPWP/NIK · Nama · Informasi

**Tombol:** Unggah 0 Data

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| - | select |  | PPN Keluaran, PPN Masukan, Nota Retur Penjualan, Nota Retur Pembelian, Prepopulated Masukan, Prepopulated PEB, Prepopulated PIB, Prepopulated Cukai, Prepopulated SPPB |
| - | select |  | Semua, Faktur Pajak, Dokumen Dipersamakan FP, Dokumen Ekspor (PEB), Digunggung |
| - | select |  | Semua, Draf / Gagal, Draf, Diproses, Berhasil, Gagal, Abaikan |
| - | select |  | [Semua Cabang], Kantor Pusat, GUDANG B, MAKASSAR |
| - | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | select |  | 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 |
| - | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| - | select |  | 2020, 2021, 2022, 2023, 2024, 2025 |
| - | text |  |  |

