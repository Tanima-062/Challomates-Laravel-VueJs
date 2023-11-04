<template>
    <div class="modal_show-more-field">
        <h5 class="text-center title">{{ title }}</h5>

        <p class="text-center">
            <a
                target="_blank"
                class="text-tints-5"
                v-for="(user_id, index) in mobile_app_user_id_arr"
                :key="user_id"
                :href="route( 'mobile-app-users.show', { mobile_app_user: user_id } )"
            >
                {{ ( index > 0 ) ? ', ' : '' }}
                {{ mobile_app_user_full_name_arr[index] }}
            </a>
        </p>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps( {
    title: {
        type: String,
        required: false,
        default: "Gewinner",
    },

    mobile_app_user_id_arr: {
        type: Array,
        required: true,
    },

    mobile_app_user_full_name_arr: {
        type: Array,
        required: true,
    }
} );
</script>



<style lang="scss" scoped>

.challo__modal .overlay{
    position: absolute;
}

.modal_show-more-field {
    width: 600px;
    padding: 32px;
    padding-bottom: 24px;
    background-color: #fff;
    box-shadow: 0 4px 4px rgb(0 0 0 / 10%);
    height: fit-content;
    border-radius: 24px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    border: 1px solid #FFBA49;
    z-index: 1;
}

.title {
    font-family: 'Poppins',serif;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 27px;
    text-align: center;
    color: #1AA1E4;
    margin-bottom: 32px;
}

.message {
    font-family: 'Poppins',serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    text-align: center;
    color: #135F84;
}

.buttons {
    width: 285px;
}
</style>

