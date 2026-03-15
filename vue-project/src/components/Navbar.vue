<script setup lang="ts">
import {computed, Ref, ref} from 'vue'
import {useI18n} from 'vue-i18n'
import LoadingBar from "@/components/generic/LoadingBar.vue";
import {useAppState} from "@/composables/app-state.js";

const {appConfig} = useAppState()
const {t} = useI18n()

const emit = defineEmits<{
  (e: 'handle-configurator-click'): void,
  (e: 'handle-price-offer-click'): void
}>()

type OffCanvasViews = "configurator" | "priceOffer";
const offCanvasActiveView: Ref<OffCanvasViews | null> = ref(null)

// const navbarImgSrc = computed(
//     () => appConfig.value ? (appConfig.value.baseUrl + '/assets/img/logo-stolarstvo-sucansky.png') : null
// )
</script>

<template>
  <nav class="navbar d-none d-sm-flex bg-white">
    <div class="container-fluid justify-content-end">
<!--      <router-link class="navbar-brand" to="/">-->
<!--        <img v-if="navbarImgSrc"-->
<!--             :src="navbarImgSrc"-->
<!--             alt="Logo"-->
<!--             class="navbar-logo"/>-->
<!--      </router-link>-->

      <ul class="nav nav-underline">
        <li class="nav-item">
          <a class="nav-link nav-link-configurator"
             :class="{active: offCanvasActiveView === 'configurator'}"
             @click="emit('handle-configurator-click'); offCanvasActiveView = 'configurator';">
            {{ t('components.Navbar.configurator') }}
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-link-price-offer"
             :class="{ active: offCanvasActiveView === 'priceOffer' }"
             @click="emit('handle-price-offer-click'); offCanvasActiveView = 'priceOffer';">
            {{ t('components.Navbar.priceOffer') }}
          </a>
        </li>
      </ul>
    </div>
    <div class="loading-bar-wrapper">
      <LoadingBar/>
    </div>
  </nav>

  <!--  Mobile-->
  <nav class="navbar d-sm-none">
    <div class="container-fluid justify-content-center">
      <!--      <router-link class="navbar-brand" to="/">-->
      <!--        <img v-if="navbarImgSrc"-->
      <!--             :src="navbarImgSrc"-->
      <!--             alt="Logo"-->
      <!--             class="navbar-logo"/>-->
      <!--      </router-link>-->
    </div>
    <div class="loading-bar-wrapper">
      <LoadingBar/>
    </div>
  </nav>

  <nav class="navbar fixed-bottom border d-sm-none bg-white">
    <div class="container-fluid">
      <ul class="nav nav-underline w-100 nav-justified">
        <li class="nav-item">
          <a class="nav-link nav-link-configurator"
             :class="{ active: offCanvasActiveView === 'configurator' }"
             @click="emit('handle-configurator-click'); offCanvasActiveView = 'configurator';">
            {{ t('components.Navbar.configurator') }}
          </a>
        </li>

        <li class="nav-item" data-bs-theme="price-offer">
          <a class="nav-link nav-link-price-offer"
             :class="{ active: offCanvasActiveView === 'priceOffer' }"
             @click="emit('handle-price-offer-click'); offCanvasActiveView = 'priceOffer';">
            <span>
              {{ t('components.Navbar.priceOffer') }}
            </span>
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

.nav-underline .nav-link-price-offer {
  color: var(--bs-secondary);
}

.nav-underline .nav-link-price-offer:hover {
  color: var(--bs-secondary);
}

.nav-underline .nav-link.nav-link-price-offer.active {
  border-bottom-color: var(--bs-secondary);
  color: var(--bs-secondary);
}
</style>