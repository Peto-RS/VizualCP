<script setup lang="ts">
import {CURRENCY_SIGN} from "@/model/common/enums/CurrencyEnum.js";
import {formatPrice} from "@/model/common/functions/formatters.js";
import {useAppState} from "@/composables/app-state.js";
const {currency} = useAppState()

const props = defineProps<{ price: number | undefined }>()
</script>

<template>
    <span
        style="background-color: var(--bs-primary);"
        class="badge align-content-center fs-7 text-wrap">{{
        formatPrice(props.price, CURRENCY_SIGN[currency!])
      }}
    </span>
</template>

<style>
</style>