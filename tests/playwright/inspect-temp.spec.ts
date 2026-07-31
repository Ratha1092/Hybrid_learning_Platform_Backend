import { test } from '@playwright/test';

test('inspect category form', async ({ page }) => {
    await page.goto('/admin/categories/create');
    await page.waitForLoadState('networkidle');
    const ids = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('input, textarea, select')).map(el => ({ tag: el.tagName, id: el.id, name: (el as HTMLInputElement).name, type: (el as HTMLInputElement).type }));
    });
    console.log(JSON.stringify(ids, null, 2));
});
