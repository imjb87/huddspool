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

test('player picker searches, selects and clears players on desktop and mobile', async ({ page }) => {
    const manifest = loadManifest();

    await login(page, manifest.accounts.home, manifest.accounts.password);

    for (const viewport of [{ width: 1280, height: 900 }, { width: 390, height: 844 }]) {
        await page.setViewportSize(viewport);
        await openResultForm(page, manifest.score_fixture_id);

        const homeTrigger = page.locator('[data-player-picker-trigger][data-result-field-key="1.home_player_id"]');
        const awayTrigger = page.locator('[data-player-picker-trigger][data-result-field-key="1.away_player_id"]');
        const homeSelect = page.locator('select[data-result-frame-field][data-frame-number="1"][data-frame-side="home"][data-frame-value="player"]');
        const awaySelect = page.locator('select[data-result-frame-field][data-frame-number="1"][data-frame-side="away"][data-frame-value="player"]');
        const dialog = page.locator('[data-result-player-picker-dialog]');
        const shell = page.locator('[data-result-player-picker-shell]');
        const homeAvatar = page.locator('[data-result-field-group][data-result-field-key="1.home_player_id"] img.ui-result-avatar');
        const awayAvatar = page.locator('[data-result-field-group][data-result-field-key="1.away_player_id"] img.ui-result-avatar');
        const scorePill = page.locator('label[for="result-frame-1-home-score"]');

        const arrowToScoreSpacing = await homeTrigger.evaluate((trigger) => {
            const playerField = trigger.closest('[data-result-field-group]');
            const scoreControl = playerField.parentElement.querySelector('.ui-result-form-score-control');
            const arrow = trigger.querySelector('svg');

            return scoreControl.getBoundingClientRect().left - arrow.getBoundingClientRect().right;
        });
        const scoreCursor = await scorePill.evaluate((label) => window.getComputedStyle(label).cursor);

        expect(arrowToScoreSpacing).toBeGreaterThanOrEqual(8);
        expect(scoreCursor).toBe('pointer');

        const pickerTriggerForClose = viewport.width >= 640
            ? page.locator('[data-player-picker-trigger]').last()
            : homeTrigger;

        await pickerTriggerForClose.scrollIntoViewIfNeeded();
        const scrollPositionBeforeOpen = await page.evaluate(() => window.scrollY);
        await pickerTriggerForClose.click();
        await expect(dialog).toBeVisible();

        const shellBounds = await shell.boundingBox();
        if (viewport.width < 640) {
            expect(Math.abs(shellBounds.width - viewport.width)).toBeLessThanOrEqual(2);
        } else {
            expect(shellBounds.width).toBeLessThanOrEqual(520);
        }

        const viewportStyle = await dialog.evaluate((element) => ({
            top: element.style.top,
            height: element.style.height,
        }));
        expect(viewport.width < 640 || scrollPositionBeforeOpen > 0).toBe(true);
        const scrollPositionBeforeClose = await page.evaluate(() => window.scrollY);

        await dialog.getByRole('button', { name: 'Close player selection' }).click();
        await expect(dialog).toBeHidden();
        expect(await dialog.evaluate((element) => ({
            top: element.style.top,
            height: element.style.height,
        }))).toEqual(viewportStyle);
        expect(await page.evaluate(() => window.scrollY)).toBe(scrollPositionBeforeClose);

        await homeTrigger.click();
        await expect(dialog).toBeVisible();

        const playerOptions = dialog.locator('[data-result-player-option]');
        const firstPlayer = dialog.locator('[data-result-player-option][data-player-id]:not([data-player-id="0"])').first();
        await expect(firstPlayer).toBeVisible();
        const playerName = (await firstPlayer.locator('[data-player-picker-name]').textContent()).trim();
        const playerId = await firstPlayer.getAttribute('data-player-id');
        const playerAvatarUrl = await firstPlayer.locator('img').getAttribute('src');

        await expect(firstPlayer.locator('img')).toBeVisible();
        await dialog.locator('[data-result-player-picker-search]').fill(playerName);
        await expect(playerOptions).toHaveCount(1);

        const livewireUpdateRoute = '**/livewire-*/update*';
        const isLivewireUpdate = (url) => url.includes('/livewire-') && url.includes('/update');
        let releaseLivewireUpdate;
        const livewireUpdateGate = new Promise((resolve) => {
            releaseLivewireUpdate = resolve;
        });

        await page.route(livewireUpdateRoute, async (route) => {
            if (route.request().method() === 'POST') {
                await livewireUpdateGate;
            }

            await route.continue();
        });

        const updateRequest = page.waitForRequest((request) => isLivewireUpdate(request.url()) && request.method() === 'POST', { timeout: 10_000 });
        const updateResponse = page.waitForResponse((response) => isLivewireUpdate(response.url()) && response.request().method() === 'POST', { timeout: 10_000 });

        try {
            await playerOptions.first().click();
            await updateRequest;

            await expect(dialog).toBeHidden();
            await expect(homeSelect).toHaveValue(playerId);
            await expect(homeTrigger).toContainText(playerName, { timeout: 500 });
            await expect(homeAvatar).toHaveAttribute('src', playerAvatarUrl, { timeout: 500 });
        } finally {
            releaseLivewireUpdate();
            await updateResponse;
            await page.unroute(livewireUpdateRoute);
        }

        const clearResponse = page.waitForResponse((response) => isLivewireUpdate(response.url()) && response.request().method() === 'POST', { timeout: 10_000 });
        await homeTrigger.click();
        await expect(dialog).toBeVisible();
        await dialog.getByRole('button', { name: 'Clear selection' }).click();
        await clearResponse;
        await expect(homeSelect).toHaveValue('');
        await expect(homeTrigger).toContainText('Select player...');

        const awardedRoute = '**/livewire-*/update*';
        let releaseAwardedUpdate;
        const awardedUpdateGate = new Promise((resolve) => {
            releaseAwardedUpdate = resolve;
        });

        await page.route(awardedRoute, async (route) => {
            if (route.request().method() === 'POST') {
                await awardedUpdateGate;
            }

            await route.continue();
        });

        const awardedRequest = page.waitForRequest((request) => isLivewireUpdate(request.url()) && request.method() === 'POST', { timeout: 10_000 });
        const awardedResponse = page.waitForResponse((response) => isLivewireUpdate(response.url()) && response.request().method() === 'POST', { timeout: 10_000 });

        await homeTrigger.click();
        await expect(dialog).toBeVisible();
        await dialog.getByRole('option', { name: 'Awarded' }).click();
        await awardedRequest;
        await expect(homeSelect).toHaveValue('0');
        await expect(awaySelect).toHaveValue('0');
        await expect(homeTrigger).toContainText('Awarded');
        await expect(awayTrigger).toContainText('Awarded');
        await expect(homeAvatar).toHaveAttribute('src', /\/images\/user\.jpg/);
        await expect(awayAvatar).toHaveAttribute('src', /\/images\/user\.jpg/);

        releaseAwardedUpdate();
        const awardedUpdateResult = await awardedResponse;
        await page.unroute(awardedRoute);
        expect(awardedUpdateResult.status()).toBe(200);
        await expect(awayTrigger).toContainText('Awarded');

        const finalClearResponse = page.waitForResponse((response) => isLivewireUpdate(response.url()) && response.request().method() === 'POST', { timeout: 10_000 });
        await homeTrigger.click();
        await expect(dialog).toBeVisible();
        await dialog.getByRole('button', { name: 'Clear selection' }).click();
        await finalClearResponse;
        await expect(homeSelect).toHaveValue('');
        await expect(awaySelect).toHaveValue('');
        await expect(homeTrigger).toContainText('Select player...');
        await expect(awayTrigger).toContainText('Select player...');
    }
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
        const homeField = homePage.locator(`[data-player-picker-trigger][data-result-field-key="${fieldKey}"]`);
        const awayField = awayPage.locator(`select[data-result-field-key="${fieldKey}"]`);
        const awayPicker = awayPage.locator(`[data-player-picker-trigger][data-result-field-key="${fieldKey}"]`);
        const awaySurface = awayPage.locator(`[data-result-field-group][data-result-field-key="${fieldKey}"]`);
        const awayScore = awayPage.locator('#result-frame-1-home-score');
        const awayScoreBadge = awayPage.locator('label[for="result-frame-1-home-score"]');

        await homeField.focus();
        await expect(homeField).toBeFocused();
        await expect(awayField).toBeDisabled({ timeout: 10_000 });
        await expect(awayPicker).toBeDisabled();
        await expect(awayField).toHaveAttribute('title', /is editing this field/);
        await expect(awaySurface).toHaveAttribute('data-result-field-disabled', 'true');
        await expect(awaySurface).toHaveAttribute('aria-disabled', 'true');
        await expect(awaySurface).toHaveAttribute('style', /outline:/);
        await expect(awayScore).toBeDisabled();
        await expect(awayScoreBadge).toHaveAttribute('data-result-field-disabled', 'true');

        await homeField.evaluate((field) => field.blur());
        await expect(awayField).toBeEnabled({ timeout: 10_000 });
        await expect(awayPicker).toBeEnabled();
        await expect(awayScore).toBeEnabled();
        await expect(awaySurface).not.toHaveAttribute('data-result-field-disabled', 'true');

        const homeScoreBadge = homePage.locator('label[for="result-frame-1-home-score"]');
        const awayScoreText = awayPage.locator('label[for="result-frame-1-home-score"] span');

        await homeScoreBadge.click();
        await expect(awayScoreText).toHaveText('1', { timeout: 10_000 });
    } finally {
        await homeContext.close();
        await awayContext.close();
    }
});
