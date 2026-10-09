import './bootstrap';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
import { bootDeferredGoogleAnalytics } from './google-analytics';
import { mobileMenuIcon } from './mobile-menu-icon';
import { notificationsDrawer, registerHeaderNotificationsStore } from './notifications';

window.Alpine = Alpine;
window.mobileMenuIcon = mobileMenuIcon;
window.notificationsDrawer = notificationsDrawer;
registerHeaderNotificationsStore(Alpine);

Livewire.start();
bootDeferredGoogleAnalytics();
