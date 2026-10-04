# Kas & Bank

## Pembayaran

Rute: `#accurate__cash-bank__other-payment`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Kas/Bank · No Cek # · Keterangan · Nilai

**Saringan:** Tanggal: Semua · Kas/Bank: Semua

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| No Cek # | text |  |  |
| Penerima | textarea |  |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Pembayaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Pembayaran | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai · Catatan

#### Tab: Rincian Pembayaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Pembayaran | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai · Catatan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| No Cek # | text |  |  |
| Penerima | textarea |  |  |
| Catatan | textarea |  |  |

**Tombol formulir:** Ambil

## Penerimaan

Rute: `#accurate__cash-bank__other-deposit`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Kas/Bank · No Cek # · Keterangan · Nilai

**Saringan:** Tanggal: Semua · Kas/Bank: Semua

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| No Cek # | text |  |  |
| Pemberi | textarea |  |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Penerimaan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Penerimaan | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Rincian Penerimaan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Penerimaan | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Kas/Bank | lookup | ya |  |
| Tanggal | date | ya |  |
| No Bukti # | checkbox | ya |  |
| No Bukti # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| No Cek # | text |  |  |
| Pemberi | textarea |  |  |
| Catatan | textarea |  |  |

**Tombol formulir:** Ambil

## Transfer Bank

Rute: `#accurate__cash-bank__bank-transfer`

**Judul layar:** Data Baru · Biaya Transfer

**Kolom daftar:** Nomor # · Tanggal · Bank (Keluar) · Bank (Masuk) · Keterangan · Total · Pembayaran Pembelian

**Saringan:** Tanggal: Semua · Ke Kas/Bank: Semua · Dari Kas/Bank: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas, Transfer Bank |
| Biaya Transfer | lookup |  |  |

### Formulir baru

**Judul:** Data Baru · Transfer Uang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas, Transfer Bank |
| Dari Kas/Bank | lookup | ya |  |
| Nilai Transfer | number | ya |  |
| Ke Kas/Bank | lookup | ya |  |

#### Tab: Transfer Uang

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas, Transfer Bank |
| Dari Kas/Bank | lookup | ya |  |
| Nilai Transfer | number | ya |  |
| Ke Kas/Bank | lookup | ya |  |

#### Tab: Biaya Transfer

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas, Transfer Bank |
| Biaya Transfer | lookup |  |  |

**Kolom rincian:** Akun · Nama Akun · Dibebankan ke · Nilai

## SmartLink e-Banking

Rute: `#accurate__cash-bank__internet-banking`

**Judul layar:** Data Baru

**Kolom daftar:** No. Rekening Bank · Relasi Akun Bank · Jenis Internet Banking

**Saringan:** Jenis Internet Banking: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Jenis Internet Banking | select | ya | (26 data) |
| No. Rekening Bank | text | ya |  |
| Relasi Akun Bank | lookup | ya |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Internet Banking | select | ya | (26 data) |
| No. Rekening Bank | text | ya |  |
| Relasi Akun Bank | lookup | ya |  |

#### Tab: Internet Banking

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Jenis Internet Banking | select | ya | (26 data) |
| No. Rekening Bank | text | ya |  |
| Relasi Akun Bank | lookup | ya |  |

## Rekening Koran

Rute: `#accurate__cash-bank__bank-statement`

**Kolom daftar:** Tanggal · Keterangan · Mutasi · Tipe · Saldo · #

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| s/d | lookup |  |  |
| s/d | date |  |  |
| s/d | date |  |  |

## Histori Bank

Rute: `#accurate__cash-bank__bank-book`

**Kolom daftar:** Tanggal · No. Sumber # · No Cek # · Tipe Transaksi · Keterangan · Mutasi · Tipe · Saldo · #

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| s/d | lookup |  |  |
| s/d | date |  |  |
| s/d | date |  |  |

## Rekonsiliasi Bank

Rute: `#accurate__cash-bank__bank-reconcile`

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| s/d | lookup |  |  |
| s/d | date |  |  |
| s/d | date |  |  |

## SmartLink Virtual Account

Rute: `#accurate__company__application-virtual-account`

## SmartLink e-Payment

Rute: `#accurate__company__application-epayment`

