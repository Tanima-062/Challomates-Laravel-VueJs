<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)"
    >
        <div class="content" v-if="!option.expand">
            <div class="td" style="width: 15%">
                {{ formateDate(booster.created_at)}}
            </div>
            <div class="td" style="width: 25%;">
                <a :href="route('sales-partner.show', {sales_partner: booster.sales_partner_id})" target="_blank" style="word-break: break-word;">{{ booster.sales_partner.company_name }}</a>
            </div>
            <div class="td break-words" style="width: 17%">
                <a :href="route('boosters.show', {booster: booster.id})" style="word-break: break-word;" target="_blank">{{ booster.title }}</a>
            </div>
            <div class="td" style="width: 13%">
                {{ booster.type=='Recurring' ? 'Wiederkehrend' : 'Einmalig'}}
            </div>
            <div class="td" style="width: 20%" v-if="booster.type=='One Time' && booster?.start">
                {{ formateDate(booster?.start) }}
            </div>
            <div class="td" style="width: 20%" v-else-if="booster.type=='Recurring' && booster?.start">
                {{ formateDate(booster?.start) }} -  {{formateDate(booster?.end)}}
            </div>
            <div class="td" style="width: 20%" v-else></div>
            <div class="td" style="width: 10%">
                <UserStatus :status="booster.status" />
            </div>

             <div class="td relative" style="width: 5%">

                <button
                class="relative"
                type="button"
                aria-haspopup="true"
                aria-expanded="false"
                @click="showDropdown = true"
                >
                <menu-bar /></button>
                <div
                    class="
                    dropdown
                    absolute
                    bg-white
                    rounded-lg
                    flex flex-col
                    z-10
                    shadow-sm
                    "
                    v-click-away="() => (showDropdown = false)" @click="showDropdown = false" v-show="showDropdown">
                    <Link
                    class="dropdown-item"
                    :href="
                        route('boosters.show', {
                        booster: booster.id  ,
                        })
                    "
                    >{{ $t("Details ansehen") }}</Link>
                    <a
                        class="dropdown-item"
                        v-if="hasPermission('booster.edit')"
                        @click.prevent="editAlert(booster)"
                        style="cursor:pointer"
                    >{{ $t("Bearbeiten") }}</a>
                    <ToggleUserStatus
                        :status="booster.status"
                        :value="booster.id"
                        routeName="boosters.toggle-status"
                        class="dropdown-item"
                        v-if="hasPermission('booster.enable/disable') && (booster.status != 'expired' && booster.status != 'posted')"
                        :message="(booster.status == 'active' || booster.status == 'new')?
                            `Sind Sie sicher, dass Sie den Booster &quot;${booster.title}&quot; für den Vertriebspartner &quot;${booster.sales_partner.company_name}&quot; wirklich deaktivieren wollen?`
                            : `Sind Sie sicher, dass Sie den Booster &quot;${booster.title}&quot; für den Vertriebspartner &quot;${booster.sales_partner.company_name}&quot; wirklich aktivieren wollen?`"
                        :title="`${booster.status == 'active' || booster.status == 'new'? 'Booster deaktivieren' : 'Booster aktivieren'}?`"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import MenuBar from "../../../Components/Icons/MenuBar.vue";
    import { trans } from "laravel-vue-i18n";
    import { reactive, ref } from "@vue/reactivity";
    import ToggleUserStatus from "../../../Components/Status/ToggleUserStatus.vue";
    import UserStatus from "../../../Components/Status/UserStatus.vue";
    import { computed } from "@vue/reactivity";
    import Status from "../../../Components/Icons/Status.vue";
    import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
    import { inject } from "@vue/runtime-core";
    import { Inertia } from "@inertiajs/inertia";
    import { Link } from "@inertiajs/inertia-vue3";

    const props = defineProps({
        booster: {
            type: Object,
            required: true,
        },
    });

    const showDropdown = ref(false);

    const date = ref(new Date().toJSON().slice(0,10).replace(/-/g,'-'));

    const option = reactive({
        expand: false,
    });

    const modal = inject('modal')

    const editAlert = (booster) => {
        if(booster.status == 'active'){
            modal.show(Confirmation, {
                props: {
                    message: `Aktiven Booster ändern?`,
                    text: `Dieser Booster ist aktiv. Sind Sie sicher, dass Sie diesen Booster trotzdem ändern wollen und bestätigen damit, dass der Vertriebspartner darüber informiert und ein entsprechender Vertrag vorhanden ist oder die
                           Änderung keinen Einfluss auf den Vertriebspartner und den Vertrag haben?`,
                },
                events: {
                    yesClick: () => {
                        Inertia.get(route('boosters.edit', { booster: props.booster.id })); modal.hide()
                    },
                    noClick: () => modal.hide(),
                },
            });
        }else{
            Inertia.get(route('boosters.edit', { booster: props.booster.id })); modal.hide()
        }
    }
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
