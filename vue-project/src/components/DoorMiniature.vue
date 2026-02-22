<script setup lang="ts">

import {computed} from "vue";
import ImageWithLoadingPlaceholder from "@/components/ImageWithLoadingPlaceholder.vue";
import {DoorState} from "@/model/interface/configurator/DoorState.js";

const props = defineProps<{
  baseUrl: string | null | undefined,
  doorCategory: string,
  doorState: DoorState,
  doorType: string
}>()

const getBaseUrl = computed(() => props.baseUrl ?? window.location.origin)
const getImgSrc = computed(() => props.doorState === 'closed' ?
    `${getBaseUrl.value}/images/doors/${props.doorCategory}/min/${props.doorType}.png` :
    `${getBaseUrl.value}/images/doors/${props.doorCategory}/min/open/o${props.doorType}.png`)
</script>

<template>
  <ImageWithLoadingPlaceholder :alt="doorCategory"
                               class="img-fluid"
                               :src="getImgSrc"/>
</template>

<style scoped>

</style>