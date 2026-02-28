import {createRouter, createWebHashHistory} from 'vue-router'
import DoorsView from "./views/DoorsView.vue";
import PriceOfferFinished from "@/views/PriceOfferFinished.vue";

const routes = [
    {
        path: '/',
        redirect: '/doors'
    },
    {
        path: '/doors',
        component: DoorsView
    },
    {
        path: '/price-offer-finished',
        component: PriceOfferFinished
    }
]

export const router = createRouter({
    history: createWebHashHistory(),
    routes
})