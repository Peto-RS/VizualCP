<script setup lang="ts">

import {onBeforeMount} from "vue";
import {useAppState} from "../composables/app-state.js";
import {getAppConfig} from "../model/api/rest.js";
import AppOffCanvas from "@/components/AppOffCanvas.vue";
import Toasts from "@/components/Toasts.vue";
import LoadingBar from "@/components/LoadingBar.vue";
import AppModal from "@/components/AppModal.vue";

const {appConfig} = useAppState()

//example door layer on top of room image - OK
//configuration component init - OK
//room selector - OK
//door categories selector - OK
//door model selector - OK
//refactor price offer to proper array of doors - OK
//material selector - OK
//wien special materials - OK
//typy skiel - OK
//have only one modal per all videos - OK
//nove features - OK
//cena kluciek chyba - OK
//popis a cena dveri - OK
//footer style

onBeforeMount(async () => {
  appConfig.value = await getAppConfig()
})
</script>

<template>
  <LoadingBar/>
  <router-view/>
  <Toasts/>
  <AppOffCanvas/>
  <AppModal/>
</template>

<style lang="scss">
* {
  box-sizing: border-box;
}

.btn-primary {
  color: white;
}

.btn-outline-primary:hover {
  color: white;
}
</style>