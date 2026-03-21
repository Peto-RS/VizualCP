<script setup lang="ts">

import {computed, nextTick, onBeforeMount, onMounted, ref, Ref, watch} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {postAddDoor} from "../model/api/rest.js";
import {useAppState} from "../composables/app-state.js";
import {useI18n} from "vue-i18n";
import DoorMiniature from "@/components/DoorMiniature.vue";
import {ConfiguratorResponse, Handle, Material} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";
import {DoorState} from "@/model/interface/configurator/DoorState.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";
import ImageWithLoadingPlaceholder from "@/components/generic/ImageWithLoadingPlaceholder.vue";
import 'photoswipe/style.css';
import {useAlerts} from "@/composables/alert-composables.js";
import {usePhotoSwipeGallery} from "@/model/functions/photoswipe-utils.js";
import {formatPrice} from "@/model/functions/formatters.js";
import BadgePrice from "@/components/BadgePrice.vue";
import Footer from "@/components/Footer.vue";
import AccordionItem from "@/components/AccordionItem.vue";

const {addAlert} = useAlerts()
const {appConfig} = useAppState()
const {t} = useI18n();

const props = defineProps<{
  configuratorApiResponse: ConfiguratorResponse,
  initialSelectedDoor: SelectedDoor | null
}>()

const emit = defineEmits<{
  (e: 'door-selected', door: SelectedDoor): void,
  (e: 'room-selected', room: Room): void
}>()

const doorTypesSelectionSection = ref<HTMLElement | null>(null);
const galleryRef = ref<HTMLElement | null>(null)
const doorsGalleryRef = ref<HTMLElement | null>(null)
const glassGalleryRef = ref<HTMLElement | null>(null)
const selectedDoor: Ref<SelectedDoor | null> = ref(null)

const doorClasses = "col-3 col-lg-2 gx-1 gy-1";

const availableHandlesForDoorCategories = computed(() => {
  return (handles: Record<string, Handle>): Record<string, Handle> => {
    const category = selectedDoor.value?.categoryId
    if (!category) return {}

    return Object.fromEntries(
        Object.entries(handles).filter(([, handle]) => {
          if (handle.availableForDoorCategory) {
            return handle.availableForDoorCategory.includes(category)
          }

          if (handle.hiddenForDoorCategory) {
            return !handle.hiddenForDoorCategory.includes(category)
          }

          return true
        })
    )
  }
})

const availableMaterialsForDoorCategories = computed(() => {
  return (materials: Record<string, Material>): Record<string, Material> => {
    const category = selectedDoor.value?.categoryId
    if (!category) return {}

    return Object.fromEntries(
        Object.entries(materials).filter(([, material]) => {
          if (material.availableForDoorCategory) {
            return material.availableForDoorCategory.includes(category)
          }

          if (material.hiddenForDoorCategory) {
            return !material.hiddenForDoorCategory.includes(category)
          }

          return true
        })
    )
  }
})

const computeDoorCategoryState = computed(() => {
  return (categoryId: string): DoorState => {
    return selectedDoor?.value?.doorCategory?.categoryId === categoryId ? 'open' : 'closed'
  }
});

const doorTypeState = computed(() => {
  return (doorType: string): DoorState => {
    return selectedDoor?.value?.type === doorType ? 'open' : 'closed'
  }
})

function handleDoorCategoryChange(doorCategory: DoorCategory) {
  selectedDoor.value = {
    categoryId: doorCategory.categoryId,
    doorCategory: doorCategory,
    handleId: doorCategory.doorConfiguratorDefault.handle,
    material: doorCategory.doorConfiguratorDefault.material,
    type: doorCategory.doorConfiguratorDefault.type
  }

  emit('door-selected', selectedDoor.value)
}

async function handleAddDoorButtonClick(): Promise<any> {
  if (selectedDoor.value) {
    try {
      const response = await postAddDoor({
        category: selectedDoor.value.categoryId!,
        handleId: selectedDoor.value.handleId,
        material: selectedDoor.value.material!,
        type: selectedDoor.value.type!
      })

      if (response.ok) {
        addAlert(t('configurator.doorAddedToPriceOffer'))
      }
    } catch (e) {
      throw new Error('Error adding doors: handleAddDoorButtonClick')
    }
  }
}

async function handleDoorCategoryChangeWithScrollIntoView(doorCategory: DoorCategory) {
  handleDoorCategoryChange(doorCategory)
  await nextTick()

  if (doorTypesSelectionSection.value) {
    doorTypesSelectionSection.value.scrollIntoView({behavior: 'smooth'})
  }
}

function handleDoorTypeChange(type: string) {
  if (selectedDoor.value) {
    selectedDoor.value.type = type
    emit('door-selected', selectedDoor.value)
  }
}

function handleHandleChange(handleId: string) {
  if (selectedDoor.value) {
    selectedDoor.value.handleId = handleId
    emit('door-selected', selectedDoor.value)
  }
}

function handleMaterialChange(material: string) {
  if (selectedDoor.value) {
    selectedDoor.value.material = material
    emit('door-selected', selectedDoor.value)
  }
}

onBeforeMount(async () => {
  selectedDoor.value = props.initialSelectedDoor
})

onMounted(() => {
  watch(props.configuratorApiResponse.glasses, () => {
    if (glassGalleryRef.value) {
      usePhotoSwipeGallery(glassGalleryRef.value);
    }
  }, {immediate: true})

  watch(galleryRef, () => {
    if (galleryRef.value) {
      usePhotoSwipeGallery(galleryRef.value);
    }
  }, {immediate: true})

  watch(doorsGalleryRef, () => {
    if (doorsGalleryRef.value) {
      usePhotoSwipeGallery(doorsGalleryRef.value);
    }
  }, {immediate: true})
})
</script>

<template>
  <div data-bs-theme="configurator">
    <div class="row" v-if="configuratorApiResponse">
      <div class="accordion mb-2">
        <AccordionItem id="doorCategory"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.doorCategory')">
          <div class="row">
            <div v-for="doorCategory in configuratorApiResponse.doorCategories"
                 :class="`${doorClasses} text-center door-miniature`">
              <DoorMiniature :base-url="appConfig?.baseUrl"
                             :door-category="doorCategory.categoryId"
                             :door-state="computeDoorCategoryState(doorCategory.categoryId)"
                             :door-type="doorCategory.doorConfiguratorDefault.type"
                             @click="handleDoorCategoryChangeWithScrollIntoView(doorCategory)"/>
              <div class="door-category-word-break text-uppercase">{{ doorCategory.name }}</div>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="doorType"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.doorType', {doorType: selectedDoor?.doorCategory?.name})">
          <div ref="doorTypesSelectionSection">{{ selectedDoor?.doorCategory?.description }}</div>
          <div class="row"
               v-if="selectedDoor?.doorCategory">
            <div v-for="(value, doorType) in selectedDoor?.doorCategory.doorTypes"
                 :key="doorType"
                 :class="`${doorClasses} text-center door-miniature`">
              <DoorMiniature :base-url="appConfig?.baseUrl"
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
          <div>{{ t('configurator.doorFramePrice', {price: formatPrice(99)}) }}</div>
        </AccordionItem>
        <AccordionItem id="laminates"
                       v-if="Object.keys(availableMaterialsForDoorCategories(configuratorApiResponse?.materials.laminates)).length"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.laminate')">
          <div class="row">
            <div
                v-for="(value, key) in availableMaterialsForDoorCategories(configuratorApiResponse?.materials.laminates)"
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
                  :src="appConfig?.baseUrl + '/images/materials/min/' + key + '.jpg'"
              />
              <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="veneers"
                       v-if="Object.keys(availableMaterialsForDoorCategories(configuratorApiResponse?.materials.veneers)).length"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.veneers')">
          <div class="row">
            <div v-for="(value, key) in availableMaterialsForDoorCategories(configuratorApiResponse?.materials.veneers)"
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
                  :src="appConfig?.baseUrl + '/images/materials/min/' + key + '.jpg'"
              />
              <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="handles"
                       v-if="!selectedDoor?.doorCategory?.hiddenConfiguratorSections.includes('handles')"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.handles')">
          <h4 class="text-uppercase">{{ t('configurator.selectHandles') }}
            <a href="https://www.mp-kovania.sk" target="_blank">mp-kovania.sk</a>
          </h4>
          <div class="row">
            <div v-for="(value, key) in availableHandlesForDoorCategories(configuratorApiResponse?.handles)"
                 :key="key"
                 :class="`${doorClasses} text-center door-miniature`">
              <ImageWithLoadingPlaceholder
                  :alt="key"
                  class="img-fluid w-100 ratio-1x1 border mb-1"
                  :class="{
                'border-primary': selectedDoor?.handleId === key,
                'border-5': selectedDoor?.handleId === key
              }"
                  @click="handleHandleChange(key)"
                  :src="appConfig?.baseUrl + value.imgSrc"
              />
              <BadgePrice
                  class="w-100"
                  :price="value.price"/>
              <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="constructionPossibilities"
                       v-if="selectedDoor?.doorCategory?.constructionPossibilities?.length ?? false"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.constructionPossibilities')">
          <div class="row">
            <div v-for="(constructionPossibility, index) in selectedDoor?.doorCategory?.constructionPossibilities"
                 :key="index">
              <span>{{ constructionPossibility.description }}</span>
              <div class="row">
                <div v-for="(item, itemIdx) in constructionPossibility.items"
                     :key="itemIdx"
                     :class="`${doorClasses} text-center`">
                  <ImageWithLoadingPlaceholder
                      :alt="itemIdx.toString()"
                      class="img-fluid w-100 ratio-1x1"
                      :src="appConfig?.baseUrl + item.imgSrc"
                  />
                  <span class="door-category-word-break text-uppercase">{{ item.label }}</span>
                </div>
              </div>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="glasses"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.glasses')">
          <div class="row" ref="glassGalleryRef">
            <div v-for="(value, key) in configuratorApiResponse?.glasses"
                 :key="key"
                 :class="`${doorClasses} text-center door-miniature`">
              <a
                  :href="appConfig?.baseUrl + '/images/glasses/' + key + '.jpg'"
                  data-pswp-width="500"
                  data-pswp-height="500"
                  rel="noreferrer">
                <ImageWithLoadingPlaceholder
                    :alt="key"
                    class="img-fluid w-100 ratio-1x1"
                    :src="appConfig?.baseUrl + '/images/glasses/min/' + key + '.jpg'"
                />
              </a>
              <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
            </div>
          </div>
        </AccordionItem>
        <AccordionItem id="doorsGallery"
                       v-if="selectedDoor?.doorCategory?.gallery && selectedDoor?.doorCategory?.gallery?.length"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.gallery')">
          <div class="row" ref="doorsGalleryRef">
            <div v-for="(value, key) in selectedDoor?.doorCategory?.gallery"
                 :key="key"
                 class="col-sm-6 gx-1 gy-1 text-center">
              <a :href="appConfig?.baseUrl + value.imgSrc"
                 data-pswp-width="1838"
                 data-pswp-height="1365"
                 rel="noreferrer">
                <ImageWithLoadingPlaceholder
                    :alt="key.toString()"
                    class="img-fluid ratio-1x1"
                    :src="appConfig?.baseUrl + value.imgSrc"/>
              </a>
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
                                           :src="appConfig?.baseUrl + room.imgSrcMiniature"
                                           @click="emit('room-selected', room)"/>
            </div>
          </div>
          <div class="row">
            <AccordionItem id="gallery"
                           :is-open-by-default="false"
                           :title="t('configurator.accordionHeaders.gallery')">
              <div class="row" ref="galleryRef">
                <div v-for="(value, key) in configuratorApiResponse.gallery"
                     :key="key"
                     :class="`col-${value.col} gx-1 gy-1 text-center`">
                  <a :href="appConfig?.baseUrl + value.imgSrc"
                     :data-pswp-width="value.width"
                     :data-pswp-height="value.height"
                     rel="noreferrer">
                    <ImageWithLoadingPlaceholder
                        :alt="key.toString()"
                        class="img-fluid ratio-1x1"
                        :src="appConfig?.baseUrl + value.imgSrc"/>
                  </a>
                </div>
              </div>
            </AccordionItem>
          </div>
        </AccordionItem>
        <AccordionItem id="currentActions"
                       :is-open-by-default="true"
                       :title="t('configurator.accordionHeaders.currentActions')">
          <div class="row mt-3">
            <div class="col-12 col-lg-4">
              <ImageWithLoadingPlaceholder
                  alt="interierove-dvere-cena.jpg"
                  class="img-fluid w-100 ratio-1x1"
                  :src="appConfig?.baseUrl + '/images/interierove-dvere-cena.jpg'"/>
              <div class="p-3">
                <div class="text-uppercase text-center mb-3">Interiérové <b>dvere so zárubňou</b> od <b>219,- €</b>
                </div>
                <div class="text-align-justify small">Interiérové dvere a zárubne Vám vo všetkých druhoch laminátov
                  ponúkame
                  za
                  akciovú cenu 219€. Cena platí aj pre dvere, ktoré prídeme pred výrobou osobne zamerať.
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <ImageWithLoadingPlaceholder
                  alt="zameranie-zadarmo.jpg"
                  class="img-fluid w-100 ratio-1x1"
                  :src="appConfig?.baseUrl + '/images/zameranie-zadarmo.jpg'"
              />
              <div class="p-3">
                <div class="text-uppercase text-center mb-3"><b>Zameranie</b> stavebných otvorov <b>zadarmo</b></div>
                <div class="text-align-justify small">Trápia Vás atypické miery otvorov? Pre nás nie sú žiadnym
                  problémom.
                  Každý
                  otvor
                  osobne zameriame a dvere vyrobíme tak, aby čo najpresnejšie zapasovali. Dvere na mieru sú u nás BEZ
                  PRÍPLATKU.
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <ImageWithLoadingPlaceholder
                  alt="montaz-dveri.jpg"
                  class="img-fluid w-100 ratio-1x1"
                  :src="appConfig?.baseUrl + '/images/montaz-dveri.jpg'"
              />
              <div class="p-3">
                <div class="text-uppercase text-center mb-3"><b>Montáž</b> inte&shy;rié&shy;ro&shy;vých dverí a zárubne
                  <b>35,-
                    €</b>
                </div>
                <div class="text-align-justify small">Montáž kompletu interiérové dvere so zárubňou Vám ponúkame za
                  zvýhodnenú
                  cenu
                  od
                  35€/ks. Táto akciová cena platí pri montáži šiestich a viac kusov dverí a zárubní.
                </div>
              </div>
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
</template>

<style lang="scss">
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