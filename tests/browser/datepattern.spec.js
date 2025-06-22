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

test('default pattern validates dates', async ({ page }) => {
  const pattern = getPattern('Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::pattern()');

  await page.setContent('<form><input id="date"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');

  await input.fill('2024-05-01');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-13-01');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('custom format d.m.Y validates dates', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::pattern('d.m.Y')");

  await page.setContent('<form><input id="date"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');

  await input.fill('01.05.2024');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-05-01');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('between pattern restricts date range', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::between(new DateTimeImmutable('2024-05-01'), new DateTimeImmutable('2024-05-03'))");

  await page.setContent('<form><input id="date"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');

  await input.fill('2024-05-02');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-05-04');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('after and before patterns', async ({ page }) => {
  const afterPattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::after(new DateTimeImmutable('2024-06-01'), 2)");
  const beforePattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::before(new DateTimeImmutable('2024-07-10'), 1)");

  await page.setContent('<form><input id="after"><input id="before"></form>');
  await page.evaluate(({after, before}) => {
    document.getElementById('after').setAttribute('pattern', after);
    document.getElementById('before').setAttribute('pattern', before);
  }, { after: afterPattern, before: beforePattern });

  const afterInput = page.locator('#after');
  await afterInput.fill('2024-06-03');
  const afterValid = await afterInput.evaluate(el => el.checkValidity());
  expect(afterValid).toBe(true);
  await afterInput.fill('2024-06-04');
  const afterInvalid = await afterInput.evaluate(el => el.checkValidity());
  expect(afterInvalid).toBe(false);

  const beforeInput = page.locator('#before');
  await beforeInput.fill('2024-07-09');
  const beforeValid = await beforeInput.evaluate(el => el.checkValidity());
  expect(beforeValid).toBe(true);
  await beforeInput.fill('2024-07-08');
  const beforeInvalid = await beforeInput.evaluate(el => el.checkValidity());
  expect(beforeInvalid).toBe(false);
});

test('custom format Y.m.d validates dates', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::pattern('Y.m.d')");

  await page.setContent('<form><input id="date"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');
  await input.fill('2024.05.01');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('2024-05-01');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('format with slashes Y/m/d validates dates', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::pattern('Y/m/d')");

  await page.setContent('<form><input id="date"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');
  await input.fill('2024/05/01');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('05/01/2024');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});

test('between pattern with custom format', async ({ page }) => {
  const pattern = getPattern("Html5PatternGenerator\\\\Pattern\\\\DatePatternGenerator::between(new DateTimeImmutable('2024-05-01'), new DateTimeImmutable('2024-05-03'), 'd.m.Y')");

  await page.setContent('<form><input id=\"date\"></form>');
  await page.evaluate((p) => {
    document.getElementById('date').setAttribute('pattern', p);
  }, pattern);

  const input = page.locator('#date');
  await input.fill('02.05.2024');
  const valid = await input.evaluate(el => el.checkValidity());
  expect(valid).toBe(true);

  await input.fill('04.05.2024');
  const invalid = await input.evaluate(el => el.checkValidity());
  expect(invalid).toBe(false);
});
