import {computed} from 'vue'
import {useRoute, useRouter} from 'vue-router'

export type Panel = 'configurator' | 'price-offer'

function isPanel(value: unknown): value is Panel {
    return value === 'configurator' || value === 'price-offer'
}

export function usePanelQuery() {
    const route = useRoute()
    const router = useRouter()

    const panel = computed<Panel | null>(() =>
        isPanel(route.query.panel) ? route.query.panel : null
    )

    async function setPanel(value: Panel | null, replace = false) {
        if (value !== null && route.query.panel === value) return
        if (value === null && route.query.panel === undefined) return

        const query = {...route.query}
        if (value) {
            query.panel = value
        } else {
            delete query.panel
        }

        const location = {path: route.path, query, hash: route.hash}
        if (replace) {
            await router.replace(location)
        } else {
            await router.push(location)
        }
    }

    return {panel, setPanel}
}
