import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import CustomModal from "./Components/Modal/CustomModal.vue"

import Layout from './Layouts/Default.vue'
import mixin from './mixin'
import { i18nVue } from 'laravel-vue-i18n'
import VueClickAway from "vue3-click-away";
import VCalendar from 'v-calendar';
import 'v-calendar/dist/style.css';


// import { SetupCalendar } from 'v-calendar'

createInertiaApp({
//   resolve: name => require(`./Pages/${name}`),
    // resolve: name => {
    //     const page = require(`./Pages/${name}`).default
    //     page.layout = page.layout || Layout
    //     return page
    // },
    resolve: async name => {
        const page =  (await import(`./Pages/${name}`)).default
        page.layout = page.layout || Layout
        return page
    },
  setup({ el, App, props, plugin }) {
      createApp({ render: () => h(App, props) })
          .mixin(mixin)
          .mixin({ methods: { route: window.route } })
          .use(VueClickAway)
        //   .use(SetupCalendar, {})
          .use(VCalendar, {})
          .use(i18nVue, {
                resolve: (lang) => import(`../lang/de.json`)
            })
      .use(plugin)
      .mount(el)
  },
})
