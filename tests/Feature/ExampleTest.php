<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Contoh tes fitur: halaman depan aplikasi harus menjawab 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // URL absolut dipakai agar tes tidak bergantung pada APP_URL
        // (aplikasi dijalankan lewat Apache di http://localhost/loker).
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
    }
}
