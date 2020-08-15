import Vue from 'vue'
import VueAnalytics from 'vue-analytics'
import { get as cookieGet } from 'js-cookie'

import { createApp } from './main'
import './registerServiceWorker'

let titles = []

const changeTitle = (depth, part) => {
  titles[depth] = part
  document.title = titles.filter(Boolean).reverse().join(' - ')
}
const actDocument = (act) => {
  switch (act) {
    case 'title':
      return document.title
      break;
    case 'url':
      return document.location.href
      break;
    default:
      return null
      break;
  }
}

const { app, store, router } = createApp({ changeTitle, document: actDocument, lang: cookieGet('lang') })

if (window.__INITIAL_STATE__) {
  store.replaceState(window.__INITIAL_STATE__)
}

if (process.env.NODE_ENV === 'production') {
  Vue.use(VueAnalytics, {
    id: 'UA-113952669-1',
    router,
    autoTracking: {
      pageviewTemplate (route) {
        return {
          page: route.path,
          title: document.title,
          location: window.location.href
        }
      }
    },
    checkDuplicatedScript: true
  })
}

app.$mount('#app')
