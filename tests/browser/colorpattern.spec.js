const { test, expect } = require('@playwright/test');
const { execSync } = require('child_process');
const path = require('path');

const root = path.resolve(__dirname, '../..');

function getPattern() {
  const cmd = "php -r \"require 'vendor/autoload.php'; echo Html5PatternGenerator\\\\Pattern\\\\ColorPatternGenerator::pattern();\"";
  let pattern = execSync(cmd, { cwd: root }).toString();
  pattern = pattern.trim().replace(/\\-/g, '-');
  return pattern;
}

test('pattern validates hex colors', async ({ page }) => {
  const pattern = getPattern();

  await page.setContent('<form><input id="color"></form>');
  await page.evaluate((p) => {
    document.getElementById('color').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#color');
  await input.fill('#abc');
  let valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('#abcdef');
  valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('abc');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});
