import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';

// Mapeia todos os arquivos .vue dentro de Pages e subpastas
const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    resolve: name => {
        const page = pages[`./Pages/${name}.vue`];
        if (!page) {
            throw new Error(`Página ${name} não encontrada.`);
        }
        return page();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
