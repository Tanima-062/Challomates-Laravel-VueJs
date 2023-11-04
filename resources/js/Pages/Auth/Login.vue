<template>
    <div class="auth__container min-h-[914px] box-border pb-20">
        <div class="auth__card">
            <div class="auth__alert auth__alert--success" v-if="attrs.flash?.message">
                <div class="auth__alert--header">
                    <h4 class="auth__alert--title">Benachrichtigung</h4>
                    <span class="auth__alert--close">
                        <close-icon />
                    </span>
                </div>
                <div class="auth__alert--body">
                    thanks_very_much_your_password_has_been_successfully_reset_you_can_now_log_in_with_your_new_password
                </div>
            </div>
            <div class="auth__card-header flex justify-center">
                <!-- <LogoDark /> -->
                <img class="w-[280px] h-[62px]" src="/images/logo.png" />
            </div>
            <div class="auth__card-body mt-[145px]">
                <h3 class="auth__card-heading text-primary-2 text-35">
                    Willkommen bei <br /> CHalloMates
                </h3>
                <form @submit.prevent="login">
                    <h4 class="auth__card-form-title text-22 text-primary-2 mt-[60px] font-semibold">
                        Anmeldung
                    </h4>
                    <div class="auth__card--input-wrapper mt-[41px]">
                        <div class="auth__card--form-input">
                            <TextInput label="E-Mail*" placeholder="E-Mail Adresse eingeben" v-model="form.username"
                                :error="form.errors.username ? 'Das Feld für den Benutzernamen ist erforderlich.' : undefined"
                                labelClass="block mb-[5px] font-ropa font-normal text-16"
                                :show_error="errors.username" />
                        </div>
                        <div class="auth__card--form-input mt-[30px]">
                            <div class="label flex mb-[8px]">
                                <label class="form-label text-16 font-ropa font-normal" for="password">Passwort*</label>
                                <PasswordTooltip svgClass="mt-0 ml-0 top-[3px] right-[-17px]" />
                            </div>
                            <PasswordInput :show-label="false" v-model="form.password" placeholder="Passwort eingeben"
                                :showError="false"
                                :error="form.errors.email ? 'Das Feld für den Benutzernamen ist erforderlich.' : undefined"
                                :hasError="errors.password" />
                        </div>

                        <div class="auth__card--form-input   mt-[30px]">
                            <button class="challo__btn btn-block btn-primary font-poppins text-15 font-semibold"
                                type="submit" :disabled="form.processing">
                                Anmelden
                            </button>
                        </div>
                        <div class="auth__card--form-input mt-[16px]">
                            <Link class="auth__card--link text-gray-text" :href="route('password.request')">
                            Passwort vergessen?
                            </Link>
                        </div>
                        <div class="auth__card--form-input mt-[32px]" v-if="form.hasErrors">
                            <div class="messages flex flex-row gap-[9px]">
                                <div class="messages__icon mt-[6px]">
                                    <failed-icon />
                                </div>
                                <div class="messages__text text-tints-5 text-14 font-normal">
                                    Ungültige Anmeldedaten. Bitte versuchen Sie es erneut oder klicken Sie auf "Passwort vergessen?" sollten Sie Ihr Passwort vergessen haben.
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
import CloseIcon from "../../Components/Icons/CloseIcon.vue";
import FailedIcon from "../../Components/Icons/FailedIcon.vue";
import { reactive } from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import { inject, useAttrs } from "@vue/runtime-core";
import FieldMissing from "../../Components/Modal/Content/FieldMissing";
import PasswordInput from "../../Components/Form/PasswordInput.vue";
import PasswordTooltip from "../../Components/Form/PasswordTooltip.vue";

export default {
    layout: AuthLayout,
    props: {
        message: {
            type: String,
            default: null,
        },
    },
    components: {
        TextInput,
        EyeClose,
        EyeOpen,
        LogoText,
        Link,
        FailedIcon,
        CloseIcon,
        PasswordInput,
        PasswordTooltip
    },
    setup() {
        const attrs = useAttrs();
        const option = reactive({
            password_type: "password",
        });

        const form = useForm({
            username: "",
            password: "",
        });

        const errors = reactive({
            username: false,
            password: false,
        })

        const modal = inject("modal");

        const login = () => {
            form.clearErrors();
            manageError()
            if (!manageError()) {
                return
            }

            form.post(route("login"), {
                forceFormData: true,
                onError: function (error) {
                    errors.username = true;
                    errors.password = true;
                },
            });
        };

        const manageError = () => {
            errors.username = false;
            errors.password = false;

            if (form.username === '' || form.password === '') {
                modal.show(FieldMissing, {
                    config: {
                        staticBackdrop: true,
                    },
                });

                if (form.username === '') {
                    errors.username = true;
                }

                if (form.password === '') {
                    errors.password = true;
                }


                return false
            }
            return true
        }

        return {
            form,
            option,
            login,
            attrs,
            errors
        };
    },
};
</script>


<style lang="scss" scoped>
.error {
    border-color: #c81717;
}

.form-error {
    font-family: "Poppins";
    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 18px;
    display: flex;
    align-items: center;
    color: #ff0000;
    margin-top: 5px;
}
</style>
