<template>
    <div class="challo__card py-10 px-6 mt-2">
        <Back :target="route('contract.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-12">Vertragdetails</h5>

        <div class="challo__card__body">

            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">
                        Erstellungsdatum
                    </h5>
                    <span class="detail__value">
                        {{  dayjs(contract.created_at).format("DD.MM.YYYY").toString()  }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">
                        Vertrags-ID
                    </h5>
                    <span class="detail__value">
                        {{  contract.prefix_id  }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">
                        <!-- Status -->
                        Status
                    </h5>
                    <span class="detail__value">
                        {{  status  }}
                    </span>
                </div>
            </div>
            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3 flex flex-col gap-5">
                    <h5 class="detail__label">Vertragsnummer/-bezeichnung</h5>
                    <span class="detail__value">
                        {{  contract.name  }}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label">Vertriebspartner</h5>
                    <a target="_blank" :href="route('sales-partner.show', { sales_partner: contract.sales_partner.id })"
                        class="detail__value underline underline-offset-4">{{  contract.sales_partner.company_name  }}</a>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-4">
                        Vertragslaufzeit
                    </h5>
                    <span class="detail__value">
                        {{  dayjs(contract.contract_term_from).format('DD.MM.YYYY')  }}
                        -
                        {{  dayjs(contract.contract_term_to).format('DD.MM.YYYY')  }}
                    </span>
                </div>
            </div>

            <div class="detail__row mb-10 gap-0">
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Paket</h5>
                    <a target="_blank" :href="route('package.show', { package: contract.package.id })"
                        class="detail__value underline underline-offset-4">{{  contract.package.package_name  }}</a>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-4">Marketinggebühr</h5>
                    <a target="_blank"
                        :href="route('marketing-fees.show', { marketing_fee: contract.marketing_fee.id })"
                        class="detail__value underline underline-offset-4">{{  contract.marketing_fee.designation  }}</a>
                </div>
            </div>

            <button v-if="hasPermission('contract.edit')" @click="editContract"
                class="flex gap-[9px] h-10 w-[416px] items-center justify-center bg-primary-1 text-white font-poppins text-15 font-semibold rounded-[37px]">
                <Pen />
                Bearbeiten
            </button>
        </div>
    </div>
</template>

<script setup>
import Back from '../../../../Components/Form/Back.vue';
import dayjs from 'dayjs';
import Pen from '../../../../Components/Icons/Pen.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { computed } from '@vue/reactivity';
import { Inertia } from '@inertiajs/inertia';
import { inject } from 'vue';
import Confirmation from '../../../../Components/Modal/Content/Confirmation.vue';

const modal = inject('modal')
const props = defineProps({
    contract: {
        type: Object,
        required: true,
    },
});


const status = computed(() => {
    const status_de = { 'active': 'Aktiv', 'inactive': 'Inaktiv', 'pending': 'Anstehend', 'new': 'Neu', 'canceled': 'Abgebrochen', 'expired': 'Abgelaufen' }
    return status_de[props.contract.current_status] || null;
})

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
