<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('package.index', { ...buildQueryParams() } )" :showModal="false" :backPrevUrl="false" />
        <h5 class="page-title mb-[42px]">Paketdetails</h5>

        <div class="challo__card__body">
            <div class="grid grid-cols-3 gap-6">
                <div class="flex flex-col w-full rounded-[24px] bg-white p-8" v-for="pkg in packages" :key="pkg.id">
                    <h2 class="title font-poppins font-semibold text-[22px] text-primary-1 leading-[33px] mb-10" style="word-break: break-word;">
                        {{pkg.package_name}}
                    </h2>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Erstellungsdatum</h4>
                        <span class="detail__value">{{formateDate(pkg.created_at)}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Paket-ID</h4>
                        <span class="detail__value">{{pkg.package_prefix_id}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Name</h4>
                        <span class="detail__value" style="word-break: break-word;">{{pkg.package_name}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Dienstleistungen</h4>
                        <div class="detail__value">
                            <span style="word-break: break-word;">{{ pkg.services }}</span>
<!--                            <span v-for="(service, index) in services[pkg.id]" :key="index">{{ (index > 0) ? ', ' : '' }} {{ service }}</span>-->
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Einmalige Anmeldegebühr</h4>
                        <span class="detail__value">CHF {{pkg.registration_fee.toFixed(2)}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Preis im 1. Jahr</h4>
                        <span class="detail__value">CHF {{pkg.first_year_package_fee.toFixed(2)}} / Jahr</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Preis ab 2. Jahr</h4>
                        <span class="detail__value">CHF {{pkg.package_fee.toFixed(2)}} / Jahr</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Coin Faktor</h4>
                        <span class="detail__value">{{pkg.coin_factor.toFixed(2)}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Beratung</h4>
                        <span class="detail__value" style="word-break: break-word;">{{pkg.consulting}}</span>
                    </div>

                    <div class="mb-6">
                        <h4 class="detail__label text-tints-4">Booster</h4>
                        <span class="detail__value">{{pkg.booster}} / Monat</span>
                    </div>

                    <div class="mb-8">
                        <h4 class="detail__label text-tints-4">Anzahl Registrationen</h4>
                        <span class="detail__value">{{pkg.number_of_registration}}</span>
                    </div>

                    <div v-if="hasPermission('package.edit') && pkg.package_assigned_count === 0">
                        <Link class="challo__btn flex w-full btn-primary justify-center gap-[10px]" :href="route('package.edit', { package : pkg.id, ...buildQueryParams() })">
                            <span><Pen height="17px" width="17px"/></span>
                            <span>Bearbeiten</span>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import Back from "../../Components/Form/Back.vue";
    import Pen from "../../Components/Icons/Pen.vue";
    import { Link } from "@inertiajs/inertia-vue3";

    const props = defineProps({
        packages: {
            type: [Object, Array],
            required: true,
        },

        /*services: {
            type: [Object, Array],
            required: true,
        },*/
    });

    const buildQueryParams = () => {
        let searchParams = Object.fromEntries(
            new URLSearchParams(location.search)
        );

        for (let list in searchParams) {
            if( /packages\[[0-9]*\]/gi.test(list) ) {
                delete searchParams[list];
            }
        }

        return searchParams;
    };
</script>


<style lang="scss" scoped>
    .detail__label {
        margin-bottom: 4px;
    }
</style>
