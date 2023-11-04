<template>
    <div class="challo__card py-10 px-6 -mt-2">
        <Back :target="route('sales-partner.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-12">Vertriebspartnerdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row mb-[29px] gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Erstellungsdatum</h5>
                    <span class="detail__value">
                        {{ dayjs(sales_partner.created_at).format("DD.MM.YYYY").toString() }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Vertriebspartner-ID</h5>
                    <span class="detail__value">
                        {{ sales_partner.prefix_id }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Status</h5>
                    <span class="detail__value">
                        {{ status }}
                    </span>
                </div>
            </div>
            <div class="detail__row mb-[29px] gap-0">
                <div class="w-1/3 flex gap-5 items-center">
                    <div
                        class="avatar w-16 h-16 rounded-full overflow-hidden bg-white flex justify-center items-center">
                        <img v-if="sales_partner.profile_picture_url" class="max-w-16 max-h-16"
                            :src="sales_partner.profile_picture_url" alt="avatar" />
                    </div>
                    <div class="flex flex-col">
                        <h5 class="detail__label mb-4">Firmenname</h5>
                        <span class="detail__value">
                            {{ sales_partner.company_name }}
                        </span>
                    </div>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-4">Belegvorlage</h5>
                    <span class="detail__value flex gap-3 cursor-pointer" v-if="sales_partner.receipt_template"
                        @click="showReceiptTemplate">
                        <Image />
                        <span> Vorlage ansehen </span>
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-4">Company Consultant</h5>
                    <span class="detail__value underline underline-offset-4">
                        {{ sales_partner.consultant.name }}

                        <!-- <Link
                v-if="hasPermission('sales_partner.edit')"
                :href="route('sales-partner.edit', { sales_partner: sales_partner.id })"
                class="flex gap-[9px] h-10 w-[416px] items-center justify-center bg-primary-1 text-white font-poppins text-15 font-semibold rounded-[37px] mt-10"
            >
                <Pen />
                Bearbeiten
            </Link> -->
                    </span>
                </div>
            </div>

            <div class="detail__row mb-14 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Branche</h5>
                    <span class="detail__value">{{ sales_partner.branch.name }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Branchenkategorie</h5>
                    <span class="detail__value">{{ sales_partner.branch_category.name }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Webseite</h5>
                    <span class="detail__value break-all">{{ sales_partner.website }}</span>
                </div>
            </div>

            <h2 class="row-title text-18x font-poppins font-semibold text-primary-3 mb-10">
                Adresse
            </h2>
            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Strasse</h5>
                    <span class="detail__value">{{ sales_partner.street }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Hausnummer</h5>
                    <span class="detail__value">{{ sales_partner.house_number }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Postleitzahl</h5>
                    <span class="detail__value break-all">{{ sales_partner.zip_code }}</span>
                </div>
            </div>

            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Ort</h5>
                    <span class="detail__value">{{ sales_partner.city }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Land</h5>
                    <span class="detail__value">
                        {{ getCountryName(sales_partner.country) }}
                    </span>
                </div>
            </div>

            <div class="mb-14 detail__row" gap-0>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Google Map Adresse</h5>
                    <div class="w-full h-[296px] rounded-[6px]" id="map-container"></div>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Breitengrad</h5>
                    <span class="detail__value">{{ sales_partner.latitude }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Längengrad</h5>
                    <span class="detail__value">{{ sales_partner.longitude }}</span>
                </div>
            </div>

            <h2 class="row-title text-18x font-poppins font-semibold text-primary-3 mb-10">
                Öffnungszeiten
            </h2>

            <div class="detail__value mb-10" v-if="sales_partner.no_information == true">
                Keine Angaben
            </div>
            <ShowWeekDayTime :opening_hours="sales_partner.opening_hours"
                v-if="sales_partner.opening_hours.length && !sales_partner.no_information" />

            <h2 class="row-title text-18x font-poppins font-semibold text-primary-3 mb-10">
                Kontaktperson
            </h2>

            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Vorname</h5>
                    <span class="detail__value">{{ sales_partner.contact_person_first_name }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Nachname</h5>
                    <span class="detail__value">{{ sales_partner.contact_person_last_name }}</span>
                </div>
            </div>

            <div class="detail__row mb-14 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">E-Mail Adresse</h5>
                    <span class="detail__value break-all">{{
                    sales_partner.contact_person_email
                    }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Telefonnummer</h5>
                    <span class="detail__value break-all">{{
                    sales_partner.contact_person_full_phone_number
                    }}</span>
                </div>
            </div>

            <h2 class="row-title text-18x font-poppins font-semibold text-primary-3 mb-10">
                Verträge
            </h2>

            <!-- Contract -->
            <h6 class="text-16 text-gray-3 font-ropa font-normal" v-if="!sales_partner.current_contract">
                Keine
            </h6>

            <div class="contract-wrapper" v-if="sales_partner.current_contract">
                <div class="detail__row mb-10 gap-0">
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Erstellungsdatum</h5>
                        <span class="detail__value break_all">{{
                        dayjs(sales_partner.current_contract.created_at).format("DD.MM.YYYY")
                        }}</span>
                    </div>
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Vertrags-ID</h5>
                        <a target="_blank" :href="
                          route('contract.show', { contract: sales_partner.current_contract.id })
                        " class="detail__value break_all underline underline-offset-4">
                            {{ sales_partner.current_contract.prefix_id }}
                        </a>
                    </div>
                    <div class="w-1/3"></div>
                </div>
                <div class="detail__row mb-10 gap-0">
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Vertragsnummer/-bezeichnung</h5>
                        <span class="detail__value break_all">{{
                        sales_partner.current_contract.name
                        }}</span>
                    </div>
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Vertragslaufzeit</h5>
                        <span class="detail__value break_all">
                            {{
                            dayjs(sales_partner.current_contract.contract_term_from).format(
                            "DD.MM.YYYY"
                            )
                            }}
                            -
                            {{
                            dayjs(sales_partner.current_contract.contract_term_to).format(
                            "DD.MM.YYYY"
                            )
                            }}
                        </span>
                    </div>
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Paket</h5>
                        <span class="detail__value break_all underline underline-offset-4">
                            <a target="_blank" :href="
                              route('package.show', {
                                package: sales_partner.current_contract.package.id,
                              })
                            ">
                                {{ sales_partner.current_contract.package.package_name }}</a>
                        </span>
                    </div>
                </div>
                <div class="detail__row mb-10 gap-0">
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Marketinggebühr</h5>
                        <span class="detail__value break_all underline underline-offset-4">
                            <a target="_blank" :href="
                              route('marketing-fees.show', {
                                marketing_fee: sales_partner.current_contract.marketing_fee.id,
                              })
                            ">
                                {{ sales_partner.current_contract.marketing_fee.designation }}</a>
                        </span>
                    </div>
                    <div class="w-1/3">
                        <h5 class="detail__label mb-4">Status</h5>
                        <span class="detail__value break_all">{{ status }}</span>
                    </div>
                    <div class="w-1/3"></div>
                </div>
            </div>

            <Link v-if="hasPermission('sales_partner.edit')"
                :href="route('sales-partner.edit', { sales_partner: sales_partner.id })"
                class="flex gap-[9px] h-10 w-[416px] items-center justify-center bg-primary-1 text-white font-poppins text-15 font-semibold rounded-[37px] mt-10">
            <Pen />
            Bearbeiten
            </Link>
        </div>
    </div>
</template>

<script setup>
import Back from "../../../../Components/Form/Back.vue";
import { computed } from "@vue/reactivity";
import { onMounted, inject } from "vue";
import dayjs from "dayjs";
import Pen from "../../../../Components/Icons/Pen.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Image from "../../../../Components/Icons/Image.vue";
import ViewImage from "../../../../Components/Modal/Content/ViewImage.vue";
import ShowWeekDayTime from "../../Components/ShowWeekDayTime.vue";
const props = defineProps({
    sales_partner: {
        type: Object,
        required: true,
    },
});

const modal = inject("modal");

let geocoder = new google.maps.Geocoder();
let googleMap = null;
let marker = null;

onMounted(() => {
    const map_container = document.getElementById("map-container");

    const zurich = new google.maps.LatLng(47.373878, 8.545094);
    googleMap = new google.maps.Map(map_container, {
        center: zurich,
        zoom: 15,
        disableDefaultUI: true,
        options: {
            gestureHandling: "greedy",
        },
    });

    //set initail marker if latitude and longitude is available
    if (props.sales_partner.latitude && props.sales_partner.longitude) {
        const latLng = new google.maps.LatLng(
            props.sales_partner.latitude,
            props.sales_partner.longitude
        );
        geocoder.geocode({ location: latLng }).then((res) => {
            console.log(res.results);
            let location_type = res.results[0]?.geometry.location_type;
            if (location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
                setMarker(latLng, googleMap);
        });
    }
});

//set marker on map and update the lattitude and longitude value
const setMarker = async (position, map) => {
    marker = new google.maps.Marker({
        position: position,
        map: map,
    });
    map.setCenter(position);
};

const status = computed(() => {
    const status_de = {
        active: "Aktiv",
        inactive: "Inaktiv",
        pending: "Anstehend",
        new: "Neu",
        canceled: "Abgebrochen",
        expired: "Abgelaufen",
    };
    return status_de[props.sales_partner.status] || null;
});

const showReceiptTemplate = () => {
    modal.show(ViewImage, {
        props: {
            image: props.sales_partner.receipt_template_url,
        },
    });
};
</script>
