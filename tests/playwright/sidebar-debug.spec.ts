/**
 * Debug: test the exact failing scenario (RR → CI) with the waitForEnforcement fix
 */
import { test, type Page, type Locator } from '@playwright/test';

async function navScrollTop(page: Page): Promise<number> {
    return page.evaluate(() => {
        const nav = document.querySelector('.fi-sidebar-nav') as HTMLElement;
        return nav ? nav.scrollTop : -1;
    });
}
async function itemOffset(page: Page, item: Locator): Promise<number> {
    const nav  = page.locator('.fi-sidebar-nav').first();
    const nBox = await nav.boundingBox();
    const iBox = await item.boundingBox();
    if (!nBox || !iBox) throw new Error('bounding box unavailable');
    return iBox.y - nBox.y;
}
async function waitForSidebar(page: Page) {
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(350);
}
async function waitForEnforcement(page: Page) {
    await page.waitForFunction(
        () => (window as any)._hlEnfTarget === null,
        { timeout: 3000 }
    );
    await page.waitForTimeout(20);
}
async function jsState(page: Page) {
    return page.evaluate(() => ({
        clickedOffset: (window as any)._hlClickedOffset,
        navScroll:     (window as any)._hlNavScroll,
        navGen:        (window as any)._hlNavGen,
        enfTarget:     (window as any)._hlEnfTarget,
    }));
}

test.describe('Sidebar scroll debug: RR→CI with waitForEnforcement', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/admin/reports/course-intelligence');
        await waitForSidebar(page);
    });

    test('Revenue Report → Course Intelligence with enforcement wait', async ({ page }) => {
        console.log('=== Start on CI page ===');
        let st = await navScrollTop(page);
        console.log(`  scrollTop=${st}, enfTarget=${(await jsState(page)).enfTarget}`);

        // Step 1: click Revenue Report (identical to test's first clickItem call)
        console.log('\n=== STEP 1: click Revenue Report ===');
        const rrBtn = page.locator('.fi-sidebar-item-btn, .fi-sidebar-item-button', { hasText: 'Revenue Report' }).first();

        await waitForEnforcement(page);
        console.log(`  After waitForEnf: st=${await navScrollTop(page)}, enf=${JSON.stringify(await jsState(page))}`);

        await rrBtn.scrollIntoViewIfNeeded();
        await page.waitForTimeout(80);
        const rrOffBefore = await itemOffset(page, rrBtn);
        const stBefore = await navScrollTop(page);
        console.log(`  Before click: st=${stBefore}, RR offset=${rrOffBefore.toFixed(1)}`);

        await rrBtn.click();
        await waitForSidebar(page);

        const rrActive = page.locator('.fi-sidebar-item.fi-active .fi-sidebar-item-btn').first();
        const rrOffAfter = await itemOffset(page, rrActive);
        const stAfterRR = await navScrollTop(page);
        console.log(`  After RR nav: st=${stAfterRR}, RR offset=${rrOffAfter.toFixed(1)}, drift=${Math.abs(rrOffAfter-rrOffBefore).toFixed(1)}px`);
        console.log(`  JS: ${JSON.stringify(await jsState(page))}`);

        // Step 2: click Course Intelligence (identical to test's second clickItem call)
        console.log('\n=== STEP 2: click Course Intelligence (the failing step) ===');
        const ciBtn = page.locator('.fi-sidebar-item-btn, .fi-sidebar-item-button', { hasText: 'Course Intelligence' }).first();

        const stBeforeWait = await navScrollTop(page);
        const enfBeforeWait = (await jsState(page)).enfTarget;
        console.log(`  BEFORE waitForEnf: st=${stBeforeWait}, enfTarget=${enfBeforeWait}`);

        await waitForEnforcement(page);
        const stAfterWait = await navScrollTop(page);
        const enfAfterWait = (await jsState(page)).enfTarget;
        console.log(`  AFTER waitForEnf: st=${stAfterWait}, enfTarget=${enfAfterWait}`);

        await ciBtn.scrollIntoViewIfNeeded();
        await page.waitForTimeout(80);
        const stAfterSiv = await navScrollTop(page);
        const ciOffBefore = await itemOffset(page, ciBtn);
        console.log(`  After scrollIntoView+80ms: st=${stAfterSiv}, CI offset=${ciOffBefore.toFixed(1)}`);
        console.log(`  JS before click: ${JSON.stringify(await jsState(page))}`);

        await ciBtn.click();

        // Poll during navigation
        for (let i = 0; i < 20; i++) {
            await page.waitForTimeout(50);
            const sc = await navScrollTop(page);
            const js = await jsState(page);
            console.log(`  t+${(i+1)*50}ms: st=${sc}, gen=${js.navGen}, enf=${js.enfTarget}, clickedOff=${js.clickedOffset}`);
        }

        await waitForSidebar(page);

        const ciActive = page.locator('.fi-sidebar-item.fi-active .fi-sidebar-item-btn').first();
        const ciOffAfter = await itemOffset(page, ciActive);
        const stFinal = await navScrollTop(page);
        const drift = Math.abs(ciOffAfter - ciOffBefore);
        console.log(`\n  RESULT: st=${stFinal}, CI offset=${ciOffAfter.toFixed(1)}, offsetBefore=${ciOffBefore.toFixed(1)}, DRIFT=${drift.toFixed(1)}px`);
    });
});
