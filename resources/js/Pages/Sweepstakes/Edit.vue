<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <Back
            :showModal="form.isDirty"
            :target="route('sweepstakes.index', { ...buildQueryParams() } )"
            :backPrevUrl="true"
            :staticBackdrop="true"
        />
        <h5 class="page-title">Gewinnspiel bearbeiten</h5>
        <div class="form-wrapper">
            <form class="challo_form" @submit.prevent="create" novalidate="novalidate">
                <div class="grid grid-cols-3 gap-4 gap-y-8">

                    <div class="input-wrapper">
                        <label>Erstellungsdatum</label>
                        <p class="value">{{ dayjs(form.creation_date).format("DD.MM.YYYY").toString() }}</p>
                    </div>

                    <div class="input-wrapper">
                        <label>Gewinnspiel-ID*</label>
                        <p class="value">{{ form.latest_id }}</p>
                    </div>

                    <div class="input-wrapper"></div>

                    <div class="input-wrapper">
                        <TextInput
                            label="Name*"
                            placeholder="Name eingeben"
                            v-model="form.name"
                            @clearError="form.errors.name = null; form_error.name.error = false;"
                            :error="form.errors.name"
                            :show_error="form_error.name.error"
                            maxlength="31"
                        />

                        <div class="form-error" v-if="v$.name.maxLength.$invalid">{{ v$.name.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <SelectOption
                            label="Art*"
                            v-model="form.type"
                            @clearError="form.errors.type = null; form_error.type.error = false;"
                            :options="sweepstake_types"
                            :error="form.errors.type"
                            :show_error="form_error.type.error"
                        />
                    </div>

                    <div class="input-wrapper">
                        <RangedDateTimePicker
                            label="Laufzeit*"
                            v-model:start-date="form.runtime_from"
                            v-model:end-date="form.runtime_to"
                            :error="form.errors.runtime_from || form.errors.runtime_to"
                            :show_error="form_error.runtime_from.error || form_error.runtime_to.error"
                        />
                    </div>

                    <div class="input-wrapper">
                        <label class="form-label block mb-[8px]">Verlosungszeitpunkt*</label>
                        <DateTimePicker
                            v-model="form.raffle_time"
                            @update:modelValue="form.errors.raffle_time = null; form_error.raffle_time.error = false;"
                            :error="form.errors.raffle_time"
                            :show_error="form_error.raffle_time.error"
                        />
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            label="Price/s*"
                            placeholder="Preis/e erfassen"
                            v-model="form.price"
                            @clearError="form.errors.price = null; form_error.price.error = false;"
                            :error="form.errors.price"
                            :show_error="form_error.price.error"
                            :disabled="price_disabled"
                            maxlength="41"
                        />

                        <div class="form-error" v-if="!price_disabled && v$.price.maxLength.$invalid">{{ v$.price.maxLength.$message }}</div>
                    </div>

                    <div class="input-wrapper" :class="price_disabled ? 'invisible' : ''">
                        <TextInput
                            type="number"
                            label="Anzahl Gewinner*"
                            placeholder="Anzahl Gewinner eingeben"
                            classLists="number-input"
                            v-model="form.number_of_winners"
                            @clearError="form.errors.number_of_winners = null; form_error.number_of_winners.error = false;"
                            :error="form.errors.number_of_winners"
                            :show_error="form_error.number_of_winners.error"
                        />
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            type="number"
                            label="Total Verlosungszahlstellen*"
                            placeholder="Zahl eingeben"
                            classLists="number-input"
                            v-model="form.total_sweepstake_number_position"
                            @clearError="form.errors.total_sweepstake_number_position = null; form_error.total_sweepstake_number_position.error = false;"
                            :error="form.errors.total_sweepstake_number_position"
                            :show_error="form_error.total_sweepstake_number_position.error"
                        />
                    </div>

                    <div class="input-wrapper">

                        <div class="grid grid-cols-2 gap-4 gap-y-8">
                            <TextInputWithPrefixAndSuffixText
                                type="number"
                                label="Gewinnzahlstellen*"
                                placeholder="Zahl eingeben"
                                buttonPrefixText="Von"
                                prefixClass="text-16 text-primary-1"
                                classLists="number-input"
                                v-model="form.winning_number_position_from"
                                @clearError="form.errors.winning_number_position_from = null; form_error.winning_number_position_from.error = false;"
                                :error="form.errors.winning_number_position_from"
                                :show_error="form_error.winning_number_position_from.error"
                            />

                            <TextInputWithPrefixAndSuffixText
                                type="number"
                                label=" "
                                labelClass="mb-[30px]"
                                placeholder="Zahl eingeben"
                                buttonPrefixText="Bis"
                                prefixClass="text-16 text-primary-1"
                                classLists="number-input"
                                v-model="form.winning_number_position_to"
                                @clearError="form.errors.winning_number_position_to = null; form_error.winning_number_position_to.error = false;"
                                :error="form.errors.winning_number_position_to"
                                :show_error="form_error.winning_number_position_to.error"
                            />
                        </div>

                        <div class="form-error" v-if="v$.winning_number_position_from.ToShouldBeEqualOrBiggerThanFrom.$invalid">{{ v$.winning_number_position_from.ToShouldBeEqualOrBiggerThanFrom.$message }}</div>
                    </div>

                    <div class="input-wrapper">
                        <TextInput
                            type="number"
                            label="Anzahl Coins für Teilnahme*"
                            placeholder="Zahl eingeben"
                            classLists="number-input"
                            v-model="form.number_of_coins_for_participation"
                            @clearError="form.errors.number_of_coins_for_participation = null; form_error.number_of_coins_for_participation.error = false;"
                            :error="form.errors.number_of_coins_for_participation"
                            :show_error="form_error.number_of_coins_for_participation.error"
                        />

                        <!--                        <div class="form-error" v-if="v$.number_of_coins_for_participation.decimal.$invalid">{{ v$.number_of_coins_for_participation.decimal.$message }}</div>-->
                    </div>

                    <div v-if="sweepstake.publish_status === 'not_published'" class="input-wrapper col-span-3">
                        <Switch
                            label="Gewinnspiel publizieren"
                            :statusText="false"
                            :value="form.published"
                            @change="showPublishOnModal"
                        />
                    </div>

                    <div class="input-wrapper">
                        <button class="btn-block challo__btn btn-primary" type="submit">
                            Speichern
                        </button>
                    </div>

                    <div class="input-wrapper">
                        <Cancel
                            :showModal="form.isDirty"
                            :staticBackdrop="true"
                            :target="route('sweepstakes.index', { ...buildQueryParams() } )"
                            message="Änderungen verwerfen?"
                            text="Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?"
                        />
                    </div>

                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import Back from "../../Components/Form/Back";
import {reactive} from "@vue/reactivity";
import TextInput from "../../Components/Form/TextInput.vue";
import Switch from "../../Components/Form/Switch.vue";
import Cancel from "../../Components/Form/Cancel.vue";
import SelectOption from "../../Components/Form/SelectOption.vue";
import RangedDateTimePicker from "../../Components/Form/DatePickers/RangedDateTimePicker.vue";
import DateTimePicker from "../../Components/Form/DatePickers/DateTimePicker.vue";
import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
import {inject, ref, watch} from "@vue/runtime-core";
import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
import {decimal, helpers, integer, maxLength, minValue, required, requiredIf} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import SweepFieldMissing from "./Components/SweepFieldMissing"
import dayjs from "dayjs";

const sweepstake_types = [
    {
        label: "Gewinnspiel",
        value: "sweepstake",
    },
    /*{
        label: "Jackpot",
        value: "jackpot",
    },*/
];
let prev_url = usePage().props.value.prev_url;
const price_disabled = ref(false);
const props = defineProps(['sweepstake']);

const modal = inject("modal");
const form_error = reactive({
    name : {
        error: false
    },

    type : {
        error: false
    },

    runtime_from : {
        error: false
    },

    runtime_to : {
        error: false
    },

    raffle_time : {
        error: false
    },

    price : {
        error: false
    },

    number_of_winners : {
        error: false
    },

    total_sweepstake_number_position : {
        error: false
    },

    winning_number_position_from : {
        error: false
    },

    winning_number_position_to : {
        error: false
    },

    number_of_coins_for_participation : {
        error: false
    },
} );
const form = useForm({
    sweepstakeId: props.sweepstake.id,
    latest_id: props.sweepstake.prefix_id,
    creation_date: props.sweepstake.created_at,
    name: props.sweepstake.name,
    type: props.sweepstake.type,
    runtime_from: props.sweepstake.runtime_from,
    runtime_to: props.sweepstake.runtime_to,
    raffle_time: props.sweepstake.raffle_time,
    total_sweepstake_number_position: props.sweepstake.total_sweepstake_number_position,
    winning_number_position_from: props.sweepstake.winning_number_position_from,
    winning_number_position_to: props.sweepstake.winning_number_position_to,
    number_of_winners: props.sweepstake.number_of_winners,
    price: props.sweepstake.price,
    number_of_coins_for_participation: props.sweepstake.number_of_coins_for_participation,
    published: props.sweepstake.publish_status === 'published',
    _method: "PUT",
});
const ToShouldBeEqualOrBiggerThanFrom = () => {
    return (form.winning_number_position_from.length < 1 || form.winning_number_position_to.length < 1) ? true : (parseInt(form.winning_number_position_from) <= parseInt(form.winning_number_position_to));
};

const numberOfWinnersIsRequired = (value) => {
    return (price_disabled.value) ? price_disabled.value : value.toString().length
};

const rules = {
    name: {
        required,
        maxLength: helpers.withMessage( 'Maximal 30 Zeichen möglich', maxLength(30) ),
    },

    type: {
        required,
    },

    /*runtime_from: {
        required,
    },

    runtime_to: {
        required,
    },*/

    raffle_time: {
        required,
    },

    price: {
        required,
        maxLength: helpers.withMessage( 'Maximal 40 Zeichen möglich', maxLength(40) ),
    },

    number_of_winners: {
        numberOfWinnersIsRequired,
        integer,
        minValue: minValue(1),
        maxLength: maxLength(10),
    },

    total_sweepstake_number_position: {
        required,
        integer,
        maxLength: maxLength(10),
    },

    winning_number_position_from: {
        required,
        integer,
        maxLength: maxLength(10),
        ToShouldBeEqualOrBiggerThanFrom: helpers.withMessage( 'Die Zahl im “Von”-Feld muss kleiner oder gleich der Zahl im “Bis”-Feld sein.', ToShouldBeEqualOrBiggerThanFrom ),
    },

    winning_number_position_to: {
        required,
        integer,
        maxLength: maxLength(10),
        ToShouldBeEqualOrBiggerThanFrom: helpers.withMessage( 'Die Zahl im “Von”-Feld muss kleiner oder gleich der Zahl im “Bis”-Feld sein.', ToShouldBeEqualOrBiggerThanFrom ),
    },

    number_of_coins_for_participation: {
        required,
        decimal,
    },
};
const v$ = useVuelidate(rules, form);

watch(form, () => {
    if (form.type === "jackpot") {
        form.price = "jackpot";
        price_disabled.value = true;
    } else {
        price_disabled.value = false;
        form.price = form.price === "jackpot" ? "" : form.price;
    }
});

const showPublishOnModal = (value) => {
    form.published = value;
    if (value) {
        modal.show(FieldMissing, {
            config: {
                staticBackdrop: true,
            },
            props: {
                title: 'Gewinnspiel publizieren?',
                description: 'Wenn Sie dieses Gewinnspiel speichern und “Gewinnspiel publizieren” aktiviert ist, wird dieses Gewinnspiel für alle Mobile App-Benutzer ersichtlich sein.',
            }
        });
    }
};

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

let canSubmit = true;

const create = () => {
    if ( canSubmit ) {
        form.post(route("sweepstakes.update", {sweepstake: props.sweepstake.id,}), {
            forcedData: true,

            onBefore:(event) => {
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

                /*Object.keys(rules).forEach( (terms) => {
                    if ( form[terms] !== null && form[terms].toString().length < 1 ) {
                        fieldMissing = true;
                    }
                } );*/

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

            onError: (err) => {
                canSubmit = true;

                if (err["runtime_from"]) {
                    modal.show(SweepFieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            title: 'Start des Gewinnspiels in der Vergangenheit',
                            description: err['runtime_from'],
                        },
                    });
                } else if (err['runtime_from.exist']) {
                    modal.show(SweepFieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            title: 'Gewinnspiel Erstellung nicht möglich',
                            description: err['runtime_from.exist']['message'],
                        },
                    });

                    form_error['runtime_from']['error'] = true;
                } else if (err['raffle_time']) {
                    modal.show(SweepFieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                        props: {
                            title: 'Gewinnspiel Erstellung nicht möglich',
                            description: err['raffle_time'],
                        },
                    });
                } else {
                    modal.show(FieldMissing, {
                        config: {
                            staticBackdrop: true,
                        },
                    });
                }

                return;
            }
        });
    }
};
</script>

<style lang="scss" scoped>
</style>
