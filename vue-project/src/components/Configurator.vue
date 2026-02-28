<script setup lang="ts">

import {computed, nextTick, onBeforeMount, onMounted, ref, Ref, watch} from "vue";
import {Room} from "@/model/api/res/configurator/Room.js";
import {postAddDoor} from "../model/api/rest.js";
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
import {usePhotoSwipeGallery} from "@/model/functions/photoswipe-utils.js";
import {formatPrice} from "@/model/functions/formatters.js";
import BadgePrice from "@/components/BadgePrice.vue";
import Footer from "@/components/Footer.vue";

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

const doorTypesSelectionSection = ref<HTMLElement | null>(null);
const doorsGallery = ref<HTMLElement | null>(null)
const glassGallery = ref<HTMLElement | null>(null)
const selectedDoor: Ref<SelectedDoor | null> = ref(null)
const selectedDoorCategory: Ref<DoorCategory | null> = ref(null)

const doorClasses = "col-3 col-lg-2 gx-1 gy-1";

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

onMounted(() => {
  watch(props.configuratorApiResponse.glasses, () => {
    if (glassGallery.value) {
      usePhotoSwipeGallery(glassGallery.value);
    }
  }, {immediate: true})

  watch(selectedDoorCategory, () => {
    if (doorsGallery.value) {
      usePhotoSwipeGallery(doorsGallery.value);
    }
  }, {immediate: true})
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
                         @click="handleDoorTypeChange(doorType)"
                         class="mb-1"/>
          <BadgePrice
              class="w-100"
              :price="value.price"/>
          <div class="door-category-word-break text-uppercase">{{ doorType }}</div>
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
              class="img-fluid w-100 ratio-1x1 border mb-1"
              :class="{
                'border-primary': selectedDoor?.handle === key,
                'border-5': selectedDoor?.handle === key
              }"
              @click="handleHandleChange(key)"
              :src="appConfig?.baseUrl + '/images/handles/min/' + key + '.jpg'"
          />
          <BadgePrice
              class="w-100"
              :price="value.price"/>
          <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
        </div>
      </div>
    </div>

    <div class="mb-2">
      <h4 class="text-uppercase">{{ t('configurator.glasses') }}</h4>
      <div class="row" ref="glassGallery">
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
      <div class="row" ref="doorsGallery">
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

    <div class="row mt-3">
      <h4 class="text-uppercase">{{ t('configurator.currentActions') }}</h4>
      <div class="col-12 col-lg-4">
        <ImageWithLoadingPlaceholder
            alt="interierove-dvere-cena.jpg"
            class="img-fluid w-100 ratio-1x1"
            :src="appConfig?.baseUrl + '/images/interierove-dvere-cena.jpg'"/>
        <div class="p-3">
          <div class="text-uppercase text-center mb-3">Interiérové <b>dvere so zárubňou</b> od <b>219,- €</b></div>
          <div class="text-align-justify">Interiérové dvere a zárubne Vám vo všetkých druhoch laminátov ponúkame za
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
          <div class="text-align-justify">Trápia Vás atypické miery otvorov? Pre nás nie sú žiadnym problémom. Každý
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
          <div class="text-uppercase text-center mb-3"><b>Montáž</b> inte&shy;rié&shy;ro&shy;vých dverí a zárubne <b>35,-
            €</b>
          </div>
          <div class="text-align-justify">Montáž kompletu interiérové dvere so zárubňou Vám ponúkame za zvýhodnenú cenu
            od
            35€/ks. Táto akciová cena platí pri montáži šiestich a viac kusov dverí a zárubní.
          </div>
        </div>
      </div>
    </div>
  </div>
  <Footer/>
  <button @click="handleAddDoorButtonClick"
          class="btn btn-primary btn-lg text-white btn-add-to-price-offer"
          type="button">
    {{ t('configurator.buttonAddToPriceOffer') }}
  </button>
</template>

<style lang="scss">
.btn-add-to-price-offer {
  position: absolute;
  bottom: 25px;
  right: 25px;
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