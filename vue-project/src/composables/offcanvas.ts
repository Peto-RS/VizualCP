import {shallowRef, ref} from 'vue'
import type {Component} from 'vue'
import {Offcanvas} from 'bootstrap'
import {isMobileDevice} from "@/model/functions/responsivity-utils.js";

interface OffCanvasParameters {
    height?: string
}

const component = shallowRef<Component | null>(null)
const offCanvasParameters = ref<OffCanvasParameters>({})
const props = ref<Record<string, any>>({})
const keys = new Map<Component, number>()

let instance: Offcanvas | null = null
let element: HTMLElement | null = null
let hideBsOffcanvas: (() => void) | null = null

function register(el: HTMLElement) {
    element = el
    instance = new Offcanvas(el, {
        backdrop: isMobileDevice(),
        keyboard: false,
        scroll: false,
    })

    // Attach listener ONCE
    element.addEventListener('hide.bs.offcanvas', () => {
        hideBsOffcanvas?.()
        hideBsOffcanvas = null
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
        resetFnc(comp)
    }

    component.value = comp
    props.value = componentProps
    hideBsOffcanvas = hideBsOffcanvasCallback ?? null
    offCanvasParameters.value = parameters
    instance?.show()
}

function close() {
    instance?.hide()
}

function resetFnc(comp: Component) {
    keys.set(comp, (keys.get(comp) ?? 0) + 1)
}

export const offcanvas = {
    parameters: offCanvasParameters,
    component,
    props,
    keys,
    register,
    open: open,
    close,
}