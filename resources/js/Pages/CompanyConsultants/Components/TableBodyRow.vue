<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)"
    >
        <div class="content" v-if="!option.expand">
            <div class="td" style="width: 15%">
                {{ formateDate(consultant.created_at)}}
            </div>
            <div class="td" style="width: 15%">
                <span>
                    {{ consultant.prefix_id }}
                </span>
            </div>
            <div class="td" style="width: 15%; word-break: break-word;">
                {{ consultant.name }}
            </div>
            <div class="td text-break" style="width: 25%; word-break: break-word;">
                {{ consultant.email }}
            </div>
            <div class="td" style="width: 15%">
                {{ consultant.full_phone_number }}
            </div>
            <div class="td" style="width: 10%">
                <UserStatus :status="consultant.status" />
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
                        v-show="showDropdown"
                    >
                        <Link
                            class="dropdown-item"
                            :backUrl="{ consultantId : consultant.id, ...buildQueryParams() }"
                            :href="route('company-consultants.edit', { company_consultant : consultant.id, ...buildQueryParams() })"
                            v-if="hasPermission('company_consultants.edit')"
                        >
                            Bearbeiten
                        </Link>

                        <TogglePackageStatus
                            :status="consultant.status"
                            routeName="company-consultants.toggle-status"
                            :value="consultant.id"
                            class="dropdown-item"
                            @click.stop="showDropdown = false"
                            v-if="
                hasPermission('company_consultants.edit') &&
                (consultant.status == 'active' || consultant.status == 'inactive')
                "
                            activated_message='Sind Sie sicher, dass Sie den Company Consultant ":consultant_name" wirklich aktivieren wollen?'
                            deactivated_message='Sind Sie sicher, dass Sie den Company Consultant ":consultant_name" wirklich deaktivieren wollen?'
                            :attributes="{ consultant_name: consultant.name }"
                            :title="(consultant.status == 'active' ? 'Company Consultant deaktivieren?' : 'Company Consultant aktivieren?')"
                        />

                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link } from "@inertiajs/inertia-vue3";
    import MenuBar from "../../../Components/Icons/MenuBar.vue";
    import { trans } from "laravel-vue-i18n";
    import {onMounted } from "vue"
    import { reactive, ref } from "@vue/reactivity";
    import UserStatus from "../../../Components/Status/UserStatus.vue";
    import { computed } from "@vue/reactivity";
    import Status from "../../../Components/Icons/Status.vue";
    import TogglePackageStatus from "./TogglePackageStatus.vue";
    import CheckBox from "../../../Components/Form/CheckBox.vue";
    import PackageTooltip from "./PackageTooltip";

    const props = defineProps({
        consultant: {
            type: Object,
            required: true,
        },
    });

    const buildQueryParams = () => {
        let searchParams = Object.fromEntries(
            new URLSearchParams(location.search)
        );

        return searchParams;
    };

    const showDropdown = ref(false);

    const option = reactive({
        expand: false,
    });
</script>

<style lang="scss" scoped>

    .content {
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
