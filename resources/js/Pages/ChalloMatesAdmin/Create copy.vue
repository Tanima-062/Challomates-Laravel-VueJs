<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <h5 class="page-title">Neuer CHalloMates Admin hinzufügen</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="create">
                <div class="form-field">
                    <div class="input-wrapper">
                        <label for="">Admin-ID*</label>
                        <p class="value">{{  latest_id  }}</p>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="Vorname eingeben"
                            label="Vorname*"
                            v-model="form.first_name"
                            :error="form.errors.first_name"
                            @clearError="form.errors.first_name = null"
                            :show_error="form_error.first_name.error"
                            maxlength="31"
                        />
                        <div class="form-error" v-if="v$.first_name.maxLength.$invalid">{{ v$.first_name.maxLength.$message }}</div>
                    </div>


                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="Nachname eingeben"
                            label="Nachname*"
                            v-model="form.last_name"
                            :error="form.errors.last_name"
                            @clearError="form.errors.last_name = null"
                            :show_error="form_error.last_name.error"
                            maxlength="31"
                        />
                        <div class="form-error" v-if="v$.last_name.maxLength.$invalid">{{ v$.last_name.maxLength.$message }}</div>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="E-Mail eingeben"
                            label="E-Mail Adresse*"
                            v-model="form.email"
                            :error="form.errors.email"
                            @clearError="form.errors.email = null"
                            :show_error="form_error.email.error"
                            maxlength="40"
                            @blur="v$.email.$touch()"
                            @input="$emit('clearError')"
                        />
                        <div class="form-error" v-if="v$.email.$errors[0] && v$.email.email.$invalid">{{ v$.email.email.$message }}</div>
                        <div class="form-error" v-else-if="v$.email.$errors[0] && v$.email.email2.$invalid">{{ v$.email.email2.$message }}</div>
                    </div>

                    <div class="input-wrapper w-1/3">
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
                </div>

                <div class="form-field" style="">
                    <div class="input-wrapper flex items-center">
                        <SwitchInput :statusText="false" :value="form.invitation" :clickable="inviteable"
                            v-model="form.invitation" />
                        <p class="switch__input--label">
                            E-Mail-Einladung an CHalloMates Admin senden
                        </p>
                    </div>
                </div>

                <div class="form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel :target="route('challo-mates-admins.index')"
                            message="Registrierung des CHalloMates Admins abbrechen?"
                            text="Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie die Registrierung dieses CHalloMates Admins wirklich abbrechen wollen?" />
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
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import PhoneInputWithCountryCode from "./Components/PhoneInputWithCountryCode.vue";
import useVuelidate from "@vuelidate/core";
import {maxLength, email as ValidEmail, helpers, email, required} from "@vuelidate/validators";
import {reactive, ref} from "@vue/reactivity";

const props = defineProps(["languages", "latest_id"]);
const modal = inject("modal");

const form_error = reactive({
    first_name : {
        error: false
    },

    last_name : {
        error: false
    },

    email : {
        error: false
    },

    phone_number : {
        error: false
    }
} );

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    country_iso_code: "ch",
    phone_number: "",
    invitation: false,
})

const inviteable = computed(() => {
    if (form.first_name == "" || form.last_name == "" || form.email == "" || form.country_iso_code == "" || form.phone_number == "") {
        return false
    } else {
        return true
    }
})

const email2 = (value) => { return (value.length > 0) ? value.match(/@[a-z0-9]{2,}(?:\.[a-z]{2,})/ig) : true };
const nameValidation = (value) => value.match(/^([a-zA-Z\s-]+)$/ig) ? true : false;
const isValidPhone = () => { return !form_error.phone_number.error };

const rules = {
    first_name: {
        nameValidation: nameValidation,
        maxLength: helpers.withMessage(
            "Maximal 30 Zeichen möglich",
            maxLength(30)
        ),
    },
    last_name: {
        nameValidation: nameValidation,
        maxLength: helpers.withMessage(
            "Maximal 30 Zeichen möglich",
            maxLength(30)
        ),
    },
    email: {
        required,
        email2: helpers.withMessage( 'Ungültiges E-Mail Format', email2 ),
        email: helpers.withMessage("Ungültiges E-Mail Format", email)
    },

    phone_number: {
        required,
        //isValidPhone
    }
};

const v$ = useVuelidate(rules, form);

const resetPhoneNumberError = (value) => {
    //form.errors.phone_number = value;
    form_error['phone_number']['error'] = (!value.toString().length);
};

const create = async () => {
    form.post(route("challo-mates-admins.store"), {
        forcedData: true,

        onBefore:(event) => {
            let errorTerms = [];

            v$.value.$silentErrors.forEach( (terms) => {
                errorTerms.push(terms.$property);
            } );

            Object.keys(rules).forEach( (terms) => {
                form_error[terms]['error'] = errorTerms.includes(terms);
            } );

            if ( v$.value.$invalid ) {
                modal.show(FieldMissing, {
                    config: {
                        staticBackdrop: true,
                    },
                });
            }

            return !v$.value.$invalid;
        },

        onSuccess: () => {
            form.reset();
        },

        onError: (error) => {
            if (error["email.unique"]) {
                modal.show(FieldMissing, {
                    props: {
                        title: "E-Mail Adresse bereits vorhanden",
                        description: err["email.unique"],
                    },
                    config: {
                        staticBackdrop: true,
                    }
                });
                form.clearErrors('email.unique')
                form.email = "";
            } else if ( error['phone_number'] ) {
                //form.phone_number = "";
                form_error['phone_number']['error'] = true;
            }
            return
        }
    })
}
</script>

<style lang="scss" scoped>
</style>
