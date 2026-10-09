import './bootstrap';
import Alpine from 'alpinejs';
import { bootDeferredGoogleAnalytics } from './google-analytics';
import { mobileMenuIcon } from './mobile-menu-icon';
import { notificationsDrawer, registerHeaderNotificationsStore } from './notifications';
import './sponsor-carousel';

window.Alpine = Alpine;
window.mobileMenuIcon = mobileMenuIcon;
window.notificationsDrawer = notificationsDrawer;
registerHeaderNotificationsStore(Alpine);

Alpine.start();
bootDeferredGoogleAnalytics();
