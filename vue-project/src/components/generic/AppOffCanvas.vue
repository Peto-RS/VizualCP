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
       data-bs-scroll="false"
       data-bs-backdrop="false"
       tabindex="-1"
       ref="el"
       aria-labelledby="offcanvasConfiguratorLabel">
    <div class="offcanvas-header pt-2 pb-2">
      <button
          type="button"
          class="btn-close"
          @click="offcanvas.close()"/>
    </div>
    <div class="offcanvas-body">
      <keep-alive>
        <component
            :is="offcanvas.component.value"
            :key="offcanvas.component.value ? offcanvas.keys.get(offcanvas.component.value) : 0"
            v-bind="offcanvas.props.value"
        />
      </keep-alive>
    </div>
  </div>
</template>

<style>
* {
  --bs-navbar-height: 57px;
}

.offcanvas-bottom {
  height: calc(75%) !important;
}

.offcanvas-end {
  top: var(--bs-navbar-height) !important;
  height: calc(100% - var(--bs-navbar-height));
}

.offcanvas-backdrop {
  background-color: transparent;
}
</style>