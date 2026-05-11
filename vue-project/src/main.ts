import {createApp, onBeforeMount} from 'vue'
import 'bootstrap';
import {router} from "./router.js";
import Home from "./views/Home.vue";
import {
    i18n
} from "@/model/functions/translation-utils.js";
import {useAppState} from "@/composables/app-state.js";
import {getAppConfig} from "@/model/api/rest.js";
import {CurrencyEnum} from "@/model/enums/CurrencyEnum.js";

/*
let vueAppInstance: ReturnType<typeof createApp> | null = null

window.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById('openclose_btn_text_area_priceOffer');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (vueAppInstance) {
            vueAppInstance.unmount();
            document.querySelector('#vueApp')!.innerHTML = "";
            vueAppInstance = null;
        }

        const app = createApp(App);
        vueAppInstance = app;
        app
            .use(i18n)
            .use(routerPriceOffer)
            .mount('#vueApp');
    });
});
 */


const {appConfig, currency} = useAppState()
appConfig.value = await getAppConfig()
currency.value = localStorage.getItem('currency') ?? CurrencyEnum.EUR

//single-page application
const app = createApp(Home);
app
    .use(i18n)
    .use(router)
    .mount('#vueAppFull');