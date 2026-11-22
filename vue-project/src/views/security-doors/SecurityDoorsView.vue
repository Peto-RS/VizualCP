<script setup lang="ts">
import NavbarSecurityDoors from "@/components/security-doors/NavbarSecurityDoors.vue";
import {offcanvas} from "@/composables/offcanvas.js";
import {useAppState} from "@/composables/app-state.js";
import {getConfiguratorSecurityDoors, postAddDoorSecurityDoors} from "@/model/security-doors/rest.js";
import {CSSProperties, nextTick, onMounted, onUnmounted, ref, watch} from "vue";
import {
  doorsHeight,
  doorsWidth,
  doorsX,
  doorsY,
  generateCanvas,
  getButtonPosition
} from "@/model/common/functions/generate-canvas.js";
import {isMobileDevice} from "@/model/common/functions/responsivity-utils.js";
import {SelectedSecurityDoor} from "@/model/security-doors/interfaces/SelectedSecurityDoor.js";
import {Room} from "@/model/interior-doors/configurator/Room.js";
import {useLoadingBar} from "@/composables/loading-bar-composables.js";
import {composeCanvasImageUrls, downloadImages} from "@/model/common/functions/canvas-image-utils.js";
import ConfiguratorSecurityDoors from "@/components/security-doors/ConfiguratorSecurityDoors.vue";
import DoorCanvasActions from "@/components/DoorCanvasActions.vue";
import DevButton from "@/components/DevButton.vue";
import {
  SecurityDoorsConfiguratorResponse
} from "@/model/security-doors/configurator/res/SecurityDoorsConfiguratorResponse.js";
import PriceOfferSecurityDoors from "@/components/security-doors/SecurityDoorsPriceOffer.vue";
import {devFunction} from "@/model/common/functions/dev.js";
import {useI18n} from "vue-i18n";
import {useAlerts} from "@/composables/alert-composables.js";
import {useRoute} from "vue-router";
import {usePanelQuery} from "@/composables/panel-query.js";

const {addAlert} = useAlerts()
const {locale, t} = useI18n()
const {appConfig, currency} = useAppState()
const {displayLoadingBar, hideLoadingBar} = useLoadingBar()
const route = useRoute()
const {panel, setPanel} = usePanelQuery()

const buttonAddToPriceOfferIsVisible = ref<boolean>(false)
const doorCanvasActionsRef = ref<InstanceType<typeof DoorCanvasActions> | null>(null)
const buttonAddToPriceOfferStyle = ref<CSSProperties>({
  position: 'absolute',
  left: '0px',
  top: '0px'
})

const selectedDoor = ref<SelectedSecurityDoor | null>(null)
const selectedRoom = ref<Room | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)

async function renderCanvas(): Promise<void> {
  if (canvasRef.value && selectedRoom.value && selectedDoor.value) {
    try {
      const urls = composeCanvasImageUrls(appConfig.value!.baseUrl!, selectedRoom.value, selectedDoor.value)
      const images = await downloadImages(urls)
      generateCanvas(canvasRef.value, images, isMobileDevice())
      await updateButtonPosition()
    } catch (error: unknown) {
      console.error("generateCanvas failed:", error)
    }
  }
}

async function handleDoorChange(door: SelectedSecurityDoor) {
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

function handleOffCanvasHidden(path: string) {
  if (route.path === path && panel.value) {
    void setPanel(null, true)
  }
}

async function showConfiguratorOffCanvas() {
  const path = route.path
  offcanvas.open(ConfiguratorSecurityDoors, {
        initialSelectedDoor: selectedDoor.value,
        onDoorUpdated: handleDoorChange,
        onRoomSelected: handleRoomChange,
        onPriceOfferClicked: () => openPriceOfferOffCanvas(),
        onConfiguratorClicked: () => openConfiguratorOffCanvas()
      },
      false,
      () => {
        handleOffCanvasHidden(path)
      },
      {
        height: '55%'
      }
  )
}

async function showPriceOfferOffCanvas() {
  const path = route.path
  offcanvas.open(PriceOfferSecurityDoors, {
        configuratorApiResponse: await getConfiguratorResponse(),
        onPriceOfferClicked: () => openPriceOfferOffCanvas(),
        onConfiguratorClicked: () => openConfiguratorOffCanvas()
      },
      true,
      () => {
        handleOffCanvasHidden(path)
      },
      {
        height: '100%'
      }
  )
}

async function openConfiguratorOffCanvas() {
  await setPanel('configurator')
}

async function openPriceOfferOffCanvas() {
  if (panel.value === 'price-offer') {
    await showPriceOfferOffCanvas()
    return
  }

  await setPanel('price-offer')
}

const handleResize = async () => {
  await renderCanvas()
};

async function getConfiguratorResponse(): Promise<SecurityDoorsConfiguratorResponse> {
  const response = await getConfiguratorSecurityDoors(currency.value!)
  if (response.ok) {
    return (await response.json()) as SecurityDoorsConfiguratorResponse;
  } else {
    throw new Error('Error fetching getConfiguratorResponse')
  }
}

async function handleAddDoorButtonClick(): Promise<any> {
  if (selectedDoor.value) {
    try {
      const response = await postAddDoorSecurityDoors({
        frameColor: selectedDoor.value.frameColor,
        handleId: selectedDoor.value.handleId,
        material: selectedDoor.value.material,
        type: selectedDoor.value.type
      }, locale.value)

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

  const rect = doorCanvasActionsRef.value?.getDesktopButtonRect();

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

function isDevButtonVisible(): boolean {
  return appConfig.value?.profile === 'dev';
}

function handleDevButton() {
  devFunction();
}

onMounted(async () => {
  const configuratorApiResponse = await getConfiguratorResponse();
  if (configuratorApiResponse.rooms.length)
    selectedRoom.value = configuratorApiResponse.rooms[0];

  if (configuratorApiResponse.doorCategories.length) {
    const selectedDoorCategory = configuratorApiResponse.doorCategories[0];
    selectedDoor.value = {
      categoryId: selectedDoorCategory.categoryId,
      doorCategory: selectedDoorCategory,
      frameColor: Object.keys(configuratorApiResponse.frameColors)[0] ?? "",
      handleId: selectedDoorCategory.doorConfiguratorDefault.handle,
      material: selectedDoorCategory.doorConfiguratorDefault.material,
      type: selectedDoorCategory.doorConfiguratorDefault.type
    }
  }

  watch(panel, async value => {
    if (value === 'configurator') {
      await showConfiguratorOffCanvas()
    } else if (value === 'price-offer') {
      await showPriceOfferOffCanvas()
    } else {
      offcanvas.close()
    }
  })

  watch(locale, async () => {
    await nextTick();
    await updateButtonPosition();
  });

  await renderCanvas();
  window.addEventListener("resize", handleResize);

  const initialPanel = panel.value
  if (initialPanel) {
    if (initialPanel === 'configurator') await showConfiguratorOffCanvas()
    else await showPriceOfferOffCanvas()
  } else if (!isMobileDevice()) {
    await setPanel('configurator', true)
  } else if (route.query.panel !== undefined) {
    await setPanel(null, true)
  }
})

onUnmounted(() => {
  offcanvas.close()
})
</script>

<template>
  <div class="configurator-canvas">
    <NavbarSecurityDoors
        @handle-configurator-click="openConfiguratorOffCanvas()"
        @handle-price-offer-click="openPriceOfferOffCanvas()"/>
  </div>
  <div class="canvas-wrapper">
    <canvas class="z-0" ref="canvasRef"></canvas>
    <DoorCanvasActions
        ref="doorCanvasActionsRef"
        :desktop-button-visible="buttonAddToPriceOfferIsVisible"
        :desktop-button-style="buttonAddToPriceOfferStyle"
        @add-door="handleAddDoorButtonClick"/>
  </div>
  <DevButton :visible="isDevButtonVisible()" @click="handleDevButton"/>
</template>

<style>
</style>