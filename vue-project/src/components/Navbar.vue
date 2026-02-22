<script setup lang="ts">
import {computed, Ref, ref} from 'vue'
import {useI18n} from 'vue-i18n'
import LoadingBar from "../components/LoadingBar.vue";
import {useAppState} from "../composables/app-state.js";

const {appConfig} = useAppState()
const {t} = useI18n()

const emit = defineEmits<{
  (e: 'handle-configurator-click'): void,
  (e: 'handle-price-offer-click'): void
}>()

type OffCanvasViews = "configurator" | "priceOffer";
const offCanvasActiveView: Ref<OffCanvasViews> = ref("configurator")

const navbarImgSrc = computed(
    () => appConfig.value ? (appConfig.value.baseUrl + '/assets/img/logo-stolarstvo-sucansky.png') : null
)
</script>

<template>
  <nav class="navbar border d-none d-lg-flex bg-white">
    <div class="container-fluid">
      <router-link class="navbar-brand" to="/">
        <img v-if="navbarImgSrc"
             :src="navbarImgSrc"
             alt="Logo"
             class="navbar-logo"/>
      </router-link>

      <ul class="nav nav-underline">
        <li class="nav-item">
          <a class="nav-link"
             :class="{ active: offCanvasActiveView === 'configurator' }"
             @click="emit('handle-configurator-click'); offCanvasActiveView = 'configurator';">
            {{ t('components.Navbar.configurator') }}
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link"
             :class="{ active: offCanvasActiveView === 'priceOffer' }"
             @click="emit('handle-price-offer-click'); offCanvasActiveView = 'priceOffer';">
            {{ t('components.Navbar.priceOffer') }}
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <nav class="navbar fixed-bottom border d-lg-none bg-white">
    <div class="container-fluid">
      <ul class="nav nav-underline w-100 nav-justified">
        <li class="nav-item">
          <a class="nav-link"
             :class="{ active: offCanvasActiveView === 'configurator' }"
             @click="emit('handle-configurator-click'); offCanvasActiveView = 'configurator';">
            {{ t('components.Navbar.configurator') }}
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link"
             :class="{ active: offCanvasActiveView === 'priceOffer' }"
             @click="emit('handle-price-offer-click'); offCanvasActiveView = 'priceOffer';">
            {{ t('components.Navbar.priceOffer') }}
          </a>
        </li>
      </ul>

      <router-link class="navbar-brand" to="/">
        <img v-if="navbarImgSrc"
             :src="navbarImgSrc"
             alt="Logo"
             class="navbar-logo justify-content-center"/>
      </router-link>
    </div>
  </nav>
  <LoadingBar/>
</template>

<style lang="scss">
.navbar-logo {
  width: 300px;
}

.nav-item {
  cursor: pointer;
}

.nav-link {
  text-transform: uppercase;
}
</style>