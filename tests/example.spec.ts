import { test, expect } from '@playwright/test';
import { exec } from 'child_process';

test('test env is running', async ({ page }) => {
  await page.goto('http://localhost:8888')

  expect(true).toBe(true)
})

test('has test plugin installed', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-admin/plugins.php');

  let pluginRow = page.locator('tr.update[data-plugin="my-test-plugin/my-test-plugin.php"]')

  await expect(pluginRow).toContainText('My Test Plugin')
});

test('BREAKING CHANGE: message is properly displayed in plugins list', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-admin/plugins.php');

  let pluginUpdateRow = page.locator('#my-test-plugin-update .update-message')

  await expect(pluginUpdateRow).toContainText('THIS UPDATE MAY CONTAIN BREAKING CHANGES: This plugin uses Semantic Versioning, and this new version is a major release. Please review the changelog before updating. Learn more')
})

test('BREAKING CHANGE: auto updates are properly disabled in plugins list', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-admin/plugins.php');

  let pluginRow = page.locator('tr.update[data-plugin="my-test-plugin/my-test-plugin.php"]')

  await expect(pluginRow).toContainText('Auto-updates disabled')

})

test('BREAKING CHANGE: plugin shows breaking change custom message', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-admin/plugins.php');

  // Turn on the filter plugin
  let customBreakingChangePluginInactiveRow = page.locator('tr.inactive[data-plugin="alter-breaking-change-text/alter-breaking-change-text.php"]')
  await customBreakingChangePluginInactiveRow.getByLabel('Activate Alter Breaking Change Text').click();

  // Validate
  let pluginUpdateRow = page.locator('#my-test-plugin-update .update-message')
  await expect(pluginUpdateRow).toContainText('Custom breaking change notice text')

  // Cleanup
  let customBreakingChangePluginActiveRow = page.locator('tr.active[data-plugin="alter-breaking-change-text/alter-breaking-change-text.php"]')
  await customBreakingChangePluginActiveRow.getByLabel('Deactivate Alter Breaking Change Text').click();
  

  await expect(customBreakingChangePluginInactiveRow).toBeVisible();
})

test('plugin does not alter plugin list row with non breaking change', async () => {
  exec('npm run wp-env -- run cli "wp option add test_plugin_version 1.0.5"')
})
