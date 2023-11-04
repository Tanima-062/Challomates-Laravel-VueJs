<template>
    <div class="auth__container pb-[200px]">
        <div class="auth__card">
            <div class="auth__card-header flex justify-center items-center">
                <img class="w-[280px] h-[62px]" src="/images/logo.png" />
            </div>
            <div class="auth__card-body">
                <form @submit.prevent="request">
                    <h4 class="auth__card-form-title mt-[145px] text-35 text-primary-2 font-semibold">Passwort vergessen?</h4>
                    <p class="mt-[24px] text-tints-5 text-17">
                        Kein Problem! Bitte geben Sie Ihre registrierte
                        E-Mail Adresse ein und wir senden Ihnen die Anleitung zum Zurücksetzen Ihres Passworts.
                    </p>
                    <div class="mt-[60px]">
                        <div class="auth__card--form-input">
                            <TextInput :label="`E-Mail*`" placeholder="E-Mail Adresse eingeben" v-model="form.username"
                                :error="form.errors.username" labelClass="block mb-[5px] font-ropa font-normal text-16"
                                :show_error="false" />
                        </div>

                        <div class="auth__card--form-input mt-[32px]">
                            <button
                                class="block text-white font-poppins text-15 font-semibold bg-primary-1 py-[8px] w-full rounded-45"
                                type="submit" :disabled="form.processing">
                                E-Mail senden
                            </button>
                            <Link
                                class="mt-4 block bg-white text-center text-tints-2 font-poppins text-15 font-semibold py-[8px] w-full rounded-45 border-[1px] border-tints-2"
                                :href="route('login')" :disabled="form.processing">
                            Zurück zur Anmeldung
                            </Link>
                        </div>
                        <div class="auth__card--form-input mt-[30px]" v-if="form.hasErrors">
                            <div class="messages flex gap-2 justify-start">
                                <div class="messages__icon mt-[6px]">
                                    <failed-icon />
                                </div>
                                <div class="messages__text text-tints-5 text-14 font-normal">
                                    Die von Ihnen angegebene E-Mail-Adresse ist leider nicht registriert.
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
import LogoText from "../../Components/Icons/LogoText.vue";
import FailedIcon from "../../Components/Icons/FailedIcon.vue";
import CloseIcon from "../../Components/Icons/CloseIcon.vue";
import { reactive } from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import SuccessIcon from "../../Components/Icons/SuccessIcon.vue";

export default {
    layout: AuthLayout,
    components: {
        TextInput,
        EyeClose,
        EyeOpen,
        LogoText,
        Link,
        FailedIcon,
        CloseIcon,
        SuccessIcon
    },
    setup() {
        const option = reactive({
            notification: false,
        });

        const request = () => {
            form.clearErrors();
            form.post(route("password.email"), {
                onSuccess: () => {
                    form.reset();
                    option.notification = true;
                },
            });
        };

        const form = useForm({
            username: "",
        });

        return {
            form,
            option,
            request,
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
