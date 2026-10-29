import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    timeout: 90_000,
    retries: 0,
    reporter: [['list']],

    use: {
        baseURL: 'http://127.0.0.1:8000',
        trace: 'retain-on-failure',
    },

    webServer: {
        command: 'C:/php83/php.exe artisan serve --host=127.0.0.1 --port=8000',
        url: 'http://127.0.0.1:8000/up',
        reuseExistingServer: true,
        timeout: 120_000,
    },
});
