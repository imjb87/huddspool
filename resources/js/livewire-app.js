import './bootstrap';
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm.js';
import { registerSiteSearch } from './site-search';

window.Alpine = Alpine;

registerSiteSearch(Alpine);

Livewire.start();
