import Vue from 'vue'
import VueI18n from 'vue-i18n'


Vue.use(VueI18n)

function loadLocaleMessages () {
const locales = require.context('../../public/locales', true, /[A-Za-z0-9-_,\s]+\.yaml$/i)
const messages = {}
locales.keys().forEach(key => {
    const matched = key.match(/([A-Za-z0-9-_]+)\./i)
    if (matched && matched.length > 1) {
    const locale = matched[1]
    messages[locale] = locales(key)
    }
})
return messages
}

const i18n = new VueI18n({
    locale: 'zh-TW',
    fallbackLocale: 'zh-TW',
    formatFallbackMessages: true,
    silentFallbackWarn: true,
    messages: loadLocaleMessages()
})

export default i18n