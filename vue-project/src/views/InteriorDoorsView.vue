<script setup lang="ts">

import {onBeforeMount, onMounted, onUnmounted, ref} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {useAppState} from "../composables/app-state.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";
import {offcanvas} from "@/composables/offcanvas.js";
import Configurator from "@/components/Configurator.vue";
import {generateCanvas} from "@/model/functions/generate-canvas.js";
import {ConfiguratorResponse} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {getConfigurator, postAddDoor} from "@/model/api/rest.js";
import Navbar from "@/components/Navbar.vue";
import PriceOffer from "@/components/PriceOffer.vue";
import {useLoadingBar} from "@/composables/loading-bar-composables.js";
import {isMobileDevice} from "@/model/functions/responsivity-utils.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";
import {useI18n} from "vue-i18n";
import {useAlerts} from "@/composables/alert-composables.js";

const {addAlert} = useAlerts()
const {appConfig} = useAppState()
const {displayLoadingBar, hideLoadingBar} = useLoadingBar()
const {t} = useI18n()

const selectedDoor = ref<SelectedDoor | null>(null)
const selectedDoorCategory = ref<DoorCategory | null>(null)
const selectedRoom = ref<Room | null>(null)
const roomBackgroundImg = ref<string | null>(null)

async function handleDoorChange(door: SelectedDoor) {
  selectedDoor.value = door
  displayLoadingBar()
  await renderCanvas()
  hideLoadingBar()
}

async function handleRoomChange(room: Room | null) {
  selectedRoom.value = room
  displayLoadingBar()
  await renderCanvas()
  hideLoadingBar()
}

async function renderCanvas(): Promise<void> {
  if (selectedRoom.value && selectedDoor.value) {
    roomBackgroundImg.value = await generateCanvas(appConfig.value?.baseUrl!, selectedRoom.value, selectedDoor.value, isMobileDevice())
  }
}

const handleResize = async () => {
  await renderCanvas()
};

async function getConfiguratorResponse(): Promise<ConfiguratorResponse> {
  const response = await getConfigurator()
  if (response.ok) {
    return (await response.json()) as ConfiguratorResponse;
  } else {
    throw new Error('Error fetching getConfiguratorResponse')
  }
}

async function openConfiguratorOffCanvas() {
  offcanvas.open(Configurator, {
    configuratorApiResponse: await getConfiguratorResponse(),
    initialSelectedDoor: selectedDoor.value,
    initialSelectedDoorCategory: selectedDoorCategory.value,
    onDoorSelected: handleDoorChange,
    onRoomSelected: handleRoomChange
  })
}

async function openPriceOfferOffCanvas() {
  offcanvas.open(PriceOffer, {
    configuratorApiResponse: await getConfiguratorResponse()
  }, true)
}

async function handleAddDoorButtonClick(): Promise<any> {
  if (selectedDoor.value) {
    try {
      const response = await postAddDoor({
        category: selectedDoor.value.categoryId!,
        handleId: selectedDoor.value.handleId,
        material: selectedDoor.value.material!,
        type: selectedDoor.value.type!
      })

      if (response.ok) {
        addAlert(t('configurator.doorAddedToPriceOffer'))
        await openPriceOfferOffCanvas();
      }
    } catch (e) {
      throw new Error('Error adding doors: handleAddDoorButtonClick')
    }
  }
}

onBeforeMount(async () => {
  let configuratorApiResponse = await getConfiguratorResponse();
  if (configuratorApiResponse.rooms.length)
    selectedRoom.value = configuratorApiResponse.rooms[0];

  if (configuratorApiResponse.doorCategories.length) {
    const selectedDoorCategory = configuratorApiResponse.doorCategories[0];
    selectedDoor.value = {
      categoryId: selectedDoorCategory.categoryId,
      doorCategory: selectedDoorCategory,
      handleId: selectedDoorCategory.doorConfiguratorDefault.handle,
      material: selectedDoorCategory.doorConfiguratorDefault.material,
      type: selectedDoorCategory.doorConfiguratorDefault.type
    }
  }

  await renderCanvas()
})

onMounted(async () => {
  window.addEventListener("resize", handleResize);

  if (!isMobileDevice()) {
    await openConfiguratorOffCanvas()
  }
})

onUnmounted(() => {
  offcanvas.close()
})
</script>

<template>
  <div
      class="configurator-canvas"
      :style="roomBackgroundImg ? `background-image: url('${roomBackgroundImg}')` : null">
    <Navbar
        @handle-configurator-click="openConfiguratorOffCanvas()"
        @handle-price-offer-click="openPriceOfferOffCanvas()"/>
    <div class="d-flex justify-content-center d-md-none">
      <button @click="handleAddDoorButtonClick"
              class="btn btn-secondary btn-lg text-white btn-add-to-price-offer-main-page text-uppercase"
              style="max-width: 240px;"
              type="button">
        <span class="row">
          <span class="col-3 align-content-center">
            <i class="fas fa-plus-circle text-white fs-1"></i>
          </span>
          <span class="col-9 fs-5">
            {{ t('configurator.buttonAddToPriceOffer') }}
          </span>
        </span>
      </button>
    </div>

    <div class="d-none d-md-block">
      <button @click="handleAddDoorButtonClick"
              class="btn btn-secondary btn-lg text-white btn-add-to-price-offer-main-page text-uppercase"
              style="max-width: 225px;"
              type="button">
        <span class="row">
          <span class="col-3 align-content-center">
            <i class="fas fa-plus-circle text-white fs-1"/>
          </span>
          <span class="col-9 fs-5">
            {{ t('configurator.buttonAddToPriceOffer') }}
          </span>
        </span>
      </button>
    </div>

  </div>
</template>

<style>
.configurator-canvas {
  color: var(--bs-primary);
  font-weight: bold;
  width: 100vw;
  height: 100vh;
  background-size: cover;
  background-position: left center;
  background-repeat: no-repeat;
  position: relative;
  transition: opacity 0.2s ease-in-out;
}

.configurator-canvas h1 {
  color: var(--bs-primary);
}

@media (max-width: 768px) {
  .btn-add-to-price-offer-main-page {
    margin-top: 630px;
  }
}

@media (min-width: 768px) {
  .btn-add-to-price-offer-main-page {
    margin-left: 265px;
    margin-top: 590px;
  }
}


</style>