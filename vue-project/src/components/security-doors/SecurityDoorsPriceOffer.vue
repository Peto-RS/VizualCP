<script setup lang="ts">
import {computed, nextTick, onMounted, Ref, ref, toRaw, watch} from 'vue'
import {PriceOfferViewResponse} from "@/model/security-doors/price-offer/res/PriceOfferViewResponse.js";
import {
  constructReactiveFormPriceOffer,
  DoorMeta,
  FormDoor,
  SecurityDoorsFormPriceOffer
} from "@/model/security-doors/function/security-doors-price-offer-form-builder.js";
import {prepareRequest} from "@/model/security-doors/function/security-doors-price-offer-api-request-builder.js";
import BadgePrice from "../BadgePrice.vue";
import AccordionItem from "../AccordionItem.vue";
import {formatPrice} from "@/model/common/functions/formatters.js";
import {
  deleteFormSecurityDoors,
  getFormSecurityDoors,
  submitFormSecurityDoors,
  updateFormSecurityDoors
} from "@/model/security-doors/rest.js";
import {ValidationMessage} from "@/model/common/validation-message.js";
import ValidationMessages from "../ValidationMessages.vue";
import {useAlerts} from "@/composables/alert-composables.js";
import {useI18n} from "vue-i18n";
import Toasts from "../generic/Toasts.vue";
import {useRouter} from "vue-router";
import {useAppState} from "@/composables/app-state.js";
import Footer from "@/components/Footer.vue";
import {SecurityDoorsConfiguratorResponse} from "@/model/security-doors/configurator/res/SecurityDoorsConfiguratorResponse.js";
import {offcanvas} from "@/composables/offcanvas.js";
import NavTabs from "@/components/NavTabs.vue";
import {CURRENCY_SIGN} from "@/model/common/enums/CurrencyEnum.js";
import {DistrictResponse} from "@/model/interior-doors/price-offer/res/DistrictResponse.js";
import {useRecaptcha} from "@/composables/use-recaptcha.js";
import SelectedSecurityDoors from "@/components/security-doors/SelectedSecurityDoors.vue";

const router = useRouter()
const {locale, t} = useI18n();
const {addAlert} = useAlerts()
const {appConfig, currency} = useAppState()

const emit = defineEmits<{
  (e: 'configurator-clicked'): void,
  (e: 'price-offer-clicked'): void
}>()

const props = defineProps<{
  configuratorApiResponse: SecurityDoorsConfiguratorResponse
}>()

const apiResponse: Ref<PriceOfferViewResponse | undefined> = ref()
const cachedForm: Ref<string> = ref('')
const formValidations: Ref<Record<string, ValidationMessage[]>> = ref({})
const reactiveForm: Ref<SecurityDoorsFormPriceOffer> = ref<SecurityDoorsFormPriceOffer>(constructReactiveFormPriceOffer(undefined))
const recaptchaContainer: Ref<HTMLDivElement | null> = ref(null);
const {
  render,
  token: reCaptchaToken
} = useRecaptcha();

const submitButtonDisabled = computed(() => {
  const isReCaptchaEnabled = appConfig.value!.reCaptchaEnabled ?? false;
  const reCaptchaCond = isReCaptchaEnabled ? !reCaptchaToken : false;
  const validationsCond = Object.keys(formValidations.value).length > 0;

  return reCaptchaCond || validationsCond;
})

const currencySign = computed(() => CURRENCY_SIGN[currency.value!])

function getFieldValidations(id: string): ValidationMessage[] {
  return formValidations.value[id] ?? []
}

const fetchDataFromApi = async (): Promise<PriceOfferViewResponse> => {
  const response = await getFormSecurityDoors(currency.value!, locale.value);
  const json = await response.json()
  if (response.ok)
    return json as PriceOfferViewResponse;
  else
    throw new Error(json);
}

// function findHandleByHandleId(handleId: string | null): Handle | undefined {
//   return handleId ? props.configuratorApiResponse.handles[handleId] : undefined;
// }

// function handleHandleClick(handleId: string): void {
//   if (reactiveForm.value.handle) {
//     reactiveForm.value.handle.id = handleId;
//   } else {
//     reactiveForm.value.handle = {
//       count: 1,
//       id: handleId,
//       isCountDirty: false
//     }
//   }
// }

async function handleFormSaveAndReRender(form: SecurityDoorsFormPriceOffer): Promise<void> {
  const req = prepareRequest(form, apiResponse.value, currency.value)

  if (req) {
    const response = await updateFormSecurityDoors(req, locale.value);
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

function debounce<F extends (...args: any[]) => any>(func: F, wait: number) {
  let timeout: ReturnType<typeof setTimeout>;
  return (...args: Parameters<F>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}

const debouncedHandleFormSave = debounce((newForm: SecurityDoorsFormPriceOffer) => {
  if (cachedForm.value !== JSON.stringify(newForm)) {
    handleFormSaveAndReRender(newForm);
  }
}, 250);

async function handleFormSubmit(e: Event): Promise<void> {
  e.preventDefault();
  await handleFormSaveAndReRender(reactiveForm.value);

  const validationsEmpty = Object.keys(formValidations.value).length === 0;
  if (validationsEmpty) {
    const recaptchaToken = (document.querySelector('.g-recaptcha-response') as HTMLTextAreaElement)?.value;
    const response = await submitFormSecurityDoors(locale.value, recaptchaToken);

    if (response.ok) {
      await deleteFormSecurityDoors()
      sessionStorage.setItem('offerCompleted', 'security-doors')
      await router.push({name: 'PriceOfferFinished'})
    } else {
      addAlert(t("alerts.MAIL_SENDING_ERROR"));
    }
  } else {
    addAlert(t("alerts.FORM_VALIDATIONS_ERROR"));
  }
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

function getDistrictLabel(d: DistrictResponse): string | null {
  return d.translationKey ? t(d.translationKey) : d.label
}

onMounted(async () => {
  if (appConfig.value!.reCaptchaEnabled && appConfig.value!.reCaptchaSiteKey) {
    await render(
        recaptchaContainer.value!,
        appConfig.value!.reCaptchaSiteKey,
        locale.value
    );
  }

  await fetchDataAndReRenderForm()

  watch(reactiveForm, (newForm) => {
    console.log('vueForm.value');
    console.log(reactiveForm.value);
    console.log('newVal');
    console.log(JSON.stringify(newForm));

    debouncedHandleFormSave(newForm);
  }, {deep: true});

  watch(currency, () => {
    handleFormSaveAndReRender(reactiveForm.value);
  });

  watch(locale, async () => {
    await handleFormSaveAndReRender(reactiveForm.value);

    if (appConfig.value!.reCaptchaEnabled && appConfig.value!.reCaptchaSiteKey) {
      await nextTick();
      await render(
          recaptchaContainer.value!,
          appConfig.value!.reCaptchaSiteKey,
          locale.value
      );
    }
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
                  <SelectedSecurityDoors @door-duplicated="handleDoorDuplication"
                                 @door-removed="handleDoorRemoval"
                                 v-if="apiResponse?.priceOffer?.doors"
                                 v-model:selected-doors="reactiveForm.doors"
                                 :base-url="appConfig!.baseUrl"
                                 :door-categories="props.configuratorApiResponse.doorCategories"
                                 default-door-category="security-doors"
                                 :show-door-frame-option="false"
                                 :selected-doors-response="apiResponse?.priceOffer?.doors"/>
                  <div class="row"
                       v-if="!Object.keys(reactiveForm.doors).length">
                    <div class="col-sm-12">{{ t('doors.notSelected') }}</div>
                  </div>
                </li>
              </ul>
            </AccordionItem>
<!--            <AccordionItem id="handles"-->
<!--                           :is-open-by-default="false"-->
<!--                           :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.handlesAndRosettes"-->
<!--                           :title="t('accordionHeaders.handles')">-->
<!--              <ul class="list-group list-group-flush">-->
<!--                <li class="list-group-item">-->
<!--                  <div v-if="reactiveForm.handle" class="row mb-4">-->
<!--                    <div class="col-3 col-xl-2 text-center">-->
<!--                      <ImageWithLoadingPlaceholder-->
<!--                          :alt="reactiveForm.handle.id"-->
<!--                          class="img-fluid w-100 ratio-1x1 border mb-1"-->
<!--                          :src="appConfig!.baseUrl + findHandleByHandleId(reactiveForm.handle.id)?.imgSrc!"-->
<!--                      />-->
<!--                      <BadgePrice-->
<!--                          class="w-100"-->
<!--                          :price="findHandleByHandleId(reactiveForm.handle.id)?.price"/>-->
<!--                      <span class="door-category-word-break text-uppercase">{{-->
<!--                          safeT(findHandleByHandleId(reactiveForm.handle.id)?.labelKey)-->
<!--                        }}</span>-->
<!--                      <button type="button"-->
<!--                              @click="reactiveForm.handle = null;"-->
<!--                              class="btn btn-outline-secondary w-100">-->
<!--                        <span class="fas fa-trash"></span>-->
<!--                        {{ t("doors.remove") }}-->
<!--                      </button>-->
<!--                    </div>-->
<!--                    <div class="col-2 col-xl-6"></div>-->
<!--                    <div class="col-4 col-xl-2">-->
<!--                      <div class="input-group">-->
<!--                        <input type="number"-->
<!--                               @input="reactiveForm.handle!.isCountDirty = true;"-->
<!--                               v-model.number="reactiveForm.handle!.count"-->
<!--                               :id="'handle-price'"-->
<!--                               class="form-control"-->
<!--                               min="0"/>-->
<!--                        <span class="input-group-text">ks</span>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                    <div class="col-3 col-xl-2">-->
<!--                      <BadgePrice-->
<!--                          class="price-offer-badge w-100"-->
<!--                          :price="apiResponse?.priceOffer?.handle?.calculatedPrice ?? 0.0"/>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                  <div class="accordion">-->
<!--                    <AccordionItem id="price-offer-handles"-->
<!--                                   :is-open-by-default="false"-->
<!--                                   :title="t('configurator.accordionHeaders.handles')">-->
<!--                      <div class="row">-->
<!--                        <div v-for="(value, key) in configuratorApiResponse?.handles"-->
<!--                             :key="key"-->
<!--                             :class="`col-3 col-lg-2 gx-1 gy-1 text-center door-miniature`">-->
<!--                          <ImageWithLoadingPlaceholder-->
<!--                              :alt="key"-->
<!--                              class="img-fluid w-100 ratio-1x1 border mb-1"-->
<!--                              :class="{-->
<!--                            'border-primary': key === reactiveForm.handle?.id,-->
<!--                            'border-5': key === reactiveForm.handle?.id-->
<!--                          }"-->
<!--                              @click="handleHandleClick(key)"-->
<!--                              :src="appConfig!.baseUrl + value.imgSrc"-->
<!--                          />-->
<!--                          <BadgePrice-->
<!--                              class="w-100"-->
<!--                              :price="value.price"/>-->
<!--                          <span class="door-category-word-break text-uppercase">{{ value.label }} ss</span>-->
<!--                        </div>-->
<!--                      </div>-->
<!--                    </AccordionItem>-->
<!--                  </div>-->
<!--                </li>-->

<!--              </ul>-->
<!--            </AccordionItem>-->
            <AccordionItem id="delivery"
                           :is-open-by-default="false"
                           :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.delivery"
                           :title="t('accordionHeaders.delivery')">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <div class="row mb-2">
                    <div class="col-sm-12 col-xl-6">{{ t("delivery.chooseDeliveryCountry") }}</div>
                    <div class="col-sm-8 col-xl-4">
                      <select class="form-select"
                              v-model="reactiveForm.address.deliveryCountry"
                              @input="reactiveForm.address.isDeliveryCountryDirty = true">
                        <option value="">{{ t("delivery.chooseDeliveryCountry") }}</option>
                        <option v-for="c in apiResponse?.deliveryCountries"
                                :key="c"
                                :value="c">{{ t("db.deliveryCountries." + c) }}
                        </option>
                      </select>
                    </div>
                    <div class="col-sm-4 col-xl-2"/>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-12 col-xl-6">{{ t("delivery.chooseDistrict") }}</div>
                    <div class="col-sm-8 col-xl-4">
                      <select class="form-select" v-model="reactiveForm.address.district">
                        <option value="">{{ t("delivery.chooseDistrict") }}</option>
                        <option v-for="d in apiResponse?.districts"
                                :key="d.id"
                                :value="d.id">
                          {{ getDistrictLabel(d) }}
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
                    <div class="col-sm-12 col-xl-12">{{
                        t("delivery.note", {price: formatPrice(apiResponse?.priceOffer?.deliveryPricePerKm, currencySign)})
                      }}
                    </div>
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
                    <div class="col-sm-12 col-xl-8 text-align-justify">{{
                        t('securityDoors.priceOffer.assemblyDoors.note', {
                          assemblyDoorsCost: formatPrice(apiResponse?.priceOffer?.assemblyDoorsCost, currencySign) || ''
                        })
                      }}
                    </div>
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
              <div class="col-sm-8 col-xl-10 align-content-center text-uppercase">
                {{ t('priceOffer.priceWoVat') }}
              </div>
              <div class="col-sm-4 col-xl-2">
                <BadgePrice
                    class="price-offer-badge w-100"
                    :price="apiResponse.priceOffer.calculatedPrice"/>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-8 col-xl-10 align-content-center text-uppercase">
                {{ t('priceOffer.priceWithVat') }}
              </div>
              <div class="col-sm-4 col-xl-2">
                <BadgePrice
                    class="price-offer-badge w-100"
                    :price="apiResponse.priceOffer.calculatedPriceVat"/>
              </div>
            </div>
          </div>
          <div>
            <div
                :key="locale"
                ref="recaptchaContainer"
                class="mb-1">
            </div>
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
  </div>
</template>

<style lang="scss">
.campaign-detail {
  background-color: var(--bs-primary);
}

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