import {createRouter, createWebHashHistory} from 'vue-router'
import InteriorDoorsView from "./views/InteriorDoorsView.vue";
import PriceOfferFinished from "@/views/PriceOfferFinished.vue";

const routes = [
    {
        path: '/',
        redirect: '/interior-doors'
    },
    {
        path: '/interior-doors',
        component: InteriorDoorsView
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