# Buku Besar

Modul sistem referensi `general-ledger`, 9 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- G-01 s.d. G-05: daftar akun dapat disunting dengan tipe akun sistem referensi, saldo awal per akun, dan preferensi akun bawaan (piutang, hutang, persediaan, HPP, pajak) yang dipakai lapisan posting.

## Layar

- [Akun Perkiraan](#akun-perkiraan)
- [Pencatatan Beban](#pencatatan-beban)
- [Pencatatan Gaji](#pencatatan-gaji)
- [Jurnal Umum](#jurnal-umum)
- [Monitor Anggaran](#monitor-anggaran)
- [Transfer Anggaran](#transfer-anggaran)
- [Anggaran](#anggaran)
- [Histori Akun](#histori-akun)
- [Log Aktifitas Jurnal](#log-aktifitas-jurnal)

## Akun Perkiraan

Rute di sistem referensi: `general-ledger__glaccount` · Jenis: layar

### Daftar

**Kolom:** Kode Perkiraan · Nama · Tipe Akun · Saldo

**Saringan:** Non Aktif: Semua · Tipe Akun: Semua

### Formulir baru

**Judul:** Data Baru · Informasi Akun · Informasi Bank

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tipe Akun | `accountType` | select |  | (16 data) |
| Sub Akun | `sub` | checkbox |  |  |
| Kode Perkiraan | `no` | text | ya |  |
| Contoh: BCA a/c XXX-XXX, dll | `name` | text |  |  |
| Catatan | `memo` | textarea |  |  |
| Nama Bank | `bank` | lookup |  |  |
| No Rekening | `bankAccount` | text |  |  |
| Atas Nama Rekening | `bankAccountName` | text |  |  |

#### Tab: Informasi Umum

_(tidak ada isian terbaca)_

#### Tab: Saldo

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Nilai |  | number |  |  |
| per Tgl | `asOf` | text |  |  |

#### Tab: Daftar Pengguna

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Semua Pengguna | `usedAllUser` | checkbox |  |  |

### Perilaku yang direplikasi

- G-01: daftar akun bertingkat (induk/anak) yang dapat ditambah, diubah, dinonaktifkan.
- G-02: tipe akun sistem referensi (Kas & Bank, Piutang, Persediaan, Aktiva Lancar Lainnya, Aktiva Tetap, Hutang, Modal, Pendapatan, HPP, Beban, dan seterusnya) menentukan laporan.
- G-04: saldo awal per akun per tanggal mulai.
- G-05: akun bawaan ditetapkan di preferensi, bukan dikodekan.

## Pencatatan Beban

Rute di sistem referensi: `general-ledger__expense-accrual` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Jatuh Tempo · Total · Dibayar · Status · Keterangan

**Saringan:** Tanggal: Semua · Status: Semua

**Tombol:** Ambil · Proses

### Formulir baru

**Judul:** Data Baru · Rincian Beban

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Hutang Beban | `expensePayable` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Beban # |  | checkbox | ya |  |
| No Beban # | `typeAutoNumber` | select | ya | Pencatatan Beban |
| Rincian Beban | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

**Tombol:** Ambil · Proses

#### Tab: Rincian Beban

_(tidak ada isian terbaca)_

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Jatuh Tempo | `dueDate` | date | ya |  |
| Catatan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- K-02: pencatatan beban/akrual multi-baris ke akun mana pun, dengan pajak dan cabang per baris.

## Pencatatan Gaji

Rute di sistem referensi: `cash-bank__employee-payment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Jatuh Tempo · Total · Tipe Pembayaran · Status · Periode · Keterangan

**Saringan:** Tanggal: Semua · Bulan: Semua · Tahun: Semua · Status: Semua

**Tombol:** Proses

### Formulir baru

**Judul:** Data Baru · Rincian Karyawan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tipe Pembayaran | `employeePaymentType` | select |  | Bulanan, Non Bulanan |
| Bulan | `paymentMonth` | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | `paymentYear` | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Pencatatan Gaji |
| Tanggal | `transDate` | date | ya |  |
| Jatuh Tempo | `dueDate` | date | ya |  |
| Rincian Karyawan | `searchDetailEmployee` | lookup | ya |  |

**Kolom rincian:** Nama Karyawan · Pendapatan Bruto · Pajak Penghasilan · Gaji dibayarkan

**Tombol:** Proses · Ambil

#### Tab: Rincian Karyawan

_(tidak ada isian terbaca)_

**Kolom rincian:** Nama Karyawan · Pendapatan Bruto · Pajak Penghasilan · Gaji dibayarkan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Hutang Beban | `expensePayable` | lookup | ya |  |
| Catatan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- Pencatatan gaji sebagai jurnal; penggajian di luar lingkup.

## Jurnal Umum

Rute di sistem referensi: `general-ledger__journal-voucher` · Jenis: layar

### Daftar

**Kolom:** Nomor # · No. Trans # · Tanggal · Keterangan · Total

**Saringan:** Tipe Transaksi: Faktur Penjualan, ...

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal | `transDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, Jurnal Umum, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Jurnal | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Akun Perkiraan · Nama Perkiraan · Debit · Kredit

**Tombol:** Ambil

#### Tab: Rincian Jurnal

_(tidak ada isian terbaca)_

**Kolom rincian:** Akun Perkiraan · Nama Perkiraan · Debit · Kredit

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Keterangan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- G-03: jurnal umum manual multi-baris, harus seimbang, bisa diubah/dihapus sesuai hak dan kunci periode.

## Monitor Anggaran

Rute di sistem referensi: `budget-target__accountbudget-monitor` · Jenis: layar

### Daftar

**Kolom:** Nama Akun · Kode# · Anggaran · Penggunaan Anggaran · Sisa Anggaran

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Cari/Pilih Akun Perkiraan... | `budgetAccount` | lookup |  |  |
| Cari/Pilih... | `branch` | lookup |  |  |

### Perilaku yang direplikasi

- G-08: realisasi vs anggaran per akun per periode.

## Transfer Anggaran

Rute di sistem referensi: `budget-target__accountbudget-transfer` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Dari Akun · Ke Akun · Nilai Transfer

**Saringan:** Tanggal: Semua

### Formulir baru

**Judul:** Data Baru · Transfer Dari Anggaran · Ke Anggaran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tahun | `budgetYear` | select | ya | 2023, 2024, 2025, 2026, 2027 |
| Tipe | `scopeType` | select |  | Umum |
| No Transfer # |  | checkbox | ya |  |
| No Transfer # | `typeAutoNumber` | select | ya | Transfer Anggaran |
| Tanggal | `transDate` | date | ya |  |
| Bulan | `transferFromMonth` | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | `transferFromAccount` | lookup | ya |  |
| Nilai Transfer |  | number | ya |  |
| Bulan | `transferToMonth` | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | `transferToAccount` | lookup | ya |  |

#### Tab: Anggaran

_(tidak ada isian terbaca)_

#### Tab: Catatan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Catatan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- G-08: pemindahan anggaran antar akun/periode.

## Anggaran

Rute di sistem referensi: `budget-target__accountbudget-target` · Jenis: layar

### Daftar

**Kolom:** Tahun · Bulan · Tipe · Penganalisa · Catatan

**Saringan:** Tipe: Semua

### Formulir baru

**Judul:** Data Baru · Rincian Anggaran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Bulan | `budgetMonth` | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan |  | number | ya |  |
| Rincian Anggaran | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Beban/Pendapatan · Kode # · Anggaran

**Tombol:** Ambil

#### Tab: Anggaran

_(tidak ada isian terbaca)_

**Kolom rincian:** Beban/Pendapatan · Kode # · Anggaran

#### Tab: Catatan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Catatan | `notes` | textarea |  |  |
| Penganalisa | `analystName` | text |  |  |

### Perilaku yang direplikasi

- G-08: anggaran per akun per periode (bulanan).

## Histori Akun

Rute di sistem referensi: `general-ledger__account-history` · Jenis: layar

### Daftar

**Kolom:** Tanggal · No. Sumber # · Tipe Transaksi · Keterangan · Mutasi · Tipe · Saldo

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| s/d | `account` | lookup |  |  |
| s/d | `fromDate` | date |  |  |
| s/d | `toDate` | date |  |  |

### Perilaku yang direplikasi

- R-04: buku besar per akun dengan saldo berjalan, dari jurnal yang diturunkan dokumen.

## Log Aktifitas Jurnal

Rute di sistem referensi: `company__audit-journal` · Jenis: layar

### Daftar

**Kolom:** Tanggal · Nomor # · No. Trans # · Tipe Transaksi

### Formulir baru

_(tidak ada isian terbaca)_

**Kolom rincian:** Tanggal · Nomor # · No. Trans # · Tipe Transaksi

### Perilaku yang direplikasi

- X-08: jejak perubahan jurnal.

