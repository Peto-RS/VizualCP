<script setup lang="ts">

import {SelectedDoorResponse} from "../model/res/SelectedDoorResponse.js";

defineProps<{
  baseUrl: string,
  door: SelectedDoorResponse
}>()
</script>

<template>
  <div class="container-door-image">
    <img :src="`${baseUrl}/images/materials/${door.material}.png`"
         alt="DoorResponse material"
         class="door-layer layer-material">
    <img
        :src="`${baseUrl}/images/doors/${door.category}/${door.type}.png`"
        alt="DoorResponse"
        class="door-layer layer-door">
    <img :src="`${baseUrl}/images/zarubna.png`" alt="DoorResponse frame" class="door-layer layer-frame">
  </div>
</template>

<style scoped>
.container-door-image {
  position: relative;
  width: 100%;
  aspect-ratio: 0.46;
}

.door-layer {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.layer-door {
  z-index: 2;
}

.layer-frame {
  z-index: 3;
  pointer-events: none;
}

.layer-material {
  z-index: 1;
}
</style>