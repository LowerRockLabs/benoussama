import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import SlimSelect from "slim-select";
import "../../vendor/lowerrocklabs/laravel-livewire-tables-advanced-filters/resources/css/numberRange.min.css";
import flatpickr from "flatpickr";
import "/node_modules/flag-icons/css/flag-icons.min.css";

// window.flatpickr = flatpickr;
window.SlimSelect = SlimSelect;
window.Alpine = Alpine;

window.Alpine.plugin(focus);

window.Alpine.start();
