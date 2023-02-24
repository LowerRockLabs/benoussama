import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import SlimSelect from "slim-select";

window.SlimSelect = SlimSelect;
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
