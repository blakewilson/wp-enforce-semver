import { test as setup, expect } from '@playwright/test';

const authFile = 'playwright/.auth/user.json';

setup('authenticate', async ({ page }) => {
  // Perform authentication steps. Replace these actions with your own.
  await page.goto('http://localhost:8888/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Password', {exact: true}).fill('password');
  await page.getByRole('button', { name: 'Log In' }).click();
  // Wait until the page receives the cookies.
  //
  // Sometimes login flow sets cookies in the process of several redirects.
  // Wait for the final URL to ensure that the cookies are actually set.
  // await page.waitForURL('http://localhost:8888/wp-admin');
  // Alternatively, you can wait until the page reaches a state where all cookies are set.
  await expect(page.getByRole('menuitem', { name: 'Howdy, admin' })).toBeVisible();

  // End of authentication steps.
  await page.context().storageState({ path: authFile });
});
