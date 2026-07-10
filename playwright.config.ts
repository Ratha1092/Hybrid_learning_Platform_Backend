import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/playwright',
    timeout: 30_000,
    retries: 1,
    reporter: 'list',
    use: {
        baseURL: 'http://localhost:8000',
        headless: true,
        screenshot: 'only-on-failure',
        video: 'off',
    },
    projects: [
        {
            name: 'setup',
            testMatch: '**/auth.setup.ts',
        },
        {
            name: 'reports',
            dependencies: ['setup'],
            testMatch: '**/reports.spec.ts',
            use: {
                ...devices['Desktop Chrome'],
                storageState: 'tests/playwright/.auth.json',
            },
        },
        {
            name: 'sidebar',
            dependencies: ['setup'],
            testMatch: '**/sidebar.spec.ts',
            use: {
                ...devices['Desktop Chrome'],
                storageState: 'tests/playwright/.auth.json',
            },
        },
        {
            name: 'debug',
            dependencies: ['setup'],
            testMatch: '**/sidebar-debug.spec.ts',
            use: {
                ...devices['Desktop Chrome'],
                storageState: 'tests/playwright/.auth.json',
            },
        },
    ],
});
