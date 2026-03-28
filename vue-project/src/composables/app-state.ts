import {Ref, ref} from 'vue'
import {AppConfigResponse} from "@/model/api/res/price-offer/AppConfigResponse.js";

type OffCanvasViews = "configurator" | "priceOffer";

const appConfig: Ref<AppConfigResponse | null> = ref(null)
const offCanvasActiveView: Ref<OffCanvasViews | null> = ref(null)

export function useAppState() {
    return {
        appConfig,
        offCanvasActiveView
    }
}