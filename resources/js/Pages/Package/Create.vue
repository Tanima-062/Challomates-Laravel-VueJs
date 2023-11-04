<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <h5 class="page-title">Neues Paket hinzufügen</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="create">

                <!--<pre>{{ rules }}</pre>-->
                <!--<pre>{{ v$.$silentErrors }}</pre>-->

                <div class="grid grid-cols-3 gap-4 gap-y-8">

                    <div class="input-wrapper col-span-3">
                        <label>Paket-ID*</label>
                        <p class="value">{{ latest_id }}</p>
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            placeholder="Name eingeben"
                            label="Name*"
                            v-model="form.package_name"
                            :error="form.errors.package_name"
                            @clearError="form.errors.package_name = null; form_error.package_name.error = false;"
                            :show_error="form_error.package_name.error"
                            maxlength="41"
                        />
                        <div class="form-error" v-if="v$.package_name.maxLength.$invalid">{{ v$.package_name.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper col-span-2">
                        <!--                        <label class="input-label block mb-[8px]">Generelle Dienstleistungen*</label>
                                                <vSelect
                                                    multiple
                                                    placeholder="Dienstleistungen erfassen"
                                                    :options="props.services"
                                                    :reduce="(option) => option.id"
                                                    label="service_name"
                                                    :class="[{ error: error, 'input-field-error' : form_error.services.error || form.errors.services}]"
                                                    class="service_select2"
                                                    v-model="form.services"
                                                />-->

                        <TextInput
                            placeholder="Dienstleistungen erfassen"
                            label="Dienstleistungen"
                            v-model="form.services"
                            :error="form.errors.services"
                            @clearError="form.errors.services = null; form_error.services.error = false;"
                            :show_error="form_error.services.error"
                            maxlength="101"
                        />
                        <div class="form-error" v-if="v$.services.maxLength.$invalid">{{ v$.services.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            label="Einmalige Anmeldegebühr*"
                            buttonPrefixText="CHF"
                            classLists="h-10"
                            placeholder="00.00"
                            v-model="form.registration_fee"
                            :error="form.errors.registration_fee"
                            @clearError="form.errors.registration_fee = null; form_error.registration_fee.error = false;"
                            :show_error="form_error.registration_fee.error"
                        />
                        <!--                        <div class="form-error" v-if="v$.registration_fee.decimal.$invalid">{{ v$.registration_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            label="Preis im 1. Jahr*"
                            buttonPrefixText="CHF"
                            buttonSuffixText="/ Jahr"
                            classLists="h-10"
                            placeholder="00.00"
                            v-model="form.first_year_fee"
                            :error="form.errors.first_year_fee"
                            @clearError="form.errors.first_year_fee = null; form_error.first_year_fee.error = false;"
                            :show_error="form_error.first_year_fee.error"
                        />
                        <!--<div class="form-error" v-if="v$.first_year_fee.maxLength.$invalid">{{ v$.first_year_fee.maxLength.$message }}</div>-->
                        <!--                        <div class="form-error" v-if="v$.first_year_fee.decimal.$invalid">{{ v$.first_year_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            label="Preis ab 2. Jahr*"
                            buttonPrefixText="CHF"
                            buttonSuffixText="/ Jahr"
                            classLists="h-10"
                            placeholder="00.00"
                            v-model="form.yearly_fee"
                            :error="form.errors.yearly_fee"
                            @clearError="form.errors.yearly_fee = null; form_error.yearly_fee.error = false;"
                            :show_error="form_error.yearly_fee.error"
                        />
                        <!--                        <div class="form-error" v-if="v$.yearly_fee.decimal.$invalid">{{ v$.yearly_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            label="Coin Faktor*"
                            buttonPrefixText="CHF"
                            classLists="h-10"
                            placeholder="Zahl eingeben"
                            v-model="form.coin_factor"
                            :error="form.errors.coin_factor"
                            @clearError="form.errors.coin_factor = null; form_error.coin_factor.error = false;"
                            :show_error="form_error.coin_factor.error"
                        />
                        <!--                        <div class="form-error" v-if="v$.coin_factor.decimal.$invalid">{{ v$.coin_factor.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            label="Beratung*"
                            classLists="h-10"
                            placeholder="Beratung erfassen"
                            v-model="form.consulting"
                            :error="form.errors.consulting"
                            @clearError="form.errors.consulting = null; form_error.consulting.error = false;"
                            :show_error="form_error.consulting.error"
                            maxlength="41"
                        />
                        <div class="form-error" v-if="v$.consulting.maxLength.$invalid">{{ v$.consulting.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            label="Booster*"
                            :buttonSuffixText="'/ '.concat( 'Monat' )"
                            classLists="h-10"
                            placeholder="Zahl eingeben"
                            v-model="form.booster"
                            :error="form.errors.booster"
                            @clearError="form.errors.booster = null; form_error.booster.error = false;"
                            :show_error="form_error.booster.error"
                        />
                        <!--                        <div class="form-error" v-if="v$.booster.integer.$invalid">{{ v$.booster.integer.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            label="Anzahl Registrationen*"
                            classLists="h-10"
                            placeholder="Zahl eingeben"
                            v-model="form.number_of_registration"
                            :error="form.errors.number_of_registration"
                            @clearError="form.errors.number_of_registration = null; form_error.number_of_registration.error = false;"
                            :show_error="form_error.number_of_registration.error"
                        />
                        <!--                        <div class="form-error" v-if="v$.number_of_registration.integer.$invalid">{{ v$.number_of_registration.integer.$message }}</div>-->
                    </div>

                    <div class="col-span-2"></div>

                    <div class="input-wrapper">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>

                    <div class="input-wrapper">
                        <Cancel
                            :target="route('package.index')"
                            message="Registrierung des Pakets abbrechen?"
                            text="Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieses Pakets wirklich abbrechen wollen?"
                        />
                    </div>

                </div>

                <div class="grid grid-cols-3 gap-4 gap-y-8"></div>

            </form>
        </div>
    </div>
</template>

<script setup>
import TextInput from "../../Components/Form/TextInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Cancel from "../../Components/Form/Cancel.vue";
import { computed, inject } from "@vue/runtime-core";
import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
import useVuelidate from "@vuelidate/core";
import {maxLength, required, helpers, decimal, integer} from "@vuelidate/validators";
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import {reactive, ref} from "@vue/reactivity";
import {debounce as _debounce} from "lodash";
import vSelect from "vue-select";

const props = defineProps(["languages", "latest_id", "services"]);
const modal = inject("modal");
const form_error = reactive({
    package_name : {
        error: false
    },

    services : {
        error: false
    },

    registration_fee : {
        error: false
    },

    first_year_fee : {
        error: false
    },

    yearly_fee : {
        error: false
    },

    coin_factor : {
        error: false
    },

    consulting : {
        error: false
    },

    booster : {
        error: false
    },

    number_of_registration : {
        error: false
    }
} );
const form = useForm({
    package_name: "",
    services: "",
    registration_fee: "",
    first_year_fee: "",
    yearly_fee: "",
    coin_factor: "",
    consulting: "",
    booster: "",
    number_of_registration: ""
});
const rules = {
    package_name: {
        required,
        maxLength: helpers.withMessage( 'Maximal 40 Zeichen möglich', maxLength(40) ),
    },

    services: {
        maxLength: helpers.withMessage( 'Maximal 100 Zeichen möglich', maxLength(100) ),
    },

    registration_fee: {
        required,
        decimal
    },

    first_year_fee: {
        required,
        decimal
    },

    yearly_fee: {
        required,
        decimal
    },

    coin_factor: {
        required,
        decimal,
    },

    consulting: {
        required,
        maxLength: helpers.withMessage( 'Maximal 40 Zeichen möglich', maxLength(40) ),
    },

    booster: {
        required,
        integer,
    },

    number_of_registration: {
        required,
        integer,
    }
};

const v$ = useVuelidate(rules, form);

const hasServices = (event) => {
    //console.log(form.services.value);
};

let canSubmit = true;

const create = () => {
    if ( canSubmit ) {
        form.post(route("package.store"), {
            forcedData: true,

            onBefore:(event) => {
                let errorTerms = [];
                let fieldMissing = false;

                v$.value.$silentErrors.forEach( (terms) =>
                    {
                        if ( terms.$validator === 'required' ) {
                            fieldMissing = true;
                        }
                        errorTerms.push(terms.$property);

                        //form_error[terms.$property]['error'] = true;
                    }
                );

                Object.keys(rules).forEach( (terms) =>
                    {
                        form_error[terms]['error'] = errorTerms.includes(terms);
                        //form_error[terms]['error'] = false;
                    }
                );

                if ( v$.value.$invalid && fieldMissing ) {
                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                    });
                }

                return !v$.value.$invalid;
            },

            onStart: () => {
                canSubmit = false;
            },

            onSuccess: () => {
                form.reset();
            },

            onError: (err) => {
                canSubmit = true;
                if (err["email.unique"]) {
                    modal.show(FieldMissing, {
                        props: {
                            title: "E-mail address already exists",
                            description: err["email.unique"],
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
                    last_name: {
                        required,
                        maxLength: helpers.withMessage(
                            "Maximum 30 characters possible",
                            maxLength(29)
                        ),
                    },
                });
            },
        })
    }
}

</script>

<style lang="scss">

.service_select2 {
    width: 100%;
    --tw-bg-opacity: 1;
    --tw-text-opacity: 1;
    color: rgb(120 120 120/var(--tw-text-opacity));
    outline: 2px solid transparent;
    outline-offset: 2px;
    position: relative;
    font-family: "Ropa Sans";

    .vs__dropdown-toggle {
        background-color: rgb(255 255 255 / var(--tw-bg-opacity));
        border-radius: 9999px;
        height: 2.5rem;
        border: 1px solid #C2C5C6;
        padding-left: 1.25rem;
        z-index: 10;
        position: relative;

        .vs__actions {
            padding-right: 1.5rem;
        }
    }

    .vs__dropdown-menu {
        position: absolute;
        padding-top: 2rem;
        top: 0.75rem;
        z-index: 9;
    }

    &.input-field-error {
        .vs__dropdown-toggle {
            border: 1px solid #ff0000;
        }
    }
}
</style>
