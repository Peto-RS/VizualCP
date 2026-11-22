<script setup lang="ts">
import {computed, onMounted} from "vue";
import {useRoute, useRouter} from 'vue-router'
import {useI18n} from "vue-i18n";
import {defaultLocale} from "@/model/common/functions/translation-utils.js";

const router = useRouter()
const route = useRoute()
const {t} = useI18n()
const completedOffer = sessionStorage.getItem('offerCompleted')
const offerType = completedOffer === 'interior-doors' || completedOffer === 'security-doors'
  ? completedOffer
  : null

if (!offerType) {
  router.replace('/')
} else {
  sessionStorage.removeItem('offerCompleted')
}

const returnRoute = computed(() => ({
  name: offerType === 'security-doors' ? 'SecurityDoors' : 'InteriorDoors',
  params: {lang: route.params.lang ?? defaultLocale}
}))

onMounted(() => {
  if (offerType && window.gtag) {
    const isSecurityDoors = offerType === 'security-doors'
    window.gtag('event', 'page_view', {
      page_title: isSecurityDoors
        ? 'Security doors price offer finished'
        : 'Interior doors price offer finished',
      page_path: isSecurityDoors
        ? '/security-doors/price-offer-finished'
        : '/interior-doors/price-offer-finished'
    })
  }
})
</script>

<template>
  <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="text-center">
      <span class="fa fa-check text-success" style="font-size: 5em"></span>
      <h3>{{ t('views.priceOfferFinished.priceOfferSentToMail') }}</h3>
      <h3>{{ t('views.priceOfferFinished.thanksMessage') }}</h3>
      <h3>STOLÁRSTVO - SUČANSKÝ</h3>
    </div>
    <div>
      <RouterLink :to="returnRoute">
        <button class="btn btn-primary">{{ t('returnToHomepage') }}</button>
      </RouterLink>
    </div>
  </div>

</template>

<style>
</style>