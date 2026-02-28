<script setup lang="ts">

import {onBeforeMount, onMounted, onUnmounted, Ref, ref} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {useAppState} from "../composables/app-state.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";
import {offcanvas} from "@/composables/offcanvas.js";
import Configurator from "@/components/Configurator.vue";
import {generateCanvas} from "@/model/functions/generate-canvas.js";
import {ConfiguratorResponse} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {getConfigurator} from "@/model/api/rest.js";
import Navbar from "@/components/Navbar.vue";
import PriceOffer from "@/components/PriceOffer.vue";
import {useLoadingBar} from "@/composables/loading-bar-composables.js";

const {appConfig} = useAppState()
const {displayLoadingBar, hideLoadingBar} = useLoadingBar()
const configuratorApiResponse: Ref<ConfiguratorResponse | null> = ref(null)
const selectedDoor = ref<SelectedDoor | null>(null)
const selectedRoom = ref<Room | null>(null)
const roomBackgroundImg = ref<string | null>(null)

async function handleDoorChange(door: SelectedDoor) {
  selectedDoor.value = door
  displayLoadingBar()
  roomBackgroundImg.value = await generateCanvas(appConfig.value?.baseUrl!, selectedRoom.value, selectedDoor.value, window.innerWidth < window.innerHeight)
  hideLoadingBar()
}

async function handleRoomChange(room: Room | null) {
  selectedRoom.value = room
  displayLoadingBar()
  roomBackgroundImg.value = await generateCanvas(appConfig.value?.baseUrl!, selectedRoom.value, selectedDoor.value, window.innerWidth < window.innerHeight)
  hideLoadingBar()
}

const handleResize = async () => {
  roomBackgroundImg.value = await generateCanvas(appConfig.value?.baseUrl!, selectedRoom.value, selectedDoor.value, window.innerWidth < window.innerHeight)
};

async function getConfiguratorResponse(): Promise<ConfiguratorResponse> {
  const response = await getConfigurator()
  if (response.ok) {
    const json = (await response.json()) as ConfiguratorResponse
    configuratorApiResponse.value = json

    return json;
  } else {
    throw new Error('Error fetching getConfiguratorResponse')
  }
}

onBeforeMount(async () => {
  configuratorApiResponse.value = await getConfiguratorResponse();
})

onMounted(async () => {
  window.addEventListener("resize", handleResize);

  offcanvas.open(Configurator, {
    configuratorApiResponse: await getConfiguratorResponse(),
    onDoorSelected: handleDoorChange,
    onRoomSelected: handleRoomChange
  })
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
        @handle-configurator-click="offcanvas.open(Configurator, {configuratorApiResponse, onDoorSelected: handleDoorChange, onRoomSelected: handleRoomChange})"
        @handle-price-offer-click="offcanvas.open(PriceOffer, {}, true)"/>
  </div>
</template>

<style scoped>
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
</style>