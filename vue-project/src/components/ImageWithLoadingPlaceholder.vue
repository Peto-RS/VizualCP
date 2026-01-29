<script setup lang="ts">
import {onMounted, Ref, ref, useAttrs} from "vue";

const attrs = useAttrs()

defineProps<{
  alt: string;
  src: string;
}>();

const imgLoaded: Ref<boolean> = ref(false);
const imgRef = ref<HTMLImageElement | null>(null);

const onImgLoad = () => {
  imgLoaded.value = true;
};

onMounted(() => {
  if (imgRef.value?.complete) {
    imgLoaded.value = true;
  }
});
</script>

<template>
  <!--  Placeholder-->
  <div
      v-if="!imgLoaded"
      class="placeholder-glow h-100 w-100">
    <span class="placeholder col-12 h-100"></span>
  </div>

  <!--  Image-->
  <img :src="src"
       :alt="alt"
       :class="{ 'opacity-0': !imgLoaded }"
       @load="onImgLoad"
       v-bind="attrs">
</template>

<style scoped>
img {
  transition: opacity 0.3s ease;
}

.door-selector-img {
  cursor: pointer;
  width: 300px;
}
</style>