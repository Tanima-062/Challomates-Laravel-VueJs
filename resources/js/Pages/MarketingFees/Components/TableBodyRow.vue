<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)">

        <div class="content" v-if="!option.expand">
            <div class="td" style="width: 13%;">
                <a :href="route('marketing-fees.show', {marketing_fee: fee.id  ,})" style="word-break: break-word;" target="_blank">{{fee.designation}}</a>
            </div>
            <div class="td" style="width: 15%">
                <Link :href="route('marketing-fees.show', {marketing_fee: fee.id  ,})">{{ getData(fee.marketing_fee) }}%</Link>
            </div>
            <div class="td" style="width: 17%">
                {{ getData(fee.direct_consumers_senior_partner_share) }}%
            </div>
            <div class="td" style="width: 13%">
                {{ getData(fee.direct_consumers_share_jackpot) }}%
            </div>
            <div class="td break-word" style="width: 17%">
                {{ getData(fee.direct_consumers_share_fee_challomates) }}%
            </div>
            <div class="td" style="width: 15%">
                {{ getData(fee.direct_consumers_share_challomates_marketing_ag) }}%
            </div>
            <div class="td" style="width: 10%">
                <UserStatus :status="fee.status" />
            </div>
            <div class="td relative float-right" style="width: 5%">
                <button type="button" aria-haspopup="true" aria-expanded="false" @click="showDropdown = true">
                    <menu-bar />
                </button>
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
                        route('marketing-fees.show', {
                        marketing_fee: fee.id  ,
                        })
                    "
                    >{{ $t("Details ansehen") }}</Link>
                    <Link
                        class="dropdown-item"
                        :href="route('marketing-fees.edit', { marketing_fee: fee.id,})"
                        v-if="hasPermission('marketing_fee.edit') && fee.marketing_fee_assigned_count===0"
                    >{{ $t("Bearbeiten") }}</Link>
                    <ToggleUserStatus
                    :status="fee.status"
                    :value="fee.id"
                    routeName="marketing-fees.toggle-status"
                    class="dropdown-item"
                    v-if="hasPermission('marketing_fee.enable/disable')"
                    :message="fee.status == 'active' ?
                        `Wenn Sie diese Marketinggebühr deaktivieren, kann sie keinem Vertriebspartner mehr neu zugeteilt werden. Ist diese Marketinggebühr einem Vertriebspartner zugewiesen und die entsprechende Vertragslaufzeit ist noch nicht abgelaufen, bleibt diese Marketinggebühr weiterhin Vertragsbestandteil bis zum Ende der Vertragslaufzeit, sofern Sie den Vertrag nicht ändern. Sind Sie sicher, dass Sie diese Marketinggebühr &quot;${fee.designation}&quot; wirklich deaktivieren wollen?`
                        : `Wenn Sie diese Marketinggebühr aktivieren, kann sie Vertriebspartnern zugeteilt werden. Sind Sie sicher, dass Sie die Marketinggebühr &quot;${fee.designation}&quot; wirklich aktivieren wollen?`"
                    :title="`Marketinggebühr ${fee.status == 'active' ? 'deaktivieren' : 'aktivieren'}?`"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link } from "@inertiajs/inertia-vue3";
    import MenuBar from "../../../Components/Icons/MenuBar.vue";
    import { trans } from "laravel-vue-i18n";
    import { reactive, ref } from "@vue/reactivity";
    import ToggleUserStatus from "../../../Components/Status/ToggleUserStatus.vue";
    import UserStatus from "../../../Components/Status/UserStatus.vue";
    import { computed } from "@vue/reactivity";
    import Status from "../../../Components/Icons/Status.vue";

    const props = defineProps({
        fee: {
            type: Object,
            required: true,
        },
    });

    const showDropdown = ref(false);

    const option = reactive({
        expand: false,
    });

    const getData = (data) =>{
        if(data % 1 === 0){
            return parseInt(data);
        }
        return data;
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
