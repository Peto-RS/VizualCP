<script setup lang="ts">
import {computed, Ref, ref} from 'vue'
import {useI18n} from 'vue-i18n'
import {useAppState} from "../composables/app-state.js";
import LoadingBar from "@/components/generic/LoadingBar.vue";

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
  <nav class="navbar border d-none d-sm-flex bg-white">
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
    <div class="loading-bar-wrapper">
      <LoadingBar />
    </div>
  </nav>

  <!--  Mobile-->
  <nav class="navbar border d-sm-none bg-white">
    <div class="container-fluid justify-content-center">
      <router-link class="navbar-brand" to="/">
        <img v-if="navbarImgSrc"
             :src="navbarImgSrc"
             alt="Logo"
             class="navbar-logo"/>
      </router-link>
    </div>
    <div class="loading-bar-wrapper">
      <LoadingBar />
    </div>
  </nav>

  <nav class="navbar fixed-bottom border d-sm-none bg-white">
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
    </div>
  </nav>

</template>

<style lang="scss">
.loading-bar-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1050;
}

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