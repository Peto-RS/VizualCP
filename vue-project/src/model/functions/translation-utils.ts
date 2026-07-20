import {createI18n} from "vue-i18n";
import {router} from "@/router.js";
import {NavigationFailure} from "vue-router";

export const supportedLocales = [
    'bg','cs','de','en','es','fr','hu','it','pl','ro','sk','sr'
] as const

export type SupportedLocale = typeof supportedLocales[number]

export const defaultLocale = 'sk' as const

export function isSupportedLocale(locale: string): locale is SupportedLocale {
    return supportedLocales.includes(locale as SupportedLocale)
}

const loadedLanguages = new Set()

export const i18n = createI18n({
        legacy: false, // you must set `false`, to use Composition API
        escapeParameterHtml: true,
        locale: 'sk',
        fallbackLocale: 'sk'
    }
)

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
}

export async function switchLanguage(lang: SupportedLocale): Promise<void | NavigationFailure | undefined> {
    const route = router.currentRoute.value

    return await router.push({
        name: route.name!,
        params: {
            ...route.params,
            lang
        },
        query: route.query,
        hash: route.hash
    })
}


export function safeT(key?: string | null, fallback: string = '') {
    if (!key) return fallback
    return i18n.global.t(key)
}