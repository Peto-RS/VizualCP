<script setup lang="ts">
import {ref, onMounted, computed, onBeforeUnmount} from 'vue'
import {offcanvas} from '@/composables/offcanvas'

const el = ref<HTMLElement | null>(null)
const windowWidth = ref(window.innerWidth);

const canvasPlacement = computed(() => {
  return windowWidth.value >= 992 ? "offcanvas-end" : "offcanvas-bottom";
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
       data-bs-scroll="true"
       data-bs-backdrop="false"
       tabindex="-1"
       ref="el"
       aria-labelledby="offcanvasConfiguratorLabel">
    <div class="offcanvas-header">
      <button
          type="button"
          class="btn-close"
          @click="offcanvas.close()"
      />
    </div>
    <div class="offcanvas-body">
      <component
          v-if="offcanvas.component.value"
          :is="offcanvas.component.value"
          v-bind="offcanvas.props.value"
      />
    </div>
  </div>
</template>

<style scoped>
* {
  --bs-navbar-height: 60px;
}

.offcanvas-bottom {
  height: calc(100% - var(--bs-navbar-height)) !important;
}

.offcanvas-end {
  top: var(--bs-navbar-height) !important;
  height: calc(100% - var(--bs-navbar-height));
}

.offcanvas-backdrop {
  background-color: transparent;
}
</style>