import {shallowRef, ref, nextTick} from 'vue'
import type {Component} from 'vue'
import {Modal, Offcanvas} from 'bootstrap'

const component = shallowRef<Component | null>(null)
const props = ref<Record<string, any>>({})
let instance: Modal | null = null
let element: HTMLElement | null = null

function register(el: HTMLElement) {
    element = el
    instance = new Modal(el)

    el.addEventListener('hidden.bs.modal', () => {
        component.value = null
        props.value = {}
    })
}

function open(comp: Component, componentProps = {}) {
    component.value = comp
    props.value = componentProps
    instance?.show()
}

function close() {
    instance?.hide()
}

export const modal = {
    component,
    props,
    register,
    open,
    close,
}