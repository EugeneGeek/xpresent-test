import '../css/app.css'
import './bootstrap'
import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

// ✅ импортируем Ziggy и Vue-плагин
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'


createInertiaApp({
    resolve: name =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) // ✅ теперь route() работает везде
            .component('Link', Link)
            .mount(el)
    },
})
