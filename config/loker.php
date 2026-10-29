<?php

return [
    /*
     |---------------------------------------------------------------------
     | Lokasi cadangan dasbor
     |---------------------------------------------------------------------
     | Dipakai ketika pengguna menolak izin lokasi atau belum pernah
     | mengizinkannya, supaya dasbor tetap menampilkan loker terurut
     | (kendali R1 di docs/PRD.md). Nilainya adalah alun-alun Kota
     | Probolinggo — keputusan pencatatannya ada di ADR-009.
     */

    'lokasi_default' => [
        'lat' => -7.7145,
        'lng' => 113.2146,
        'nama' => 'pusat Kota Probolinggo',
    ],
];
