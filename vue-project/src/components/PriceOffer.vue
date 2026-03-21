<script setup lang="ts">
import {computed, onBeforeUnmount, onMounted, Ref, ref, toRaw, watch, WatchHandle} from 'vue'
import {ApiResponse} from "@/model/api/res/price-offer/ApiResponse.js";
import {
  constructReactiveFormPriceOffer, DoorMeta,
  findTechnicalSurchargeById,
  findRosetteById,
  findAestheticAccessoryById,
  findSpecialSurchargeById, FormDoor,
  FormPriceOffer
} from "@/model/functions/price-offer-form-builder.js";
import {prepareRequest} from "@/model/functions/price-offer-api-request-builder.js";
import BadgePrice from "./BadgePrice.vue";
import AccordionItem from "./AccordionItem.vue";
import {formatPrice} from "@/model/functions/formatters.js";
import {getForm, submitForm, updateForm} from "../model/api/rest.js";
import {ValidationMessage} from "../model/validation-message.js";
import ValidationMessages from "./ValidationMessages.vue";
import Hint from "./Hint.vue";
import LineItems from "./LineItems.vue";
import {useAlerts} from "../composables/alert-composables.js";
import {useI18n} from "vue-i18n";
import Toasts from "./generic/Toasts.vue";
import {useRouter} from "vue-router";
import {HintInterface} from "../model/interface/HintInterface.js";
import SelectedDoorsLineItems from "./SelectedDoorsLineItems.vue";
import SelectedDoors from "./SelectedDoors.vue";
import {useAppState} from "../composables/app-state.js";
import Footer from "@/components/Footer.vue";
import {ConfiguratorResponse, Handle} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import ImageWithLoadingPlaceholder from "@/components/generic/ImageWithLoadingPlaceholder.vue";
import {sassFalse} from "sass";

const router = useRouter()
const {t} = useI18n();
const {addAlert} = useAlerts()
const {appConfig} = useAppState()

const props = defineProps<{
  configuratorApiResponse: ConfiguratorResponse
}>()

const apiResponse: Ref<ApiResponse | undefined> = ref()
const cachedForm: Ref<string> = ref('')
const formValidations: Ref<Record<string, ValidationMessage[]>> = ref({})
const reactiveForm: Ref<FormPriceOffer> = ref<FormPriceOffer>(constructReactiveFormPriceOffer(undefined))
const reCaptchaToken: Ref<string | undefined> = ref()

let formWatchHandle: WatchHandle | undefined = undefined

const submitButtonDisabled = computed(() => {
  const isReCaptchaEnabled = appConfig.value?.reCaptchaEnabled ?? false;
  const reCaptchaCond = isReCaptchaEnabled ? !reCaptchaToken : false;
  const validationsCond = Object.keys(formValidations.value).length > 0;

  return reCaptchaCond || validationsCond;
})

function getFieldValidations(id: string): ValidationMessage[] {
  return formValidations.value[id] ?? []
}

const fetchDataFromApi = async (): Promise<ApiResponse> => {
  const response = await getForm();
  const json = await response.json()
  if (response.ok)
    return json as ApiResponse;
  else
    throw new Error(json);
}

function findHandleByHandleId(handleId: string | null): Handle | undefined {
  return handleId ? props.configuratorApiResponse.handles[handleId] : undefined;
}

function handleHandleClick(handleId: string): void {
  if (reactiveForm.value.handle) {
    reactiveForm.value.handle.id = handleId;
  } else {
    reactiveForm.value.handle = {
      count: 1,
      id: handleId,
      isCountDirty: false
    }
  }
}

async function handleFormSaveAndReRender(form: FormPriceOffer): Promise<void> {
  const req = prepareRequest(form, apiResponse.value)

  if (req) {
    const response = await updateForm(req);
    if (response.ok) {
      formValidations.value = await response.json();
    } else {
      addAlert(t("alerts.FORM_SAVE_ERROR"));
      throw new Error(response.statusText);
    }

    return fetchDataAndReRenderForm();
  } else {
    return Promise.resolve()
  }
}

async function fetchDataAndReRenderForm(): Promise<void> {
  const json = await fetchDataFromApi()
  apiResponse.value = json;
  reactiveForm.value = constructReactiveFormPriceOffer(json);
  cachedForm.value = JSON.stringify(reactiveForm.value);
}

function renderReCaptcha(): void {
  if (window.grecaptcha && appConfig.value?.reCaptchaSiteKey) {
    window.grecaptcha.render('recaptcha', {
      sitekey: appConfig.value?.reCaptchaSiteKey,
      callback: (token: string) => {
        reCaptchaToken.value = token;
      },
      'expired-callback': () => {
        reCaptchaToken.value = undefined;
      }
    });
  } else {
    console.error('grecaptcha not loaded');
  }
}

function debounce<F extends (...args: any[]) => any>(func: F, wait: number) {
  let timeout: ReturnType<typeof setTimeout>;
  return (...args: Parameters<F>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}

const debouncedHandleFormSave = debounce((newForm: FormPriceOffer) => {
  if (cachedForm.value !== JSON.stringify(newForm)) {
    handleFormSaveAndReRender(newForm);
  }
}, 250);

onMounted(async () => {
  if (appConfig.value?.reCaptchaEnabled) {
    renderReCaptcha();
  }

  await fetchDataAndReRenderForm()

  formWatchHandle = watch(reactiveForm, (newForm) => {
    console.log('vueForm.value');
    console.log(reactiveForm.value);
    console.log('newVal');
    console.log(JSON.stringify(newForm));

    debouncedHandleFormSave(newForm);
  }, {deep: true});
})

onBeforeUnmount(async () => {
  formWatchHandle?.()
})

async function handleFormSubmit(e: Event): Promise<void> {
  e.preventDefault();
  await handleFormSaveAndReRender(reactiveForm.value);

  const validationsEmpty = Object.keys(formValidations.value).length === 0;
  if (validationsEmpty) {
    const recaptchaToken = (document.querySelector('.g-recaptcha-response') as HTMLTextAreaElement)?.value;
    const response = await submitForm(recaptchaToken);

    if (response.ok) {
      await handleFormSaveAndReRender(constructReactiveFormPriceOffer(undefined));
      sessionStorage.setItem('offerCompleted', 'true')
      await router.push('/price-offer-finished')
    } else {
      addAlert(t("alerts.MAIL_SENDING_ERROR"));
    }
  } else {
    addAlert(t("alerts.FORM_VALIDATIONS_ERROR"));
  }
}

function handleCustomHandleCountChange(e: Event) {
  const target = e.target as HTMLInputElement;
  reactiveForm.value.assemblyHandlesRosettesCount = Number(target.value);
}

function handleDoorDuplication(index: number) {
  // Duplicate UI state
  const duplicatedForm: FormDoor = structuredClone(toRaw(reactiveForm.value.doors[index]))
  reactiveForm.value.doors.push(duplicatedForm)

  // Duplicate API/reference data
  const duplicatedMeta: DoorMeta = structuredClone(toRaw(reactiveForm.value.doorsMeta[index]))
  reactiveForm.value.doorsMeta.push(duplicatedMeta)

  addAlert(t('configurator.doorAddedToPriceOffer'))
}

function handleDoorRemoval(index: number) {
  reactiveForm.value.doors.splice(index, 1)
  reactiveForm.value.doorsMeta.splice(index, 1)
}
</script>

<template>
  <div data-bs-theme="price-offer">
    <Toasts/>
    <div>
      <form novalidate>
        <div class="accordion mb-2">
          <AccordionItem id="doors"
                         :is-open-by-default="true"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.doors"
                         :title="t('accordionHeaders.doors')">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <SelectedDoors @door-duplicated="handleDoorDuplication"
                               @door-removed="handleDoorRemoval"
                               v-if="apiResponse?.priceOffer?.doors"
                               v-model:selected-doors="reactiveForm.doors"
                               :base-url="appConfig?.baseUrl"
                               :door-categories="props.configuratorApiResponse.doorCategories"
                               :selected-doors-response="apiResponse?.priceOffer?.doors"/>
                <SelectedDoorsLineItems v-model:line-items="reactiveForm.selectedDoorsLineItems"
                                        :selected-doors-line-items-response="apiResponse?.priceOffer?.selectedDoorsLineItems"/>
                <div class="row"
                     v-if="!Object.keys(reactiveForm.doors).length && !reactiveForm.selectedDoorsLineItems.length">
                  <div class="col-sm-12">{{ t('doors.notSelected') }}</div>
                </div>
              </li>
            </ul>
          </AccordionItem>
          <AccordionItem id="handles"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.handlesAndRosettes"
                         :title="t('accordionHeaders.handles')">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div v-if="reactiveForm.handle" class="row mb-4">
                  <div class="col-3 col-xl-2 text-center">
                    <ImageWithLoadingPlaceholder
                        :alt="reactiveForm.handle.id"
                        class="img-fluid w-100 ratio-1x1 border mb-1"
                        :src="appConfig?.baseUrl + findHandleByHandleId(reactiveForm.handle.id)?.imgSrc!"
                    />
                    <BadgePrice
                        class="w-100"
                        :price="findHandleByHandleId(reactiveForm.handle.id)?.price"/>
                    <span class="door-category-word-break text-uppercase">{{
                        findHandleByHandleId(reactiveForm.handle.id)?.label
                      }}</span>
                    <button type="button"
                            @click="reactiveForm.handle = null;"
                            class="btn btn-outline-secondary w-100">
                      <span class="fas fa-trash"></span>
                      {{ t("doors.remove") }}
                    </button>
                  </div>
                  <div class="col-2 col-xl-6"></div>
                  <div class="col-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             @input="reactiveForm.handle!.isCountDirty = true;"
                             v-model.number="reactiveForm.handle!.count"
                             :id="'handle-price'"
                             class="form-control"
                             min="0"/>
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-3 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="apiResponse?.priceOffer?.handle?.calculatedPrice ?? 0.0"/>
                  </div>
                </div>
                <div class="accordion">
                  <AccordionItem id="price-offer-handles"
                                 :is-open-by-default="false"
                                 :title="t('configurator.accordionHeaders.handles')">
                    <div class="row">
                      <div v-for="(value, key) in configuratorApiResponse?.handles"
                           :key="key"
                           :class="`col-3 col-lg-2 gx-1 gy-1 text-center door-miniature`">
                        <ImageWithLoadingPlaceholder
                            :alt="key"
                            class="img-fluid w-100 ratio-1x1 border mb-1"
                            :class="{
                            'border-primary': key === reactiveForm.handle?.id,
                            'border-5': key === reactiveForm.handle?.id
                          }"
                            @click="handleHandleClick(key)"
                            :src="appConfig?.baseUrl + value.imgSrc"
                        />
                        <BadgePrice
                            class="w-100"
                            :price="value.price"/>
                        <span class="door-category-word-break text-uppercase">{{ value.label }}</span>
                      </div>
                    </div>
                  </AccordionItem>
                </div>
              </li>
              <li class="list-group-item">
                <div class="row">
                  <p>
                  <span class="text-transform-none">{{ t('handlesAdditionalText') }}
                    <a href="https://www.kluckynadvere.sk/" target="_blank">www.kluckynadvere.sk</a>
                  </span>
                  </p>
                </div>
                <div class="row">
                  <div class="col-xm-12 col-xl-6">
                    <input type="text"
                           v-model.lazy="reactiveForm.customHandle.name"
                           :id="'handle-name'"
                           :placeholder="t('handles.namePlaceholder')"
                           class="form-control"/>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="reactiveForm.customHandle.price"
                             :id="'handle-price'"
                             class="form-control"
                             min="0"/>
                      <span class="input-group-text">€</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="reactiveForm.customHandle.count"
                             v-on:change="handleCustomHandleCountChange($event)"
                             :id="'handle-count'"
                             class="form-control"
                             min="0"
                             step="1"/>
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="apiResponse?.priceOffer?.customHandle?.calculatedPrice ?? 0.0"/>
                  </div>
                </div>
              </li>
              <template v-for="r of reactiveForm.rosettes" :key="r.id">
                <li class="list-group-item">
                  <div class="row">
                    <div class="col-sm-12 col-xl-8 align-self-center">{{ findRosetteById(r.id, apiResponse)?.label }}
                      <span>{{ formatPrice(findRosetteById(r.id, apiResponse)?.price) }}/ks </span>
                      <Hint
                          v-if="findRosetteById(r.id, apiResponse)"
                          :hintObj="findRosetteById(r.id, apiResponse) as HintInterface"/>
                    </div>
                    <div class="col-sm-4 d-xl-none"></div>
                    <div class="col-sm-4 col-xl-2">
                      <div class="input-group">
                        <input type="number"
                               v-model.number="r.count"
                               :id="'rosette-count-' + r.id"
                               class="form-control"
                               min="0"
                               step="1">
                        <span class="input-group-text">ks</span>
                      </div>
                    </div>
                    <div class="col-sm-4 col-xl-2">
                      <BadgePrice
                          class="price-offer-badge w-100"
                          :price="findRosetteById(r.id, apiResponse)?.calculatedPrice"/>
                    </div>
                  </div>
                </li>
              </template>
              <LineItems
                  v-model:line-items="reactiveForm.rosettesLineItems"
                  :line-items-response="apiResponse?.priceOffer.rosettesLineItems"
              />
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-8 align-self-center">{{ t('handles.assemblyHandlesRosettes') }}</div>
                  <div class="col-sm-4 d-xl-none"></div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="reactiveForm.assemblyHandlesRosettesCount"
                             @input="reactiveForm.isAssemblyHandlesRosettesCountDirty = true"
                             id="assemblyHandlesRosettesCount"
                             class="form-control"
                             min="0"
                             step="1">
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="apiResponse?.priceOffer.assemblyPriceHandlesRosettesCalculatedPrice"/>
                  </div>
                </div>
              </li>
            </ul>
          </AccordionItem>
          <AccordionItem id="delivery"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.delivery"
                         :title="t('accordionHeaders.delivery')">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div class="row mb-2">
                  <div class="col-sm-12 col-xl-6">{{ t("delivery.district") }}</div>
                  <div class="col-sm-8 col-xl-4">
                    <select class="form-select" v-model="reactiveForm.address.district">
                      <option value="">Vyberte okres</option>
                      <option v-for="d in apiResponse?.districts"
                              :key="d.id"
                              :value="d.id">{{ d.label }}
                      </option>
                    </select>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="apiResponse?.priceOffer.deliveryPrice"/>
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-sm-12 col-xl-12">{{ t("delivery.note") }}</div>
                </div>
              </li>
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-12"><h5>{{ t("delivery.address") }}</h5></div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="address-street"
                             type="text"
                             class="form-control"
                             placeholder=" "
                             v-model.lazy="reactiveForm.address.street"/>
                      <label for="address-street">{{ t("delivery.street") }}</label>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="address-streetNumber"
                             type="text"
                             class="form-control"
                             placeholder=" "
                             v-model.lazy="reactiveForm.address.streetNumber"/>
                      <label for="address-streetNumber">{{ t("delivery.streetNumber") }}</label>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="address-city"
                             type="text"
                             class="form-control"
                             placeholder=" "
                             v-model.lazy="reactiveForm.address.city"/>
                      <label for="address-city">{{ t("delivery.city") }}</label>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="address-zipCode"
                             type="text"
                             class="form-control"
                             placeholder=" "
                             v-model.lazy="reactiveForm.address.zipCode"/>
                      <label for="address-zipCode">{{ t("delivery.zipCode") }}</label>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </AccordionItem>
          <AccordionItem id="assemblyDoors"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.assemblyDoors"
                         :title="t('accordionHeaders.assemblyDoors')">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-8 text-align-justify">{{ t("assemblyDoors.note") }}</div>
                  <div class="col-sm-4 d-xl-none"></div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="reactiveForm.assemblyDoorsCount"
                             @input="reactiveForm.isAssemblyDoorsCountDirty = true"
                             id="assemblyDoorsCount"
                             class="form-control"
                             min="0"
                             step="1">
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="apiResponse?.priceOffer.assemblyDoorsCalculatedPrice"/>
                  </div>
                </div>
              </li>
            </ul>
          </AccordionItem>
          <AccordionItem id="aestheticAccessories"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.aestheticAccessories"
                         :title="t('accordionHeaders.aestheticAccessories')">
            <ul class="list-group list-group-flush">
              <li v-for="it of reactiveForm.aestheticAccessories"
                  :key="it.id"
                  class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-6 align-self-center">{{
                      findAestheticAccessoryById(it.id, apiResponse)?.label
                    }}
                    <span v-if="findAestheticAccessoryById(it.id, apiResponse)?.configuredPrice">{{
                        formatPrice(findAestheticAccessoryById(it.id, apiResponse)?.configuredPrice)
                      }}/ks </span>
                    <Hint
                        v-if="findAestheticAccessoryById(it.id, apiResponse)"
                        :hintObj="findAestheticAccessoryById(it.id, apiResponse) as HintInterface"/>
                  </div>
                  <div class="col-sm-4 col-xl-2"
                       :class="{ invisible: findAestheticAccessoryById(it.id, apiResponse)?.configuredPrice !== null }">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="it.selectedPrice"
                             :id="'special-accessory-price-' + it.id"
                             class="form-control"
                             min="0"
                             step="1"/>
                      <span class="input-group-text">€</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="it.count"
                             :id="'special-accessory-count-' + it.id"
                             class="form-control"
                             min="0"
                             step="1">
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="findAestheticAccessoryById(it.id, apiResponse)?.calculatedPrice"/>
                  </div>
                </div>
              </li>
              <LineItems
                  v-model:line-items="reactiveForm.aestheticAccessoriesLineItems"
                  :line-items-response="apiResponse?.priceOffer.aestheticAccessoriesLineItems"
              />
            </ul>
          </AccordionItem>
          <AccordionItem id="technicalSurcharges"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.technicalSurcharges"
                         :title="t('accordionHeaders.technicalSurcharges')">
            <ul class="list-group list-group-flush">
              <li v-for="it of reactiveForm.technicalSurcharges"
                  :key="it.id"
                  class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-8 align-self-center text-align-justify">
                    {{ findTechnicalSurchargeById(it.id, apiResponse)?.label }}
                    <Hint
                        v-if="findTechnicalSurchargeById(it.id, apiResponse)"
                        :hintObj="findTechnicalSurchargeById(it.id, apiResponse) as HintInterface"/>
                  </div>
                  <div class="col-sm-4 d-xl-none"></div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="it.count"
                             @input="it.isCountDirty = true"
                             :id="'possible-additional-charge-count-' + it.id"
                             class="form-control"
                             min="0"
                             step="1">
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="findTechnicalSurchargeById(it.id, apiResponse)?.calculatedPrice"/>
                  </div>
                </div>
              </li>
              <LineItems
                  v-model:line-items="reactiveForm.technicalSurchargesLineItems"
                  :line-items-response="apiResponse?.priceOffer.technicalSurchargesLineItems"
              />
            </ul>
          </AccordionItem>
          <AccordionItem id="specialSurcharges"
                         :is-open-by-default="false"
                         :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.specialSurcharges"
                         :title="t('accordionHeaders.specialSurcharges')">
            <ul class="list-group list-group-flush">
              <li v-for="it of reactiveForm.specialSurcharges"
                  :key="it.id"
                  class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-8 align-self-center">{{
                      findSpecialSurchargeById(it.id, apiResponse)?.label
                    }}
                    <Hint v-if="findSpecialSurchargeById(it.id, apiResponse)"
                          :hintObj="findSpecialSurchargeById(it.id, apiResponse) as HintInterface"/>
                  </div>
                  <div class="col-sm-4 d-xl-none"/>
                  <div class="col-sm-4 col-xl-2">
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="input-group">
                          <input type="number"
                                 v-model.number="it.count"
                                 :id="'special-surcharges-count-' + it.id"
                                 class="form-control"
                                 min="0"
                                 step="1">
                          <span class="input-group-text">ks</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="price-offer-badge w-100"
                        :price="findSpecialSurchargeById(it.id, apiResponse)?.calculatedPrice"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8 col-xl-8 align-self-center">{{
                      findSpecialSurchargeById(it.id, apiResponse)?.labelAssembly
                    }}
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="form-check">
                      <input class="form-check-input"
                             type="checkbox"
                             :disabled="it.count === 0"
                             @input="it.isAssemblySelectedDirty = true"
                             v-model="it.isAssemblySelected"/>
                      <label class="form-check-label" for="checkDefault">Montáž</label>
                    </div>
                  </div>
                  <div class="d-sm-none col-xl-2"></div>
                </div>
              </li>
              <LineItems
                  v-model:line-items="reactiveForm.specialSurchargesLineItems"
                  :line-items-response="apiResponse?.priceOffer.specialSurchargesLineItems"
              />
            </ul>
          </AccordionItem>
          <AccordionItem id="contact"
                         :is-open-by-default="true"
                         :title="t('accordionHeaders.contact')">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="contact-fullName"
                             v-model.lazy="reactiveForm.contact.fullName"
                             type="text"
                             class="form-control"
                             placeholder=" "/>
                      <label for="contact-fullName">{{ t("contact.fullName") }}</label>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="contact-email"
                             v-model.lazy="reactiveForm.contact.email"
                             type="email"
                             class="form-control"
                             :class="{ 'is-invalid': getFieldValidations('contact-email').length }"
                             placeholder=" "
                             required/>
                      <label for="contact-email">{{ t("contact.email") }}</label>
                      <div class="invalid-feedback">
                        <ValidationMessages :validations="getFieldValidations('contact-email')"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
                      <input id="contact-phoneNumber"
                             v-model.lazy="reactiveForm.contact.phoneNumber"
                             type="text"
                             class="form-control"
                             placeholder=" "/>
                      <label for="contact-phoneNumber">{{ t("contact.phoneNumber") }}</label>
                    </div>
                  </div>
                  <div class="col-sm-6 col-xl-6">
                    <div class="form-floating">
          <textarea id="price-offer-note"
                    v-model="reactiveForm.note"
                    class="form-control"
                    placeholder=" "/>
                      <label for="price-offer-note">{{ t("contact.note") }}</label>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </AccordionItem>
        </div>
        <div v-if="apiResponse" class="price-sticky py-1 px-3 rounded mb-2">
          <div class="row mb-2">
            <div class="col-sm-8 col-xl-10 align-content-center">
              CENA SPOLU BEZ DPH
            </div>
            <div class="col-sm-4 col-xl-2">
              <BadgePrice
                  class="price-offer-badge w-100"
                  :price="apiResponse.priceOffer.calculatedPrice"/>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-8 col-xl-10 align-content-center">
              CENA SPOLU S DPH
            </div>
            <div class="col-sm-4 col-xl-2">
              <BadgePrice
                  class="price-offer-badge w-100"
                  :price="apiResponse.priceOffer.calculatedPriceVat"/>
            </div>
          </div>
        </div>
        <div>
          <div id="recaptcha"
               class="mb-1"></div>
          <button type="submit"
                  class="btn btn-lg bg-primary text-white bold"
                  :disabled="submitButtonDisabled"
                  v-on:click="handleFormSubmit($event)">{{ t('submitButton') }}
          </button>
        </div>
      </form>
    </div>
    <Footer/>
  </div>
</template>

<style lang="scss">
.color-dark-red {
  color: darkred;
}

.input-group-text {
  min-width: 45px;
  text-align: center;
}

.price-offer-badge {
  height: $input-height;
}

.text-transform-none {
  text-transform: none;
}

.text-align-justify {
  text-align: justify;
}

.list-group-item:nth-child(odd) {
  background-color: #fff;
}

.list-group-item:nth-child(even) {
  background-color: var(--bs-primary-bg-subtle);
}

.price-sticky {
  background-color: white;
  padding-top: 0.5rem;
  position: static;
  z-index: 10;
}

@media (min-width: 1200px) {
  .price-sticky {
    background-color: white;
    bottom: 0;
    position: sticky;
  }
}
</style>