import {shallowRef, ref, nextTick} from 'vue'
import type {Component} from 'vue'
import {Offcanvas} from 'bootstrap'

const component = shallowRef<Component | null>(null)
const props = ref<Record<string, any>>({})
let instance: Offcanvas | null = null
let element: HTMLElement | null = null
const keys = new Map<Component, number>()

function register(el: HTMLElement) {
    element = el
    instance = new Offcanvas(el)

    // el.addEventListener('hidden.bs.offcanvas', () => {
    //     component.value = null
    //     props.value = {}
    // })
}

function open(comp: Component, componentProps = {}, reset = false) {
    if (reset) {
        resetFnc(comp)
    }

    component.value = comp
    props.value = componentProps
    instance?.show()
}

function close() {
    instance?.hide()
}

function resetFnc(comp: Component) {
    keys.set(comp, (keys.get(comp) ?? 0) + 1)
}

export const offcanvas = {
    component,
    props,
    keys,
    register,
    open,
    close,
}