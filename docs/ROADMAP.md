# Peta jalan August's ERP

Replikasi REFERENSI Online fungsi demi fungsi, lalu dimodifikasi dan diganti antarmukanya.
Spesifikasinya `docs/spec/` (dibangkitkan dari pemindaian); sebuah fase **selesai** ketika
setiap layar di halaman spec fasenya ada dengan semua isiannya, dan setiap butir *Perilaku
yang direplikasi* punya uji. Antarmuka selama fase 1–14 adalah pengganti sementara (panel
Filament); pemilik mendesain ulang setelahnya.

| Fase | Lingkup | Halaman spec | Selesai ketika |
|---|---|---|---|
| 0 | Pemindaian, spec, kerangka Laravel + Filament, CI | — | Sesi ini: repositori ini ada, `docs/spec` terisi, `php artisan test` dan `npm test` hijau |
| 1 | **Pengaturan**: Preferensi, Penomoran, Info perusahaan (Perusahaan/Cabang, Pajak, Syarat Pembayaran, Pengiriman, FOB) | pengaturan.md, perusahaan.md | Setiap saklar Preferensi REFERENSI punya padanan dan dibaca lewat satu kelas; nomor dokumen diformat dari Penomoran |
| 2 | **Data master**: Pelanggan, Kategori Pelanggan, Kategori Penjualan, Pemasok, Kategori Pemasok, Karyawan, Kontak, Barang & Jasa, Satuan, Kategori Barang, Merek, Gudang | penjualan.md, pembelian.md, persediaan.md | Semua isian formulir REFERENSI ada, dengan nama REFERENSI-nya; impor Excel pelanggan dan barang memakai templat REFERENSI |
| 3 | **Buku Besar**: Akun Perkiraan (tipe akun, saldo awal, akun bawaan), Jurnal Umum, Histori Akun, Proses Akhir Bulan (kunci periode), Log Aktifitas | buku-besar.md, perusahaan.md | Lapisan posting ada: setiap dokumen menghasilkan jurnal seimbang lewat `posting_key`; kunci periode menolak tanggal lama dan baru |
| 4 | **Persediaan**: Penyesuaian Persediaan (termasuk saldo awal), Pemindahan Barang, Perintah & Hasil Stok Opname, Barang per Gudang, Stok Minimum, Pemenuhan Pesanan; HPP rata-rata dengan tanggal dokumen dan hitung ulang | persediaan.md | Mutasi stok hanya dari posting; HPP terbukti dengan uji dokumen mundur |
| 5 | **Pembelian**: Permintaan Barang, Pesanan Pembelian, Penerimaan Barang, Uang Muka, Faktur Pembelian (Biaya Lainnya, landed cost), Pembayaran Pembelian, Retur, Klaim Pemasok, Harga Pemasok, Perintah Pembayaran, Transfer Pemasok | pembelian.md | Rantai PO → terima → faktur → bayar dengan sebagian dan Ambil; hutang lunas hanya lewat alokasi |
| 6 | **Penjualan**: Penawaran, Pesanan, Pengiriman Pesanan, Uang Muka, Faktur, Penerimaan, Retur, Tukar Faktur, Penyesuaian Harga/Diskon, Komisi, Target, Check In | penjualan.md | Rantai penawaran → pesanan → kirim → faktur → terima dengan sebagian dan Ambil; status pemenuhan diturunkan dari kuantitas; saklar WebTransaction (reservasi, tagih sebelum kirim, pecah gudang, beku kredit) berlaku |
| 7 | **Kas & Bank**: Pembayaran, Penerimaan, Transfer Bank, Rekening Koran, Histori Bank, Rekonsiliasi Bank, Giro | kas-bank.md | Rekonsiliasi memblokir perubahan dokumen yang sudah direkonsiliasi |
| 8 | **Aset Tetap**: Aset, Kategori Aset, Kategori Pajak, Perubahan, Disposisi, Pindah Aset, Aset per Lokasi; penyusutan bulanan | aset-tetap.md | Penyusutan diposting otomatis dengan metode yang REFERENSI tawarkan |
| 9 | **Pajak**: kode pajak per baris (termasuk/belum termasuk), e-Faktur CTAS (Coretax XML) dan e-Faktur Legacy (CSV), NSFP kembali ke faktur | pajak.md, perusahaan.md | Akuntan mengonfirmasi angka DPP/PPN; berkas Coretax diterima utuh |
| 10 | **Laporan**: setiap laporan di Daftar Laporan dengan parameternya; ekspor Excel dan PDF; SPT PPN | laporan.md | Setiap laporan dihitung dari jurnal/ledger, tidak dari kolom tersimpan |
| 11 | **Cetakan**: Desain Cetakan per dokumen | pengaturan.md | Setiap dokumen punya cetakan yang dapat didesain |
| 12 | **Hak akses**: Akses Grup (per menu + hak khusus), Pengguna (cabang & gudang), Penyetuju Transaksi; `PemisahanTugas` | pengaturan.md | Enam grup sistem mereproduksi matriks peran WebTransaction sel demi sel |
| 13 | **Lain-lain Perusahaan**: Transaksi Berulang, Transaksi Favorit, Kalender, Gaji/Tunjangan dan Pencatatan Gaji (sebatas jurnal), Anggaran | perusahaan.md, buku-besar.md | — |
| 14 | **Impor**: templat Excel REFERENSI untuk pelanggan, pemasok, barang, saldo awal; pembacaan ekspor REFERENSI untuk migrasi | — | Data CV JAVAINDO 35 dapat dimuat dari ekspor REFERENSI-nya |
| 15 | **UAT** dengan data nyata, lalu desain ulang antarmuka oleh pemilik | — | Pemilik menandatangani `docs/spec` |

Di luar lingkup: mata uang asing, penggajian penuh, Manufaktur dan Departemen/Proyek (tidak
ada di edisi REFERENSI yang dipindai), SmartLink (e-Banking, Virtual Account, e-Payment,
e-Commerce), Referensi Store/Capital, Analisa AI, gerbang pembayaran.

Kelebihan WebTransaction yang dipertahankan di balik saklar (bawaan nyala): portal pembeli,
situs publik, persetujuan marketing wajib, peringatan 120 hari dan beku 150 hari, pecah
gudang, reservasi stok, tagih sebelum kirim, komisi atas faktur lunas, kunjungan toko,
ekspor Coretax XML, pipeline impor daftar harga, aturan dua kunci.
