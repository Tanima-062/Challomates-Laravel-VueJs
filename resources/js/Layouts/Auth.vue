<template>
    <CustomModal>
        <StatusShower />
        <div class="auth">
            <slot />
        </div>
    </CustomModal>
</template>

<script>
import CustomModal from "../Components/Modal/CustomModal.vue";
import StatusShower from "../Components/StatusShower.vue";
import { onMounted } from 'vue';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { usePage } from "@inertiajs/inertia-vue3";


export default {
    components: { CustomModal, StatusShower },
    setup() {
        onMounted(() => {
            const lang = "de";
            loadLanguageAsync(lang);

            const user = usePage().props.value;

        });
        return {};
    },
};

</script>
