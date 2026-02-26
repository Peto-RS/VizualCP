import {nextTick, onBeforeUnmount, Ref, ref, watch} from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";

export async function usePhotoSwipeGallery(
    galleryElement: HTMLElement
) {
    const lightbox = ref<PhotoSwipeLightbox | null>(null)
    lightbox.value?.destroy()
    await nextTick()

    lightbox.value = new PhotoSwipeLightbox({
        gallery: galleryElement,
        children: 'a',
        pswpModule: () => import('photoswipe')
    })

    lightbox.value.init()

    onBeforeUnmount(() => {
        lightbox.value?.destroy()
    })
}