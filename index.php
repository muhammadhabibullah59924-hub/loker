<?php

/*
 * Titik masuk aplikasi untuk XAMPP.
 *
 * Document root XAMPP menunjuk ke folder proyek, sementara berkas aplikasi
 * Laravel berada di folder public/. Berkas .htaccess pada folder ini
 * meneruskan seluruh permintaan ke berkas di bawah ini sehingga
 * http://localhost/loker/ berfungsi penuh sesuai ADR-005.
 */

require __DIR__.'/public/index.php';
