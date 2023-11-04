<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <h5 class="page-title">Neuer Vertrag hinzufügen</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="createOrRediect(false)">
                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="prefix_id">Vertrags-ID*</label>
                        <p class="value">{{ latest_id }}</p>
                    </div>
                    <div class="input-wrapper w-1/3"></div>
                    <div class="input-wrapper w-1/3"></div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="prefix_id" class="block mb-2">Vertragsnummer/-bezeichnung*</label>
                        <TextInput v-model="form.name" :error="
                          form.errors.name ||
                          form.errors['contract.name'] ||
                          v$.name.$errors[0]?.$message
                        " placeholder="Nummer oder Bezeichnung eingeben" @clearError="
                () => {
                  form.clearErrors('name', 'contract.name');
                  v$.name.$touch();
                }
              " :show_error="v$.name.$invalid ? true : false" maxLength="41" />
                    </div>
                    <div class="input-wrapper w-1/3" v-if="!sales_partner">
                        <label for="sales_partner_id" class="block mb-2">Vertriebspartner*</label>
                        <SearchSelect v-model="form.sales_partner_id" :options="sales_partners" valueKey="id"
                            placeholder="Vertriebspartner auswählen" labelKey="company_name"
                            :searchable="['prefix_id', 'company_name']" :error="form.errors.sales_partner_id"
                            @change="form.clearErrors('sales_partner_id')" />
                    </div>
                    <div class="input-wrapper w-1/3"></div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="sales_partner_id" class="block mb-2">Vertragslaufzeit*</label>
                        <RangedDatePicker label="Vertragslaufzeit*" v-model:startDate="form.contract_term_from"
                            v-model:endDate="form.contract_term_to" :error="
                              form.errors.contract_term_from ||
                              form.errors.contract_term_to ||
                              form.errors.contract_already_exists ||
                              form.errors['contract.contract_already_exists'] ||
                              form.errors['contract.contract_term_from'] ||
                              form.errors['contract.contract_term_to']
                            " @change="
                form.clearErrors(
                  'contract_term_from',
                  'contract_term_to',
                  'contract.contract_already_exists',
                  'contract.contract_term_from',
                  'contract.contract_term_to'
                )
              " />
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label for="package_id" class="block mb-2">Paket*</label>
                        <SearchSelect v-model="form.package_id" :options="packages" valueKey="id"
                            placeholder="Paket auswählen" labelKey="package_name"
                            :searchable="['package_prefix_id', 'package_name']"
                            :error="form.errors.package_id || form.errors['contract.package_id']"
                            @change="form.clearErrors('package_id', 'contract.package_id')">
                            <template #label="{ label }">
                                <div class="flex justify-between">
                                    <span>{{ label }}</span>
                                    <a v-if="form.package_id"
                                        :href="route('package.show', { package: form.package_id })"
                                        class="text-gray-3 text-16 underline underline-offset-2 font-ropa"
                                        target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                            <template #option="{ option, isSelected }">
                                <div class="item flex justify-between pl-5 pr-6 pt-[11px] pb-3"
                                    :class="{ 'bg-gray-corner': isSelected }">
                                    <p class="label text-tints-5 text-16 font-ropa">
                                        {{ option.package_name }}
                                    </p>
                                    <a :href="route('package.show', { package: option.id })"
                                        class="text-gray-3 text-16 underline underline-offset-2 font-ropa"
                                        target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                        </SearchSelect>
                    </div>
                    <div class="input-wrapper w-1/3"></div>
                </div>

                <div class="!mb-10 form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="marketing_fee_id" class="block mb-2">Marketinggebühr*</label>
                        <SearchSelect v-model="form.marketing_fee_id" :options="marketing_fees" valueKey="id"
                            :placeholder="marketing_fees_placeholder" labelKey="designation"
                            :searchable="['prefix_id', 'designation']" :error="
                              form.errors.marketing_fee_id || form.errors['contract.marketing_fee_id']
                            " @change="form.clearErrors('marketing_fee_id', 'contract.marketing_fee_id')" @showOptions="
                (value) =>
                  (marketing_fees_placeholder = value
                    ? 'Nach Marketinggebührnamen oder -ID suchen'
                    : 'Marketinggebühr auswählen')
              ">
                            <template #label="{ label }">
                                <div class="flex justify-between">
                                    <span>{{ label }}</span>
                                    <a v-if="form.marketing_fee_id" :href="
                                      route('marketing-fees.show', {
                                        marketing_fee: form.marketing_fee_id,
                                      })
                                    " class="text-gray-3 text-16 underline underline-offset-2 font-ropa" target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                            <template #option="{ option, isSelected }">
                                <div class="item flex justify-between pl-5 pr-6 pt-[11px] pb-3"
                                    :class="{ 'bg-gray-corner': isSelected }">
                                    <p class="label text-tints-5 text-16 font-ropa">
                                        {{ option.designation }}
                                    </p>
                                    <a :href="route('marketing-fees.show', { marketing_fee: option.id })"
                                        class="text-gray-3 text-16 underline underline-offset-2 font-ropa"
                                        target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                        </SearchSelect>
                    </div>
                    <div class="input-wrapper w-1/3"></div>
                    <div class="input-wrapper w-1/3"></div>
                </div>

                <div class="!mb-[18px] form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit" :disabled="form.processing">
                            Speichern
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel v-if="!sales_partner" :target="route('contract.index')"
                            message="Registrierung des Vertrages abbrechen?"
                            text="Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieses Vertrages wirklich abbrechen wollen?"
                            :backPrevUrl="true" />
                        <button v-if="sales_partner" class="btn-block challo__btn btn-outline-primary"
                            @click.prevent="cancelToSalesPartner">
                            Abbrechen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import Cancel from "../../../../Components/Form/Cancel.vue";
import TextInput from "../../../../Components/Form/TextInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { inject, useAttrs } from "@vue/runtime-core";
import FieldMissing from "../../../../Components/Modal/Content/FieldMissing.vue";
import useVuelidate from "@vuelidate/core";
import { maxLength, helpers } from "@vuelidate/validators";
import SearchSelect from "../../../../Components/Form/SearchSelect.vue";
import { ref } from "@vue/reactivity";
import Confirmation from "../../../../Components/Modal/Content/Confirmation.vue";
import RangedDatePicker from "../../../../Components/Form/DatePickers/RangedDatePicker.vue";

const attrs = useAttrs();
const props = defineProps({
    latest_id: {
        type: String,
        require: false,
    },
    sales_partners: {
        type: [Array, Object],
        required: true,
    },
    marketing_fees: {
        type: [Array, Object],
        required: true,
    },
    packages: {
        type: [Object, Array],
        required: true,
    },
});

const sales_partner = attrs.flash.data?.sales_partner;

const marketing_fees_placeholder = ref("Marketinggebühr auswählen");

const modal = inject("modal");

const form = useForm({
    name: sales_partner?.contract?.name ?? "",
    contract_term_from: sales_partner?.contract?.contract_term_from ?? "",
    contract_term_to: sales_partner?.contract?.contract_term_to ?? "",
    package_id: sales_partner?.contract?.package_id ?? "",
    marketing_fee_id: sales_partner?.contract?.marketing_fee_id ?? "",
    sales_partner_id: "",
});

const rules = {
    name: { maxLength: helpers.withMessage("Maximal 40 Zeichen möglich", maxLength(40)) },
};
const v$ = useVuelidate(rules, form);

const createOrRediect = (cancel = false) => {
    if (sales_partner) {
        form
            .transform((data) => ({ ...sales_partner, contract: !cancel ? data : null}))
            .post(route("contract.set-session.redirect"));
        return;
    }

    form.post(route("contract.store"), {
        onError: () => {
            if (form.errors.contract_already_exists) {
                modal.show(FieldMissing, {
                    props: {
                        title: "Vertragserstellung nicht möglich",
                        description:
                            "Für den selektierten Vertriebspartner besteht bereits ein Vertrag für ein, mehrere oder alle Tage der gewählten Vertragslaufszeitperiode. Bitte prüfen Sie die bestehenden Verträge und erfassen Sie eine Vertragslaufzeit die keine Daten enthält, welche bereits Teil eines anderen vorhandenen Vertrages sind.",
                    },
                    config: {
                        staticBackdrop: true,
                    },
                });
                return;
            }
            modal.show(FieldMissing, {
                config: {
                    staticBackdrop: true,
                },
            });
        },
    });
};


const cancelToSalesPartner = () => {
    modal.show(Confirmation, {
                config: {
                    staticBackdrop: props.staticBackdrop,
                },
                props: {
                    class: "top",
                    message: "Registrierung des Vertrages abbrechen?",
                    text: "Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieses Vertrages wirklich abbrechen wollen?",
                },
                events: {
                    yesClick: () => {
                        modal.hide()
                        createOrRediect(true)
                    },
                    noClick: () => {
                        modal.hide();
                    },
                },
            });
}
</script>

<style lang="scss" scoped>

</style>
