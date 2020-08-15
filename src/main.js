import Vue from 'vue'
import App from './App.vue'
import { createRouter } from './router'
import { createStore } from './store'
import { sync } from 'vuex-router-sync'
import VueMeta from 'vue-meta'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

import i18n from './lang/index'
import axios from 'axios'

Vue.use(VueMeta)
Vue.config.productionTip = false

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.prototype.$axios = axios

export const createApp = ({ lang = 'zh-TW', changeTitle, document }) => {
  const router = createRouter()
  const store = createStore({ lang, changeTitle, document })

  sync(store, router)

  const app = new Vue({
    router,
    store,
    i18n,
    render: h => h(App)
  })
  return { app, router, store }
}