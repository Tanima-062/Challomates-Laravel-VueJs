<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <Back :target="route('sales-partner.index')" :backPrevUrl="true" :show-modal="form.isDirty" />
        <h5 class="page-title">Vertriebspartner bearbeiten</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="create">
                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="prefix_id">Erstellungsdatum*</label>
                        <p class="value">
                            {{ dayjs(props.sales_partner.created_at).format("DD.MM.YYYY").toString() }}
                        </p>
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label for="prefix_id">Vertriebspartner-ID*</label>
                        <p class="value">{{ props.sales_partner.prefix_id }}</p>
                    </div>
                    <div class="input-wrapper w-1/3 flex">
                        <div class="flex gap-4">
                            <label for="profile-picture"
                                class="image-preview w-[64px] h-[64px] bg-tints-5 rounded-full flex items-center justify-center overflow-hidden">
                                <Upload v-if="!form.profile_picture" />
                                <img v-if="profile_picture" :src="profile_picture" alt="profile_picture" />
                                <input type="file" name="profile_picture" id="profile-picture" class="hidden"
                                    accept=".png, .jpg" ref="profile_picture_input" @change="
                                      (e) =>
                                        e.target.files[0] ? (profile_picture = e.target.files[0]) : null
                                    " />
                            </label>
                            <div class="flex justify-center flex-col">
                                <label for="profile-picture">Profilbild</label>
                                <p v-if="!profile_picture" class="value !mt-2">
                                    Bild hochladen .jpg oder .png Format
                                </p>
                                <div class="flex gap-[37px] mt-[11px] items-center" v-if="profile_picture">
                                    <label class="value flex gap-3 !text-gray-3 !font-normal font-ropa text-16"
                                        for="profile-picture">
                                        <Upload fill="#787878" /> Bild hochladen
                                    </label>
                                    <button class="value flex text-gray-3 text-16 font-normal font-ropa gap-[13px]"
                                        @click.prevent="clearProfilePicture">
                                        <Cross color="#787878" width="14px" height="14px" /> Entfernen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput maxlength="41" placeholder="Firmenname eingeben" label="Firmenname*"
                            v-model="form.company_name"
                            :error="form.errors.company_name || v$.company_name.$errors[0]?.$message"
                            :show_error="v$.company_name.$errors[0]?.$message ? true : false" @clearError="
                              () => {
                                form.clearErrors('company_name');
                                v$.company_name.$touch();
                              }
                            " />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label for="receipt_template" class="form-label mb-2 block">Belegvorlage*</label>
                        <label for="receipt_template"
                            class="file-upload w-full h-[40px] bg-white border-[1px] border-gray-corner rounded-[48px] flex px-6 py-[11px] gap-[12px]"
                            :class="{ '!border-error': form.errors.receipt_template }">
                            <Upload fill="#135F84" />
                            <p class="value !m-[0px]" v-if="!receipt_template_name">
                                Belevorlage hochladen .jpg oder .png Format
                            </p>
                            <div class="flex w-full justify-between" v-if="receipt_template_name">
                                <p class="value !text-16 !m-0 text-tints-5 whitespace-nowrap overflow-hidden text-ellipsis max-w-[70%]">
                                    {{ receipt_template_name }}
                                </p>
                                <p class="value !m-0">Vorlage ersetzen</p>
                            </div>
                            <input class="hidden" accept=".jpg, .png" type="file" name="receipt_template"
                                id="receipt_template" ref="receipt_template_input" @change="
                                  (e) => {
                                    if (e.target.files[0]) {
                                      form.receipt_template = e.target.files[0];
                                      form.receipt_template_name = e.target.files[0].name;
                                      form.clearErrors('receipt_template');
                                    }
                                  }
                                " />
                        </label>
                    </div>

                    <div class="w-1/3"></div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="branch" class="mb-2 block">Branche*</label>
                        <SelectOption name="branch" v-model="form.branch_id" :options="branch" value_name="id"
                            label_name="name" placeholder="Branche auswählen" class="h-[40px]"
                            :error="form.errors.branch_id" @change="form.clearErrors('branch_id')" />
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label for="branch" class="mb-2 block">Branchenkategorie*</label>
                        <SelectOption name="branch_categories" v-model="form.branch_category_id"
                            :options="branch_categories" value_name="id" label_name="name"
                            placeholder="Branchenkategorie auswählen" :class="[
                              'h-[40px]',
                              !branch_categories.length ? 'pointer-events-none' : '',
                            ]" :error="form.errors.branch_category_id"
                            @change="form.clearErrors('branch_category_id')" />
                    </div>

                    <div class="w-1/3"></div>
                </div>
                <div class="!mb-[56px] form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="Webseitenlink eingeben" label="Webseite" v-model="form.website" />
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label for="company_consultant" class="mb-2 block">Company Consultant*</label>
                        <SearchSelect :options="company_consultants" placeholder="Company Consultant auswählen"
                            class="h-[40px]" labelKey="name" valueKey="id"
                            :searchable="['prefix_id', 'first_name', 'last_name', 'name']" v-model="form.consultant_id"
                            :error="form.errors.consultant_id" @change="form.clearErrors('consultant_id')" />
                    </div>

                    <div class="w-1/3"></div>
                </div>

                <h2 class="w-full text-primary-3 mb-10 text-18x">Adresse</h2>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="Strasse eingeben" label="Strasse*" v-model="form.street"
                            :error="form.errors.street || v$.street.$errors[0]?.$message"
                            :show_error="v$.street.$errors[0]?.$message ? true : false" @clearError="
                              () => {
                                form.clearErrors('street');
                                v$.street.$touch();
                              }
                            " maxLength="31" />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="Hausnummer eingeben" label="Hausnummer*" v-model="form.house_number"
                            :error="form.errors.house_number || v$.house_number.$errors[0]?.$message" @clearError="
                              () => {
                                form.clearErrors('house_number');
                                v$.house_number.$touch();
                              }
                            " :show_error="v$.house_number.$errors[0]?.$message ? true : false" maxLength="31" />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="0000" label="Postleitzahl*" v-model="form.zip_code"
                            :error="form.errors.zip_code || v$.zip_code.$error ? 'error' : undefined" @clearError="
                              () => {
                                form.clearErrors('zip_code');
                                v$.zip_code.$touch();
                              }
                            " :maxLength="form.country == 'ch' ? 5 : 11" :show_error="false" />
                        <div class="error text-error font-ropa" v-if="v$.zip_code.$error">
                            Maximal {{ form.country == "ch" ? 4 : 10 }} Zeichen möglich
                        </div>
                    </div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="Ort eingeben" label="Ort*" v-model="form.city"
                            :error="form.errors.city || v$.city.$errors[0]?.$message" @clearError="
                              () => {
                                form.clearErrors('city');
                                v$.city.$touch();
                              }
                            " maxLength="31" :show_error="v$.city.$errors[0]?.$message ? true : false" />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label for="land" class="mb-2 block">Land*</label>
                        <SearchSelect :options="countries" :searchable="['name', 'name_translate', 'iso']"
                            placeholder="Land auswählen" class="h-10" labelKey="name_translate" valueKey="iso"
                            v-model="form.country" :error="form.errors.country" @change="form.clearErrors('country')" />
                    </div>

                    <div class="w-1/3"></div>
                </div>

                <div class="!mb-6 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput placeholder="Storenamen oder Adresse eingeben" label="Google Map Adresse*"
                            id="map-address" v-model="form.map_address" :error="form.errors.map_address"
                            @clearError="form.clearErrors('map_address')" />
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label class="form-label block mb-[8px]">Breitengrad*</label>
                        <input class="challo__input" :class="{ 'border-error': form.errors.latitude }"
                            ref="latitude_input" type="number" step="any" v-model="form.latitude"
                            placeholder="Breitengrad eingeben" />
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label class="form-label block mb-[8px]">Längengrad*</label>
                        <input class="challo__input" :class="{ 'border-error': form.errors.longitude }"
                            ref="longitude_input" type="number" step="any" v-model="form.longitude"
                            placeholder="Längengrad eingeben" />
                    </div>
                </div>

                <div class="mb-14 rounded-[6px] min-h-[296px] w-2/3 flex" id="map-container"></div>

                <h2 class="w-full text-primary-3 mb-[35px] text-18x font-semibold">
                    Öffnungszeiten
                </h2>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper flex items-center gap-4">
                        <CheckBox classLists="border-gray-corner" :value="form.no_information"
                            @toggle="form.no_information = form.no_information == 0 ? 1 : 0" :selected="form.no_information" />
                        <p class="switch__input--label !text-primary-1 !font-semibold !text-15 !font-poppins">
                            Keine Angaben
                        </p>
                    </div>
                </div>

                <div class="!mb-14 form-field">
                    <WeekDayTimeInputVue v-model="opening_hours" :editable="!form.no_information"
                        :error="form.errors['opening_hours.required']" />
                </div>

                <h2 class="w-full text-primary-3 mb-10 text-18x">Kontaktperson</h2>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput maxlength="41" placeholder="Nachname eingeben" label="Vorname*"
                            v-model="form.contact_person_first_name" :error="
                              form.errors.contact_person_first_name ||
                              v$.contact_person_first_name.$errors[0]?.$message
                            " @clearError="
                              () => {
                                form.clearErrors('contact_person_first_name');
                                v$.contact_person_first_name.$touch();
                              }
                            " :show_error="
                v$.contact_person_first_name.$errors[0]?.$message ? true : false
              " maxLength="31" />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInput maxlength="41" placeholder="Vorname eingeben" label="Nachname*"
                            v-model="form.contact_person_last_name" :error="
                              form.errors.contact_person_last_name ||
                              v$.contact_person_last_name.$errors[0]?.$message
                            " @clearError="
                              () => {
                                form.clearErrors('contact_person_last_name');
                                v$.contact_person_last_name.$touch();
                              }
                            " :show_error="
                v$.contact_person_last_name.$errors[0]?.$message ? true : false
              " maxLength="31" />
                    </div>

                    <div class="w-1/3"></div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput label="E-Mail Adresse*" placeholder="E-Mail eingeben"
                            v-model="form.contact_person_email" :error="
                              form.errors.contact_person_email ||
                              v$.contact_person_email.$errors[0]?.$message
                            " @clearError="
                              () => {
                                form.clearErrors('contact_person_email');
                              }
                            " :show_error="v$.contact_person_email.$errors[0]?.$message ? true : false"
                            @blur="v$.contact_person_email.$touch()" />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label class="form-label mb-2 block" for="phone_number">Telefonnummer*</label>
                        <PhoneInputWithCounryCode v-model:phone_number="form.contact_person_phone_number"
                            v-model:country_code="form.contact_person_country_iso_code"
                            :error="form.errors.contact_person_phone_number" @updated="
                              () => {
                                form.clearErrors(
                                  'contact_person_phone_number',
                                  'contact_person_country_iso_code'
                                );
                              }
                            " />
                    </div>

                    <div class="w-1/3"></div>
                </div>

                <h2 class="w-full text-primary-3 mb-10 text-18x">Verträge</h2>

                <h3 v-if="!form.contract" class="w-full text-primary-1 mb-10 text-18x">Keine</h3>

                <div class="form-field-wrapper" v-if="form.contract">
                    <div class="!mb-10 form-field">
                        <div class="input-wrapper w-1/3">
                            <label class="block mb-4 detail__label">Vertrags-ID</label>
                            <a class="detail__value underline underline-offset-4" target="_blank"
                                :href="route('contract.show', { contract: form.contract.id })">
                                {{ form.contract.prefix_id }}
                            </a>
                        </div>
                        <div class="input-wrapper w-1/3">
                            <label class="block mb-4 detail__label">Vertragsnummer/-bezeichnung</label>
                            <span class="detail__value">{{ form.contract.name }}</span>
                        </div>
                        <div class="input-wrapper w-1/3">
                            <label class="block mb-4 detail__label">Vertragslaufzeit</label>
                            <span class="detail__value">
                                {{ dayjs(form.contract.contract_term_from).format("DD.MM.YYYY") }}
                                -
                                {{ dayjs(form.contract.contract_term_to).format("DD.MM.YYYY") }}
                            </span>
                            <p></p>
                        </div>
                    </div>

                    <div class="!mb-10 form-field">
                        <div class="input-wrapper w-1/3">
                            <label class="block mb-4 detail__label">Paket</label>
                            <a class="detail__value underline underline-offset-4" target="_blank"
                                :href="route('package.show', { package: form.contract.package.id })">{{
                                form.contract.package.package_name }}</a>
                        </div>
                        <div class="input-wrapper w-1/3">
                            <label class="block mb-4 detail__label">Marketinggebühr</label>
                            <a class="detail__value underline underline-offset-4" target="_blank" :href="
                              route('marketing-fees.show', {
                                marketing_fee: form.contract.marketing_fee.id,
                              })
                            ">{{ form.contract.marketing_fee.designation }}</a>
                        </div>

                        <div class="w-1/3"></div>
                    </div>
                </div>

                <div class="!mb-10 form-field" v-if="form.contract">
                    <button @click="
                      form
                        .transform((data) => ({
                          ...data,
                          id: sales_partner.id,
                          profile_picture: temp_profile_picture || null,
                          _method: null,
                        }))
                        .post(route('sales-partner.set-session.redirect', { to_edit: true }))
                    " type="button" class="challo__btn bg-primary-2 w-1/3 gap-[13px] flex justify-center items-center"
                        :disable="form.processing">
                        <Pen /> <span>Vertrag bearbeiten</span>
                    </button>
                </div>

                <div class="!mb-[18px] form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel :target="route('sales-partner.index')" :show-modal="form.isDirty"
                            message="Änderungen verwerfen?"
                            text="Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import Cancel from "../../../../Components/Form/Cancel.vue";
import TextInput from "../../../../Components/Form/TextInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { computed, inject, watch, useAttrs, onMounted } from "@vue/runtime-core";
import FieldMissing from "../../../../Components/Modal/Content/FieldMissing.vue";
import useVuelidate from "@vuelidate/core";
import { maxLength, email, helpers } from "@vuelidate/validators";
import SelectOption from "../../../../Components/Form/SelectOption.vue";
import PhoneInputWithCounryCode from "../../../../Components/PhoneInputWithCounryCode.vue";
import WeekDayTimeInputVue from "../../Components/WeekDayTimeInput.vue";
import Upload from "../../../../Components/Icons/Upload.vue";
import { Link } from "@inertiajs/inertia-vue3";
import SearchSelect from "../../../../Components/Form/SearchSelect.vue";
import Cross from "../../../../Components/Icons/Cross.vue";
import { ref } from "@vue/reactivity";
import CheckBox from "../../../../Components/Form/CheckBox.vue";
import Back from "../../../../Components/Form/Back.vue";
import dayjs from "dayjs";
import axios from "axios";
import Pen from "../../../../Components/Icons/Pen.vue";
import mixin from "./../../../../mixin";

const country_list = require("../../../../../js/countries.json");

const props = defineProps({
    sales_partner: {
        type: Object,
        require: true,
    },
    branch: {
        type: [Array, Object],
        required: true,
    },
    branch_categories: {
        type: [Array, Object],
        required: true,
    },
    company_consultants: {
        type: [Object, Array],
        required: true,
    },
});
const attrs = useAttrs();
const modal = inject("modal");
const sales_partner = attrs.flash?.data?.sales_partner ?? props.sales_partner;

const form = useForm({
    company_name: sales_partner.company_name ?? "",
    receipt_template: sales_partner.receipt_template ?? null,
    receipt_template_name: sales_partner.receipt_template_name ?? null,
    branch_id: sales_partner.branch_id ?? "",
    branch_category_id: sales_partner.branch_category_id ?? "",
    consultant_id: sales_partner.consultant_id ?? "",
    profile_picture: props.sales_partner.profile_picture ?? "",
    website: sales_partner.website ?? "",
    street: sales_partner.street ?? "",
    house_number: sales_partner.house_number ?? "",
    zip_code: sales_partner.zip_code ?? "",
    city: sales_partner.city ?? "",
    country: sales_partner.country ?? "",
    no_information: sales_partner.no_information ?? "",
    contact_person_first_name: sales_partner.contact_person_first_name ?? "",
    contact_person_last_name: sales_partner.contact_person_last_name ?? "",
    contact_person_email: sales_partner.contact_person_email ?? "",
    contact_person_country_iso_code: sales_partner.contact_person_country_iso_code ?? "",
    contact_person_phone_number: sales_partner.contact_person_phone_number ?? "",
    opening_hours: sales_partner.opening_hours ?? [],
    contract: sales_partner.contract ?? "",
    map_address: sales_partner.map_address ?? "",
    latitude: sales_partner.latitude ?? "",
    longitude: sales_partner.longitude ?? "",
    _method: "PUT",
});

let geocoder = new google.maps.Geocoder();
let googleMap = null;
let marker = null;

onMounted(() => {
    const map_container = document.getElementById("map-container");
    const map_address_input = document.getElementById("map-address");

    const zurich = new google.maps.LatLng(47.373878, 8.545094);
    googleMap = new google.maps.Map(map_container, {
        center: zurich,
        zoom: 15,
        options: {
            gestureHandling: "greedy",
        },
    });

    //set market on click
    googleMap.addListener("click", (e) => {
        setMarker(e.latLng, googleMap);
    });

    //address autocomplete
    const autocomplete = new google.maps.places.Autocomplete(map_address_input, {
        fields: ["geometry"],
        strictBounds: false,
    });

    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        setMarker(place.geometry.location, googleMap, map_address_input.value);
    });

    //set initail marker if latitude and longitude is available
    if (form.latitude && form.longitude) {
        const latLng = new google.maps.LatLng(form.latitude, form.longitude);
        geocoder.geocode({ location: latLng }).then((res) => {
            let location_type = res.results[0]?.geometry.location_type;
            if (location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
                setMarker(latLng, googleMap, res.results[0]["formatted_address"]);
        });
    }
});

watch(
    () => [form.latitude, form.longitude],
    () => {
        const latLng = new google.maps.LatLng(form.latitude, form.longitude);
        setMarker(latLng, googleMap);
    }
);

//set marker on map and update the lattitude and longitude value
const setMarker = async (position, map, address = null) => {
    if (!address) {
        let res = await geocoder.geocode({ location: position });
        res = res.results[0];

        if (
            res.geometry.location_type !== "ROOFTOP" &&
            res.geometry.location_type !== "RANGE_INTERPOLATED"
        ) {
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
            return;
        }

        address = res["formatted_address"];
    }

    if (marker) {
        marker.setPosition(position);
    } else {
        marker = new google.maps.Marker({
            position: position,
            map: map,
        });
    }

    map.setCenter(position);
    const latLng = position.toJSON();
    form.map_address = address;
    form.latitude = latLng.lat;
    form.longitude = latLng.lng;
};

const countries = computed(() => {
    return country_list.map((country) => {
        return { ...country, ["name_translate"]: mixin.methods.getCountryName(country.iso) };
    });
});

const opening_hours = computed({
    get: () => {
        const opening_hours = {};
        form.opening_hours?.forEach((opening_hour) => {
            if (opening_hour.day) {
                opening_hours[opening_hour.day] = opening_hour;
            }
        });
        return opening_hours;
    },

    set: (value) => {
        const opening_hours = [];
        for (const key in value) {
            if (Object.hasOwnProperty.call(value, key)) {
                const element = value[key];
                opening_hours.push(element);
            }
        }
        form.opening_hours = opening_hours.length ? opening_hours : [];
        return;
    },
});

const temp_profile_picture = ref(sales_partner?.profile_picture ?? "");
const receipt_template_input = ref("");
const profile_picture_input = ref("");

const profile_picture = computed({
    get: () => {
        if (temp_profile_picture.value && typeof temp_profile_picture.value == "object") {
            return URL.createObjectURL(temp_profile_picture.value);
        }
        if (temp_profile_picture.value && typeof temp_profile_picture.value == "string" && sales_partner.temp_profile_picture_url) {
            return sales_partner.temp_profile_picture_url;
        }

        if (form.profile_picture) {
            return props.sales_partner.profile_picture_url;
        }
        return null;
    },
    set: (value) => {
        temp_profile_picture.value = value;
    },
});

const clearProfilePicture = () => {
    if (temp_profile_picture.value) {
        profile_picture_input.value.value = "";
        URL.revokeObjectURL(temp_profile_picture.value);
        temp_profile_picture.value = "";
        return;
    }
    if (!temp_profile_picture.value && form.profile_picture) {
        axios.delete(
            route("sales-partner.profile-picture.delete", {
                sales_partner: props.sales_partner.id,
            })
        );
        form.profile_picture = null;
        return;
    }
};

const receipt_template_name = computed(() => {
    if (form.receipt_template && typeof form.receipt_template == "object")
        return receipt_template_input.value.files[0].name;

    return form.receipt_template_name
});

const rules = {
    company_name: {
        maxLength: helpers.withMessage("Maximal 40 Zeichen möglich", maxLength(40)),
    },
    street: {
        maxLength: helpers.withMessage("Maximal 30 Zeichen möglich", maxLength(30)),
    },
    house_number: {
        maxLength: helpers.withMessage("Maximal 30 Zeichen möglich", maxLength(30)),
    },
    city: {
        maxLength: helpers.withMessage("Maximal 30 Zeichen möglich", maxLength(30)),
    },
    contact_person_first_name: {
        maxLength: helpers.withMessage("Maximal 30 Zeichen möglich", maxLength(30)),
    },
    contact_person_last_name: {
        maxLength: helpers.withMessage("Maximal 30 Zeichen möglich", maxLength(30)),
    },
    contact_person_email: {
        email: helpers.withMessage("Ungültiges E-Mail Format", email),
    },
    zip_code: {
        length: (value) => {
            if (form.country == "ch") {
                return value.length < 5 ? true : false;
            } else {
                return value.length < 11 ? true : false;
            }
        },
    },
};

const branch_categories = computed(() => {
    if (form.branch_id) {
        return props.branch_categories.filter(
            (category) => category.branch_id == form.branch_id
        );
    }
    return [];
});

watch(
    () => form.branch_id,
    () => {
        form.branch_category_id = "";
    }
);

watch(
    () => form.no_information,
    () => {
        if (form.no_information) {
            opening_hours.value = {};
        }
    }
);

watch(
    () => form.opening_hours,
    () => {
        form.clearErrors("opening_hours.required");
    }
);

const v$ = useVuelidate(rules, form);

const create = async () => {
    form
        .transform((data) => ({
            ...data,
            profile_picture: temp_profile_picture.value ? temp_profile_picture.value : null,
            temp_receipt_template_name:
                attrs.flash.data?.sales_partner?.temp_receipt_template_name,
        }))
        .post(route("sales-partner.update", { sales_partner: props.sales_partner.id }), {
            forcedData: true,

            onSuccess: () => {
                form.reset();
            },
            onError: () => {
                if (form.errors.contract_already_exists) {
                    modal.show(FieldMissing, {
                        props: {
                            title: "Vertragserstellung nicht möglich",
                            description:
                                "Für den selektierten Vertriebspartner besteht bereits ein Vertrag für ein, mehrere oder alle Tage der gewählten Vertragslaufszeitperiode. Bitte prüfen Sie die bestehenden Verträge und erfassen Sie eine Vertragslaufzeit die keine Daten enthält, welche bereits Teil eines anderen vorhandenen Vertrages sind.",
                        },
                        config: {
                            staticBackdrop: true,
                        },
                    });
                    return;
                }

                modal.show(FieldMissing, {
                    config: {
                        staticBackdrop: true,
                    },
                });
            },
        });
};
</script>

<style lang="scss" scoped>

</style>
