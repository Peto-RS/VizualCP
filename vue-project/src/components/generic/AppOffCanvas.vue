<script setup lang="ts">
import {ref, onMounted, computed, onBeforeUnmount} from 'vue'
import {offcanvas} from '@/composables/offcanvas.js'

const el = ref<HTMLElement | null>(null)
const windowWidth = ref(window.innerWidth);

const canvasPlacement = computed(() => {
  return windowWidth.value >= 576 ? "offcanvas-end" : "offcanvas-bottom";
});

const handleResize = async () => {
  windowWidth.value = window.innerWidth;
  if (el.value) {
    el.value.classList.remove('offcanvas-end', 'offcanvas-bottom');
    el.value.classList.add(canvasPlacement.value);
  }
};

const offCanvasBottomDefaultHeight = '75%';

onMounted(() => {
  window.addEventListener("resize", handleResize);

  if (el.value) {
    offcanvas.register(el.value)
  }

  handleResize();
})

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
});
</script>

<template>
  <div class="offcanvas offcanvas-end"
       tabindex="-1"
       ref="el"
       aria-labelledby="offcanvasConfiguratorLabel"
       :style="canvasPlacement === 'offcanvas-bottom'
    ? { height: offcanvas.parameters.value.height || offCanvasBottomDefaultHeight }
    : {}">
    <keep-alive>
      <component
          :is="offcanvas.component.value"
          :key="offcanvas.component.value ? offcanvas.keys.get(offcanvas.component.value) : 0"
          v-bind="offcanvas.props.value"
      />
    </keep-alive>
  </div>
</template>

<style>
* {
  --bs-navbar-height: 57px;
}

.offcanvas-backdrop {
  background-color: transparent !important;
}
</style>