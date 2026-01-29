<script setup lang="ts">
import {computed, Ref, ref} from 'vue'
import {useI18n} from 'vue-i18n'
import PriceOffer from "./PriceOffer.vue";
import LoadingBar from "../components/LoadingBar.vue";
import Configurator from "../components/Configurator.vue";

const navbarUrl = 'http://localhost:8080'
const {t} = useI18n()

const isConfiguratorActive: Ref<boolean> = ref(false)
const isPriceOfferActive: Ref<boolean> = ref(false)

const navbarImgSrc = computed(
    () => navbarUrl + '/assets/img/logo-stolarstvo-sucansky.png'
)
</script>

<template>
  <nav class="navbar border">
    <div class="container-fluid">
      <router-link class="navbar-brand" to="/">
        <img :src="navbarImgSrc" alt="Logo" class="navbar-logo"/>
      </router-link>

      <ul class="nav nav-underline">
        <li class="nav-item">
          <a data-bs-toggle="offcanvas"
             data-bs-target="#offcanvasConfigurator"
             class="nav-link"
             :class="{ active: isConfiguratorActive }">
            {{ t('components.Navbar.configurator') }}
          </a>
        </li>

        <li class="nav-item">
          <a data-bs-toggle="offcanvas"
             data-bs-target="#offcanvasPriceOffer"
             class="nav-link"
             :class="{ active: isPriceOfferActive }">
            {{ t('components.Navbar.priceOffer') }}
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <!--  <nav class="navbar fixed-bottom border">-->
  <!--    <div class="container-fluid">-->
  <!--      <ul class="nav nav-underline">-->
  <!--        <li class="nav-item">-->
  <!--          <a class="nav-link"-->
  <!--             :class="{ active: true }">-->
  <!--            {{ t('components.Navbar.configurator') }}-->
  <!--          </a>-->
  <!--        </li>-->
  <!--        <li class="nav-item">-->
  <!--          <a data-bs-toggle="offcanvas"-->
  <!--             data-bs-target="#offcanvasPriceOffer"-->
  <!--             class="nav-link"-->
  <!--             :class="{ active: true }">-->
  <!--            {{ t('components.Navbar.priceOffer') }}-->
  <!--          </a>-->
  <!--        </li>-->
  <!--      </ul>-->
  <!--    </div>-->
  <!--  </nav>-->
  <LoadingBar/>

  <Teleport to="body">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasConfigurator"
         aria-labelledby="offcanvasConfiguratorLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasConfiguratorLabel">Konfigurátor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <Configurator/>
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPriceOffer"
         aria-labelledby="offcanvasPriceOfferLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasPriceOfferLabel">Cenová ponuka</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <PriceOffer/>
      </div>
    </div>
  </Teleport>
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