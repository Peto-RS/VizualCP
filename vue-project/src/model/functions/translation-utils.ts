import {createI18n, useI18n} from "vue-i18n";

export type SupportedLocale =
    | 'bg'
    | 'cs'
    | 'de'
    | 'en'
    | 'es'
    | 'fr'
    | 'hu'
    | 'it'
    | 'pl'
    | 'ro'
    | 'sk'
    | 'sr'

const loadedLanguages = new Set()

export const i18n = createI18n({
        legacy: false, // you must set `false`, to use Composition API
        escapeParameterHtml: true,
        locale: 'sk',
        fallbackLocale: 'sk'
    }
)

export function getDefaultLocale(): SupportedLocale {
    return (localStorage.getItem('locale') as SupportedLocale) || 'sk'
}

export async function loadLocaleMessages(locale: SupportedLocale) {
    if (!loadedLanguages.has(locale)) {
        const messages = await import(`@/locales/${locale}.json`)
        i18n.global.setLocaleMessage(locale, messages)
        loadedLanguages.add(locale)
    }
}

export async function setLocale(locale: SupportedLocale) {
    await loadLocaleMessages(locale)
    i18n.global.locale.value = locale
    localStorage.setItem('locale', locale)
}

export function safeT(key?: string | null, fallback: string = '') {
    if (!key) return fallback
    return i18n.global.t(key)
}