const fs = require('node:fs');
const path = require('node:path');
const { expect, test } = require('@playwright/test');

function loadManifest() {
    const manifestPath = process.env.E2E_FIXTURE_MANIFEST
        ?? path.resolve(process.cwd(), 'storage/app/e2e-fixtures.json');

    if (! fs.existsSync(manifestPath)) {
        throw new Error(`E2E fixture manifest not found at ${manifestPath}. Run npm run e2e:up first.`);
    }

    return JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
}

async function login(page, email, password) {
    await page.goto('/login');
    await page.getByLabel('Email address').fill(email);
    await page.getByLabel('Password').fill(password);
    await page.getByRole('button', { name: 'Log in', exact: true }).click();
    await expect(page.locator('[data-login-page]')).toHaveCount(0);
}

async function openResultForm(page, fixtureId) {
    await page.goto(`/results/create/${fixtureId}`, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('[data-result-form]')).toBeVisible();
    await expect(page.locator('[data-result-form-connection-status]'))
        .toHaveAttribute('aria-label', 'Live updates connected', { timeout: 15_000 });
}

test('score radios select a winner and reset an existing winner', async ({ page }) => {
    const manifest = loadManifest();

    await login(page, manifest.accounts.home, manifest.accounts.password);
    await openResultForm(page, manifest.score_fixture_id);

    const homeScore = page.locator('#result-frame-1-home-score');
    const awayScore = page.locator('#result-frame-1-away-score');
    const homeBadge = page.locator('label[for="result-frame-1-home-score"]');
    const awayBadge = page.locator('label[for="result-frame-1-away-score"]');

    await expect(page.locator('input[type="radio"][data-frame-value="score"]')).toHaveCount(20);
    await expect(homeScore).not.toBeChecked();
    await expect(awayScore).not.toBeChecked();
    await expect(homeBadge.locator('span')).toHaveText('0');
    await expect(awayBadge.locator('span')).toHaveText('0');
    await expect(homeBadge).toHaveClass(/ui-fixture-badge-neutral/);
    await expect(awayBadge).toHaveClass(/ui-fixture-badge-neutral/);

    await homeBadge.click();

    await expect(homeScore).toBeChecked();
    await expect(awayScore).not.toBeChecked();
    await expect(homeBadge.locator('span')).toHaveText('1');
    await expect(awayBadge.locator('span')).toHaveText('0');
    await expect(homeBadge).toHaveClass(/ui-live-score-badge-win/);
    await expect(awayBadge).toHaveClass(/ui-live-score-badge-loss/);

    await homeBadge.click();

    await expect(homeScore).not.toBeChecked();
    await expect(awayScore).not.toBeChecked();
    await expect(homeBadge.locator('span')).toHaveText('0');
    await expect(awayBadge.locator('span')).toHaveText('0');
    await expect(homeBadge).toHaveClass(/ui-fixture-badge-neutral/);
    await expect(awayBadge).toHaveClass(/ui-fixture-badge-neutral/);
});

test('presence and player-field locks synchronise between two editors', async ({ browser }) => {
    const manifest = loadManifest();
    const homeContext = await browser.newContext();
    const awayContext = await browser.newContext();
    const homePage = await homeContext.newPage();
    const awayPage = await awayContext.newPage();

    try {
        await login(homePage, manifest.accounts.home, manifest.accounts.password);
        await openResultForm(homePage, manifest.collaboration_fixture_id);
        await expect(homePage.getByText('1 person here', { exact: true })).toBeVisible();

        await login(awayPage, manifest.accounts.away, manifest.accounts.password);
        await openResultForm(awayPage, manifest.collaboration_fixture_id);

        await expect(homePage.getByText('2 people here', { exact: true })).toBeVisible();
        await expect(awayPage.getByText('2 people here', { exact: true })).toBeVisible();

        const fieldKey = '1.home_player_id';
        const homeField = homePage.locator(`select[data-result-field-key="${fieldKey}"]`);
        const awayField = awayPage.locator(`select[data-result-field-key="${fieldKey}"]`);

        await homeField.focus();
        await expect(homeField).toBeFocused();
        await expect(awayField).toBeDisabled({ timeout: 10_000 });
        await expect(awayField).toHaveAttribute('title', /is editing this field/);

        await homeField.evaluate((field) => field.blur());
        await expect(awayField).toBeEnabled({ timeout: 10_000 });

        const homeScoreBadge = homePage.locator('label[for="result-frame-1-home-score"]');
        const awayScoreBadge = awayPage.locator('label[for="result-frame-1-home-score"] span');

        await homeScoreBadge.click();
        await expect(awayScoreBadge).toHaveText('1', { timeout: 10_000 });
    } finally {
        await homeContext.close();
        await awayContext.close();
    }
});
