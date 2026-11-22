<script setup lang="ts">
import BadgePrice from "../BadgePrice.vue";
import {useI18n} from "vue-i18n";
import DoorImage from "../DoorImageSecurityDoors.vue";
import PopOverBootstrapWrapper from "@/components/generic/PopOverBootstrapWrapper.vue";
import {SecurityDoorCategory} from "@/model/security-doors/configurator/SecurityDoorCategory.js";
import {FormDoor} from "@/model/security-doors/function/security-doors-price-offer-form-builder.js";
import DoorImageSecurityDoors from "@/components/DoorImageSecurityDoors.vue";
import {SecurityDoorsDoorResponse} from "@/model/security-doors/price-offer/res/SecurityDoorsDoorResponse.js";

const emit = defineEmits<{
  (e: 'door-duplicated', index: number): void,
  (e: 'door-removed', index: number): void
}>()

const selectedDoors = defineModel<FormDoor[]>('selectedDoors', {
  required: true
})

const props = defineProps<{
  baseUrl: string | null | undefined,
  doorCategories: SecurityDoorCategory[],
  selectedDoorsResponse?: SecurityDoorsDoorResponse[]
}>()

const {t} = useI18n();

const handleDoorRemove = (index: number) => {
  emit('door-removed', index)
}

const handleDoorDuplicate = (index: number) => {
  emit('door-duplicated', index)
}

const popOverContent = () => {
  return t('doors.duplicate');
}
</script>

<template>
  <div class="row mb-1">
    <div
        class="col-3 col-lg-4 col-xl-2 g-1"
        v-for="(door, index) in selectedDoors"
        :key="index">
      <div
          class="row gy-1"
          v-if="selectedDoorsResponse && selectedDoorsResponse[index]">
        <div class="d-flex flex-column w-100 h-100 gap-1">
          <span class="d-md-none">
            <DoorImageSecurityDoors
                @click="handleDoorDuplicate(index)"
                :base-url="baseUrl"
                :door-category="selectedDoorsResponse[index].category || null"
                :door-frame-color="selectedDoorsResponse[index].frameColor"
                :door-handle="null"
                :door-material="selectedDoorsResponse[index].material"
                :door-type="selectedDoorsResponse[index].type"
            />
          </span>
          <span class="d-none d-md-block">
            <PopOverBootstrapWrapper :content-function="popOverContent">
              <DoorImageSecurityDoors
                  @click="handleDoorDuplicate(index)"
                  :base-url="baseUrl"
                  :door-category="selectedDoorsResponse[index].category || null"
                  :door-frame-color="selectedDoorsResponse[index].frameColor"
                  :door-handle="null"
                  :door-material="selectedDoorsResponse[index].material"
                  :door-type="selectedDoorsResponse[index].type"
              />
          </PopOverBootstrapWrapper>
          </span>
          <div class="text-center" style="word-break: break-all; min-height: 3rem">
            {{ selectedDoorsResponse[index].type?.toUpperCase() }}
          </div>
        </div>

        <div class="col-12">
          <select class="form-select" v-model="door.doorWidth">
            <option value="">{{ t("doors.doorWidth") }}</option>
            <option value="W60">60</option>
            <option value="W70">70</option>
            <option value="W80">80</option>
            <option value="W90">90</option>
          </select>
        </div>

        <div class="col-12">
          <BadgePrice
              class="selected-doors-badge w-100"
              :price="selectedDoorsResponse[index]?.calculatedPrice ?? undefined"
          />
        </div>

        <div class="col-12">
          <button
              type="button"
              @click="handleDoorRemove(index)"
              class="btn btn-outline-secondary w-100">
            <span class="fas fa-trash"></span>
            {{ t("doors.remove") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
.selected-doors-badge {
  height: $input-height;
}
</style>