<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold">
        <div class="content">
            <div class="text-tints-5 w-[5%]" v-if="hasPermission('sales_partner.edit')">
                <CheckBox :value="sales_partner.id" :selected="selected"
                    @toggle="emit('toggleSelected', sales_partner.id)" classLists="border-gray-corner" />
            </div>
            <div class="ml-2 text-tints-5 w-[15%]">
                {{ formateDate(sales_partner.created_at) }}
            </div>
            <div class="text-tints-5 w-[15%]">
                <Link class="break-words" :href="route('sales-partner.show', { sales_partner: sales_partner.id })">
                {{ sales_partner.company_name }}
                </Link>
            </div>
            <div class="text-tints-5 w-[18%]">
                <span v-if="sales_partner.current_contract">
                    {{ formateDate(sales_partner.current_contract?.contract_term_from) }}-{{
                    formateDate(sales_partner.current_contract?.contract_term_to)
                    }}
                </span>
            </div>
            <div class="text-tints-5 w-[15%] break-words">
                {{ sales_partner.branch_category.name }}
            </div>
            <div class="text-tints-5 break-words" :class="[hasPermission('sales_partner.edit') ? 'w-[17%]' : 'w-[20%]']">
                {{
                `${sales_partner.consultant.first_name} ${sales_partner.consultant.last_name}`
                }}
            </div>
            <div class="text-tints-5 w-[10%]">
                <UserStatus :status="sales_partner.status" />
            </div>

            <div class="text-tints-5 relative" style="width: 5%">
                <button class="relative" type="button" aria-haspopup="true" aria-expanded="false"
                    @click="showDropdown = true">
                    <menu-bar />
                    <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)" v-show="showDropdown"
                        @click.stop="showDropdown = false">
                        <Link class="dropdown-item"
                            :href="route('sales-partner.show', { sales_partner: sales_partner.id })"
                            v-if="hasPermission('sales_partner.view')">
                        Details ansehen
                        </Link>
                        <Link class="dropdown-item"
                            :href="route('sales-partner.edit', { sales_partner: sales_partner.id })"
                            v-if="hasPermission('sales_partner.edit')">
                        Bearbeiten
                        </Link>
                        <button @click="updateStatus('inactive')" class="dropdown-item" v-if="
                          (sales_partner.status == 'active' || sales_partner.status == 'new') &&
                          hasPermission('sales_partner.edit')
                        ">
                            Deaktivieren
                        </button>
                        <button @click="updateStatus('active')" class="dropdown-item" v-if="
                          (sales_partner.status == 'inactive' || sales_partner.status == 'new') &&
                          hasPermission('sales_partner.edit')
                        ">
                            Aktivieren
                        </button>
                        <button class="dropdown-item" @click.stop="
                          () => {
                            showQrCodeModal = true;
                            showDropdown = false;
                          }
                        ">
                            QR Code ansehen
                        </button>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <QrCodePreview :text="
      JSON.stringify({
        company_name: sales_partner.company_name,
        company_id: sales_partner.id,
      })
    " v-if="showQrCodeModal" @close="showQrCodeModal = false" />
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { reactive, ref } from "@vue/reactivity";
import { inject } from "vue";
import CheckBox from "../../../Components/Form/CheckBox.vue";
import UserStatus from "../../../Components/Status/UserStatus.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
import QrCodePreview from "../../../Components/Modal/QrCodePreview.vue";

const emit = defineEmits(["toggleSelected"]);
const modal = inject("modal");
const props = defineProps({
    sales_partner: {
        type: Object,
        required: true,
    },

    selected: {
        type: Boolean,
        required: true,
    },
});

const showDropdown = ref(false);
const showQrCodeModal = ref(false);

const updateStatus = (status) => {
    const title = `Vertriebspartner ${status == "active" ? "aktivieren" : "deaktivieren"}?`;
    const description = `Sind Sie sicher, dass Sie den Vertriebspartner "${props.sales_partner.company_name
        }" wirklich ${status == "active" ? "aktivieren" : "deaktivieren"} wollen?`;
    modal.show(Confirmation, {
        props: {
            message: title,
            text: description,
        },
        events: {
            yesClick: () => {
                Inertia.put(
                    route("sales-partner.update-status", {
                        status: status,
                        sales_partner: props.sales_partner.id,
                    })
                );
            },
            noClick: () => modal.hide(),
        },
    });
};
</script>

<style lang="scss" scoped>
.dropdown {
    right: 0;
    top: 20px;
    width: max-content;

    .dropdown-item {
        border-bottom: 1px solid #8ed5f6;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 30px;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        color: #135f84;
        text-align: left;

        &:hover {
            font-weight: 600;
        }

        &:last-child {
            border-bottom: none;
        }
    }
}

.remaining {
    display: flex;
    flex-direction: column;

    .title {
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135f84;
        display: block;
    }

    .subtitle {
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}
</style>
