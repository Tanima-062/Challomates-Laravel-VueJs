<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold">
        <div class="content gap-[10px]">
            <div class="text-tints-5 w-[20%]">
                {{ dayjs(contract.created_at).format('DD.MM.YYYY').toString() }}
            </div>
            <div class="text-tints-5 w-[30%]">
                <Link class="break-words" :href="route('contract.show', { contract: contract.id })">
                {{ contract.name }}
                </Link>
            </div>
            <div class="text-tints-5 w-[17%]">
                <Link class="break-words" :href="route('sales-partner.show', { sales_partner: contract.sales_partner.id })">
                {{ contract.sales_partner.company_name }}
                </Link>
            </div>
            <div class="text-tints-5 w-[17%]">
                {{ dayjs(contract.contract_term_from).format('DD.MM.YYYY').toString() }}
                -
                {{ dayjs(contract.contract_term_to).format('DD.MM.YYYY').toString() }}
            </div>
            <div class="text-tints-5 w-[11%]">
                <UserStatus :status="contract.current_status" />
            </div>

            <div class="relative" style="width: 5%">
                <button class="relative" type="button" aria-haspopup="true" aria-expanded="false"
                    @click="showDropdown = true">
                    <menu-bar />
                    <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)" v-show="showDropdown"
                        @click.stop="showDropdown = false">
                        <Link class="dropdown-item" :href="route('contract.show', { contract: contract.id })"
                            v-if="hasPermission('contract.view')">
                        Details ansehen
                        </Link>
                        <button class="dropdown-item" @click="editContract"
                            v-if="hasPermission('contract.edit') && contract.current_status !== 'expired'">
                            Bearbeiten
                        </button>
                        <button @click="cancelContract" class="dropdown-item"
                            v-if="hasPermission('contract.edit') && (contract.current_status !== 'expired' && contract.current_status !== 'canceled')">Abbrechen</button>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, inject } from 'vue';
import dayjs from 'dayjs';
import UserStatus from '../../../Components/Status/UserStatus.vue';
import { Link } from '@inertiajs/inertia-vue3';
import MenuBar from '../../../Components/Icons/MenuBar.vue';
import Confirmation from '../../../Components/Modal/Content/Confirmation.vue';
import { Inertia } from '@inertiajs/inertia';
const props = defineProps({
    contract: {
        type: Object,
        required: true,
    }
})
const modal = inject('modal')
const showDropdown = ref(false)

const cancelContract = () => {
    modal.show(Confirmation, {
        props: {
            message: "Vertrag abbrechen?",
            text: `Sind Sie sicher, dass Sie den Vertrag "${props.contract.name}" wirklich abbrechen wollen?`
        },
        events: {
            yesClick: () => { Inertia.put(route('contract.cancel', { contract: props.contract.id })); modal.hide() },
            noClick: () => modal.hide()
        }
    })
}

const editContract = () => {
    modal.show(Confirmation, {
        props: {
            message: "Vertragsdaten ändern?",
            text: "Wenn Sie Vertragsdaten ändern, muss sichergestellt sein, dass ein entsprechender Vertrag mit dem betroffenen Vertriebspartner vereinbart und schriftlich vorhanden ist. Sind Sie sicher, dass Sie die Vertragsdaten wirklich ändern wollen und somit bestätigen, dass ein entsprechender Vertrag vorhanden ist?",
        },
        events: {
            yesClick: () => { Inertia.visit(route('contract.edit', { contract: props.contract.id })); modal.hide() },
            noClick: () => modal.hide()
        }
    })
}
</script>

<style lang="scss">
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
