<script setup lang="ts">
import {onMounted, Ref, ref, useAttrs} from "vue";

const attrs = useAttrs()

const props = defineProps<{
  alt: string;
  src: string | null;
}>();

const imgLoaded: Ref<boolean> = ref(false);
const imgRef = ref<HTMLImageElement | null>(null);

const onImgLoad = () => {
  imgLoaded.value = true;
};

onMounted(() => {
  if (props.src === null) {
    imgLoaded.value = false;
  }

  if (imgRef.value?.complete) {
    imgLoaded.value = true;
  }
});
</script>

<template>
  <!--  Placeholder-->
  <div
      v-if="!imgLoaded"
      class="placeholder-glow w-100" style="aspect-ratio: 1 / 1"
      v-bind="attrs">
    <span class="placeholder col-12 h-100"></span>
  </div>

  <!--  Image-->
  <img v-if="src"
       v-show="imgLoaded"
       :src="src"
       :alt="alt"
       :class="{ 'opacity-0': !imgLoaded }"
       @load="onImgLoad"
       v-bind="attrs">
</template>

<style scoped>
img {
  transition: opacity 0.3s ease;
}
</style>