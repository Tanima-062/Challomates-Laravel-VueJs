<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)">
        <div class="content flex" v-if="!option.expand">
            <div class="text-tints-5" style="width: 15%">
                {{ formateDate(user.created_at) }}
            </div>
            <div class="text-tints-5" style="width: 15%">
                <span>
                    {{ user.username }}
                </span>
            </div>
            <div class="text-tints-5 capitalize" style="width: 15%">
                {{ user_type }}
            </div>
            <div class="text-tints-5" style="width: 20%">
                <Link class="break-words" :href="route('mobile-app-users.show', { mobile_app_user: user.id })"> {{ `${user.first_name}
                ${user.last_name}` }}</Link>
            </div>
            <div class="text-tints-5 break-all" style="width: 20%">
                {{ user.email }}
            </div>
            <div class="text-tints-5" style="width: 10%">
                <UserStatus :status="user.status == 'pending' ? 'registration pending' : user.status" />
            </div>
            <div class="relative" style="width: 5%">
                <button type="button" aria-haspopup="true" aria-expanded="false" @click="showDropdown = true">
                    <menu-bar />
                </button>
                <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                    v-click-away="() => (showDropdown = false)" @click="showDropdown = false" v-show="showDropdown">
                    <Link class="dropdown-item" :href="
                      route('mobile-app-users.show', {
                        mobile_app_user: user.id,
                      })
                    ">Details ansehen</Link>
                    <ToggleUserStatus :status="user.status == 'inactive' ? 'Aktivieren' : 'Deaktivieren'"
                        routeName="mobile-app-users.toggle-status" :value="user.id" class="dropdown-item"
                        v-if="hasPermission('mobile_app_user.edit')" :title="
                          user.status == 'inactive'
                            ? 'Mobile App-Benutzer aktivieren?'
                            : 'Mobile App-Benutzer deaktivieren?'
                        " :message="toggleMessage" />
                    <button class="dropdown-item" v-if="hasPermission('mobile_app_user.delete')"
                        @click.prevent="deleteUser">
                        Löschen
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { reactive, ref } from "@vue/reactivity";
import UserStatus from "../../../Components/Status/UserStatus.vue";
import { computed } from "@vue/reactivity";
import { inject } from "@vue/runtime-core";
import ToggleUserStatus from "../Components/ToggleUserStatus.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
const modal = inject("modal");

const showDropdown = ref(false);

const option = reactive({
    expand: false,
});

const user_type = computed(() => {
    const types = {
        direct_consumer: "Direkter Konsument",
        distribution_consumer: "Vertriebskonsument",
    };
    return types[props.user.type] || props.user.type;
});

const toggleMessage = computed(() => {
    return props.user.status == "inactive"
        ? `Wird ein Mobile App-Benutzer aktiviert, hat er/sie Zugriff für die CHalloMates Mobile App. Sind Sie sicher, dass Sie den Mobile App-Benutzer "${props.user.first_name} ${props.user.last_name}" wirklich aktivieren wollen?`
        : `Wird ein Mobile App-Benutzer deaktiviert, hat er/sie keinen Zugriff mehr für die CHalloMates Mobile App. Sind Sie sicher, dass Sie den Mobile App-Benutzer "${props.user.first_name} ${props.user.last_name}" wirklich deaktivieren wollen?`
});

const deleteUser = () => {
    modal.show(Confirmation, {
        props: {
            message: "Mobile App-Benutzer löschen?",
            text: `Ein Mobile-App Benutzer und seine/ihre Daten dürfen nur auf ausdrücklichen Wunsch des entsprechenden Mobile App-Benutzers gelöscht werden. Die Löschung kann nicht rückgängig gemacht werden. Sind Sie sicher, dass Sie den Mobile App-Benutzer "${props.user.first_name} ${props.user.last_name}" wirklich löschen wollen?`,
        },

        events: {
            yesClick: () => {
                Inertia.delete(
                    route("mobile-app-users.destroy", { mobile_app_user: props.user.id })
                );
            },

            noClick: () => modal.hide(),
        },
    });
};
</script>

<style lang="scss" scoped>
.dropdown {
    right: 0;
    top: 20px;
    width: max-content;

    .dropdown-item {
        border-bottom: 1px solid #8ed5f6;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 30px;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        color: #135f84;
        text-align: left;

        &:hover {
            font-weight: 600;
        }

        &:last-child {
            border-bottom: none;
        }
    }
}
</style>
