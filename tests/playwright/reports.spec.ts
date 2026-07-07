import { test, expect, type Page } from '@playwright/test';

// ── helpers ─────────────────────────────────────────────────────────────────

async function goToReport(page: Page, slug: string) {
    await page.goto(`/admin/${slug}`);
    // Wait for Livewire to settle (spinner gone or network idle)
    await page.waitForLoadState('networkidle');
    // Wait for the pill to appear which means getViewData() ran
    await page.waitForSelector('.rp-drp-pill', { timeout: 15_000 });
}

async function openDatePill(page: Page) {
    await page.click('.rp-drp-pill');
    await expect(page.locator('.rp-drp-panel')).toBeVisible();
}

async function pickPreset(page: Page, label: string) {
    await page.locator('.rp-drp-pre', { hasText: label }).click();
    // Livewire request completes — pill updates, panel closes
    await page.waitForLoadState('networkidle');
    await expect(page.locator('.rp-drp-panel')).toBeHidden();
}

async function pillText(page: Page) {
    const from = await page.locator('.rp-drp-from').innerText();
    const to   = await page.locator('.rp-drp-to').innerText();
    return { from, to };
}

// ── report definitions ───────────────────────────────────────────────────────

const REPORTS = [
    { name: 'Course Intelligence', slug: 'reports/course-intelligence',    kpi: '.rp-kpi-card' },
    { name: 'Instructor Intelligence', slug: 'reports/instructor-intelligence', kpi: '.rp-kpi-card' },
    { name: 'Learning Intelligence', slug: 'reports/learning-intelligence',   kpi: '.rp-kpi-card' },
    { name: 'Revenue',              slug: 'reports/revenue',               kpi: '.rp-kpi-card' },
    { name: 'Payment',              slug: 'reports/payments',              kpi: '.rp-kpi-card' },
    { name: 'Payout',               slug: 'reports/payouts',               kpi: '.rp-kpi-card' },
    { name: 'User',                 slug: 'reports/users',                 kpi: '.rp-kpi-card' },
];

// ── tests ────────────────────────────────────────────────────────────────────

for (const report of REPORTS) {
    test.describe(report.name + ' Report', () => {

        test('page loads and shows date pill', async ({ page }) => {
            await goToReport(page, report.slug);
            await expect(page.locator('.rp-drp-pill')).toBeVisible();
            await expect(page.locator('.rp-kpi-card').first()).toBeVisible();
        });

        test('date pill shows a date range (not blank)', async ({ page }) => {
            await goToReport(page, report.slug);
            const { from, to } = await pillText(page);
            expect(from).not.toBe('');
            expect(to).not.toBe('');
            // Default preset is "This Month" — should show real dates, not "—|—"
            expect(from + to).not.toBe('——');
        });

        test('clicking pill opens dropdown with presets', async ({ page }) => {
            await goToReport(page, report.slug);
            await openDatePill(page);
            await expect(page.locator('.rp-drp-pre', { hasText: 'Today' })).toBeVisible();
            await expect(page.locator('.rp-drp-pre', { hasText: 'Last 30 Days' })).toBeVisible();
            await expect(page.locator('.rp-drp-pre', { hasText: 'All Time' })).toBeVisible();
        });

        test('selecting "Today" preset updates pill and refreshes data', async ({ page }) => {
            await goToReport(page, report.slug);
            await openDatePill(page);
            await pickPreset(page, 'Today');

            const { from, to } = await pillText(page);
            // Both sides should show today's date (same)
            expect(from).not.toBe('—');
            expect(from).toBe(to);
        });

        test('selecting "This Year" preset updates pill and refreshes data', async ({ page }) => {
            await goToReport(page, report.slug);
            await openDatePill(page);
            await pickPreset(page, 'This Year');

            const { from, to } = await pillText(page);
            expect(from).not.toBe('—');
            expect(to).not.toBe('—');
            // Year span: from should be Jan 1, to should be Dec 31
            expect(from).toContain('Jan 1');
            expect(to).toContain('Dec 31');
        });

        test('selecting "All Time" shows — | — in pill', async ({ page }) => {
            await goToReport(page, report.slug);
            await openDatePill(page);
            await pickPreset(page, 'All Time');

            const { from, to } = await pillText(page);
            expect(from).toBe('—');
            expect(to).toBe('—');
        });

        test('custom date range applies correctly', async ({ page }) => {
            await goToReport(page, report.slug);
            await openDatePill(page);

            // Fill custom range
            await page.fill('.rp-drp-dt:nth-of-type(1)', '2024-01-01');
            await page.fill('.rp-drp-dt:nth-of-type(2)', '2024-06-30');
            await page.click('.rp-drp-apply');
            await page.waitForLoadState('networkidle');

            const { from, to } = await pillText(page);
            expect(from).toContain('Jan 1, 2024');
            expect(to).toContain('Jun 30, 2024');
        });

        test('reset button returns to This Month', async ({ page }) => {
            await goToReport(page, report.slug);
            // First go to a different preset
            await openDatePill(page);
            await pickPreset(page, 'Last 30 Days');

            // Then open again and reset
            await openDatePill(page);
            await page.click('.rp-drp-reset');
            await page.waitForLoadState('networkidle');

            // Panel should close and pill should show This Month range
            await expect(page.locator('.rp-drp-panel')).toBeHidden();
            const { from } = await pillText(page);
            expect(from).not.toBe('—');
        });

        test('KPI cards render values (not blank/error)', async ({ page }) => {
            await goToReport(page, report.slug);
            const cards = page.locator('.rp-kpi-card');
            const count = await cards.count();
            expect(count).toBeGreaterThan(0);

            for (let i = 0; i < count; i++) {
                const val = await cards.nth(i).locator('.rp-kpi-value').innerText();
                // Should not be empty
                expect(val.trim()).not.toBe('');
            }
        });

        test('table renders without PHP errors', async ({ page }) => {
            await goToReport(page, report.slug);
            // No Laravel error page
            await expect(page.locator('body')).not.toContainText('Whoops!');
            await expect(page.locator('body')).not.toContainText('ErrorException');
            await expect(page.locator('.rp-table')).toBeVisible();
        });

    });
}

// ── Secondary filter tests ────────────────────────────────────────────────────

test.describe('Payment Report – gateway filter', () => {
    test('changing gateway select refreshes data without page reload', async ({ page }) => {
        await goToReport(page, 'reports/payments');

        // Track navigations — should be zero after filter change
        let navigations = 0;
        page.on('framenavigated', () => navigations++);

        // Change gateway
        const select = page.locator('select[wire\\:model\\.live="gateway"]');
        await expect(select).toBeVisible();
        await select.selectOption('stripe');
        await page.waitForLoadState('networkidle');

        expect(navigations).toBe(0); // no full navigation
        await expect(page.locator('body')).not.toContainText('Whoops!');
    });

    test('date filter preserved when gateway changes', async ({ page }) => {
        await goToReport(page, 'reports/payments');
        // Set year preset first
        await openDatePill(page);
        await pickPreset(page, 'This Year');

        const { from: fromBefore } = await pillText(page);

        // Now change gateway
        await page.locator('select[wire\\:model\\.live="gateway"]').selectOption('aba');
        await page.waitForLoadState('networkidle');

        const { from: fromAfter } = await pillText(page);
        expect(fromAfter).toBe(fromBefore); // date unchanged
    });
});

test.describe('Payout Report – status filter', () => {
    test('changing status updates table without page reload', async ({ page }) => {
        await goToReport(page, 'reports/payouts');

        let navigations = 0;
        page.on('framenavigated', () => navigations++);

        await page.locator('select[wire\\:model\\.live="status"]').selectOption('approved');
        await page.waitForLoadState('networkidle');

        expect(navigations).toBe(0);
        await expect(page.locator('body')).not.toContainText('Whoops!');
    });
});

test.describe('User Report – role and status filters', () => {
    test('role filter works reactively', async ({ page }) => {
        await goToReport(page, 'reports/users');

        let navigations = 0;
        page.on('framenavigated', () => navigations++);

        await page.locator('select[wire\\:model\\.live="role"]').selectOption('instructor');
        await page.waitForLoadState('networkidle');

        expect(navigations).toBe(0);
        await expect(page.locator('body')).not.toContainText('Whoops!');
    });

    test('status filter works reactively', async ({ page }) => {
        await goToReport(page, 'reports/users');

        let navigations = 0;
        page.on('framenavigated', () => navigations++);

        await page.locator('select[wire\\:model\\.live="status"]').selectOption('active');
        await page.waitForLoadState('networkidle');

        expect(navigations).toBe(0);
    });
});

test.describe('Paginated reports – pagination', () => {
    test('Revenue Report next page button works', async ({ page }) => {
        await goToReport(page, 'reports/revenue');

        const nextBtn = page.locator('button[wire\\:click*="page"]', { hasText: 'Next' });
        if (await nextBtn.isVisible()) {
            let navigations = 0;
            page.on('framenavigated', () => navigations++);

            await nextBtn.click();
            await page.waitForLoadState('networkidle');

            expect(navigations).toBe(0); // AJAX, not navigation
        } else {
            // Only 1 page of data — that's fine
            test.skip(true, 'Only 1 page of results, skipping pagination test');
        }
    });
});
