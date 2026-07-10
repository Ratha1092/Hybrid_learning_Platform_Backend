import { test as setup } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const authFile = path.join(__dirname, '.auth.json');

setup('authenticate as super-admin', async ({ page }) => {
    await page.goto('/admin/login');
    await page.waitForLoadState('networkidle');

    // If already redirected to admin (session still valid), just save state
    if (!page.url().includes('/login')) {
        await page.context().storageState({ path: authFile });
        return;
    }

    // Filament 3 uses wire:model inputs identified by id="form.email"
    await page.locator('#form\\.email').fill('superadmin@example.com');
    await page.locator('#form\\.password').fill('admin123');
    await page.locator('button[type="submit"].fi-btn').click();

    // Wait until we leave the login page
    await page.waitForFunction(() => !window.location.href.includes('/login'), { timeout: 15_000 });
    await page.waitForLoadState('networkidle');

    await page.context().storageState({ path: authFile });
});
