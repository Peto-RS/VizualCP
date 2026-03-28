<script setup lang="ts">

import {CSSProperties, nextTick, onBeforeMount, onMounted, onUnmounted, ref, watch} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {useAppState} from "../composables/app-state.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";
import {offcanvas} from "@/composables/offcanvas.js";
import Configurator from "@/components/Configurator.vue";
import {
  doorsHeight,
  doorsWidth,
  doorsX,
  doorsY,
  generateCanvas,
  getButtonPosition
} from "@/model/functions/generate-canvas.js";
import {ConfiguratorResponse} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {getAppConfig, getConfigurator, postAddDoor} from "@/model/api/rest.js";
import Navbar from "@/components/Navbar.vue";
import PriceOffer from "@/components/PriceOffer.vue";
import {useLoadingBar} from "@/composables/loading-bar-composables.js";
import {isMobileDevice} from "@/model/functions/responsivity-utils.js";
import {useI18n} from "vue-i18n";
import {useAlerts} from "@/composables/alert-composables.js";

const {addAlert} = useAlerts()
const {offCanvasActiveView} = useAppState()
const {displayLoadingBar, hideLoadingBar} = useLoadingBar()
const {locale, t} = useI18n()

const buttonAddToPriceOfferIsVisible = ref<Boolean>(false)
const buttonAddToPriceOfferRef = ref<HTMLButtonElement | null>(null)
const buttonAddToPriceOfferStyle = ref<CSSProperties>({
  position: 'absolute',
  left: '0px',
  top: '0px'
})

const selectedDoor = ref<SelectedDoor | null>(null)
const selectedRoom = ref<Room | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

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
  if (canvasRef.value && selectedRoom.value && selectedDoor.value) {
    await generateCanvas(canvasRef.value, (await getAppConfig()).baseUrl!, selectedRoom.value, selectedDoor.value, isMobileDevice())
    await updateButtonPosition()
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
  offCanvasActiveView.value = 'configurator'
  offcanvas.open(Configurator, {
        configuratorApiResponse: await getConfiguratorResponse(),
        initialSelectedDoor: selectedDoor.value,
        onDoorSelected: handleDoorChange,
        onRoomSelected: handleRoomChange,
        onPriceOfferClicked: () => openPriceOfferOffCanvas(),
        onConfiguratorClicked: () => openConfiguratorOffCanvas()
      },
      false,
      () => {
        offCanvasActiveView.value = null
      },
      {
        height: '55%'
      }
  )
}

async function openPriceOfferOffCanvas() {
  offCanvasActiveView.value = 'priceOffer'
  offcanvas.open(PriceOffer, {
        configuratorApiResponse: await getConfiguratorResponse(),
        onPriceOfferClicked: () => openPriceOfferOffCanvas(),
        onConfiguratorClicked: () => openConfiguratorOffCanvas()
      },
      true,
      () => {
        offCanvasActiveView.value = null
      },
      {
        height: '100%'
      },
  )
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

async function updateButtonPosition() {
  if (!canvasRef.value) return;
  buttonAddToPriceOfferIsVisible.value = true
  await nextTick(); // ensure DOM is rendered

  const rect = buttonAddToPriceOfferRef?.value?.getBoundingClientRect();

  const pos = getButtonPosition(
      doorsX,
      doorsY,
      doorsWidth,
      doorsHeight,
      canvasRef.value,
      rect?.width ?? 0
  );

  buttonAddToPriceOfferStyle.value = {
    position: 'absolute',
    left: `${pos.x}px`,
    top: `${pos.y}px`
  };
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

  watch(locale, async () => {
    await nextTick();
    await updateButtonPosition();
  });

  await renderCanvas();
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
  <div class="configurator-canvas">
    <Navbar
        @handle-configurator-click="openConfiguratorOffCanvas()"
        @handle-price-offer-click="openPriceOfferOffCanvas()"/>
    <div class="canvas-wrapper">
      <canvas class="z-0" ref="canvasRef"></canvas>
      <div class="d-flex justify-content-center align-content-center d-md-none">
        <button @click="handleAddDoorButtonClick"
                class="btn btn-secondary btn-lg text-white text-uppercase d-flex align-items-center justify-content-center position-fixed"
                style="bottom: 80px;"
                type="button">
          <i class="fas fa-plus-circle text-white fs-1 me-2"></i>
          <span class="fs-5">{{ t('configurator.buttonAddToPriceOffer') }}</span>
        </button>
      </div>
      <div class="d-none d-md-block">
        <button v-if="buttonAddToPriceOfferIsVisible"
                ref="buttonAddToPriceOfferRef"
                @click="handleAddDoorButtonClick"
                class="btn btn-secondary btn-lg text-white text-uppercase d-flex align-items-center justify-content-center"
                :style="buttonAddToPriceOfferStyle"
                type="button">
          <i class="fas fa-plus-circle text-white fs-1 me-2"></i>
          <span class="fs-5">
          {{ t('configurator.buttonAddToPriceOffer') }}
        </span>
        </button>
      </div>
    </div>
  </div>
</template>

<style>
.configurator-canvas {
  position: relative;
}

.configurator-canvas h1 {
  color: var(--bs-primary);
}

.canvas-wrapper {
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

canvas {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: left center;
}

@media (max-width: 768px) {
  canvas {
    object-position: center center;
  }
}

@media (min-width: 768px) {
}
</style>