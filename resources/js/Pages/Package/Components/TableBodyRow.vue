<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)"
    >
        <div class="content" v-if="!option.expand">
            <div class="td" style="width: 5%">
                <CheckBox  :value="pkg.id" :selected="selected" @toggle="set_selected_package(pkg.id)" classLists="border-gray-corner"/>
            </div>
            <div class="td" style="width: 15%">
                {{ formateDate(pkg.created_at)}}
            </div>
            <div class="td" style="width: 15%">
                <Link
                    style="word-break: break-word;"
                    :href="route('package.show', { package : pkg.id, ...buildQueryParams() })"
                    v-if="hasPermission('package.view')"
                >
                    {{ pkg.package_name }}
                </Link>
            </div>
            <div class="td" style="width: 10%">
                <div style="width: 92%">{{ 'CHF ' .concat( pkg.package_fee.toFixed(2) ) }}</div>
                <PackageTooltip v-if="!pkg.first_year_package_fee" :toolTipId="pkg.id" />
            </div>
            <div class="td" style="width: 20%; word-break: break-word;">
                {{ pkg.booster.toString().concat( ' / '.concat( 'Monat' ) ) }}
            </div>
            <div class="td" style="width: 20%">
                {{ pkg.number_of_registration }}
            </div>
            <div class="td" style="width: 10%">
                <UserStatus :status="pkg.status" />
            </div>
            <div class="td relative" style="width: 5%">
                <button
                    class="relative"
                    type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    @click="showDropdown = true"
                >
                    <menu-bar />
                    <div
                        class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)"
                        @click.stop="showDropdown = false"
                        v-show="showDropdown"
                    >
                        <Link
                            class="dropdown-item"
                            :href="route('package.show', { package : pkg.id, ...buildQueryParams() })"
                            v-if="hasPermission('package.view')"
                        >
                            Details ansehen
                        </Link>

                        <Link
                            class="dropdown-item"
                            :backUrl="{ package : pkg.id, ...buildQueryParams() }"
                            :href="route('package.edit', { package : pkg.id, ...buildQueryParams() })"
                            v-if="hasPermission('package.edit') && pkg.package_assigned_count === 0"
                        >
                            Bearbeiten
                        </Link>

                        <TogglePackageStatus
                            :staticBackdrop='true'
                            :status="pkg.status"
                            routeName="package.toggle-status"
                            :value="pkg.id"
                            class="dropdown-item"
                            v-if="
                hasPermission('package.edit') &&
                (pkg.status === 'active' || pkg.status === 'inactive')
                "
                            :activated_message='"Wenn Sie dieses Paket aktivieren, kann es Vertriebspartnern zugeteilt werden. Sind Sie sicher, dass Sie das Paket \":package_name\" wirklich aktivieren wollen?".replace(":package_name", pkg.package_name)'
                            :deactivated_message='"Wenn Sie dieses Paket deaktivieren, kann es keinem Vertriebspartner mehr neu zugeteilt werden. Ist dieses Paket einem Vertriebspartner zugewiesen und die entsprechende Vertragslaufzeit ist noch nicht abgelaufen, bleibt dieses Paket weiterhin Vertragsbestandteil bis zum Ende der Vertragslaufzeit, sofern Sie den Vertrag nicht ändern. Sind Sie sicher, dass Sie das Paket \":package_name\" wirklich deaktivieren wollen?".replace(":package_name", pkg.package_name)'
                            :title="(pkg.status === 'active' ? 'Paket deaktivieren?' : 'Paket aktivieren?')"
                        />

                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import {reactive, ref} from "@vue/reactivity";
import UserStatus from "../../../Components/Status/UserStatus.vue";
import TogglePackageStatus from "./TogglePackageStatus.vue";
import CheckBox from "../../../Components/Form/CheckBox.vue";
import PackageTooltip from "./PackageTooltip";

const props = defineProps({
        pkg: {
            type: Object,
            required: true,
        },

        selected : {
            type: Boolean,
            required: true,
        },

        set_selected_package: {
            type: Function,
            required: true,
        }
    });

    const buildQueryParams = () => {
        return Object.fromEntries(
            new URLSearchParams(location.search)
        );
    };

    const showDropdown = ref(false);

    const option = reactive({
        expand: false,
    });
</script>

<style lang="scss" scoped>

    .content {
        gap: 10px;
        .td {
            font-family: 'Poppins';
            font-style: normal;
            // font-weight: 400;
            font-size: 16px;
            line-height: 24px;
            color: #135F84;
            font-weight: inherit;
        }
    }

    .dropdown {
        right: 0;
        top: 20px;
        width: max-content;

        .dropdown-item {
            border-bottom: 1px solid #8ED5F6;
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 16px;
            padding-right: 30px;
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            font-size: 13px;
            line-height: 20px;
            color: #135F84;
            text-align: left;

            &:hover {
                font-weight: 600;
            }

            &:last-child {
                border-bottom: none;
            }
        }
    }

</style>
