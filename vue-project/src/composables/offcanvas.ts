import {shallowRef, ref} from 'vue'
import {Offcanvas} from 'bootstrap'
import {isMobileDevice} from "@/model/common/functions/responsivity-utils.js";
import type {Component} from 'vue'

interface OffCanvasParameters {
    height?: string
}

const component = shallowRef<Component | null>(null)
const offCanvasParameters = ref<OffCanvasParameters>({})
const props = ref<Record<string, any>>({})
const componentKey = ref(0)

let instance: Offcanvas | null = null
let element: HTMLElement | null = null
let hideBsOffcanvas: (() => void) | null = null
let isHiding = false
let reopenAfterHide = false

function register(el: HTMLElement) {
    element = el
    instance = new Offcanvas(el, {
        backdrop: isMobileDevice(),
        keyboard: false,
        scroll: false,
    })

    // Attach listener ONCE
    element.addEventListener('hide.bs.offcanvas', () => {
        isHiding = true
        hideBsOffcanvas?.()
        hideBsOffcanvas = null
    })

    element.addEventListener('hidden.bs.offcanvas', () => {
        isHiding = false

        if (reopenAfterHide) {
            reopenAfterHide = false
            instance?.show()
            return
        }

        component.value = null
        props.value = {}
        offCanvasParameters.value = {}
    })
}

function open(
    comp: Component,
    componentProps: Record<string, any> = {},
    reset = false,
    hideBsOffcanvasCallback?: () => void,
    parameters: OffCanvasParameters = {}
) {
    if (reset) {
        componentKey.value += 1
    }

    component.value = comp
    props.value = componentProps
    hideBsOffcanvas = hideBsOffcanvasCallback ?? null
    offCanvasParameters.value = parameters

    if (isHiding) {
        reopenAfterHide = true
        return
    }

    instance?.show()
}

function close() {
    reopenAfterHide = false
    instance?.hide()
}

export const offcanvas = {
    parameters: offCanvasParameters,
    component,
    componentKey,
    props,
    register,
    open: open,
    close,
}