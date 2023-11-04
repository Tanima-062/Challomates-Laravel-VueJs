<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <h5 class="page-title">Neuer Company Consultant hinzufügen</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="create">

                <!--<pre>{{ rules }}</pre>-->
                <!--<pre>{{ v$.$silentErrors }}</pre>-->

                <div class="grid grid-cols-3 gap-4 gap-y-8">

                    <div class="input-wrapper col-span-3">
                        <label>Consultant-ID*</label>
                        <p class="value">{{ latest_id }}</p>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="Vorname eingeben"
                            label="Vorname*"
                            v-model="form.first_name"
                            :error="form.errors.first_name"
                            @clearError="form.errors.first_name = null; form_error.first_name.error = false"
                            :show_error="form_error.first_name.error"
                            maxlength="31"
                        />
                        <div class="form-error" v-if="v$.first_name.maxLength.$invalid">{{ v$.first_name.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="Nachname eingeben"
                            label="Nachname*"
                            v-model="form.last_name"
                            :error="form.errors.last_name"
                            @clearError="form.errors.last_name = null; form_error.last_name.error = false"
                            :show_error="form_error.last_name.error"
                            maxlength="31"
                        />
                        <div class="form-error" v-if="v$.last_name.maxLength.$invalid">{{ v$.last_name.maxLength.$message }}</div>
                    </div>

                    <div class="col-span-1"></div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="E-Mail eingeben"
                            label="E-Mail Adresse*"
                            v-model="form.email_address"
                            :error="form.errors.email_address"
                            @clearError="form.errors.email_address = null; form_error.email_address.error = false"
                            :show_error="form_error.email_address.error"
                            maxlength="40"
                            @blur="v$.email_address.$touch()"
                            @input="$emit('clearError')"
                        />
                        <div class="form-error" v-if="v$.email_address.$errors[0] && v$.email_address.email.$invalid">{{ v$.email_address.email.$message }}</div>
                        <div class="form-error" v-else-if="v$.email_address.$errors[0] && v$.email_address.email2.$invalid">{{ v$.email_address.email2.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <PhoneInputWithCountryCode
                            label="Telefonnummer*"
                            v-model:phone_number="form.phone_number"
                            v-model:country_code="form.country_iso_code"
                            :error="form.errors.phone_number"
                            @update:phone_number="resetPhoneNumberError"
                            :show_error="form_error.phone_number.error"
                            @updated="() => { form.clearErrors('phone_number', 'country_iso_code'); }"
                        />
                    </div>

                    <div class="col-span-1"></div>

                    <div class="input-wrapper">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>

                    <div class="input-wrapper">
                        <Cancel
                            :target="route('company-consultants.index')"
                            :staticBackdrop="true"
                            message="Registrierung des Company Consultants abbrechen?"
                            text="Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieses Company Consultants wirklich abbrechen wollen?"
                        />
                    </div>

                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import TextInput from "../../Components/Form/TextInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Cancel from "../../Components/Form/Cancel.vue";
import {computed, inject, nextTick, watch} from "@vue/runtime-core";
import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
import useVuelidate from "@vuelidate/core";
import {maxLength, required, helpers, decimal, integer, email, alpha} from "@vuelidate/validators";
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import PhoneInputWithCountryCode from "./Components/PhoneInputWithCountryCode";
import {reactive, ref} from "@vue/reactivity";

const props = defineProps(["latest_id"]);
const modal = inject("modal");
const form_error = reactive({
    first_name : {
        error: false
    },

    last_name : {
        error: false
    },

    email_address : {
        error: false
    },

    phone_number : {
        error: false
    }
} );
const form = useForm({
    first_name: "",
    last_name: "",
    email_address: "",
    country_iso_code: "ch",
    phone_number: "",
});
const email2 = (value) => (value.length > 0) ? value.match(/@[a-z0-9]{2,}(?:\.[a-z]{2,})/ig) : true;
const nameValidation = (value) => value.match(/^([a-zA-ZäöüÄÖÜß\s-]+)$/ig) ? true : false;
const isValidPhone = () => { return !form_error.phone_number.error };

const rules = {
    first_name: {
        required,
        nameValidation: nameValidation,
        maxLength: helpers.withMessage( "Maximal 30 Zeichen möglich", maxLength(30) ),
    },

    last_name: {
        required,
        nameValidation: nameValidation,
        maxLength: helpers.withMessage( "Maximal 30 Zeichen möglich", maxLength(30) ),
    },

    email_address: {
        required,
        email2: helpers.withMessage( 'Ungültiges E-Mail Format', email2 ),
        email: helpers.withMessage("Ungültiges E-Mail Format", email)
    },

    phone_number: {
        required
    }
};

const v$ = useVuelidate(rules, form);

const resetPhoneNumberError = (value) => {
    //form.errors.phone_number = value;
    form_error['phone_number']['error'] = (!value.toString().length);
};

let canSubmit = true;

const create = () => {
    if ( canSubmit ) {
        form.post( route("company-consultants.store"), {
            onBefore: (event) => {
                let errorTerms = [];
                let fieldMissing = false;

                v$.value.$silentErrors.forEach( (terms) => {
                    if ( terms.$validator === 'required' ) {
                        fieldMissing = true;
                    }
                    errorTerms.push(terms.$property);
                } );

                Object.keys(rules).forEach( (terms) => {
                    form_error[terms]['error'] = errorTerms.includes(terms);
                } );

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

            onError: (error) => {
                canSubmit = true;
                if ( error['email_address'] ) {
                    form.email_address = "";
                    form_error['email_address']['error'] = true;

                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            description: error['email_address'],
                        },
                    });
                } else if ( error['email'] ) {
                    form.email_address = "";
                    form_error['email_address']['error'] = true;

                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            description: error['email'],
                        },
                    });
                } else if ( error['email_address.unique'] ) {
                    form.email_address = "";
                    form_error['email_address']['error'] = true;

                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            title: "E-Mail Adresse bereits vorhanden",
                            description: error["email_address.unique"],
                        },
                    });
                } else if ( error['email.unique'] ) {
                    form.email_address = "";
                    form_error['email_address']['error'] = true;

                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            title: "E-Mail Adresse bereits vorhanden",
                            description: error["email.unique"],
                        },
                    });
                } else if ( error['phone_number'] ) {
                    //form.phone_number = "";
                    form_error['phone_number']['error'] = true;
                }
                return;
            },

            onSuccess: (res) => {
                form.reset();
            },

            onFinish: (event) => {
            },
        } );
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
    font-family: "Ropa Sans",serif;

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
