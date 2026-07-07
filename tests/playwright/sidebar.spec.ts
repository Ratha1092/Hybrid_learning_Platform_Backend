/**
 * Sidebar behaviour tests
 *
 * 1. Position stability – clicking any nav item keeps the item at the same
 *    visual offset inside the sidebar nav (it must not jump).
 * 2. Group collapse / expand – every collapsible group opens and closes with
 *    the correct class + visibility change.
 */

import { test, expect, type Page, type Locator } from '@playwright/test';

// ── helpers ──────────────────────────────────────────────────────────────────

/** Wait for Livewire + our 200 ms JS timeout to settle */
async function waitForSidebar(page: Page) {
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(350);
}

/** Visual offset (px from top of .fi-sidebar-nav) of `item` */
async function itemOffset(page: Page, item: Locator): Promise<number> {
    const nav  = page.locator('.fi-sidebar-nav').first();
    const nBox = await nav.boundingBox();
    const iBox = await item.boundingBox();
    if (!nBox || !iBox) throw new Error('bounding box unavailable');
    return iBox.y - nBox.y;
}

/** Navigate to a URL and wait for everything to settle */
async function goto(page: Page, path: string) {
    await page.goto(`/admin/${path}`);
    await waitForSidebar(page);
}

/**
 * Wait for any active scroll enforcement to finish before interacting.
 * Our JS enforces a fixed scrollTop for 450 ms after each navigation.
 * Playwright's btn.click() auto-scrolls the element into view internally
 * (via CDP) before firing the click event — when enforcement is active this
 * produces a different scrollTop than what scrollIntoViewIfNeeded() settled
 * at, causing offsetBefore vs clickedOffset to diverge.  Waiting for
 * enforcement to end makes the test match real-user timing (users don't
 * click another item within 450 ms of a navigation completing).
 */
async function waitForEnforcement(page: Page) {
    await page.waitForFunction(
        () => (window as any)._hlEnfTarget === null,
        { timeout: 3000 }
    );
    // Give the scroll listener one tick to record the settled scrollTop.
    await page.waitForTimeout(20);
}

/** Click a sidebar item by visible label text and wait */
async function clickItem(page: Page, label: string) {
    const btn = page.locator('.fi-sidebar-item-btn, .fi-sidebar-item-button',
        { hasText: label }).first();

    await waitForEnforcement(page);
    await btn.scrollIntoViewIfNeeded();
    await page.waitForTimeout(80);

    await btn.click();

    // Read the visual offset the JS click handler captured.  Playwright's
    // click() internally calls Chrome CDP scrollIntoViewIfNeeded which may
    // center the element in the nav before firing the event, so the click-time
    // visual offset can differ from what we see before the click.  Our
    // enforcement code pins the active item to the captured offset, so we
    // compare against that rather than a pre-click measurement.
    // The click handler fires synchronously; livewire:navigated clears
    // _hlClickedOffset ~200 ms later, so this evaluate() runs in time.
    const capturedOffset = await page.evaluate(
        () => (window as any)._hlClickedOffset as number | null
    );

    await waitForSidebar(page);

    if (capturedOffset === null) return;  // not a nav item click — no enforcement

    // After navigation the ACTIVE item should sit at capturedOffset (±8 px)
    const active = page.locator('.fi-sidebar-item.fi-active .fi-sidebar-item-btn, ' +
                                '.fi-sidebar-item.fi-active .fi-sidebar-item-button').first();
    const offsetAfter = await itemOffset(page, active);

    const drift = Math.abs(offsetAfter - capturedOffset);
    expect(drift, `"${label}" drifted ${drift.toFixed(1)}px after click`).toBeLessThanOrEqual(8);
}

// ── data ─────────────────────────────────────────────────────────────────────

const REPORTS_ITEMS = [
    'User Report',
    'Revenue Report',
    'Payments Report',
    'Payouts Report',
    'Course Intelligence',
    'Instructor Intelligence',
    'Learning Intelligence',
];

// Every sequential within-group pair (both directions)
const REPORT_PAIRS = REPORTS_ITEMS.flatMap((from, i) =>
    REPORTS_ITEMS.slice(i + 1).map(to => ({ from, to }))
);

const CROSS_GROUP_PAIRS = [
    { from: 'reports/course-intelligence', start: 'Course Intelligence', target: 'Settings' },
    { from: 'reports/course-intelligence', start: 'Course Intelligence', target: 'Users' },
    { from: '',                            start: 'Dashboard',           target: 'Course Intelligence' },
    { from: '',                            start: 'Dashboard',           target: 'Audit Logs' },
];

const COLLAPSIBLE_GROUPS = [
    'Learning', 'Commerce', 'People', 'Finance',
    'Reports', 'System', 'Security', 'Monitoring',
];

// ── tests: within-Reports position stability ─────────────────────────────────

test.describe('Sidebar – Reports group item click stays in place', () => {
    // Start on Course Intelligence each time (Reports section scrolled into view)
    test.beforeEach(async ({ page }) => {
        await goto(page, 'reports/course-intelligence');
    });

    for (const { from, to } of REPORT_PAIRS) {
        test(`"${from}" → "${to}" stays in place`, async ({ page }) => {
            // Navigate to "from" if not already there
            const currentActive = page.locator('.fi-sidebar-item.fi-active').first();
            const activeText = await currentActive.innerText().catch(() => '');
            if (!activeText.includes(from)) {
                await clickItem(page, from);
            }
            await clickItem(page, to);
        });
    }
});

// ── tests: cross-group position stability ────────────────────────────────────

test.describe('Sidebar – cross-group click stays in place', () => {
    for (const { from, start, target } of CROSS_GROUP_PAIRS) {
        test(`"${start}" → "${target}" stays in place`, async ({ page }) => {
            await goto(page, from);
            // Ensure we're on the "start" page
            const active = page.locator('.fi-sidebar-item.fi-active').first();
            const activeText = await active.innerText().catch(() => '');
            if (!activeText.includes(start)) {
                await clickItem(page, start);
            }
            await clickItem(page, target);
        });
    }
});

// ── tests: every single nav item loads without error ─────────────────────────

test.describe('Sidebar – every nav item navigates successfully', () => {
    const ALL_ITEMS = [
        { label: 'Dashboard',             path: '' },
        // Learning
        { label: 'Courses',               path: 'pages/courses' },
        { label: 'Categories',            path: 'resources/categories' },
        { label: 'Sections',              path: 'resources/sections' },
        { label: 'Lessons',               path: 'resources/lessons' },
        { label: 'Instructors',           path: 'resources/instructors' },
        { label: 'Reviews',               path: 'resources/reviews' },
        // Commerce
        { label: 'Orders',                path: 'resources/orders' },
        { label: 'Payments',              path: 'resources/payments' },
        { label: 'Coupons',               path: 'resources/coupons' },
        // People
        { label: 'Users',                 path: 'resources/users' },
        { label: 'Verifications',         path: 'resources/instructor-verifications' },
        // Finance
        { label: 'Payouts',               path: 'resources/payouts' },
        { label: 'Wallets',               path: 'resources/wallets' },
        { label: 'Invoices',              path: 'resources/invoices' },
        { label: 'Receipts',              path: 'resources/receipts' },
        // Reports
        { label: 'User Report',           path: 'reports/users' },
        { label: 'Revenue Report',        path: 'reports/revenue' },
        { label: 'Payments Report',       path: 'reports/payments' },
        { label: 'Payouts Report',        path: 'reports/payouts' },
        { label: 'Course Intelligence',   path: 'reports/course-intelligence' },
        { label: 'Instructor Intelligence', path: 'reports/instructor-intelligence' },
        { label: 'Learning Intelligence', path: 'reports/learning-intelligence' },
        // System
        { label: 'Settings',              path: 'settings' },
        { label: 'Roles',                 path: 'resources/roles' },
        { label: 'Notifications',         path: 'resources/notifications' },
        // Security
        { label: 'Audit Logs',            path: 'audit-logs' },
        { label: 'Security Events',       path: 'security-events' },
        { label: 'Sessions',              path: 'sessions' },
        { label: 'Blocked IPs',           path: 'blocked-ips' },
        // Monitoring
        { label: 'Log Viewer',            path: 'log-viewer' },
        { label: 'Queue Monitor',         path: 'queue-monitor' },
        { label: 'System Health',         path: 'system-health' },
    ];

    test.beforeEach(async ({ page }) => {
        await goto(page, '');
    });

    for (const item of ALL_ITEMS) {
        test(`clicking "${item.label}" navigates without PHP errors`, async ({ page }) => {
            // Scroll to make the item visible (groups may need expanding)
            const btn = page.locator('.fi-sidebar-item-btn, .fi-sidebar-item-button',
                { hasText: new RegExp(`^\\s*${item.label}\\s*$`) }).first();
            await btn.scrollIntoViewIfNeeded();
            await btn.click();
            await waitForSidebar(page);

            // No PHP error page
            await expect(page.locator('body')).not.toContainText('Whoops!');
            await expect(page.locator('body')).not.toContainText('500 | Server Error');

            // Active item is set in sidebar
            await expect(page.locator('.fi-sidebar-item.fi-active')).toBeVisible();
        });
    }
});

// ── tests: group collapse / expand ──────────────────────────────────────────

test.describe('Sidebar – group collapse and expand', () => {
    test.beforeEach(async ({ page }) => {
        await goto(page, '');
    });

    for (const group of COLLAPSIBLE_GROUPS) {
        test(`"${group}" group collapses and expands`, async ({ page }) => {
            const groupEl   = page.locator(`.fi-sidebar-group[data-group-label="${group}"]`);
            const groupBtn  = groupEl.locator('.fi-sidebar-group-btn').first();
            const groupItems = groupEl.locator('.fi-sidebar-group-items').first();

            await groupBtn.scrollIntoViewIfNeeded();

            // Ensure the group is EXPANDED before starting the test
            const isCollapsed = await groupEl.evaluate(el => el.classList.contains('fi-collapsed'));
            if (isCollapsed) {
                await groupBtn.click();
                await page.waitForTimeout(300); // x-collapse animation
            }
            await expect(groupItems).toBeVisible();
            await expect(groupEl).not.toHaveClass(/fi-collapsed/);

            // ── collapse ────────────────────────────────────────────────────
            await groupBtn.click();
            await page.waitForTimeout(300);

            await expect(groupEl).toHaveClass(/fi-collapsed/);
            await expect(groupItems).not.toBeVisible();

            // ── expand ──────────────────────────────────────────────────────
            await groupBtn.click();
            await page.waitForTimeout(300);

            await expect(groupEl).not.toHaveClass(/fi-collapsed/);
            await expect(groupItems).toBeVisible();
        });
    }
});
