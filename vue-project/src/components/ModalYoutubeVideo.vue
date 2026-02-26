<script setup lang="ts">
import {computed, ref} from "vue";
import {HintInterface} from "@/model/interface/HintInterface.js";

const props = defineProps<{
  hintObj: HintInterface
}>()

const getYoutubeUrl = computed(() => {
  return `https://www.youtube.com/embed/${props.hintObj.youtubeVideoCode}?autoplay=1&mute=1`;
})
</script>

<template>
  <div class="modal-content">
    <div class="ratio ratio-16x9"
         v-if="hintObj.youtubeVideoCode">
      <iframe
          :src="getYoutubeUrl"
          loading="lazy"
          allowfullscreen>
      </iframe>
    </div>
  </div>
</template>

<style scoped>

</style>