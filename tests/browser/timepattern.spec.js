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

test('default pattern validates times', async ({ page }) => {
  const pattern = getPattern('Html5PatternGenerator\\\\Pattern\\\\TimePatternGenerator::pattern()');

  await page.setContent('<form><input id="time"></form>');
  await page.evaluate((p) => {
    document.getElementById('time').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#time');
  await input.fill('23:59');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('24:00');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('pattern with seconds validates times', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\TimePatternGenerator::pattern('H:i:s')");

  await page.setContent('<form><input id="time"></form>');
  await page.evaluate((p) => {
    document.getElementById('time').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#time');
  await input.fill('12:30:15');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('12:30:60');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});
