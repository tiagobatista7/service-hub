import '../css/app.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import './bootstrap'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'

import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: title => `${title} - ${appName}`,

  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),

  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(plugin)

    app.use(ZiggyVue, Ziggy)

    app.mount(el)

    return app
  },

  progress: {
    color: '#4B5563',
  },
})
