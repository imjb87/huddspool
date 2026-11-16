import './bootstrap';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
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

    if (document.querySelector('[data-result-form]')) {
        modules.push(import('./result-form'));
    }

    if (document.querySelector('[data-account-push-settings], [data-auto-push-permission-prompt]')) {
        modules.push(import('./push-notifications'));
    }

    await Promise.all(modules);
};

loadPageModules().then(() => {
    Livewire.start();
    bootDeferredGoogleAnalytics();
});
