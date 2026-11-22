<script setup lang="ts">
defineProps<{
  visible: boolean;
}>();

const emit = defineEmits<{
  (event: "click"): void;
}>();
</script>

<template>
  <button v-if="visible"
          @click="emit('click')"
          style="bottom: 20px"
          class="btn btn-secondary btn-lg"
          type="button">
    <span class="fs-5">TEST</span>
  </button>
</template>
