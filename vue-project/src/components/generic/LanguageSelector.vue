<script setup lang="ts">
import {useI18n} from "vue-i18n";
import {setLocale, SupportedLocale} from "@/model/functions/translation-utils.js";

const {locale} = useI18n()

const locales: Record<SupportedLocale, string> = {
  sk: 'Slovenčina',
  bg: 'Български',
  cs: 'Čeština',
  de: 'Deutsch',
  en: 'English',
  es: 'Español',
  fr: 'Français',
  it: 'Italiano',
  hu: 'Magyar',
  pl: 'Polski',
  ro: 'Română',
  sr: 'Srpski'
}

const changeLocale = async (event: any) => {
  const newLocale = event.target.value
  await setLocale(newLocale)
}
</script>

<template>
  <select class="form-select" :value="locale" @change="changeLocale">
    <option
        v-for="(label, code) in locales"
        :key="code"
        :value="code">
      {{ label }}
    </option>
  </select>
</template>

<style scoped>

</style>