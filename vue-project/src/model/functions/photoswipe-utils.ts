import {nextTick, onBeforeUnmount, ref, UnwrapRef, watch} from "vue";
import PhotoSwipeLightbox from "photoswipe/lightbox";

export function usePhotoSwipeGalleryWithWatcher(
    selector: string,
    sourceToWatch: () => unknown
): UnwrapRef<PhotoSwipeLightbox | null> {
    const lightbox = ref<PhotoSwipeLightbox | null>(null)

    watch(sourceToWatch, async (val) => {
        if (!val) return
        lightbox.value = await createGallery(selector, lightbox.value)
    }, {immediate: true})

    onBeforeUnmount(() => lightbox.value?.destroy())

    return lightbox.value
}

async function createGallery(
    gallerySelector: string,
    current: UnwrapRef<PhotoSwipeLightbox | null>
): Promise<PhotoSwipeLightbox> {
    current?.destroy()
    await nextTick()

    const lightbox = new PhotoSwipeLightbox({
        gallery: gallerySelector,
        children: 'a',
        pswpModule: () => import('photoswipe')
    })

    lightbox.init()
    return lightbox
}