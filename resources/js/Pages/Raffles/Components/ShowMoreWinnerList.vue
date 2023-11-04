<template>
    <div>
        <a class="dropdown-item" href="#" @click.prevent="toggle">
            + {{ (mobile_app_user_id.length-2) }} Weitere
        </a>
    </div>
</template>

<script>
import {inject} from "vue";
import WinnerList from './WinnerList';

export default {
    props: {
        staticBackdrop: {
            type: Boolean,
            default: false
        },

        mobile_app_user_id: {
            type: Array,
            required: true,
        },

        mobile_app_user_full_name: {
            type: Array,
            required: true,
        }
    },
    components: {
        WinnerList,
    },

    setup(props) {
        const modal = inject("modal");
        const toggle = () => {
            modal.show(WinnerList, {
                props: {
                    title: "Gewinner",
                    mobile_app_user_id_arr: props.mobile_app_user_id,
                    mobile_app_user_full_name_arr: props.mobile_app_user_full_name,
                },
                config: {
                    staticBackdrop: props.staticBackdrop
                },
                events: {
                },
            });
        };

        const buildQueryParams = () => {
            return Object.fromEntries(
                new URLSearchParams(location.search)
            );
        };

        return {
            toggle,
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
