import './bootstrap';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
import { bootDeferredGoogleAnalytics } from './google-analytics';
import { notificationsDrawer, registerHeaderNotificationsStore } from './notifications';

window.Alpine = Alpine;
window.notificationsDrawer = notificationsDrawer;
registerHeaderNotificationsStore(Alpine);

Livewire.start();
bootDeferredGoogleAnalytics();
