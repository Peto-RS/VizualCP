<script setup lang="ts">
import {useLoadingBar} from "../composables/loading-bar-composables.js";

const {isVisible} = useLoadingBar()
</script>

<template>
  <p v-if="isVisible" class="placeholder-glow bg-primary mb-0">
    <span class="placeholder placeholder-xs col-12"></span>
  </p>
</template>