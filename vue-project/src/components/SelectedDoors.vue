<script setup lang="ts">
import {FormDoor} from "@/model/functions/price-offer-form-builder.js";
import BadgePrice from "../components/BadgePrice.vue";
import {useI18n} from "vue-i18n";
import {SelectedDoorResponse} from "@/model/api/res/price-offer/SelectedDoorResponse.js";
import DoorImage from "../components/DoorImage.vue";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";
import PopOverBootstrapWrapper from "@/components/generic/PopOverBootstrapWrapper.vue";
import {isMobileDevice} from "@/model/functions/responsivity-utils.js";

const emit = defineEmits<{
  (e: 'door-duplicated', index: number): void
}>()

const selectedDoors = defineModel<FormDoor[]>('selectedDoors', {
  required: true
})

const props = defineProps<{
  baseUrl: string | null | undefined,
  doorCategories: DoorCategory[],
  selectedDoorsResponse?: SelectedDoorResponse[]
}>()

const {t} = useI18n();

function getDoorCategoryByCategoryId(categoryId: string | null): DoorCategory | undefined {
  return props.doorCategories.find(it => it.categoryId === categoryId)
}

const handleDoorRemove = (index: number) => {
  selectedDoors.value.splice(index, 1)
}

const handleDoorDuplicate = (index: number) => {
  if (!isMobileDevice()) {
    emit('door-duplicated', index)
  }
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
            <DoorImage
                @click="handleDoorDuplicate(index)"
                :base-url="baseUrl"
                :door-category="selectedDoorsResponse[index].category"
                :door-handle="null"
                :door-material="selectedDoorsResponse[index].material"
                :door-type="selectedDoorsResponse[index].type"
                :excludedDoorPartsFromCanvas="
              getDoorCategoryByCategoryId(selectedDoorsResponse[index].category)
                ?.excludedDoorPartsFromCanvas ?? []
            "/>
          </span>
          <span class="d-none d-md-block">
            <PopOverBootstrapWrapper :content-function="popOverContent">
              <DoorImage
                  @click="handleDoorDuplicate(index)"
                  :base-url="baseUrl"
                  :door-category="selectedDoorsResponse[index].category"
                  :door-handle="null"
                  :door-material="selectedDoorsResponse[index].material"
                  :door-type="selectedDoorsResponse[index].type"
                  :excludedDoorPartsFromCanvas="
              getDoorCategoryByCategoryId(selectedDoorsResponse[index].category)
                ?.excludedDoorPartsFromCanvas ?? []
            "/>
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
          <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                v-model="door.isDoorFrameEnabled"
            />
            <label class="form-check-label">
              {{ t("doors.isDoorFrameEnabled") }}
            </label>
          </div>
        </div>

        <div class="col-12">
          <BadgePrice
              class="selected-doors-badge w-100"
              :price="selectedDoorsResponse[index]?.calculatedPrice"
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