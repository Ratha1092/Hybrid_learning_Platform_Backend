import { test, expect } from '@playwright/test';

test('E2E2: create category and course via admin', async ({ page }) => {
    // --- Category ---
    await page.goto('/admin/categories/create');
    await page.waitForLoadState('networkidle');

    await page.locator('#data\\.name').fill('E2E2 Test Category');
    await page.locator('#data\\.description').fill('E2E2 automated end-to-end test category');

    await page.locator('button[type="submit"]').first().click();
    await page.waitForLoadState('networkidle');

    // Should redirect away from create page (to list or edit)
    await expect(page).not.toHaveURL(/categories\/create$/);
    await page.screenshot({ path: 'tests/playwright/.e2e2-category-created.png' });

    // Confirm listing shows it
    await page.goto('/admin/categories');
    await page.waitForLoadState('networkidle');
    await expect(page.getByText('E2E2 Test Category')).toBeVisible();

    // --- Course ---
    await page.goto('/admin/courses/create');
    await page.waitForLoadState('networkidle');

    await page.locator('#data\\.title').fill('E2E2 Test Course');
    await page.locator('#data\\.short_description').fill('E2E2 short description');

    // Instructor select (searchable Select - Filament renders a choices.js/select2-like UI)
    await page.locator('[id="data.instructor_id"]').click();
    await page.waitForTimeout(300);
    await page.keyboard.type('E2E2 Instructor');
    await page.waitForTimeout(800);
    await page.getByRole('option', { name: /E2E2 Instructor/i }).first().click();

    await page.locator('[id="data.category_id"]').click();
    await page.waitForTimeout(300);
    await page.keyboard.type('E2E2 Test Category');
    await page.waitForTimeout(800);
    await page.getByRole('option', { name: /E2E2 Test Category/i }).first().click();

    await page.locator('#data\\.price').fill('19.99');
    await page.locator('[id="data.level"]').click();
    await page.waitForTimeout(300);
    await page.getByRole('option', { name: 'Beginner' }).first().click();

    await page.locator('#data\\.language').fill('English');

    await page.locator('[id="data.status"]').click();
    await page.waitForTimeout(300);
    await page.getByRole('option', { name: 'Published' }).first().click();

    await page.screenshot({ path: 'tests/playwright/.e2e2-course-form.png' });

    await page.locator('button[type="submit"]').first().click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    await expect(page).not.toHaveURL(/courses\/create$/);
    await page.screenshot({ path: 'tests/playwright/.e2e2-course-created.png' });

    await page.goto('/admin/courses');
    await page.waitForLoadState('networkidle');
    await expect(page.getByText('E2E2 Test Course')).toBeVisible();
});
