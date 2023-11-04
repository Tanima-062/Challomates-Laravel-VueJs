<template>
    <div class="challo__card py-10 px-6 -mt-2">
        <Back :target="route('mobile-app-users.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-8">Mobile App-Benutzerdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row mb-[30px] gap-0">
                <div class="w-1/3 flex gap-5 items-center">
                    <div class="avatar w-16 h-16 rounded-full overflow-hidden bg-white flex items-center justify-center">
                        <img class="max-w-16 max-h-16" :src="mobile_app_user.photo_url"
                            alt="avatar" />
                    </div>
                    <div class="flex flex-col">
                        <h5 class="detail__label mb-3">
                            Benutzername
                        </h5>
                        <span class="detail__value">
                            {{ mobile_app_user.username }}
                        </span>
                    </div>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Privatsphäre
                    </h5>
                    <span class="detail__value">
                        {{ $t(mobile_app_user.privacy ? "Ein" : "Aus") }}
                    </span>
                </div>

            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3 ">
                    <h5 class="detail__label mb-2">Benutzer-ID</h5>
                    <span class="detail__value">{{ mobile_app_user.prefix_id }}</span>
                </div>
                <div class="w-1/3">
                    <h5 class="detail__label mb-2">Art</h5>
                    <span class="detail__value capitalize">{{ user_type }}</span>
                </div>

            </div>
            <div class="detail__row gap-0 mb-[56px]">
                <div class="w-1/3">
                    <h4 class="detail__label">Vorname</h4>
                    <span class="detail__value">{{ mobile_app_user.first_name }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">{{ $t('Last name') }}</h4>
                    <span class="detail__value">{{mobile_app_user.last_name}}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Geburtsdatum</h4>
                    <span class="detail__value">{{ formateDate(mobile_app_user.date_of_birth) }}</span>
                </div>
            </div>
            <div class="field-header mb-10">
                <h3 class="font-semibold text-18xl font-poppins text-primary-3">Wohnadresse</h3>
            </div>
            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Strasse</h4>
                    <span class="detail__value">{{ mobile_app_user.street }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Hausnummer</h4>
                    <span class="detail__value">{{ mobile_app_user.house_number }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Postleitzahl</h4>
                    <span class="detail__value">{{ mobile_app_user.zip_code }}</span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Ort</h4>
                    <span class="detail__value">{{ mobile_app_user.city }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Land</h4>
                    <span class="detail__value">{{ mobile_app_user.country }}</span>
                    <!-- {{ getCountryName(mobile_app_user.country) }} -->
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">E-Mail</h4>
                    <span class="detail__value">{{ mobile_app_user.email }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Telefonnummer</h4>
                    <span class="detail__value">{{ mobile_app_user.full_phone_number }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Back from "../../Components/Form/Back.vue";
import { computed } from "@vue/reactivity";

const props = defineProps({
    mobile_app_user: {
        type: Object,
        required: true,
    },
});

const user_type = computed(() => {
    const types = { 'direct_consumer': 'Direkter Konsument', 'distribution_consumer': 'Vertriebskonsument' }
    return types[props.mobile_app_user.type] || props.mobile_app_user.type
})
</script>
