<script setup lang="ts">
import {useRouter} from 'vue-router'
import {useI18n} from "vue-i18n";
import {useAppState} from "@/composables/app-state.js";
import {onMounted} from "vue";

const {appConfig} = useAppState()
const router = useRouter()
const {t} = useI18n()

if (sessionStorage.getItem('offerCompleted') !== 'true') {
  router.replace('/')
} else {
  sessionStorage.removeItem('offerCompleted')
}

onMounted(() => {
  if (window.gtag) {
    window.gtag('event', 'page_view', {
      page_title: 'Price offer finished',
      page_path: '/price-offer-finished'
    })
  }
})
</script>

<template>
  <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="text-center">
      <span class="fa fa-check text-success" style="font-size: 5em"></span>
      <h3>Ďakujeme.</h3>
      <h3>Cenovú ponuku sme Vám poslali na e-mailovú adresu.</h3>
      <h3>STOLÁRSTVO - SUČANSKÝ</h3>
    </div>
    <div>
      <a :href="appConfig?.baseUrl ?? '/'">
        <button class="btn btn-primary">{{ t('returnToHomepage') }}</button>
      </a>
    </div>
  </div>

</template>

<style>
</style>