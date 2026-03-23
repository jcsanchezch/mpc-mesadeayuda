import './bootstrap';
//import "../css/app.css";
//import "@fortawesome/fontawesome-free/css/all.min.css";

import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import {ZiggyVue} from "ziggy-js";

createInertiaApp({
    title: (title) => (title ? `${title} - Mesa de Servicio` : 'Mesa de Servicio'),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#29CC57",
        showSpinner: true,
        delay: 250,
    },
}).then((r) => {
    console.log("Loaded!");
});
