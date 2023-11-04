<template>
    <div class="challo__card challo__card-p6 rounded-b-3x">
        <Back :showModal="form.isDirty" :target="route('package.index', { ...buildQueryParams() } )" :backPrevUrl="true"
            :staticBackdrop="true" />
        <h5 class="page-title">Paket bearbeiten</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="update">

                <div class="grid grid-cols-3 gap-4 gap-y-8">

                    <div class="input-wrapper">
                        <label>Erstellungsdatum</label>
                        <p class="value">{{ form.creation_date }}</p>
                    </div>

                    <div class="input-wrapper">
                        <label>Paket-ID</label>
                        <p class="value">{{ form.latest_id }}</p>
                    </div>

                    <div class="input-wrapper"></div>

                    <div class="input-wrapper">
                        <TextInput placeholder="Name eingeben" label="Name*" v-model="form.package_name"
                            :error="form.errors.package_name = null"
                            @clearError="form.errors.package_name = null; form_error.package_name.error = false;"
                            :show_error="form_error.package_name.error" maxlength="41" />
                        <div class="form-error" v-if="v$.package_name.maxLength.$invalid">{{
                        v$.package_name.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper col-span-2">
                        <TextInput placeholder="Dienstleistungen erfassen" label="Dienstleistungen"
                            v-model="form.services" :error="form.errors.services"
                            @clearError="form.errors.services = null; form_error.services.error = false;"
                            :show_error="form_error.services.error" maxlength="101" />
                        <div class="form-error" v-if="v$.services.maxLength.$invalid">{{ v$.services.maxLength.$message
                        }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText label="Einmalige Anmeldegebühr*" buttonPrefixText="CHF"
                            classLists="h-10" placeholder="00.00" v-model="form.registration_fee"
                            :error="form.errors.registration_fee"
                            @clearError="form.errors.registration_fee = null; form_error.registration_fee.error = false;"
                            :show_error="form_error.registration_fee.error" />
                        <!--                        <div class="form-error" v-if="v$.registration_fee.decimal.$invalid">{{ v$.registration_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText label="Preis im 1. Jahr*" buttonPrefixText="CHF"
                            buttonSuffixText="/ Jahr" classLists="h-10" placeholder="00.00"
                            v-model="form.first_year_fee" :error="form.errors.first_year_fee"
                            @clearError="form.errors.first_year_fee = null; form_error.first_year_fee.error = false;"
                            :show_error="form_error.first_year_fee.error" />
                        <!--<div class="form-error" v-if="v$.first_year_fee.maxLength.$invalid">{{ v$.first_year_fee.maxLength.$message }}</div>-->
                        <!--<div class="form-error" v-if="v$.first_year_fee.decimal.$invalid">{{ v$.first_year_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText label="Preis ab 2. Jahr*" buttonPrefixText="CHF"
                            buttonSuffixText="/ Jahr" classLists="h-10" placeholder="00.00" v-model="form.yearly_fee"
                            :error="form.errors.yearly_fee"
                            @clearError="form.errors.yearly_fee = null; form_error.yearly_fee.error = false;"
                            :show_error="form_error.yearly_fee.error" />
                        <!--                        <div class="form-error" v-if="v$.yearly_fee.decimal.$invalid">{{ v$.yearly_fee.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput label="Coin Faktor*" buttonPrefixText="CHF" classLists="h-10"
                            placeholder="Zahl eingeben" v-model="form.coin_factor" :error="form.errors.coin_factor"
                            @clearError="form.errors.coin_factor = null; form_error.coin_factor.error = false;"
                            :show_error="form_error.coin_factor.error" />
                        <!--                        <div class="form-error" v-if="v$.coin_factor.decimal.$invalid">{{ v$.coin_factor.decimal.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput label="Beratung*" classLists="h-10" placeholder="Beratung erfassen"
                            v-model="form.consulting" :error="form.errors.consulting"
                            @clearError="form.errors.consulting = null; form_error.consulting.error = false;"
                            :show_error="form_error.consulting.error" maxlength="41" />
                        <div class="form-error" v-if="v$.consulting.maxLength.$invalid">{{
                        v$.consulting.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInputWithPrefixAndSuffixText label="Booster*" :buttonSuffixText="'/ '.concat( 'Monat' )"
                            classLists="h-10" placeholder="Zahl eingeben" v-model="form.booster"
                            :error="form.errors.booster" @clearError="form.errors.booster = null"
                            :show_error="form_error.booster.error" />
                        <!--                        <div class="form-error" v-if="v$.booster.integer.$invalid">{{ v$.booster.integer.$message }}</div>-->
                    </div>

                    <div class="input-wrapper">
                        <TextInput label="Anzahl Registrationen*" classLists="h-10" placeholder="Zahl eingeben"
                            v-model="form.number_of_registration" :error="form.errors.number_of_registration"
                            @clearError="form.errors.number_of_registration = null; form_error.number_of_registration.error = false;"
                            :show_error="form_error.number_of_registration.error" />
                        <!--                        <div class="form-error" v-if="v$.number_of_registration.integer.$invalid">{{ v$.number_of_registration.integer.$message }}</div>-->
                    </div>

                    <div class="col-span-2"></div>

                    <div class="input-wrapper">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>

                    <div class="input-wrapper">
                        <Cancel :showModal="form.isDirty" :target="route('package.index')" :backPrevUrl="true"
                            message="Änderungen verwerfen?"
                            text="Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?" />
                    </div>

                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import TextInput from "../../Components/Form/TextInput.vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import Back from "../../Components/Form/Back.vue";
import Cancel from "../../Components/Form/Cancel.vue";
import { inject } from "@vue/runtime-core";
import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
import useVuelidate from "@vuelidate/core";
import { maxLength, required, helpers, decimal, integer } from "@vuelidate/validators";
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import { reactive } from "@vue/reactivity";

let prev_url = usePage().props.value.prev_url;

const props = defineProps(["package", "services"]);
const modal = inject("modal");
const form_error = reactive(
    {
        package_name: {
            error: false
        },

        services: {
            error: false
        },

        registration_fee: {
            error: false
        },

        first_year_fee: {
            error: false
        },

        yearly_fee: {
            error: false
        },

        coin_factor: {
            error: false
        },

        consulting: {
            error: false
        },

        booster: {
            error: false
        },

        number_of_registration: {
            error: false
        }
    }
);
const form = useForm({
    packageId: props.package.id,
    latest_id: props.package.package_prefix_id,
    creation_date: props.package.created_at,
    package_name: props.package.package_name,
    services: props.package.services,
    registration_fee: props.package.registration_fee,
    first_year_fee: props.package.first_year_package_fee,
    yearly_fee: props.package.package_fee,
    coin_factor: props.package.coin_factor,
    consulting: props.package.consulting,
    booster: props.package.booster,
    number_of_registration: props.package.number_of_registration,
    _method: "PUT",
});
const rules = {
    package_name: {
        required,
        maxLength: helpers.withMessage('Maximal 40 Zeichen möglich', maxLength(40)),
    },

    services: {
        maxLength: helpers.withMessage('Maximal 100 Zeichen möglich', maxLength(100)),
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
        decimal
    },

    consulting: {
        required,
        maxLength: helpers.withMessage('Maximal 40 Zeichen möglich', maxLength(40)),
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

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

let canSubmit = true;

const update = () => {
    if (canSubmit) {
        form.post(
            route("package.update", {
                package: props.package.id,
            }),
            {
                forceFormData: true,

                onBefore: (event) => {
                    let errorTerms = [];
                    let fieldMissing = false;

                    v$.value.$silentErrors.forEach((terms) => {
                        if (terms.$validator === 'required') {
                            fieldMissing = true;
                        }
                        errorTerms.push(terms.$property);
                        //form_error[terms.$property]['error'] = true;
                    }
                    );

                    Object.keys(rules).forEach((terms) => {
                        form_error[terms]['error'] = errorTerms.includes(terms);
                        //form_error[terms]['error'] = false;
                    }
                    );

                    if (v$.value.$invalid && fieldMissing) {
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
                    });
                },
            }
        );
    }


};

</script>

<style lang="scss">
.service_select2 {
    width: 100%;
    --tw-bg-opacity: 1;
    --tw-text-opacity: 1;
    color: rgb(19 95 132 / var(--tw-text-opacity));
    outline: 2px solid transparent;
    outline-offset: 2px;
    position: relative;

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

.value {
    /* Ropa/P/16 */
    font-family: "Ropa Sans";
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 17px;

    /* Gray / 3 */
    color: #787878;
}
</style>
