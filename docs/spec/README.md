# Spesifikasi fungsional August's ERP

Dibangkitkan dari studi sistem referensi 2026-10-04T07:49:40.531Z (`docs/referensi/scan.json`) dan catatan `docs/spec/_catatan.json` oleh `tools/referensi-scan/spec.mjs`. Jangan sunting berkas modul dengan tangan; sunting catatannya, lalu bangkitkan ulang.

## Perilaku yang direplikasi

- August's ERP mereplikasi fungsi sistem referensi layar demi layar, lalu dimodifikasi dan diganti antarmukanya. Spesifikasi ini (docs/spec) adalah definisi selesai; antarmuka saat ini hanya pengganti sementara.
- Uang disimpan sebagai BIGINT rupiah; DPP dan PPN dihitung dan disimpan per baris; posting (jurnal, mutasi stok, alokasi pembayaran) diturunkan dari dokumen oleh satu lapisan posting; log audit hanya bertambah (append-only).
- Dokumen yang sudah tercatat boleh diubah dan dihapus seperti di sistem referensi, tunduk pada hak akses, kunci periode tertutup, dan pemblokir (sudah direkonsiliasi, sudah dilunasi, dirujuk dokumen lain).
- Cabang adalah penanda (tag) dalam satu pembukuan, bukan pembukuan terpisah: satu buku besar, satu persediaan, satu HPP rata-rata.
- Setiap layar transaksi sistem referensi punya pola yang sama: formulir "Data Baru" dengan kepala dokumen (pelanggan/pemasok, tanggal, nomor otomatis dengan saklar), tab Rincian Barang (grid baris), Info lainnya, Biaya Lainnya; tombol Daftar membuka daftar dokumen dengan saringan (Tanggal, Status, Sudah dicetak) dan Tambah Kriteria. Replikasi mengikuti pola ini untuk semua dokumen.

## Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)

- Portal pembeli, situs publik, pipeline impor daftar harga, pemecahan pesanan antar gudang, reservasi stok saat disetujui, faktur sebelum kirim, komisi atas faktur lunas, kunjungan toko, ekspor Coretax XML, dan aturan dua kunci (pengaju ≠ pemverifikasi) adalah kelebihan WebTransaction yang tidak ada di sistem referensi. Masing-masing dipertahankan di balik saklar, bawaan: nyala.

## Pertanyaan terbuka

- Edisi sistem referensi yang dipindai (CV JAVAINDO 35) tidak menampilkan modul Manufaktur (M-01 s.d. M-06) maupun Departemen/Proyek (G-06, G-07). Replikasi mengikuti menu yang ada; keduanya masuk hanya jika pemilik mengaktifkan modulnya di sistem referensi dan pemindaian diulang.
- Mata uang asing (X-11) tetap di luar lingkup; layar Mata Uang direplikasi hanya sebagai daftar satu mata uang (IDR).

## Modul

| Modul | Kunci | Layar | Berkas |
|---|---|---|---|
| Pengaturan | `setting` | 8 | [pengaturan.md](pengaturan.md) |
| Perusahaan | `company` | 14 | [perusahaan.md](perusahaan.md) |
| Buku Besar | `general-ledger` | 9 | [buku-besar.md](buku-besar.md) |
| Kas & Bank | `cash-bank` | 9 | [kas-bank.md](kas-bank.md) |
| Penjualan | `sales` | 16 | [penjualan.md](penjualan.md) |
| Pembelian | `purchase` | 12 | [pembelian.md](pembelian.md) |
| Persediaan | `inventory` | 13 | [persediaan.md](persediaan.md) |
| Aset Tetap | `asset` | 7 | [aset-tetap.md](aset-tetap.md) |
| Pajak | `smartlink-tax` | 3 | [pajak.md](pajak.md) |
| Laporan | `report` | 5 | [laporan.md](laporan.md) |
