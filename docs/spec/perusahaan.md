# Perusahaan

Modul sistem referensi `company`, 14 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- X-01 Info perusahaan (nama, NPWP, alamat, rekening) adalah data master yang dicetak pada setiap dokumen dan faktur pajak.

## Layar

- [Mata Uang](#mata-uang)
- [Cabang](#cabang)
- [Pajak](#pajak)
- [Syarat Pembayaran](#syarat-pembayaran)
- [Pengiriman](#pengiriman)
- [FOB](#fob)
- [Gaji/Tunjangan](#gajitunjangan)
- [Karyawan](#karyawan)
- [Transaksi Berulang](#transaksi-berulang)
- [Proses Akhir Bulan](#proses-akhir-bulan)
- [Kontak](#kontak)
- [Transaksi Favorit](#transaksi-favorit)
- [Kalender](#kalender)
- [Log Aktifitas](#log-aktifitas)

## Mata Uang

Rute di sistem referensi: `#accurate__company__currency` · Jenis: layar

### Daftar

**Kolom:** Simbol · Kode · Negara/Nama

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Negara/Nama | `base` | lookup | ya |  |

#### Tab: Mata Uang

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- X-11: di luar lingkup; satu mata uang, IDR.

## Cabang

Rute di sistem referensi: `#accurate__company__branch` · Jenis: layar

### Daftar

**Kolom:** Non Aktif · Nama · Daftar Pengguna

**Saringan:** Non Aktif: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Pajak · Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| No. Telepon | `phoneNumber` | text |  |  |
| NITKU | `nitku` | text |  |  |

#### Tab: Cabang

_(tidak ada isian terbaca)_

#### Tab: Daftar Pengguna

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Semua Pengguna | `usedAllUser` | checkbox |  |  |

### Perilaku yang direplikasi

- G-10: cabang adalah penanda pada dokumen dan baris jurnal dalam satu pembukuan; laporan dapat disaring per cabang.
- Dokumen yang dipecah antar gudang memakai cabang gudang pengirim (lihat saklar PecahGudang).

## Pajak

Rute di sistem referensi: `#accurate__company__tax` · Jenis: layar

### Daftar

**Kolom:** Keterangan · Tipe Pajak · Persentase

**Saringan:** Tipe Pajak: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tipe Pajak | `taxType` | select | ya | - Pilih Tipe Pajak -, Pajak Pertambahan Nilai, Pajak Pertambahan Barang Mewah, Pajak Penghasilan Ps.4(2), Pajak Penghasilan Ps.15, Pajak Penghasilan Ps.21, Pajak Penghasilan Ps.22, Pajak Penghasilan Ps.23 |
| Keterangan | `description` | text | ya |  |
| Akun Pajak Penjualan | `salesTaxGlAccount` | lookup | ya |  |
| Akun Pajak Pembelian | `purchaseTaxGlAccount` | lookup | ya |  |

#### Tab: Pajak

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- T-01: kode pajak per baris dengan tarif dan akun; harga dapat termasuk atau belum termasuk pajak (saklar Total termasuk Pajak di setiap dokumen). PPN 12% dengan DPP 11/12 (PMK 131/2024) menjadi satu kode pajak bawaan, bukan konstanta.
- T-03: PPh pemotongan sebagai kode pajak terpisah.

### Pertanyaan terbuka

- T-01: perubahan logika pajak dikonfirmasi ke akuntan sebelum digabung.
- T-03: apakah PPh pemotongan dipakai oleh bisnis ini?

## Syarat Pembayaran

Rute di sistem referensi: `#accurate__company__payment-term` · Jenis: layar

### Daftar

**Kolom:** Nama · Diskon (%) · Masa Diskon (hari) · Masa Jatuh Tempo (hari) · Keterangan · Non Aktif · Default

**Saringan:** Non Aktif: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Jika membayar antara |  | number |  |  |
| Akan mendapat diskon |  | number |  |  |
| Masa Jatuh Tempo |  | number |  |  |
| Keterangan | `memo` | textarea |  |  |
| Ya | `isDefault` | checkbox |  |  |

#### Tab: Syarat Pembayaran

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-14: syarat pembayaran bernama dengan jatuh tempo (hari), diskon pelunasan awal (mis. 2/10 n/30) dan masa diskon; dipilih pada pelanggan dan dokumen.

## Pengiriman

Rute di sistem referensi: `#accurate__company__shipment` · Jenis: layar

### Daftar

**Kolom:** Nama · PIC · No. Telp · Alamat Lengkap · Non Aktif

**Saringan:** Non Aktif: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum · Info Lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| PIC | `picName` | text |  |  |
| No. Telp | `picPhoneNumber` | text |  |  |

#### Tab: Pengiriman

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- S-15: metode pengiriman/ekspedisi sebagai data master, dipilih pada pesanan dan pengiriman.

## FOB

Rute di sistem referensi: `#accurate__company__freeonboard` · Jenis: layar

### Daftar

**Kolom:** Nama

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |

#### Tab: FOB

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Syarat penyerahan (FOB) sebagai data master pada pesanan dan pengiriman.

## Gaji/Tunjangan

Rute di sistem referensi: `#accurate__company__employee-fee` · Jenis: layar

### Daftar

**Kolom:** Nama · Tipe Gaji/Tunjangan · Non Aktif

**Saringan:** Non Aktif: Semua · Tipe Gaji/Tunjangan: Semua

### Formulir baru

**Judul:** Data Baru · Info Umum

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama | `name` | text | ya |  |
| Tipe Gaji/Tunjangan | `feeType` | select |  | Gaji/Pensiun atau THT/JHT, Tunjangan PPh, Subsidi PPh, Tunjangan Lainnya, Uang lembur dan sebagainya, Tunjangan Jaminan Kecelakaan Kerja, Jaminan Kematian, Honorarium dan Imbalan lain sejenisnya, Premi asuransi kesehatan yang dibayarkan pemberi kerja, Penerimaan dalam bentuk natura dan kenikmatan lainnya, Tantiem, Bonus, Rapel, Gratifikasi, Jasa Produksi dan THR, Tunjangan Iuran Pensiun/THT/JHT dibayarkan Pemberi Kerja, Potongan Gaji (Tidak Mengurangi PPh), Pengurangan Gaji (Mengurangi PPh), Premi asuransi kesehatan dibayarkan pekerja, Iuran Pensiun/THT/JHT dibayarkan Pekerja |
| Akun Beban | `expenseAccount` | lookup | ya |  |

#### Tab: Gaji/Tunjangan

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Komponen gaji dan tunjangan; dasar untuk Pencatatan Gaji di Buku Besar. Penggajian di luar lingkup replikasi.

## Karyawan

Rute di sistem referensi: `#accurate__company__employee` · Jenis: layar

### Daftar

**Kolom:** Nama · Posisi Jabatan · Email · Handphone · ID Karyawan · Status PTKP · Status Pekerja · Utang

**Saringan:** Non Aktif: Semua · Penjual: Semua · Status Pekerja: Semua

### Formulir baru

**Judul:** Data Baru · Data Pribadi · Data Karyawan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Lengkap | `salutation` | select | ya | (3 data) |
| Nama Lengkap | `name` | text | ya |  |
| No. KTP | `nikNo` | text |  |  |
| Email | `email` | text |  |  |
| Handphone | `mobilePhone` | text |  |  |
| No. Telp. Bisnis | `workPhone` | text |  |  |
| No. Telp. Rumah | `homePhone` | text |  |  |
| No. WhatsApp | `bbmPin` | text |  |  |
| Website | `website` | text |  |  |
| Kewarganegaraan | `domisiliType` | select |  | Afrika Selatan, Aljazair, Amerika Serikat, Australia, Austria, Bangladesh, Belanda, Belgia, Brunei Darussalam, Bulgaria, China, Denmark, Finlandia, Hong Kong, Hungaria, India, Indonesia, Inggris, Iran, Italia, Jepang, Jerman, Kanada, Korea Selatan, Korea Utara, Kuwait, Luxembourg, Malaysia, Maroko, Mesir, Mongolia, Norwegia, Pakistan, Perancis, Philipina, Polandia, Portugal, Qatar, Republik Ceko, Romania |
| ID Karyawan |  | checkbox | ya |  |
| ID Karyawan | `typeAutoNumber` | select | ya | (1 data) |
| Posisi Jabatan | `position` | text |  |  |
| Tgl Masuk | `joinDate` | date |  |  |
| Cabang | `branch` | select |  | (4 data) |
| Ya | `salesman` | checkbox |  |  |
| Catatan | `notes` | textarea |  |  |

#### Tab: Karyawan

_(tidak ada isian terbaca)_

#### Tab: Alamat

_(tidak ada isian terbaca)_

#### Tab: Pajak Penghasilan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ya | `pph` | checkbox |  |  |
| No. NPWP | `npwpNo` | text |  |  |
| Status Pekerja | `employeeWorkStatus` | select |  | Pegawai Tetap, Pegawai Tidak Tetap, Bukan Pegawai - Distributor MLM, Bukan Pegawai - Petugas Dinas Luar Asuransi, Bukan Pegawai - Penjaja Barang Dagangan, Bukan Pegawai - Tenaga Ahli, Anggota Dewan Komisaris atau dewan pengawas, Bukan Pegawai yang menerima imbalan yang bersifat berkesinambungan, Bukan Pegawai yang menerima imbalan yang tidak bersifat berkesinambungan |
| Status PTKP | `employeeTaxStatus` | select |  | Tidak Kawin (Tidak ada tanggungan), Tidak Kawin (1 Tanggungan), Tidak Kawin (2 Tanggungan), Tidak Kawin (3 Tanggungan), Kawin (Tidak ada tanggungan), Kawin (1 Tanggungan), Kawin (2 Tanggungan), Kawin (3 Tanggungan) |
| PPh mulai dihitung | `startMonthPayment` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| PPh mulai dihitung | `startYearPayment` | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Penghasilan Sebelumnya |  | number |  |  |
| PPh Sebelumnya |  | number |  |  |

#### Tab: Rekening Gaji

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nama Bank | `bank` | lookup |  |  |
| No Rekening | `bankAccount` | text |  |  |
| Atas Nama Rekening | `bankAccountName` | text |  |  |

### Perilaku yang direplikasi

- S-11: penjual (salesman) adalah karyawan yang dicantumkan pada dokumen penjualan, bukan hanya pada pelanggan.

### Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- Komisi: komisi penjual dihitung atas faktur yang lunas, dengan target per penjual (lihat Penjualan/Komisi Penjual dan Target Penjualan).

## Transaksi Berulang

Rute di sistem referensi: `#accurate__company__recurring` · Jenis: layar

### Daftar

**Kolom:** Kategori · Nama · Tipe Transaksi · Status

**Saringan:** Tipe Transaksi: Semua · Kategori: Semua · Status: Semua

**Tombol:** Jalankan

### Formulir baru

_(tidak ada isian terbaca)_

**Kolom rincian:** Kategori · Nama · Tipe Transaksi · Status

**Tombol:** Jalankan

### Perilaku yang direplikasi

- Jadwal transaksi berulang (faktur, pembayaran) yang dibuatkan otomatis pada tanggalnya.

## Proses Akhir Bulan

Rute di sistem referensi: `#accurate__company__period-end` · Jenis: layar

### Daftar

**Kolom:** Nama · Tanggal Input · Keterangan

**Saringan:** Bulan: Semua · Tahun: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Bulan | `monthPE` | select | ya | [Pilih Bulan], Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Tahun | `yearPE` | select |  | 2027, 2026, 2025, 2024, 2023, 2022, 2021, 2020 |

**Kolom rincian:** Nama Mata Uang · Nilai Tukar

#### Tab: Nilai Tukar Mata Uang

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Mata Uang · Nilai Tukar

### Perilaku yang direplikasi

- G-09: proses akhir bulan/tutup periode mengunci tanggal dan menghitung ulang; kunci periode berlaku untuk tanggal lama dan baru pada setiap perubahan dokumen.

### Pertanyaan terbuka

- G-09: apakah laba ditahan dibentuk oleh jurnal penutup, atau dihitung?

## Kontak

Rute di sistem referensi: `#accurate__company__contact` · Jenis: layar

### Daftar

**Kolom:** Nama Lengkap · Tipe · Perusahaan · Email · Handphone

**Saringan:** Tipe: Semua

### Formulir baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ketik dan [Enter] | `keyword` | text |  |  |

**Kolom rincian:** Nama Lengkap · Tipe · Perusahaan · Email · Handphone

### Perilaku yang direplikasi

- Kontak umum (bukan pelanggan/pemasok) sebagai data master.

## Transaksi Favorit

Rute di sistem referensi: `#accurate__company__memorize-transaction` · Jenis: layar

### Daftar

**Kolom:** Nama Favorit · Tipe Transaksi · Daftar Pengguna

**Saringan:** Tipe Transaksi: Semua

### Formulir baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Ketik dan [Enter] | `keyword` | text |  |  |

**Kolom rincian:** Nama Favorit · Tipe Transaksi · Daftar Pengguna

### Perilaku yang direplikasi

- Templat transaksi yang disimpan untuk dipakai ulang (memorize).

## Kalender

Rute di sistem referensi: `#accurate__company__calendar` · Jenis: layar

### Daftar

**Kolom:** Sen · Sel · Rab · Kam · Jum · Sab · Min

**Tombol:** hari ini · Bulan · Minggu · Hari · Subscribe · Buat Baru

### Formulir baru

**Judul:** Oktober 2026

_(tidak ada isian terbaca)_

**Kolom rincian:** Sen · Sel · Rab · Kam · Jum · Sab · Min

**Kolom rincian:** Sen · Sel · Rab · Kam · Jum · Sab · Min

**Tombol:** hari ini · Bulan · Minggu · Hari · Subscribe · Buat Baru

### Perilaku yang direplikasi

- Kalender kegiatan dan pengingat jatuh tempo.

## Log Aktifitas

Rute di sistem referensi: `#accurate__company__audit` · Jenis: layar

### Daftar

**Kolom:** Tgl Transaksi · No/Nama Referensi · Tipe Tindakan · Tipe Transaksi · Tanggal · Pengguna · Email · Alamat IP

**Saringan:** Tgl Transaksi: Semua · Tanggal: Semua · Tipe Transaksi: Semua · Pengguna: Semua · Tipe Tindakan: Semua

### Formulir baru

_(tidak ada isian terbaca)_

**Kolom rincian:** Tgl Transaksi · No/Nama Referensi · Tipe Tindakan · Tipe Transaksi · Tanggal · Pengguna · Email · Alamat IP

### Perilaku yang direplikasi

- X-08: riwayat aktivitas per pengguna dan dokumen; August's ERP menyimpannya sebagai log audit append-only ditambah revisi dokumen (sebelum/sesudah) untuk setiap ubah/hapus.

