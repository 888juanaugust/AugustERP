# Buku Besar

## Akun Perkiraan

Rute: `#referensi__general-ledger__glaccount`

**Judul layar:** Data Baru · Akses Pengguna

**Kolom daftar:** Kode Perkiraan · Nama · Tipe Akun · Saldo

**Saringan:** Non Aktif: Semua · Tipe Akun: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Semua Pengguna | checkbox |  |  |

### Formulir baru

**Judul:** Data Baru · Informasi Akun · Informasi Bank

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Akun | select |  | (16 data) |
| Sub Akun | checkbox |  |  |
| Kode Perkiraan | text | ya |  |
| Contoh: BCA a/c XXX-XXX, dll | text |  |  |
| Catatan | textarea |  |  |
| Nama Bank | lookup |  |  |
| No Rekening | text |  |  |
| Atas Nama Rekening | text |  |  |

#### Tab: Informasi Umum

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Akun | select |  | (16 data) |
| Sub Akun | checkbox |  |  |
| Kode Perkiraan | text | ya |  |
| Contoh: BCA a/c XXX-XXX, dll | text |  |  |
| Catatan | textarea |  |  |
| Nama Bank | lookup |  |  |
| No Rekening | text |  |  |
| Atas Nama Rekening | text |  |  |

#### Tab: Saldo

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Nilai | number |  |  |
| per Tgl | text |  |  |

#### Tab: Daftar Pengguna

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Semua Pengguna | checkbox |  |  |

## Pencatatan Beban

Rute: `#referensi__general-ledger__expense-accrual`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Jatuh Tempo · Total · Dibayar · Status · Keterangan

**Saringan:** Tanggal: Semua · Status: Semua

**Tombol:** Ambil · Proses

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Hutang Beban | lookup | ya |  |
| Tanggal | date | ya |  |
| No Beban # | checkbox | ya |  |
| No Beban # | select | ya | Pencatatan Beban |
| Jatuh Tempo | date | ya |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Beban

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Hutang Beban | lookup | ya |  |
| Tanggal | date | ya |  |
| No Beban # | checkbox | ya |  |
| No Beban # | select | ya | Pencatatan Beban |
| Rincian Beban | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Rincian Beban

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Hutang Beban | lookup | ya |  |
| Tanggal | date | ya |  |
| No Beban # | checkbox | ya |  |
| No Beban # | select | ya | Pencatatan Beban |
| Rincian Beban | lookup | ya |  |

**Kolom rincian:** Akun · Nama Akun · Nilai

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Hutang Beban | lookup | ya |  |
| Tanggal | date | ya |  |
| No Beban # | checkbox | ya |  |
| No Beban # | select | ya | Pencatatan Beban |
| Jatuh Tempo | date | ya |  |
| Catatan | textarea |  |  |

**Tombol formulir:** Ambil · Proses

## Pencatatan Gaji

Rute: `#referensi__cash-bank__employee-payment`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Jatuh Tempo · Total · Tipe Pembayaran · Status · Periode · Keterangan

**Saringan:** Tanggal: Semua · Bulan: Semua · Tahun: Semua · Status: Semua

**Tombol:** Proses

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tipe Pembayaran | select |  | Bulanan, Non Bulanan |
| Bulan | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pencatatan Gaji |
| Tanggal | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Hutang Beban | lookup | ya |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Karyawan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Pembayaran | select |  | Bulanan, Non Bulanan |
| Bulan | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pencatatan Gaji |
| Tanggal | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Rincian Karyawan | lookup | ya |  |

**Kolom rincian:** Nama Karyawan · Pendapatan Bruto · Pajak Penghasilan · Gaji dibayarkan

#### Tab: Rincian Karyawan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Pembayaran | select |  | Bulanan, Non Bulanan |
| Bulan | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pencatatan Gaji |
| Tanggal | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Rincian Karyawan | lookup | ya |  |

**Kolom rincian:** Nama Karyawan · Pendapatan Bruto · Pajak Penghasilan · Gaji dibayarkan

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tipe Pembayaran | select |  | Bulanan, Non Bulanan |
| Bulan | select |  | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | select |  | 2021, 2022, 2023, 2024, 2025, 2026, 2027 |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Pencatatan Gaji |
| Tanggal | date | ya |  |
| Jatuh Tempo | date | ya |  |
| Hutang Beban | lookup | ya |  |
| Catatan | textarea |  |  |

**Tombol formulir:** Proses · Ambil

## Jurnal Umum

Rute: `#referensi__general-ledger__journal-voucher`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · No. Trans # · Tanggal · Keterangan · Total

**Saringan:** Tipe Transaksi: Faktur Penjualan, ...

**Tombol:** Ambil

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, Jurnal Umum, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Keterangan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, Jurnal Umum, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Jurnal | lookup | ya |  |

**Kolom rincian:** Akun Perkiraan · Nama Perkiraan · Debit · Kredit

#### Tab: Rincian Jurnal

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, Jurnal Umum, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Rincian Jurnal | lookup | ya |  |

**Kolom rincian:** Akun Perkiraan · Nama Perkiraan · Debit · Kredit

#### Tab: Info lainnya

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tanggal | date | ya |  |
| Nomor # | checkbox | ya |  |
| Nomor # | select | ya | Ayat/Pos Silang, BCA, BRI, Bank, Deposito Bank, Jurnal Umum, KAS, KAS JK, Kas & Bank, Kas Kecil, MANDIRI, Nomor Bukti Kas/Bank, OCBC, OCBC PAYROLL, Setara Kas |
| Keterangan | textarea |  |  |

**Tombol formulir:** Ambil

## Monitor Anggaran

Rute: `#referensi__budget-target__accountbudget-monitor`

**Kolom daftar:** Nama Akun · Kode# · Anggaran · Penggunaan Anggaran · Sisa Anggaran

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Cari/Pilih Akun Perkiraan... | lookup |  |  |
| Cari/Pilih... | lookup |  |  |

## Transfer Anggaran

Rute: `#referensi__budget-target__accountbudget-transfer`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Nomor # · Tanggal · Dari Akun · Ke Akun · Nilai Transfer

**Saringan:** Tanggal: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Tahun | select | ya | 2023, 2024, 2025, 2026, 2027 |
| Tipe | select |  | Umum |
| No Transfer # | checkbox | ya |  |
| No Transfer # | select | ya | Transfer Anggaran |
| Tanggal | date | ya |  |
| Catatan | textarea |  |  |

### Formulir baru

**Judul:** Data Baru · Transfer Dari Anggaran · Ke Anggaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tahun | select | ya | 2023, 2024, 2025, 2026, 2027 |
| Tipe | select |  | Umum |
| No Transfer # | checkbox | ya |  |
| No Transfer # | select | ya | Transfer Anggaran |
| Tanggal | date | ya |  |
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | lookup | ya |  |
| Nilai Transfer | number | ya |  |
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | lookup | ya |  |

#### Tab: Anggaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tahun | select | ya | 2023, 2024, 2025, 2026, 2027 |
| Tipe | select |  | Umum |
| No Transfer # | checkbox | ya |  |
| No Transfer # | select | ya | Transfer Anggaran |
| Tanggal | date | ya |  |
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | lookup | ya |  |
| Nilai Transfer | number | ya |  |
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Anggaran | lookup | ya |  |

#### Tab: Catatan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Tahun | select | ya | 2023, 2024, 2025, 2026, 2027 |
| Tipe | select |  | Umum |
| No Transfer # | checkbox | ya |  |
| No Transfer # | select | ya | Transfer Anggaran |
| Tanggal | date | ya |  |
| Catatan | textarea |  |  |

## Anggaran

Rute: `#referensi__budget-target__accountbudget-target`

**Judul layar:** Data Baru · Info lainnya

**Kolom daftar:** Tahun · Bulan · Tipe · Penganalisa · Catatan

**Saringan:** Tipe: Semua

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Ketik dan [Enter] | text |  |  |
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | number | ya |  |
| Catatan | textarea |  |  |
| Penganalisa | text |  |  |

### Formulir baru

**Judul:** Data Baru · Rincian Anggaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | number | ya |  |
| Rincian Anggaran | lookup | ya |  |

**Kolom rincian:** Beban/Pendapatan · Kode # · Anggaran

#### Tab: Anggaran

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | number | ya |  |
| Rincian Anggaran | lookup | ya |  |

**Kolom rincian:** Beban/Pendapatan · Kode # · Anggaran

#### Tab: Catatan

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| Bulan | select | ya | Januari, Februari, Maret, April, Mei, Juni, Juli, Agustus, September, Oktober, November, Desember |
| Bulan | number | ya |  |
| Catatan | textarea |  |  |
| Penganalisa | text |  |  |

**Tombol formulir:** Ambil

## Histori Akun

Rute: `#referensi__general-ledger__account-history`

**Kolom daftar:** Tanggal · No. Sumber # · Tipe Transaksi · Keterangan · Mutasi · Tipe · Saldo

### Isian layar

| Isian | Jenis | Wajib | Pilihan |
|---|---|---|---|
| s/d | lookup |  |  |
| s/d | date |  |  |
| s/d | date |  |  |

## Log Aktifitas Jurnal

Rute: `#referensi__company__audit-journal`

**Kolom daftar:** Tanggal · Nomor # · No. Trans # · Tipe Transaksi

### Formulir baru

_(tidak ada isian)_

**Kolom rincian:** Tanggal · Nomor # · No. Trans # · Tipe Transaksi

