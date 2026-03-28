<script setup lang="ts">

import {computed} from "vue";

const props = defineProps<{
  baseUrl: string | null | undefined,
  doorCategory: string | null,
  doorHandle: string | null | undefined,
  doorMaterial: string | null,
  doorType: string | null,
  excludedDoorPartsFromCanvas: string[]
}>()

const getBaseUrl = computed(() => props.baseUrl ?? window.location.origin)
</script>

<template>
  <div class="container-door-image">
    <img v-if="doorMaterial"
         :src="`${getBaseUrl}/images/materials/${doorMaterial}.png`"
         alt="doorMaterial"
         class="door-layer layer-material">
    <img
        v-if="doorCategory && doorType"
        :src="`${getBaseUrl}/images/doors/${doorCategory}/${doorType}.png`"
        alt="door"
        class="door-layer layer-door">
    <img v-if="!excludedDoorPartsFromCanvas.includes('frame')"
         :src="`${getBaseUrl}/images/zarubna.png`"
         alt="doorFrame"
         class="door-layer layer-frame">
    <img v-if="doorHandle"
         :src="`${getBaseUrl}/images/handles/${doorHandle}.png`"
         alt="doorHandle"
         class="door-layer layer-handle">
  </div>
</template>

<style>
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

.layer-material {
  z-index: 1;
}

.layer-door {
  z-index: 2;
}

.layer-frame {
  z-index: 3;
  pointer-events: none;
}

.layer-handle {
  z-index: 4;
  pointer-events: none;
}
</style>