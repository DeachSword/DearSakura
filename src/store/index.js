import Vue from 'vue'
import Vuex from 'vuex'
import { set as cookieSet } from 'js-cookie'
import i18n from '@/lang'

Vue.use(Vuex)

export const createStore = ({ lang, changeTitle , document}) => {
  const countParent = instance => instance.$parent ? 1 + countParent(instance.$parent) : 0
  let owners = []

  const updateTitle = (instance, part) => {
    const depth = countParent(instance)
    owners[depth] = instance
    changeTitle(depth, part)
  }
  const removeTitle = instance => {
    const depth = countParent(instance)
    if (!owners[depth] || owners[depth] === instance) {
      changeTitle(depth)
    }
  }
  function getTitle() {
    return document('title')
  }
  function getUrl() {
    return document('url')
  }
  return new Vuex.Store({
    state: {
      lang,
      cTitle: null,
      description: null,
      profile: null,
      isLogin: false,
      isApi: false,
      isJsonReander: false,
      users: {}
    },
    getters: {},
    mutations: {
      setLang(state, data) {
        cookieSet('lang', data)
        state.lang = data
        i18n.locale = data
      },
      setProfile(state, data) {
        state.profile = data
        var p = data.profile
        p['userId'] = p['id']
        delete p['id']
        state.users[p.userId] = p
      },
      setLoginState(state, data) {
        state.isLogin = data
      },
      updateTitle(_state, [instance, part]) {
        updateTitle(instance, part)
      },
      removeTitle(_state, instance) {
        removeTitle(instance)
      },
      getUrl() {
        return getUrl()
      },
      getTitle() {
        return getTitle()
      },
      setApiState(state, data) {
        state.isApi = data
      },
      setJsonReander(state, data) {
        state.isJsonReander = data
      },
      setUsers(state, data) {
        data.forEach(p => {
          state.users[p.userId] = p
        });
      },
    },
    actions: {}
  })
}
