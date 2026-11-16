import './bootstrap';
import './home-hero-orbits';
import Alpine from 'alpinejs';
import { bootDeferredGoogleAnalytics } from './google-analytics';
import { headerActionIcon, mobileMenuIcon } from './mobile-menu-icon';
import { notificationsDrawer, registerHeaderNotificationsStore } from './notifications';
import './sponsor-carousel';

window.Alpine = Alpine;
window.mobileMenuIcon = mobileMenuIcon;
window.headerActionIcon = headerActionIcon;
window.notificationsDrawer = notificationsDrawer;
registerHeaderNotificationsStore(Alpine);

const loadPageModules = async () => {
    const modules = [];

    if (document.querySelector('[data-home-live-scores]')) {
        modules.push(import('./home-live-scores'));
    }

    if (document.querySelector('[data-account-push-settings], [data-auto-push-permission-prompt]')) {
        modules.push(import('./push-notifications'));
    }

    await Promise.all(modules);
};

loadPageModules().then(() => {
    Alpine.start();
    bootDeferredGoogleAnalytics();
});
