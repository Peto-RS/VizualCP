<script setup lang="ts">

import Navbar from "../components/Navbar.vue";
// import {useLoadingBar} from "../composables/loading-bar-composables.js"; //TODO:
import ImgWithLoadingPlaceholder from "../components/ImageWithLoadingPlaceholder.vue";
import {getConfigurator} from "../model/rest.js";
import {onMounted, Ref, ref} from "vue";
import {Room} from "../model/res/configurator/Room.js";

// let {displayLoadingBar} = useLoadingBar();//TODO:

//example door layer on top of room image
//configuration component init
//room selector
//door categories selector
//door model selector
//refactor price offer to proper array of doors
//material selector

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
  <Navbar/>
  <ImgWithLoadingPlaceholder class="w-100 h-100"
                             alt="logo"
                             src="http://localhost:8080/assets/img/rooms/Miestnosti vizualizácia 2026 4.png"/>
  <router-view/>
</template>

<style scoped>

</style>