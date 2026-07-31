# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: auth.setup.ts >> authenticate as super-admin
- Location: tests/playwright/auth.setup.ts:8:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: page.goto: Test timeout of 30000ms exceeded.
Call log:
  - navigating to "http://localhost:8000/admin/login", waiting until "load"

```

# Page snapshot

```yaml
- main [ref=e4]:
  - generic [ref=e8]: Hybrid Learning
```

# Test source

```ts
  1  | import { test as setup } from '@playwright/test';
  2  | import path from 'path';
  3  | import { fileURLToPath } from 'url';
  4  | 
  5  | const __dirname = path.dirname(fileURLToPath(import.meta.url));
  6  | const authFile = path.join(__dirname, '.auth.json');
  7  | 
  8  | setup('authenticate as super-admin', async ({ page }) => {
> 9  |     await page.goto('/admin/login');
     |                ^ Error: page.goto: Test timeout of 30000ms exceeded.
  10 |     await page.waitForLoadState('networkidle');
  11 | 
  12 |     // If already redirected to admin (session still valid), just save state
  13 |     if (!page.url().includes('/login')) {
  14 |         await page.context().storageState({ path: authFile });
  15 |         return;
  16 |     }
  17 | 
  18 |     // Filament 3 uses wire:model inputs identified by id="form.email"
  19 |     await page.locator('#form\\.email').fill('superadmin@example.com');
  20 |     await page.locator('#form\\.password').fill('admin123');
  21 |     await page.locator('button[type="submit"].fi-btn').click();
  22 | 
  23 |     // Wait until we leave the login page
  24 |     await page.waitForFunction(() => !window.location.href.includes('/login'), { timeout: 15_000 });
  25 |     await page.waitForLoadState('networkidle');
  26 | 
  27 |     await page.context().storageState({ path: authFile });
  28 | });
  29 | 
```