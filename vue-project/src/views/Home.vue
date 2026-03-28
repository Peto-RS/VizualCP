<script setup lang="ts">

import {onBeforeMount} from "vue";
import {useAppState} from "../composables/app-state.js";
import {getAppConfig} from "../model/api/rest.js";
import AppOffCanvas from "@/components/generic/AppOffCanvas.vue";
import Toasts from "@/components/generic/Toasts.vue";
import AppModal from "@/components/generic/AppModal.vue";

const {appConfig} = useAppState()

onBeforeMount(async () => {
  appConfig.value = await getAppConfig()
})
</script>

<template>
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