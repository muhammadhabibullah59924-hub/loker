<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Sesuaikan URL uji dengan lokasi aplikasi.
     *
     * `APP_URL` di .env memakai http://localhost/loker karena aplikasi dibuka
     * lewat Apache (ADR-005), sehingga `$this->get('/')` tanpa penyesuaian ini
     * akan memanggil path /loker/ dan berujung 404. Metode di bawah memakai
     * root server saja supaya tes memanggil rute yang sama seperti di browser.
     *
     * @param  string  $uri
     */
    protected function prepareUrlForRequest($uri)
    {
        if (str_starts_with($uri, '/')) {
            $uri = 'http://localhost'.$uri;
        }

        return $uri;
    }
}
