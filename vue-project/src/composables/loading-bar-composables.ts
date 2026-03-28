import { ref } from 'vue'

const isVisible = ref(false)

export function useLoadingBar() {
    return {
        isVisible,
        displayLoadingBar: () => (isVisible.value = true),
        hideLoadingBar: () => (isVisible.value = false)
    }
}