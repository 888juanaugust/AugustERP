# Kas & Bank

Modul sistem referensi `cash-bank`, 9 layar. Dibangkitkan; sunting `_catatan.json`, bukan berkas ini.

## Perilaku yang direplikasi

- K-01: beberapa akun kas dan bank (tipe akun Kas & Bank), masing-masing dengan histori dan rekonsiliasi.
- K-06: giro masuk/keluar dicatat pada pembayaran dan penerimaan dengan tanggal jatuh tempo dan status cair/tolak.

## Layar

- [Pembayaran](#pembayaran)
- [Penerimaan](#penerimaan)
- [Transfer Bank](#transfer-bank)
- [SmartLink e-Banking](#smartlink-e-banking)
- [Rekening Koran](#rekening-koran)
- [Histori Bank](#histori-bank)
- [Rekonsiliasi Bank](#rekonsiliasi-bank)
- [SmartLink Virtual Account](#smartlink-virtual-account)
- [SmartLink e-Payment](#smartlink-e-payment)

## Pembayaran

Rute di sistem referensi: `#referensi__cash-bank__other-payment` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Kas/Bank · No Cek # · Keterangan · Nilai

**Saringan:** Tanggal: Semua · Kas/Bank: Semua

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru · Rincian Pembayaran

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kas/Bank | `bank` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Bukti # |  | checkbox | ya |  |
| No Bukti # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Pembayaran | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai · Catatan

**Tombol:** Ambil

#### Tab: Rincian Pembayaran

_(tidak ada isian terbaca)_

**Kolom rincian:** Akun · Nama Akun · Nilai · Catatan

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| No Cek # | `chequeNo` | text |  |  |
| Penerima | `payee` | textarea |  |  |
| Catatan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- K-02: pengeluaran kas/bank multi-baris ke akun mana pun (bukan hanya beban), dengan pajak, cabang, dan giro.

## Penerimaan

Rute di sistem referensi: `#referensi__cash-bank__other-deposit` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Kas/Bank · No Cek # · Keterangan · Nilai

**Saringan:** Tanggal: Semua · Kas/Bank: Semua

**Tombol:** Ambil

### Formulir baru

**Judul:** Data Baru · Rincian Penerimaan

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Kas/Bank | `bank` | lookup | ya |  |
| Tanggal | `transDate` | date | ya |  |
| No Bukti # |  | checkbox | ya |  |
| No Bukti # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Penerimaan | `searchDetailAccount` | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

**Tombol:** Ambil

#### Tab: Rincian Penerimaan

_(tidak ada isian terbaca)_

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Info lainnya

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| No Cek # | `chequeNo` | text |  |  |
| Pemberi | `payee` | textarea |  |  |
| Catatan | `description` | textarea |  |  |

### Perilaku yang direplikasi

- K-03: penerimaan kas/bank lain-lain multi-baris.

## Transfer Bank

Rute di sistem referensi: `#referensi__cash-bank__bank-transfer` · Jenis: layar

### Daftar

**Kolom:** Nomor # · Tanggal · Bank (Keluar) · Bank (Masuk) · Keterangan · Total · Pembayaran Pembelian

**Saringan:** Tanggal: Semua · Ke Kas/Bank: Semua · Dari Kas/Bank: Semua

### Formulir baru

**Judul:** Data Baru · Transfer Uang

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Tanggal | `transDate` | date | ya |  |
| Nomor # |  | checkbox | ya |  |
| Nomor # | `typeAutoNumber` | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas, Transfer Bank |
| Dari Kas/Bank | `fromBank` | lookup | ya |  |
| Nilai Transfer |  | number | ya |  |
| Ke Kas/Bank | `toBank` | lookup | ya |  |

#### Tab: Transfer Uang

_(tidak ada isian terbaca)_

#### Tab: Biaya Transfer

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Biaya Transfer | `searchDetailAccount` | lookup |  |  |

**Kolom rincian:** Akun · Nama Akun · Dibebankan ke · Nilai

### Perilaku yang direplikasi

- K-04: pemindahan antar akun kas/bank dengan biaya transfer.

## SmartLink e-Banking

Rute di sistem referensi: `#referensi__cash-bank__internet-banking` · Jenis: layar

### Daftar

**Kolom:** No. Rekening Bank · Relasi Akun Bank · Jenis Internet Banking

**Saringan:** Jenis Internet Banking: Semua

### Formulir baru

**Judul:** Data Baru

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| Jenis Internet Banking | `internetBankingType` | select | ya | (26 data) |
| No. Rekening Bank | `accountNumber` | text | ya |  |
| Relasi Akun Bank | `cashBankGlAccount` | lookup | ya |  |

#### Tab: Internet Banking

_(tidak ada isian terbaca)_

### Perilaku yang direplikasi

- Integrasi bank pihak ketiga sistem referensi; tidak direplikasi.

## Rekening Koran

Rute di sistem referensi: `#referensi__cash-bank__bank-statement` · Jenis: layar

### Daftar

**Kolom:** Tanggal · Keterangan · Mutasi · Tipe · Saldo · #

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| s/d | `bank` | lookup |  |  |
| s/d | `fromDate` | date |  |  |
| s/d | `toDate` | date |  |  |

### Perilaku yang direplikasi

- K-05: impor rekening koran (CSV/Excel) sebagai dasar rekonsiliasi.

## Histori Bank

Rute di sistem referensi: `#referensi__cash-bank__bank-book` · Jenis: layar

### Daftar

**Kolom:** Tanggal · No. Sumber # · No Cek # · Tipe Transaksi · Keterangan · Mutasi · Tipe · Saldo · #

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| s/d | `bank` | lookup |  |  |
| s/d | `fromDate` | date |  |  |
| s/d | `toDate` | date |  |  |

### Perilaku yang direplikasi

- R-15: mutasi per akun kas/bank dengan saldo berjalan.

## Rekonsiliasi Bank

Rute di sistem referensi: `#referensi__cash-bank__bank-reconcile` · Jenis: layar

### Isian

| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |
|---|---|---|---|---|
| s/d | `bank` | lookup |  |  |
| s/d | `startDate` | date |  |  |
| s/d | `endDate` | date |  |  |

### Perilaku yang direplikasi

- K-05: rekonsiliasi per akun bank per periode; transaksi yang sudah direkonsiliasi menjadi pemblokir perubahan dokumen.

### Pertanyaan terbuka

- K-05: bisakah transaksi diubah setelah baris banknya direkonsiliasi?

## SmartLink Virtual Account

Rute di sistem referensi: `#referensi__company__application-virtual-account` · Jenis: layar

### Perilaku yang direplikasi

- Layanan sistem referensi; tidak direplikasi (tidak ada gerbang pembayaran).

## SmartLink e-Payment

Rute di sistem referensi: `#referensi__company__application-epayment` · Jenis: layar

### Perilaku yang direplikasi

- Layanan sistem referensi; tidak direplikasi (tidak ada gerbang pembayaran).

