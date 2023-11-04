<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <h5 class="page-title">{{ $t("Neue Marketinggebühr hinzufügen") }}</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="create">
                <div class="form-field">
                    <div class="input-wrapper">
                        <label for="">{{ $t("Marketinggebühren-ID") }}*</label>
                        <p class="value">{{ latest_id }}</p>
                    </div>
                </div>
                 <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput
                            :placeholder="$t('Bezeichnung')"
                            :label="`${$t('Bezeichnung')}*`"
                            v-model="form.designation"
                            :error="form.errors.designation"
                            @clearError="form.errors.designation = null"
                            :show_error="form_error.designation.error"
                            @keypress="form_error.designation.error=false"
                            maxlength="41"
                        />
                        <div class="form-error" v-if="v$.designation.maxLength.$invalid">{{ v$.designation.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Marketinggebühr')}*`"
                            v-model="form.marketing_fee"
                            :error="form.errors.marketing_fee"
                            @clearError="form.errors.marketing_fee = null"
                            :show_error="form_error.marketing_fee.error"
                            @keypress="isNumber($event,form.marketing_fee);form_error.marketing_fee.error=false"
                        />
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper-new">
                        <label for="">{{ $t("Direkte Konsumenten") }}</label>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Senior Partner')}*`"
                            v-model="form.direct_consumers_senior_partner_share"
                            :error="form.errors.direct_consumers_senior_partner_share"
                            @clearError="form.errors.direct_consumers_senior_partner_share = null"
                            :show_error="form_error.direct_consumers_senior_partner_share.error"
                            @keypress="isNumber($event, form.direct_consumers_senior_partner_share);form_error.direct_consumers_senior_partner_share.error=false"
                        />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Jackpot')}*`"
                            v-model="form.direct_consumers_share_jackpot"
                            :error="form.errors.direct_consumers_share_jackpot"
                            @clearError="form.errors.direct_consumers_share_jackpot = null"
                            :show_error="form_error.direct_consumers_share_jackpot.error"
                            @keypress="isNumber($event, form.direct_consumers_share_jackpot);form_error.direct_consumers_share_jackpot.error=false"
                        />
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Gebühr CHalloMates')}*`"
                            v-model="form.direct_consumers_share_fee_challomates"
                            :error="form.errors.direct_consumers_share_fee_challomates"
                            @clearError="form.errors.direct_consumers_share_fee_challomates = null"
                            :show_error="form_error.direct_consumers_share_fee_challomates.error"
                            @keypress="isNumber($event, form.direct_consumers_share_fee_challomates);form_error.direct_consumers_share_fee_challomates.error=false"
                        />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil CHalloMates Marketing AG')}*`"
                            v-model="form.direct_consumers_share_challomates_marketing_ag"
                            :error="form.errors.direct_consumers_share_challomates_marketing_ag"
                            @clearError="form.errors.direct_consumers_share_challomates_marketing_ag = null"
                            :show_error="form_error.direct_consumers_share_challomates_marketing_ag.error"
                            @keypress="isNumber($event, form.direct_consumers_share_challomates_marketing_ag);form_error.direct_consumers_share_challomates_marketing_ag.error=false"
                        />
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper-new">
                        <label for="">{{ $t("Vertriebskonsumenten") }}</label>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Berater')}*`"
                            v-model="form.distribution_consumers_share_of_consultants"
                            :error="form.errors.distribution_consumers_share_of_consultants"
                            @clearError="form.errors.distribution_consumers_share_of_consultants = null"
                            :show_error="form_error.distribution_consumers_share_of_consultants.error"
                            @keypress="isNumber($event, form.distribution_consumers_share_of_consultants);form_error.distribution_consumers_share_of_consultants.error=false"
                        />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Jackpot')}*`"
                            v-model="form.distribution_consumers_share_jackpot"
                            :error="form.errors.distribution_consumers_share_jackpot"
                            @clearError="form.errors.distribution_consumers_share_jackpot = null"
                            :show_error="form_error.distribution_consumers_share_jackpot.error"
                            @keypress="isNumber($event, form.distribution_consumers_share_jackpot);form_error.distribution_consumers_share_jackpot.error=false"
                        />
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil Vertriebspartner')}*`"
                            v-model="form.distribution_consumers_proportion_of_sales_partners"
                            :error="form.errors.distribution_consumers_proportion_of_sales_partners"
                            @clearError="form.errors.distribution_consumers_proportion_of_sales_partners = null"
                            :show_error="form_error.distribution_consumers_proportion_of_sales_partners.error"
                            @keypress="isNumber($event, form.distribution_consumers_proportion_of_sales_partners);form_error.distribution_consumers_proportion_of_sales_partners.error=false"
                        />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`%`"
                            :label="`${$t('Anteil CHalloMates Marketing AG')}*`"
                            v-model="form.distribution_consumers_share_challomates_marketing_ag"
                            :error="form.errors.distribution_consumers_share_challomates_marketing_ag"
                            @clearError="form.errors.distribution_consumers_share_challomates_marketing_ag = null"
                            :show_error="form_error.distribution_consumers_share_challomates_marketing_ag.error"
                            @keypress="isNumber($event, form.distribution_consumers_share_challomates_marketing_ag);form_error.distribution_consumers_share_challomates_marketing_ag.error=false"
                        />
                    </div>
                </div>



                <div class="form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit" :disabled="form.processing">
                            {{ $t("Save") }}
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel
                            :target="route('marketing-fees.index')"
                            message="Registrierung der Marketinggebühr abbrechen?"
                            text="Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieser Marketinggebühr wirklich abbrechen wollen?"
                        />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import SwitchInput from "../../Components/Form/Switch.vue";
    import TextInput from "../../Components/Form/TextInput.vue";
    import { useForm } from "@inertiajs/inertia-vue3";
    import Cancel from "../../Components/Form/Cancel.vue";
    import { computed, inject } from "@vue/runtime-core";
    import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
    import PhoneInputWithCounryCode from "../../Components/PhoneInputWithCounryCode.vue";
    import useVuelidate from "@vuelidate/core";
    import { maxLength, required, helpers } from "@vuelidate/validators";
    import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
    import {reactive, ref} from "@vue/reactivity";

    const props = defineProps(["latest_id"]);
    const modal = inject("modal");
    const form_error = reactive({
        designation:{
            error : false
        },
        marketing_fee:{
            error : false
        },
        direct_consumers_senior_partner_share:{
            error : false
        },
        direct_consumers_share_jackpot:{
            error : false
        },
        direct_consumers_share_fee_challomates:{
            error:false
        },
        direct_consumers_share_challomates_marketing_ag:{
            error:false
        },
        distribution_consumers_share_of_consultants:{
            error:false
        },
        distribution_consumers_proportion_of_sales_partners:{
            error:false
        },
        distribution_consumers_share_jackpot:{
            error:false
        },
        distribution_consumers_share_challomates_marketing_ag:{
            error : false
        }
    });
    const rules = {
        designation: {
            required,
            maxLength: helpers.withMessage( 'Maximal 40 Zeichen möglich', maxLength(40) ),
        },
        marketing_fee:{
            required
        },
        direct_consumers_senior_partner_share:{
            required
        },
        direct_consumers_share_jackpot:{
            required
        },
        direct_consumers_share_fee_challomates:{
            required
        },
        direct_consumers_share_challomates_marketing_ag:{
            required
        },
        distribution_consumers_share_of_consultants:{
            required
        },
        distribution_consumers_proportion_of_sales_partners:{
            required
        },
        distribution_consumers_share_jackpot:{
            required
        },
        distribution_consumers_share_challomates_marketing_ag:{
            required
        }
    }

    const form = useForm({
        designation: "",
        marketing_fee: "",
        direct_consumers_senior_partner_share: "",
        direct_consumers_share_jackpot: "",
        direct_consumers_share_fee_challomates: "",
        direct_consumers_share_challomates_marketing_ag: "",
        distribution_consumers_share_of_consultants: "",
        distribution_consumers_proportion_of_sales_partners: "",
        distribution_consumers_share_jackpot: "",
        distribution_consumers_share_challomates_marketing_ag: ""

    })

    const v$ = useVuelidate(rules, form);

    const inviteable = computed(() => {
        if(form.designation == "" || form.marketing_fee == "" || form.direct_consumers_senior_partner_share == "" || form.direct_consumers_share_jackpot == "" || form.direct_consumers_share_fee_challomates == ""
          || form.direct_consumers_share_challomates_marketing_ag == "" || form.distribution_consumers_share_of_consultants == "" || form.distribution_consumers_proportion_of_sales_partners == "" || form.distribution_consumers_share_jackpot == "" || form.distribution_consumers_share_challomates_marketing_ag == "") {
            return false
        } else {
            return true
        }
    })

    const isNumber = (evt, formName) =>{
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode < 48 || charCode > 57 || formName.split('.')[1]?.length > 1) && charCode !== 46) {
            evt.preventDefault();;
        } else {
            return true;
        }
    }

    const isEqual = computed(()=>{
        if((parseFloat(form.marketing_fee) == parseFloat(form.direct_consumers_senior_partner_share)+parseFloat(form.direct_consumers_share_challomates_marketing_ag)+parseFloat(form.direct_consumers_share_fee_challomates)+parseFloat(form.direct_consumers_share_jackpot)) &&
            (parseFloat(form.marketing_fee) == parseFloat(form.distribution_consumers_proportion_of_sales_partners)+parseFloat(form.distribution_consumers_share_challomates_marketing_ag)+parseFloat(form.distribution_consumers_share_jackpot)+parseFloat(form.distribution_consumers_share_of_consultants))){
                return true
        }
        return false
    })


    const create = async () => {
        if(inviteable.value == false){
            Object.keys(rules).forEach( (terms) =>
                {
                    form_error[terms]['error'] = false;
                }
            );

            v$.value.$silentErrors.forEach( (terms) =>
                {
                    form_error[terms.$property]['error'] = true;
                }
            );

            if ( v$.value.$invalid ) {
                modal.show(FieldMissing,{
                    config: {
                        staticBackdrop: true,
                    },
                });
            }

            return !v$.value.$invalid;

        }
        else if(isEqual.value == false){
            form_error.marketing_fee.error = true;
            form_error.direct_consumers_senior_partner_share.error = true;
            form_error.direct_consumers_share_jackpot.error = true;
            form_error.direct_consumers_share_fee_challomates.error = true;
            form_error.direct_consumers_share_challomates_marketing_ag.error = true;
            form_error.distribution_consumers_share_of_consultants.error = true;
            form_error.distribution_consumers_proportion_of_sales_partners.error = true;
            form_error.distribution_consumers_share_jackpot.error = true;
            form_error.distribution_consumers_share_challomates_marketing_ag.error = true;
            modal.show(FieldMissing, {
                config: {
                    staticBackdrop: true,
                },
                props: {
                    title: 'Die Summe der Anteile entspricht nicht der Marketinggebühr',
                    description: 'Die Summe aller erfassten Prozente für die verschiedenen Anteile für Direkte sowie Vertriebskunden, muss der Marketinggebühr entsprechen. Bitte überprüfen und korrigieren Sie die erfassten Prozente entsprechend.'
                }
            });
        }else{
            if(form.designation.length < 41){
                form.post(route("marketing-fees.store"), {
                    forcedData: true,
                    onSuccess: () => {
                        form.reset();
                    },
                })
            }
        }
    }
</script>
