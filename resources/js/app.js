import './bootstrap';
import Alpine from 'alpinejs';
import { bootDeferredGoogleAnalytics } from './google-analytics';
import { notificationsDrawer, registerHeaderNotificationsStore } from './notifications';
import './sponsor-carousel';

window.Alpine = Alpine;
window.notificationsDrawer = notificationsDrawer;
registerHeaderNotificationsStore(Alpine);

Alpine.start();
bootDeferredGoogleAnalytics();
