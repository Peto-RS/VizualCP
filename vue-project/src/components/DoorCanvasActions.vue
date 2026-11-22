<script setup lang="ts">
import {ref, type CSSProperties} from "vue";
import {useI18n} from "vue-i18n";

defineProps<{
  desktopButtonVisible: boolean;
  desktopButtonStyle: CSSProperties;
}>();

const emit = defineEmits<{
  (event: "add-door"): void;
}>();

const {t} = useI18n();
const desktopButtonRef = ref<HTMLButtonElement | null>(null);

function getDesktopButtonRect(): DOMRect | undefined {
  return desktopButtonRef.value?.getBoundingClientRect();
}

defineExpose({getDesktopButtonRect});
</script>

<template>
  <div class="d-flex justify-content-center align-content-center d-md-none">
    <button
        @click="emit('add-door')"
        class="btn btn-secondary btn-lg text-white text-uppercase d-flex align-items-center justify-content-center position-fixed"
        style="bottom: 80px;"
        type="button">
      <i class="fas fa-plus-circle text-white fs-1 me-2"></i>
      <span class="fs-5">{{ t('configurator.buttonAddToPriceOffer') }}</span>
    </button>
  </div>
  <div class="d-none d-md-block">
    <button v-if="desktopButtonVisible"
            ref="desktopButtonRef"
            @click="emit('add-door')"
            class="btn btn-secondary btn-lg text-white text-uppercase d-flex align-items-center justify-content-center"
            :style="desktopButtonStyle"
            type="button">
      <i class="fas fa-plus-circle text-white fs-1 me-2"></i>
      <span class="fs-5">{{ t('configurator.buttonAddToPriceOffer') }}</span>
    </button>
  </div>
</template>
