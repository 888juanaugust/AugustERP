<?php

/*
 * The tax office's own reference codes, printed on the e-Tax Invoice Export
 * screen and written into the Coretax bulk-import file. Confirm any change
 * with the accountant; see docs/spec/pajak.md.
 */
return [
    // Which export the e-Tax screen produces by default: 'coretax' (XML bulk import) or 'legacy' (e-Faktur CSV).
    'format_ekspor' => env('PAJAK_FORMAT_EKSPOR', 'coretax'),

    'coretax' => [
        // Transaction code on the tax invoice: 04 for ordinary goods under the 11/12 tax base (PMK 131/2024), 01 otherwise.
        'trx_code_dpp_lain' => '04',
        'trx_code_normal' => '01',
        'tax_invoice_opt' => 'Normal',
        // Buyer country and document kinds.
        'buyer_country' => 'IDN',
        'buyer_document' => ['npwp' => 'TIN', 'nik' => 'National ID', 'passport' => 'Passport', 'other' => 'Other ID'],
        // The goods/services code when an item carries none, and the unit code fallback.
        'goods_code' => '000000',
        'service_code' => '000000',
        'unit_code' => 'UM.0018',
        // The ID TKU suffix for a head office (NPWP + 6 digits).
        'idtku_suffix' => '000000',
        // The VAT rate printed on each line.
        'vat_rate' => 12,
    ],

    'legacy' => [
        'kd_jenis_transaksi' => '01',
        'fg_pengganti' => '0',
        'header' => ['FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI'],
        'lt' => ['LT', 'NPWP', 'NAMA', 'JALAN', 'BLOK', 'NOMOR', 'RT', 'RW', 'KECAMATAN', 'KELURAHAN', 'KABUPATEN', 'PROPINSI', 'KODE_POS', 'NOMOR_TELEPON'],
        'of' => ['OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'HARGA_TOTAL', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM'],
    ],
];
