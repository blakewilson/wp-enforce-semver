import { test, expect } from '@playwright/test';

test('test env is running', async ({ page }) => {
  await page.goto('http://localhost:8888')

  expect(true).toBe(true)
})

test('has test plugin installed', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-admin/plugins.php');

  await expect(page.getByRole('row').nth(2)).toContainText('My Test Plugin');
});

// test('get started link', async ({ page }) => {
//   await page.goto('https://playwright.dev/');
// 
//   // Click the get started link.
//   await page.getByRole('link', { name: 'Get started' }).click();
// 
//   // Expects page to have a heading with the name of Installation.
//   await expect(page.getByRole('heading', { name: 'Installation' })).toBeVisible();
// });
