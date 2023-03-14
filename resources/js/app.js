import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import SlimSelect from "slim-select";
import "../../vendor/lowerrocklabs/laravel-livewire-tables-advanced-filters/resources/css/numberRange.min.css";
import flatpickr from "flatpickr";

// window.flatpickr = flatpickr;
window.SlimSelect = SlimSelect;
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
