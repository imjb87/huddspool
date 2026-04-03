import './bootstrap';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
import { bootDeferredGoogleAnalytics } from './google-analytics';

window.Alpine = Alpine;
window.registerHeaderNotificationsStore?.(Alpine);

Livewire.start();
bootDeferredGoogleAnalytics();
