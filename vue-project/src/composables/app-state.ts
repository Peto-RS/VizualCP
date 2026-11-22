import {Ref, ref} from 'vue'
import {AppConfigResponse} from "@/model/interior-doors/price-offer/res/AppConfigResponse.js";

const appConfig: Ref<AppConfigResponse | null> = ref(null)
const currency: Ref<string | null> = ref(null)

export function useAppState() {
    return {
        appConfig,//properties from the backend
        currency
    }
}