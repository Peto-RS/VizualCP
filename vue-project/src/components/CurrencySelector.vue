<script setup lang="ts">
import {CurrencyEnum} from "@/model/enums/CurrencyEnum.js";

const supportedCurrencies: Record<string, string> = {
  [CurrencyEnum.EUR]: CurrencyEnum.EUR,
  [CurrencyEnum.CZK]: CurrencyEnum.CZK
}

const currency = defineModel<string | null>('currency', {
  required: true
})

function handleCurrencyChange() {
  if (currency.value) {
    localStorage.setItem('currency', currency.value)
  }
}
</script>

<template>
  <select class="form-select" v-model="currency" @change="handleCurrencyChange">
    <option
        v-for="(label, code) in supportedCurrencies"
        :key="code"
        :value="code">
      {{ label }}
    </option>
  </select>
</template>

<style scoped>

</style>