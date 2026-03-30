import Alpine from 'alpinejs';
import { bootDeferredGoogleAnalytics } from './google-analytics';

window.Alpine = Alpine;

Alpine.start();
bootDeferredGoogleAnalytics();
