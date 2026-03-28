import {onBeforeUnmount, ref} from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";

export function usePhotoSwipeGallery(
    galleryElement: HTMLElement
) {
    const lightbox = ref<PhotoSwipeLightbox | null>(null)
    lightbox.value?.destroy()

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