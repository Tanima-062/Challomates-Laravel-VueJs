<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('package.index', { ...buildQueryParams() } )" :showModal="false" :backPrevUrl="false" />
        <h5 class="page-title mb-[42px]">Paketdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row gap-0 mb-10">
                <div class="w-1/3">
                    <div class="detail__label mb-3">Erstellungsdatum</div>
                    <div class="detail__value">{{formateDate(pkg.created_at)}}</div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Paket-ID</div>
                    <div class="detail__value">{{pkg.package_prefix_id}}</div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Name</div>
                    <div class="detail__value" style="word-break: break-word;">{{pkg.package_name}}</div>
                </div>
            </div>
            <div class="detail__row gap-0 mb-10">
                <div class="w-1/3">
                    <div class="detail__label mb-3">Dienstleistungen</div>
                    <div class="detail__value pr-3">
<!--                        <span v-for="(service, index) in services" :key="index">{{ (index > 0) ? ', ' : '' }} {{ service.service_name }}</span>-->
                        <span style="word-break: break-word;">{{ pkg.services }}</span>
                    </div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Einmalige Anmeldegebühr</div>
                    <div class="detail__value">CHF {{ pkg.registration_fee.toFixed(2) }}</div>
                </div>
            </div>
            <div class="detail__row gap-0 mb-10">
                <div class="w-1/3">
                    <div class="detail__label mb-3">Preis im 1. Jahr</div>
                    <div class="detail__value">CHF {{pkg.first_year_package_fee.toFixed(2)}} / Jahr</div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Preis ab 2. Jahr</div>
                    <div class="detail__value">CHF {{pkg.package_fee.toFixed(2)}} / Jahr</div>
                </div>
            </div>
            <div class="detail__row gap-0 mb-10">
                <div class="w-1/3">
                    <div class="detail__label mb-3">Coin Faktor</div>
                    <div class="detail__value">{{pkg.coin_factor.toFixed(2)}}</div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Beratung</div>
                    <div class="detail__value" style="word-break: break-word;">{{pkg.consulting}}</div>
                </div>
            </div>

            <div class="detail__row gap-0 mb-10">
                <div class="w-1/3">
                    <div class="detail__label mb-3">Booster</div>
                    <div class="detail__value">{{pkg.booster}} / Monat</div>
                </div>
                <div class="w-1/3">
                    <div class="detail__label mb-3">Anzahl Registrationen</div>
                    <div class="detail__value">{{pkg.number_of_registration}}</div>
                </div>
            </div>

            <div class="detail__row gap-0 mb-10" v-if="hasPermission('package.edit') && pkg.package_assigned_count === 0">
                <div class="w-1/3">
                    <Link class="challo__btn flex w-full btn-primary justify-center gap-[10px]" :href="route('package.edit', { package : pkg.id, ...buildQueryParams() })">
                        <span><Pen height="17px" width="17px"/></span>
                        <span>Bearbeiten</span>
                    </Link>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import Back from "../../Components/Form/Back.vue";
import Pen from "../../Components/Icons/Pen.vue";
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    pkg: {
        type: [Object, Array],
        required: true,
    },

    /*services: {
        type: [Object, Array],
        required: true,
    }*/
});

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};
</script>
