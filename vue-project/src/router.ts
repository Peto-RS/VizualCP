import {createRouter, createWebHashHistory} from 'vue-router'
import DoorsView from "./views/DoorsView.vue";

const routes = [
    {
        path: '/',
        redirect: '/doors'
    },
    {
        path: '/doors',
        component: DoorsView
    }
]

export const router = createRouter({
    history: createWebHashHistory(),
    routes
})