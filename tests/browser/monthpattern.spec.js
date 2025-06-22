const { test, expect } = require('@playwright/test');
const { execSync } = require('child_process');
const path = require('path');

const root = path.resolve(__dirname, '../..');

function getPattern(expr) {
  const cmd = `php -r "require 'vendor/autoload.php'; echo ${expr};"`;
  let pattern = execSync(cmd, { cwd: root }).toString();
  pattern = pattern.trim().replace(/\\-/g, '-');
  return pattern;
}

test('default pattern validates months', async ({ page }) => {
  const pattern = getPattern('Html5PatternGenerator\\\\Pattern\\\\MonthPatternGenerator::pattern()');

  await page.setContent('<form><input id="month"></form>');
  await page.evaluate((p) => {
    document.getElementById('month').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#month');
  await input.fill('2024-05');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-13');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('custom format m/Y validates months', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\MonthPatternGenerator::pattern('m/Y')");

  await page.setContent('<form><input id="month"></form>');
  await page.evaluate((p) => {
    document.getElementById('month').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#month');
  await input.fill('05/2024');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-05');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});
