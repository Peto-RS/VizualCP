import {Ref, ref} from 'vue'
import {AppConfigResponse} from "@/model/api/res/price-offer/AppConfigResponse.js";

const appConfig: Ref<AppConfigResponse | null> = ref(null)

export function useAppState() {
    return {
        appConfig
    }
}