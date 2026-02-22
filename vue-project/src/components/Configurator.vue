<script setup lang="ts">

import {computed, nextTick, onBeforeMount, onMounted, ref, Ref} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {getConfigurator, postAddDoor} from "../model/api/rest.js";
import {useAppState} from "../composables/app-state.js";
import {useI18n} from "vue-i18n";
import DoorMiniature from "@/components/DoorMiniature.vue";
import {ConfiguratorResponse, Material} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";
import {DoorState} from "@/model/interface/configurator/DoorState.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";
import ImageWithLoadingPlaceholder from "@/components/ImageWithLoadingPlaceholder.vue";
import 'photoswipe/style.css';
import {useAlerts} from "@/composables/alert-composables.js";
import {usePhotoSwipeGalleryWithWatcher} from "@/model/functions/photoswipe-utils.js";
import {formatPrice} from "@/model/functions/formatters.js";
import BadgePrice from "@/components/BadgePrice.vue";

const {addAlert} = useAlerts()
const {appConfig} = useAppState()
const {t} = useI18n();

const props = defineProps<{
  configuratorApiResponse: ConfiguratorResponse
}>()

const emit = defineEmits<{
  (e: 'door-selected', door: SelectedDoor): void,
  (e: 'room-selected', room: Room): void
}>()

const selectedDoorCategory: Ref<DoorCategory | null> = ref(null)
const doorClasses = "col-2 col-lg-2 gx-1 gy-1";

usePhotoSwipeGalleryWithWatcher(
    '#glass-gallery',
    () => props.configuratorApiResponse.glasses
);

usePhotoSwipeGalleryWithWatcher(
    '#doors-gallery',
    () => selectedDoorCategory.value
);

const doorTypesSelectionSection = ref<HTMLElement | null>(null);
const selectedDoor: Ref<SelectedDoor | null> = ref(null)

const availableMaterialsForDoorCategories = computed(() => {
  return (materials: Record<string, Material>): Record<string, Material> => {
    const category = selectedDoor.value?.category
    if (!category) return {}

    return Object.fromEntries(
        Object.entries(materials).filter(([, material]) => {
          if (material.availableFor) {
            return material.availableFor.includes(category)
          }

          if (material.hiddenFor) {
            return !material.hiddenFor.includes(category)
          }

          return true
        })
    )
  }
})

const computeDoorCategoryState = computed(() => {
  return (categoryId: string): DoorState => {
    return selectedDoorCategory?.value?.categoryId === categoryId ? 'open' : 'closed'
  }
});

const doorTypeState = computed(() => {
  return (doorType: string): DoorState => {
    return selectedDoor?.value?.type === doorType ? 'open' : 'closed'
  }
})

function handleDoorCategoryChange(doorCategory: DoorCategory) {
  selectedDoorCategory.value = doorCategory

  selectedDoor.value = {
    category: doorCategory.categoryId,
    handle: doorCategory.doorConfiguratorDefault.handle,
    material: doorCategory.doorConfiguratorDefault.material,
    type: doorCategory.doorConfiguratorDefault.type
  }

  emit('door-selected', selectedDoor.value)
}

async function handleAddDoorButtonClick(): Promise<any> {
  try {
    const response = await postAddDoor({
      category: selectedDoor.value?.category!,
      material: selectedDoor.value?.material!,
      type: selectedDoor.value?.type!
    })

    if (response.ok) {
      addAlert('Dvere boli pridané do cenovej ponuky.')
    }
  } catch (e) {
    throw new Error('Error adding doors: handleAddDoorButtonClick')
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

function handleHandleChange(handle: string) {
  if (selectedDoor.value) {
    selectedDoor.value.handle = handle
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
  if (props.configuratorApiResponse.rooms.length)
    emit('room-selected', props.configuratorApiResponse.rooms[0]);
  if (props.configuratorApiResponse.doorCategories.length)
    handleDoorCategoryChange(props.configuratorApiResponse.doorCategories[0]);
  if (props.configuratorApiResponse.handles) {
    handleHandleChange(Object.keys(props.configuratorApiResponse.handles)[0]);
  }
})
</script>

<template>
  <div class="row" v-if="configuratorApiResponse">
    <div class="mb-2">
      <h4 class="text-uppercase">{{ t('configurator.selectDoorCategory') }}</h4>
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
    </div>

    <div class="mb-2"
         ref="doorTypesSelectionSection">
      <h4 class="text-uppercase">{{ t('configurator.selectDoorType', {doorType: selectedDoorCategory?.name}) }}</h4>
      <div>{{ selectedDoorCategory?.description }}</div>
      <div class="row"
           v-if="selectedDoorCategory">
        <div v-for="(value, doorType) in selectedDoorCategory.doorTypes"
             :key="doorType"
             :class="`${doorClasses} text-center door-miniature`">
          <DoorMiniature :base-url="appConfig?.baseUrl"
                         :door-category="selectedDoorCategory.categoryId"
                         :door-state="doorTypeState(doorType)"
                         :door-type="doorType"
                         @click="handleDoorTypeChange(doorType)"/>
          <div class="door-category-word-break text-uppercase">{{ doorType }}</div>
          <BadgePrice
              class="badge-full-width"
              :price="value.price"/>
        </div>
      </div>
      <div>{{ t('configurator.doorFramePrice', {price: formatPrice(99)}) }}</div>
    </div>

    <div class="mb-2"
         v-if="Object.keys(availableMaterialsForDoorCategories(configuratorApiResponse?.materials.laminates)).length">
      <h4 class="text-uppercase">{{ t('configurator.selectLaminate') }}</h4>
      <div class="row">
        <div v-for="(value, key) in availableMaterialsForDoorCategories(configuratorApiResponse?.materials.laminates)"
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
    </div>

    <div class="mb-2"
         v-if="Object.keys(availableMaterialsForDoorCategories(configuratorApiResponse?.materials.veneers)).length">
      <h4 class="text-uppercase">{{ t('configurator.selectVeneers') }}</h4>
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
    </div>

    <div class="mb-2" v-if="!selectedDoorCategory?.hiddenConfiguratorCategories.includes('handles')">
      <h4 class="text-uppercase">{{ t('configurator.selectHandles') }}
        <a href="https://www.mp-kovania.sk" target="_blank">mp-kovania.sk</a>
      </h4>
      <div class="row">
        <div v-for="(value, key) in configuratorApiResponse?.handles"
             :key="key"
             :class="`${doorClasses} text-center door-miniature`">
          <ImageWithLoadingPlaceholder
              :alt="key"
              class="img-fluid w-100 ratio-1x1 border"
              :class="{
                'border-primary': selectedDoor?.handle === key,
                'border-5': selectedDoor?.handle === key
              }"
              @click="handleHandleChange(key)"
              :src="appConfig?.baseUrl + '/images/handles/min/' + key + '.jpg'"
          />
          <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
        </div>
      </div>
    </div>

    <div class="mb-2">
      <h4 class="text-uppercase">{{ t('configurator.glasses') }}</h4>
      <div class="row" id="glass-gallery">
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
    </div>

    <div class="mb-2"
         v-show="selectedDoorCategory?.gallery && selectedDoorCategory?.gallery?.length">
      <h4 class="text-uppercase">{{ t('configurator.gallery') }}</h4>
      <div class="row" id="doors-gallery">
        <div v-for="(value, key) in selectedDoorCategory?.gallery"
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
    </div>

    <div>
      <h4 class="text-uppercase">{{ t('configurator.selectRoom') }}</h4>
      <div class="row">
        <div v-for="room in configuratorApiResponse.rooms" class="col-sm-6 gx-1 gy-1">
          <ImageWithLoadingPlaceholder class="img-fluid door-selector-img"
                                       alt="logo"
                                       :src="appConfig?.baseUrl + room.imgSrcMiniature"
                                       @click="emit('room-selected', room)"/>
        </div>
      </div>
    </div>
  </div>

  <button @click="handleAddDoorButtonClick"
          class="btn btn-primary btn-lg text-white btn-add-to-price-offer"
          type="button">
    {{ t('configurator.buttonAddToPriceOffer') }}
  </button>
</template>

<style>
.btn-add-to-price-offer {
  position: absolute;
  bottom: 25px;
  right: 25px;
}

.door-miniature:hover {
  filter: brightness(1.08);
  transform: scale(1.02);
  cursor: pointer;
}

.door-category-word-break {
  max-width: 120px;
  hyphens: auto;
  overflow-wrap: break-word;
}

.door-selector-img:hover {
  filter: brightness(1.08);
  transform: scale(1.02);
  cursor: pointer;
}
</style>