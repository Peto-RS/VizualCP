import {createRouter, createWebHashHistory} from 'vue-router'
import InteriorDoorsView from "./views/InteriorDoorsView.vue";
import PriceOfferFinished from "@/views/PriceOfferFinished.vue";
import {
    defaultLocale,
    isSupportedLocale,
    setLocale,
    SupportedLocale
} from "@/model/functions/translation-utils.js";

// const langPattern = supportedLocales.join('|')

const routes = [
    {
        path: '/',
        redirect: '/sk/interior-doors'
    },
    {
        path: `/:lang`,
        children: [
            {
                path: '',
                redirect: '/sk/interior-doors'
            },
            {
                path: 'interior-doors',
                name: 'InteriorDoors',
                component: InteriorDoorsView
            },
            {
                path: 'price-offer-finished',
                name: 'PriceOfferFinished',
                component: PriceOfferFinished
            }
        ]
    }
]

export const router = createRouter({
    history: createWebHashHistory(),
    routes
})

router.beforeEach(async (to, _, next) => {
    const routeLang = to.params.lang as string | undefined

    if (!routeLang || !isSupportedLocale(routeLang)) {
        return next({
            path: `/${defaultLocale}`,
            replace: true
        })
    }

    const locale = routeLang as SupportedLocale
    await setLocale(locale)

    next()
})