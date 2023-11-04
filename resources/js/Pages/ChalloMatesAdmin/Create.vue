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
                        <TextInput maxlength="31" placeholder="Vorname eingeben" label="Vorname*"
                            v-model="form.first_name"
                            :error="form.errors.first_name || (v$.first_name.maxLength.$invalid ? v$.first_name.maxLength.$message : undefined)"
                            @clearError="form.clearErrors('first_name')" />
                        <div class="form-error" v-if="v$.first_name.maxLength.$invalid">{{
                             v$.first_name.maxLength.$message
                            }}</div>
                    </div>


                    <div class="input-wrapper w-1/3">
                        <TextInput maxlength="31" placeholder="Nachname eingeben" label="Nachname*"
                            v-model="form.last_name"
                            :error="form.errors.last_name || (v$.last_name.maxLength.$invalid ? v$.last_name.maxLength.$message : undefined)"
                            @clearError="form.clearErrors('last_name')" />
                        <div class="form-error" v-if="v$.last_name.maxLength.$invalid">{{
                             v$.last_name.maxLength.$message
                            }}</div>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInput label="E-Mail Adresse*" placeholder="E-Mail eingeben" v-model="form.email"
                            :error="form.errors.email || form.errors['email.unique'] || v$.email.$errors[0]?.$message"
                            @clearError="() => { form.clearErrors('email', 'email.unique') }" :show-error="false"
                            @blur="v$.email.$touch()" />
                        <div class="form-error" v-if="v$.email.$invalid">{{
                             v$.email.$errors[0]?.$message
                            }}</div>
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label class="form-label block mb-[8px]" for="phone_number">Telefonnummer*</label>
                        <PhoneInputWithCounryCode v-model:phone_number="form.phone_number"
                            v-model:country_code="form.country_iso_code" :error="form.errors.phone_number"
                            @updated="() => { form.clearErrors('phone_number', 'country_iso_code'); }" />
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
                        <button class="btn-block challo__btn btn-primary" type="submit" :disabled="form.processing">
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
import PhoneInputWithCounryCode from "../../Components/PhoneInputWithCounryCode.vue";
import useVuelidate from "@vuelidate/core";
import { maxLength, email as ValidEmail, helpers } from "@vuelidate/validators";

const props = defineProps(["languages", "latest_id"]);
const modal = inject("modal");

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    country_iso_code: "CH",
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

const rules = {
    first_name: {
        maxLength: helpers.withMessage(
            "Maximal 30 Zeichen möglich",
            maxLength(30)
        ),
    },
    last_name: {
        maxLength: helpers.withMessage(
            "Maximal 30 Zeichen möglich",
            maxLength(30)
        ),
    },
    email: {
        email: helpers.withMessage("Ungültiges E-Mail Format", ValidEmail)
    }
};

const v$ = useVuelidate(rules, form);

const create = async () => {
    form.post(route("challo-mates-admins.store"), {
        forcedData: true,

        onSuccess: () => {
            form.reset();
        },

        onError: (err) => {
            if (err["email.unique"]) {
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
                return;
            }

            modal.show(FieldMissing, {
                config: {
                    staticBackdrop: true,
                },
            });
        }
    })
}
</script>

<style lang="scss" scoped>
</style>
