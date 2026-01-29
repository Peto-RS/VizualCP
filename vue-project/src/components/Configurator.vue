<script setup lang="ts">

import ImgWithLoadingPlaceholder from "../components/ImageWithLoadingPlaceholder.vue";
import {onMounted, ref, Ref} from "vue";
import {Room} from "../model/res/configurator/Room.js";
import {getConfigurator} from "../model/rest.js";

const rooms: Ref<Room[]> = ref([])

async function getRooms(): Promise<void> {
  const response = await getConfigurator()
  if (response.ok) {
    const json = await response.json()
    rooms.value = json as Room[]
  } else {
    throw new Error("Error fetching rooms")
  }
}

onMounted(() => {
  getRooms()
})
</script>

<template>
  <div class="row">
    <ImgWithLoadingPlaceholder v-for="room in rooms"
                               class="col gy-2 door-selector-img"
                               alt="logo"
                               :src="'http://localhost:8080/' + room.imgSrcMiniature"/>
  </div>
</template>

<style scoped>
</style>