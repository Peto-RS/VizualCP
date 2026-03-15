<script setup lang="ts">
import {onBeforeUnmount, onMounted, ref} from "vue";
import {Popover} from "bootstrap";

const props = defineProps<{
  contentFunction?: () => string;
}>();

const popoverEl = ref<HTMLElement | null>(null)
let popOverBootstrap: Popover | null = null

onBeforeUnmount(() => {
  if (popOverBootstrap) {
    popOverBootstrap.dispose()
  }
})

onMounted(() => {
  if (popoverEl.value) {
    popOverBootstrap = new Popover(popoverEl.value, {
      content: () => {
        return props.contentFunction ? props.contentFunction() : ''
      },
      container: 'body',
      html: true,
      trigger: 'hover'
    })
  }
})

// Popover
// if (popoverBtn.value) {
//   new Popover(popoverBtn.value, {
//     trigger: 'hover focus',
//     fallbackPlacements: ['top'],
//     placement: 'bottom',
//     html: true,
//     content: () => {
//       const container = document.createElement('div')
//       const popoverApp = createApp(PopOverContent, {
//         hint: props.hintObj.hint,
//         imgSrc: props.hintObj.imgSrc,
//         videoSrc: props.hintObj.videoSrc,
//       })
//       popoverApp.mount(container)
//
//       popoverBtn.value?.addEventListener(
//           'hidden.bs.popover',
//           () => {
//             popoverApp.unmount()
//             container.remove()
//           },
//           {once: true}
//       )
//
//       return container
//     },
//   })
// }
</script>

<template>
  <a
      ref="popoverEl"
      tabindex="0"
      role="button">
    <slot/>
  </a>


</template>

<style scoped>

</style>

