<template>
    <div class="auth__container">
        <div class="auth__card">
            <div class="auth__card-header flex justify-center">
                <img class="w-[280px] h-[62px]" src="/images/logo.png" />
            </div>
            <div class="auth__card-body">
                <h3 class="auth__card-heading text-primary-2 text-35 mt-[55px] font-semibold">
                    Passwort zurücksetzen
                </h3>
                <p class="mt-[24px] font-normal text-18 text-tints-5">
                    Bitte geben Sie Ihr neues Passwort ein und bestätigen dieses, um Ihr Passwort zurückzusetzen. Aus
                    Sicherheitsgründen können keine bereits zuvor benutzten Passwörter verwendet werden.
                </p>
                <form @submit.prevent="update">
                    <div class="">
                        <div class="auth__card--form-input mt-[64px]">
                            <div class="mb-[8px] font-normal text-primary-1 text-16 font-ropa">
                                Benutzername
                            </div>
                            <div class="text-tints-5 text-15 font-normal font-poppins">
                                {{ form.username }}
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <div class="label flex mb-[8px]">
                                <label class="form-label  font-normal text-primary-1 text-16 font-ropa"
                                    for="password">Passwort*</label>
                                <PasswordTooltip :resetPassword="reset" />
                            </div>

                            <div class="password-input">
                                <PasswordInput name="password" id="password" :showLabel="false" v-model="form.password"
                                    placeholder="Passwort eingeben"
                                    :error="v$.password.$errors.length ? 'Das Passwort erfüllt die Passwortbedingungen nicht.' : form.errors.password || undefined"
                                    @blur="v$.password.$touch" />
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <div class="label flex mb-[8px]">
                                <label class="form-label  font-normal text-primary-1 text-16 font-ropa"
                                    for="password-confirmation">Passwort bestätigen*</label>
                            </div>

                            <div class="password-input">
                                <PasswordInput name="password_confirmation" id="password-confirmation"
                                    :showLabel="false" placeholder="Passwort erneut eingeben"
                                    v-model="form.password_confirmation"
                                    :error="(v$.password_confirmation.sameAsPassword.$invalid && v$.password_confirmation.$dirty) ? true : undefined"
                                    :showError="false" @blur="v$.password_confirmation.$touch" />

                                <div v-if="v$.password_confirmation.sameAsPassword.$invalid && v$.password_confirmation.$dirty"
                                    class="form-error">
                                    {{ v$.password_confirmation.sameAsPassword.$message }}
                                </div>
                            </div>
                        </div>
                        <div class="auth__card--form-input mt-[32px]">
                            <button
                                class="block w-full bg-primary-1 rounded-45 text-white text-15 font-semibold py-[8px] px-[151px]"
                                style="padding: 8px 96px" type="submit" :disabled="form.processing">
                                Passwort zurücksetzen
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
import { reactive } from "@vue/reactivity";
import SuccessIcon from "../../Components/Icons/SuccessIcon.vue";
import PasswordTooltip from "../../Components/Form/PasswordTooltip.vue";
import PasswordInput from "../../Components/Form/PasswordInput.vue";

import useVuelidate from '@vuelidate/core'
import { required, minLength, helpers } from '@vuelidate/validators'

export default {
    layout: AuthLayout,
    props: {
        username: {
            type: String,
            required: true,
        },
        token: {
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
        const option = reactive({
            password_type: "password",
            confirm_password_type: "password",
        });

        const form = useForm({
            password: "",
            password_confirmation: "",
            username: props.username,
            token: props.token,
        });

        const update = async () => {
            const isValid = await v$.value.$validate()
            if(!isValid) return;

            form.clearErrors();
            form.post(route("password.update"), {
                onSuccess: () => {
                    form.reset();
                },
                onError: (err) => console.log(err),
            });
        };


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

        const v$ = useVuelidate(rules, form)

        return {
            v$,
            form,
            option,
            update,
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
