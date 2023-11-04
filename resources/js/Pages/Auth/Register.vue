<template>
    <div class="auth__container">
        <div class="auth__card">
            <div class="auth__card-header flex justify-center">
                <img class="w-[280px] h-[62px]" src="/images/logo.png" />
            </div>
            <div class="auth__card-body">
                <h3 class="auth__card-heading text-primary-2 text-35 mt-[55px] font-semibold">
                    Willkommen
                    <br>
                    <span class="break-all">{{ name }}</span>
                </h3>

                <p class="mt-[24px] font-normal text-18 text-tints-5">
                    <template v-if="type == 'challo_mates_admin'">
                        Bitte setzen und registrieren Sie Ihr persönliches Passwort um CHalloMates als Administrator
                        nutzen zu können.
                    </template>
                </p>
                <form @submit.prevent="register">
                    <h4 class="auth__card-form-title text-22 text-primary-2 mt-[64px]">
                        Registrierung
                    </h4>
                    <div>
                        <div class="auth__card--form-input mt-[40px]">
                            <div class="mb-[8px] font-normal text-primary-1 text-16 font-ropa">
                                Benutzername
                            </div>
                            <div class="text-tints-5 text-15 font-normal font-poppins">
                                {{ form.username }}
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <div class="label flex mb-[8px]">
                                <label class="form-label" style="color: #1aa1e4" for="password">Passwort*</label>
                                <PasswordTooltip :resetPassword="reset" />
                            </div>

                            <div class="password-input">
                                <PasswordInput
                                    name="password"
                                    id="password"
                                    placeholder="Passwort eingeben"
                                    v-model="form.password"
                                    :showLabel="false"
                                    :error="v$.password.$errors.length ? 'Das Passwort erfüllt die Passwortbedingungen nicht.' : undefined"
                                    @clearError="form.errors.password = null"
                                    :show_error="form_error.password.error"
                                    @blur="v$.password.$touch" />
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <div class="label flex mb-[8px]">
                                <label class="form-label" for="password-confirmation" style="color: #1aa1e4">
                                    Passwort bestätigen*
                                </label>
                            </div>

                            <div class="password-input">
                                <PasswordInput
                                    name="password_confirmation"
                                    id="password-confirmation"
                                    :showLabel="false" placeholder="Passwort erneut eingeben"
                                    v-model="form.password_confirmation"
                                    :error="(v$.password_confirmation.sameAsPassword.$invalid && v$.password_confirmation.$dirty) ? v$.password_confirmation.sameAsPassword.$message : undefined"
                                    :showError="true"
                                    @clearError="form.errors.password_confirmation = null"
                                    :show_error="form_error.password_confirmation.error"
                                    @blur="v$.password_confirmation.$touch" />
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <button
                                class="block w-full bg-primary-1 rounded-45 text-white text-15 font-semibold py-[8px] px-[151px]"
                                type="submit" :disabled="form.processing">
                                Registrieren
                            </button>
                        </div>
                        <div class="auth__card--form-input mt-[32px]" v-if="v$.password.$invalid">
                            <p class="messages__message mb-2 font-normal text-14 text-tints-5" v-if="v$.$anyDirty">
                                Das Passwort erfüllt folgende Bedingungen nicht:
                            </p>
                            <div class="messages mb-2 flex gap-2" v-for="error of v$.password.$errors"
                                :key="error.$uid">
                                <div class="messages__icon mt-[6px]">
                                    <failed-icon />
                                </div>
                                <div class="messages__text font-normal text-14 text-tints-5">
                                    <div class="messages__text">
                                        {{ error.$message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import AuthLayout from "../../Layouts/Auth.vue";
import TextInput from "../../Components/Form/TextInput.vue";

import { useForm } from "@inertiajs/inertia-vue3";
import EyeClose from "../../Components/Icons/EyeClose.vue";
import EyeOpen from "../../Components/Icons/EyeOpen.vue";
import FailedIcon from "../../Components/Icons/FailedIcon.vue";
import SuccessIcon from "../../Components/Icons/SuccessIcon.vue";
import { reactive } from "@vue/reactivity";
import PasswordTooltip from "../../Components/Form/PasswordTooltip.vue";
import PasswordInput from "../../Components/Form/PasswordInput.vue";
import useVuelidate from '@vuelidate/core'
import { required, minLength, helpers } from '@vuelidate/validators'
import FieldMissing from "../../Components/Modal/Content/FieldMissing";
import {inject} from "@vue/runtime-core";

export default {
    layout: AuthLayout,
    props: {
        username: {
            type: String,
            required: true,
        },

        name: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        reset: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        TextInput,
        EyeClose,
        EyeOpen,
        FailedIcon,
        SuccessIcon,
        PasswordTooltip,
        PasswordInput,
    },
    setup(props) {
        const modal = inject("modal");
        const option = reactive({
            password_type: "password",
            confirm_password_type: "password",
        });

        const form_error = reactive({
            password : {
                error: false
            },

            password_confirmation : {
                error: false
            },
        } );

        const form = useForm({
            password: "",
            password_confirmation: "",
            username: props.username,
            token: props.token,
        });

        const capitalLetter = helpers.withMessage('Mindestens 1 Grossbuchstabe', helpers.regex(/[A-Z]/));
        const smallLetter = helpers.withMessage('Mindestens 1 Kleinbuchstabe', helpers.regex(/[a-z]/));
        const number = helpers.withMessage('Mindestens 1 Zahl', helpers.regex(/[0-9]/));
        const specialCharacter = helpers.withMessage('Mindestens 1 Sonderzeichen', helpers.regex(/[@#$%^&+=!<>\*\?]/));
        const rules = {
            password: {
                required: helpers.withMessage('Das Passwortfeld ist erforderlich', required),
                minLength: helpers.withMessage('Mindestens 8 Zeichen', minLength(8)),
                capitalLetter,
                smallLetter,
                number,
                specialCharacter
            },
            password_confirmation: {
                required: helpers.withMessage('Das Feld zur Bestätigung des Passworts ist erforderlich', required),
                sameAsPassword: helpers.withMessage('Passwort und Bestätigung stimmen nicht überein.', () => form.password == form.password_confirmation),
            },
        }
        const v$ = useVuelidate(rules, form);

        const register = () => {
            form.clearErrors();
            form.post(route("register"), {
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

                onError: (err) => console.log(err),
            });
        };

        return {
            v$,
            form,
            form_error,
            option,
            register,
        };
    },
};
</script>

<style lang="scss" scoped>
.error {
    border-color: #c81717;
}

label {
    color: #323232;
}
</style>
