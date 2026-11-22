import {
    createRouter,
    createWebHashHistory,
    type RouteRecordRaw
} from 'vue-router'
import InteriorDoorsView from "./views/interior-doors/InteriorDoorsView.vue";
import PriceOfferFinished from "@/views/PriceOfferFinished.vue";
import {
    defaultLocale,
    isSupportedLocale,
    setLocale,
    SupportedLocale
} from "@/model/common/functions/translation-utils.js";
import {getSecurityDoorsTenant} from "@/model/security-doors/rest.js";
import SecurityDoorsView from "@/views/security-doors/SecurityDoorsView.vue";

const DEFAULT_TENANT_SLUG = 'stolarstvo-sucansky'
const validatedTenantSlugs = new Set<string>()

const routes: RouteRecordRaw[] = [
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
                path: 'security-doors',
                redirect: to => ({
                    name: 'SecurityDoors',
                    params: {
                        lang: to.params.lang,
                        tenantSlug: DEFAULT_TENANT_SLUG
                    },
                    query: to.query,
                    hash: to.hash
                })
            },
            {
                path: 'security-doors/:tenantSlug',
                name: 'SecurityDoors',
                component: SecurityDoorsView
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

    if (to.name === 'SecurityDoors') {
        const tenantSlug = to.params.tenantSlug
        if (typeof tenantSlug !== 'string') {
            return next({
                name: 'SecurityDoors',
                params: {lang: routeLang, tenantSlug: DEFAULT_TENANT_SLUG},
                replace: true
            })
        }

        if (!validatedTenantSlugs.has(tenantSlug)) {
            const response = await getSecurityDoorsTenant(tenantSlug)
            if (response.status === 400 || response.status === 404) {
                if (tenantSlug === DEFAULT_TENANT_SLUG) {
                    throw new Error(`Default security doors tenant "${DEFAULT_TENANT_SLUG}" was not found`)
                }

                return next({
                    name: 'SecurityDoors',
                    params: {lang: routeLang, tenantSlug: DEFAULT_TENANT_SLUG},
                    replace: true
                })
            }

            if (!response.ok) {
                throw new Error(`Tenant lookup failed with HTTP status ${response.status}`)
            }

            validatedTenantSlugs.add(tenantSlug)
        }
    }

    next()
})