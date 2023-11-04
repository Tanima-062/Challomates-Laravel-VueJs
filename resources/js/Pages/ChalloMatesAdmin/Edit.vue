<template>
    <div class="challo__card challo__card-p6 rounded-b-3x">
        <Back :target="route('challo-mates-admins.index')" :show-modal="form.isDirty" />
        <h5 class="page-title">{{ $t("Edit CHalloMates Admin") }}</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="update">
                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <label for="">Erstellungsdatum*</label>
                        <p class="value">
                            {{
                            dayjs(challo_mates_admin.created_at)
                            .format("DD.MM.YYYY")
                            .toString()
                            }}
                        </p>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <label for="">Admin-ID*</label>
                        <p class="value">{{ challo_mates_admin.prefix_id }}</p>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText placeholder="Vorname eingeben" label="Vorname*"
                            v-model="form.first_name"
                            :error="form.errors.first_name || (v$.first_name.maxLength.$invalid ? v$.first_name.maxLength.$message : undefined)"
                            @clearError="form.errors.first_name = null; form_error.first_name.error = false"
                            :show_error="form_error.first_name.error" maxlength="31" />
                        <div class="form-error" v-if="v$.first_name.maxLength.$invalid">{{
                        v$.first_name.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText placeholder="Nachname eingeben" label="Nachname*"
                            v-model="form.last_name"
                            :error="form.errors.last_name || (v$.last_name.maxLength.$invalid ? v$.last_name.maxLength.$message : undefined)"
                            @clearError="form.errors.last_name = null; form_error.last_name.error = false"
                            :show_error="form_error.last_name.error" maxlength="31" />
                        <div class="form-error" v-if="v$.last_name.maxLength.$invalid">{{
                        v$.last_name.maxLength.$message }}</div>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText placeholder="E-Mail eingeben" label="E-Mail Adresse*"
                            v-model="form.email"
                            :error="form.errors.email || (v$.email.email2.$invalid ? v$.email.email2.$message : undefined)"
                            @clearError="form.errors.email = null; form_error.email.error = false"
                            :show_error="form_error.email.error" maxlength="40" @blur="v$.email.$touch()"
                            @input="$emit('clearError')" />
                        <div class="form-error" v-if="v$.email.$errors[0] && v$.email.email.$invalid">{{
                        v$.email.email.$message }}</div>
                        <div class="form-error" v-else-if="v$.email.$errors[0] && v$.email.email2.$invalid">{{
                        v$.email.email2.$message }}</div>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <PhoneInputWithCountryCode label="Telefonnummer*" v-model:phone_number="form.phone_number"
                            v-model:country_code="form.country_iso_code" :error="form.errors.phone_number"
                            @update:phone_number="resetPhoneNumberError" :show_error="form_error.phone_number.error"
                            @updated="() => { form.clearErrors('phone_number', 'country_iso_code'); }" />
                    </div>
                </div>

                <div class="form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel :target="route('challo-mates-admins.index')" :show-modal="form.isDirty"
                            message="Änderungen verwerfen?"
                            text="Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?" />
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
import Back from "../../Components/Form/Back.vue";
import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import PhoneInputWithCountryCode from "./Components/PhoneInputWithCountryCode";
import dayjs from "dayjs";

import useVuelidate from "@vuelidate/core";
import { maxLength, email, helpers, required } from "@vuelidate/validators";
import { reactive } from "@vue/reactivity";

const props = defineProps(["languages", "challo_mates_admin"]);
const modal = inject("modal");
const form_error = reactive(
    {
        first_name: {
            error: false
        },

        last_name: {
            error: false
        },

        email: {
            error: false
        },

        phone_number: {
            error: false
        },
    }
);

const form = useForm({
    first_name: props.challo_mates_admin.first_name,
    last_name: props.challo_mates_admin.last_name,
    email: props.challo_mates_admin.email,
    country_iso_code: props.challo_mates_admin.country_iso_code,
    phone_number: props.challo_mates_admin.phone_number,
    _method: "PUT",
});

const inviteable = computed(() => {
    if (form.first_name == "" || form.last_name == "" || form.email == "" || form.country_iso_code == "" || form.phone_number == "") {
        return false
    } else {
        return true
    }
})

const email2 = (value) => { return (value.length > 0) ? value.match(/@[a-z0-9]{2,}(?:\.[a-z]{2,})/ig) : true };
const isValidPhone = () => { return !form_error.phone_number.error };
const nameValidation = (value) => value.match(/^([a-zA-ZäöüÄÖÜß\s-]+)$/ig) ? true : false;

const rules = {
    first_name: {
        nameValidation: nameValidation,
        maxLength: helpers.withMessage(
            "Maximum 30 characters possible",
            maxLength(30)
        ),
    },
    last_name: {
        nameValidation: nameValidation,
        maxLength: helpers.withMessage(
            "Maximum 30 characters possible",
            maxLength(30)
        ),
    },
    email: {
        required,
        email2: helpers.withMessage('Ungültiges E-Mail Format', email2),
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

const update = () => {
    form.post(
        route("challo-mates-admins.update", {
            challo_mates_admin: props.challo_mates_admin.id,
        }),
        {
            forceFormData: true,

            onBefore: (event) => {
                let errorTerms = [];

                v$.value.$silentErrors.forEach((terms) => {
                    errorTerms.push(terms.$property);
                });

                Object.keys(rules).forEach((terms) => {
                    form_error[terms]['error'] = errorTerms.includes(terms);
                });

                if (v$.value.$invalid) {
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
                            description: error["email.unique"],
                        },
                        config: {
                            staticBackdrop: true,
                        }
                    });
                    form.clearErrors('email.unique')
                    form.email = "";
                } else if (error['phone_number']) {
                    //form.phone_number = "";
                    form_error['phone_number']['error'] = true;
                }
                return
            },
        }
    );
};
</script>

<style lang="scss" scoped>
.value {
    /* Ropa/P/16 */
    font-family: "Poppins", serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 17px;

    /* Gray / 3 */
    color: #787878;
}
</style>
