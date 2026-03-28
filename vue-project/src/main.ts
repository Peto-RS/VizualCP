import {createApp} from 'vue'
import 'bootstrap';
import {router} from "./router.js";
import Home from "./views/Home.vue";
import {
    getDefaultLocale,
    i18n,
    setLocale,
    SupportedLocale
} from "@/model/functions/translation-utils.js";

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

const locale: SupportedLocale = getDefaultLocale()
await setLocale(locale)

//single-page application
const app = createApp(Home);
app
    .use(i18n)
    .use(router)
    .mount('#vueAppFull');