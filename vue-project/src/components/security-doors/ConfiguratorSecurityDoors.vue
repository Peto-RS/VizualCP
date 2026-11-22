<script setup lang="ts">

import {computed, onBeforeMount, onMounted, ref, Ref, watch} from "vue";
import {Room} from "@/model/interior-doors/configurator/Room.js";
import {getConfiguratorSecurityDoors, postAddDoorSecurityDoors} from "@/model/security-doors/rest.js";
import {useAppState} from "@/composables/app-state.js";
import {useI18n} from "vue-i18n";
import DoorMiniature from "@/components/DoorMiniature.vue";
import {DoorState} from "@/model/interior-doors/configurator/interface/DoorState.js";
import ImageWithLoadingPlaceholder from "@/components/generic/ImageWithLoadingPlaceholder.vue";
import {useAlerts} from "@/composables/alert-composables.js";
import BadgePrice from "@/components/BadgePrice.vue";
import Footer from "@/components/Footer.vue";
import AccordionItem from "@/components/AccordionItem.vue";
import {offcanvas} from "@/composables/offcanvas.js";
import NavTabs from "@/components/NavTabs.vue";
import {safeT} from "@/model/common/functions/translation-utils.js";
import {
  SecurityDoorsConfiguratorResponse
} from "@/model/security-doors/configurator/res/SecurityDoorsConfiguratorResponse.js";
import {SelectedSecurityDoor} from "@/model/security-doors/interfaces/SelectedSecurityDoor.js";

const {addAlert} = useAlerts()
const {appConfig, currency} = useAppState()
const {locale, t} = useI18n();

const props = defineProps<{
  initialSelectedDoor: SelectedSecurityDoor | null
}>()

const emit = defineEmits<{
  (e: 'door-updated', door: SelectedSecurityDoor): void,
  (e: 'room-selected', room: Room): void,
  (e: 'configurator-clicked'): void,
  (e: 'price-offer-clicked'): void
}>()

const configuratorApiResponse = ref<SecurityDoorsConfiguratorResponse | null>(null);
const selectedDoor: Ref<SelectedSecurityDoor | null> = ref<SelectedSecurityDoor | null>(null)

const doorClasses = "col-3 col-lg-2 gx-1 gy-1";

const doorTypeState = computed(() => {
  return (doorType: string): DoorState => {
    return selectedDoor?.value?.type === doorType ? 'open' : 'closed'
  }
})

async function handleAddDoorButtonClick(): Promise<any> {
  if (selectedDoor.value) {
    try {
      const response = await postAddDoorSecurityDoors({
        frameColor: selectedDoor.value.frameColor!,
        handleId: selectedDoor.value.handleId,
        material: selectedDoor.value.material!,
        type: selectedDoor.value.type!
      }, locale.value)

      if (response.ok) {
        addAlert(t('configurator.doorAddedToPriceOffer'))
      }
    } catch (e) {
      throw new Error('Error adding doors: handleAddDoorButtonClick')
    }
  }
}

function handleDoorTypeChange(type: string) {
  if (selectedDoor.value) {
    selectedDoor.value.type = type
    emit('door-updated', selectedDoor.value)
  }
}

function handleFrameColorChange(frameColor: string) {
  if (selectedDoor.value) {
    selectedDoor.value.frameColor = frameColor
    emit('door-updated', selectedDoor.value)
  }
}

function handleMaterialChange(material: string) {
  if (selectedDoor.value) {
    selectedDoor.value.material = material
    emit('door-updated', selectedDoor.value)
  }
}

async function getConfiguratorResponse(): Promise<SecurityDoorsConfiguratorResponse> {
  const response = await getConfiguratorSecurityDoors(currency.value!)
  if (response.ok) {
    return (await response.json()) as SecurityDoorsConfiguratorResponse;
  } else {
    throw new Error('Error fetching getConfiguratorResponse')
  }
}

onBeforeMount(async () => {
  configuratorApiResponse.value = await getConfiguratorResponse();
  selectedDoor.value = props.initialSelectedDoor;
})

onMounted(async () => {
  watch(currency, async () => {
    configuratorApiResponse.value = await getConfiguratorResponse();
  });
})
</script>

<template>
  <div class="offcanvas-header pb-0 pt-1">
    <NavTabs @handle-configurator-click="emit('configurator-clicked')"
             @handle-price-offer-click="emit('price-offer-clicked')"/>
    <button
        type="button"
        class="btn-close"
        @click="offcanvas.close()"/>
  </div>
  <div class="offcanvas-body">
    <div data-bs-theme="configurator">
      <div class="row" v-if="configuratorApiResponse">
        <div class="accordion mb-2">
          <AccordionItem id="doorType"
                         :is-open-by-default="true"
                         :title="t('securityDoors.configurator.accordionHeaders.doorType')">
            <div class="row"
                 v-if="selectedDoor?.doorCategory">
              <div v-for="(value, doorType) in selectedDoor?.doorCategory.doorTypes"
                   :key="doorType"
                   :class="`${doorClasses} text-center door-miniature`">
                <DoorMiniature :base-url="appConfig!.baseUrl"
                               :door-category="selectedDoor?.doorCategory.categoryId"
                               :door-state="doorTypeState(doorType)"
                               :door-type="doorType"
                               @click="handleDoorTypeChange(doorType)"
                               class="mb-1"/>
                <BadgePrice
                    class="w-100"
                    :price="value.price"/>
                <div class="door-category-word-break text-uppercase">{{ doorType }}</div>
              </div>
            </div>
          </AccordionItem>
          <AccordionItem id="frameColors"
                         v-if="Object.keys(configuratorApiResponse?.frameColors).length"
                         :is-open-by-default="true"
                         :title="t('securityDoors.configurator.accordionHeaders.frameColor')">
            <div class="row">
              <div
                  v-for="(value, key) in configuratorApiResponse?.frameColors"
                  :key="key"
                  :class="`${doorClasses} text-center door-miniature`">
                <div @click="handleFrameColorChange(key)"
                     class="w-100 ratio ratio-1x1 border"
                     :class="{
                  'border-primary': selectedDoor?.frameColor === key,
                  'border-5': selectedDoor?.frameColor === key
                }"
                     :style="{backgroundColor: value.color}"/>
                <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
              </div>
            </div>
          </AccordionItem>
          <AccordionItem id="materials"
                         v-if="Object.keys(configuratorApiResponse?.materials).length"
                         :is-open-by-default="true"
                         :title="t('securityDoors.configurator.accordionHeaders.materials')">
            <div class="row">
              <div
                  v-for="(value, key) in configuratorApiResponse?.materials"
                  :key="key"
                  :class="`${doorClasses} text-center door-miniature`">
                <ImageWithLoadingPlaceholder
                    :alt="key"
                    class="img-fluid w-100 ratio-1x1 border"
                    :class="{
                'border-primary': selectedDoor?.material === key,
                'border-5': selectedDoor?.material === key
              }"
                    @click="handleMaterialChange(key)"
                    :src="appConfig!.baseUrl + '/images/security-doors/materials/min/' + key + '.jpg'"
                />
                <span class="door-category-word-break text-uppercase">{{ safeT(value.labelKey) }}</span>
              </div>
            </div>
          </AccordionItem>
          <AccordionItem id="equipmentIncluded"
                         v-if="configuratorApiResponse?.equipmentIncluded?.length ?? false"
                         :is-open-by-default="true"
                         :title="t('securityDoors.configurator.accordionHeaders.equipmentIncluded')">
            <div class="row">
              <div v-for="(equipment, itemIdx) in configuratorApiResponse?.equipmentIncluded"
                   :key="itemIdx"
                   :class="`${doorClasses} text-center`">
                <ImageWithLoadingPlaceholder
                    :alt="itemIdx.toString()"
                    class="img-fluid w-100 ratio-1x1"
                    :src="appConfig!.baseUrl + '/' + equipment.imgSrc"
                />
                <span class="door-category-word-break text-uppercase">{{ safeT(equipment.labelKey) }}</span>
              </div>
            </div>
          </AccordionItem>
          <AccordionItem id="equipmentOptional"
                         v-if="configuratorApiResponse?.equipmentOptional?.length ?? false"
                         :is-open-by-default="true"
                         :title="t('securityDoors.configurator.accordionHeaders.equipmentOptional')">
            <div class="row">
              <div v-for="(equipment, itemIdx) in configuratorApiResponse?.equipmentOptional"
                   :key="itemIdx"
                   :class="`${doorClasses} text-center`">
                <ImageWithLoadingPlaceholder
                    :alt="itemIdx.toString()"
                    class="img-fluid w-100 ratio-1x1 mb-1"
                    :src="appConfig!.baseUrl + '/' + equipment.imgSrc"
                />
                <BadgePrice
                    class="w-100"
                    :price="equipment.price"/>
                <span class="door-category-word-break text-uppercase">{{ safeT(equipment.labelKey) }}</span>
              </div>
            </div>
          </AccordionItem>
          <AccordionItem id="rooms"
                         :is-open-by-default="true"
                         :title="t('configurator.accordionHeaders.rooms')">
            <div class="row mb-1">
              <div v-for="room in configuratorApiResponse.rooms" class="col-sm-6 gx-1 gy-1">
                <ImageWithLoadingPlaceholder class="img-fluid door-selector-img"
                                             alt="logo"
                                             :src="appConfig!.baseUrl + room.imgSrcMiniature"
                                             @click="emit('room-selected', room)"/>
              </div>
            </div>
          </AccordionItem>
        </div>
      </div>
      <Footer/>
      <div class="d-md-none d-flex justify-content-center">
        <button @click="handleAddDoorButtonClick"
                class="btn btn-secondary btn-lg text-white btn-add-to-price-offer text-uppercase d-flex align-items-center justify-content-center"
                type="button">
          <i class="fas fa-plus-circle text-white fs-1 me-2"></i>
          <span class="fs-5">
          {{ t('configurator.buttonAddToPriceOffer') }}
        </span>
        </button>
      </div>

    </div>
  </div>
</template>

<style lang="scss">
#accordion-button-gallery .accordion-button.collapsed {
  background-color: var(--bs-accordion-active-bg);
  color: var(--bs-accordion-active-color);
}

.btn-add-to-price-offer {
  position: absolute;
  bottom: 25px;
}

.door-miniature:hover {
  transform: scale(1.02);
  cursor: pointer;
}

.door-category-word-break {
  max-width: 120px;
  hyphens: auto;
  overflow-wrap: break-word;
}

.door-selector-img:hover {
  transform: scale(1.02);
  cursor: pointer;
}

.text-align-justify {
  text-align: justify;
}
</style>