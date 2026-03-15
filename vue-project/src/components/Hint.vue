<script setup lang="ts">
import {ref, onMounted, onBeforeUnmount, watch} from 'vue'
import {Modal} from 'bootstrap'
// import PopOverContent from './PopOverContent.vue'
import {HintInterface} from "../model/interface/HintInterface.js";
import {modal} from "@/composables/modal.js";
import ModalYoutubeVideo from "@/components/ModalYoutubeVideo.vue";
import {useAppState} from "@/composables/app-state.js";
import {usePhotoSwipeGallery} from "@/model/functions/photoswipe-utils.js";

const props = defineProps<{
  hintObj: HintInterface
}>()

// const modalEl = ref<HTMLElement | null>(null)
// const videoEl = ref<HTMLVideoElement | null>(null)
const galleryContainer = ref<HTMLElement | null>(null)

// const showIframe = ref<Boolean>(false)

const {appConfig} = useAppState()

function openModal() {
  modal.open(ModalYoutubeVideo, {
    hintObj: props.hintObj
  })
}

onMounted(() => {
  watch(() => props.hintObj.imgSrc, () => {
    if (galleryContainer.value) {
      usePhotoSwipeGallery(galleryContainer.value);
    }
  }, {immediate: true})


})
</script>

<template>
  <span ref="galleryContainer">
    <a v-if="hintObj.imgSrc"
       :href="appConfig?.baseUrl + '/' + hintObj.imgSrc"
       data-pswp-width="714"
       data-pswp-height="1024"
       rel="noreferrer">
      <i class="fas fa-question-circle color-dark-red"></i>
    </a>
  </span>

  <a v-if="hintObj.videoSrc || hintObj.youtubeVideoCode"
     @click="openModal"
     role="button">
    <i class="fas fa-question-circle color-dark-red"></i>
  </a>
</template>

<style>
</style>