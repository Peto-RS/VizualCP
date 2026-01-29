import {createRouter, createWebHashHistory} from 'vue-router'
import Home from "./views/Home.vue";

const routes = [
    {path: '/?test', component: Home}
]

export const router = createRouter({
    history: createWebHashHistory(),
    routes
})