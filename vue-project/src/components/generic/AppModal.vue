<script setup lang="ts">
import {ref, onMounted} from 'vue'
import {modal} from "@/composables/modal.js";

const el = ref<HTMLElement | null>(null)

onMounted(() => {
  if (el.value) {
    modal.register(el.value)
  }
})
</script>

<template>
  <div class="modal-body">
    <div ref="el" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <component
              v-if="modal.component.value"
              :is="modal.component.value"
              v-bind="modal.props.value"/>
      </div>
    </div>
  </div>
</template>