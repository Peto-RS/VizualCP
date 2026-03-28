<script setup lang="ts">
import {useI18n} from 'vue-i18n'
import {useAppState} from "@/composables/app-state.js";

const {offCanvasActiveView} = useAppState()
const {t} = useI18n()

const emit = defineEmits<{
  (e: 'handle-configurator-click'): void,
  (e: 'handle-price-offer-click'): void
}>()
</script>

<template>
  <ul class="nav nav-underline fs-6 font-bold">
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

.navbar-wrapper {
  position: fixed;
  overflow: hidden;
}

/* default = hidden (slide OUT → faster) */
.navbar-bg {
  position: absolute;
  inset: 0;
  background: white;

  opacity: 0;
  transition: opacity 0.15s ease-in; /* fade OUT */

  z-index: -1;
}

.navbar-bg.active {
  opacity: 1;
  transition: opacity 0.3s ease-out; /* fade IN */
}
</style>