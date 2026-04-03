import './bootstrap';
import Alpine from 'alpinejs';
import { bootDeferredGoogleAnalytics } from './google-analytics';

window.Alpine = Alpine;
window.registerHeaderNotificationsStore?.(Alpine);

Alpine.start();
bootDeferredGoogleAnalytics();
