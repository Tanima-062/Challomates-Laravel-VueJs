<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)">
        <div class="content" v-if="!option.expand">
            <div class="text-tints-5" style="width: 15%">
                {{ formateDate(admin.created_at) }}
            </div>
            <div class="text-tints-5" style="width: 15%">
                <span>
                    {{ admin.prefix_id }}
                </span>
            </div>
            <div class="text-tints-5" style="width: 15%;">
                {{ admin.name }}
            </div>
            <div class="text-tints-5 break-all" style="width: 20%">
                {{ admin.email }}
            </div>
            <div class="text-tints-5" style="width: 20%">
                {{ admin.full_phone_number }}
            </div>
            <div class="text-tints-5" style="width: 10%">
                <UserStatus :status="admin.status == 'pending' ? 'registration pending' : admin.status" />
            </div>
            <div class="relative" style="width: 5%">
                <button class="relative" type="button" aria-haspopup="true" aria-expanded="false"
                    @click="showDropdown = true">
                    <menu-bar />
                    <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)" @click.stop="showDropdown = false"
                        v-show="showDropdown">
                        <Link class="dropdown-item"
                            :href="route('challo-mates-admins.edit', { challo_mates_admin: admin.id })"
                            v-if="hasPermission('challo_mates_admin.edit')">{{ $t("Bearbeiten") }}</Link>
                        <ToggleUserStatus :status="admin.status == 'inactive' ? 'Aktivieren' : 'Deaktivieren'"
                            routeName="challo-mates-admins.toggle-status" :value="admin.id" class="dropdown-item"
                            v-if="hasPermission('challo_mates_admin.edit')" :title="
                              admin.status == 'inactive'
                                ? 'CHalloMates Admin aktivieren?'
                                : 'CHalloMates Admin deaktivieren?'
                            " :message='`Sind Sie sicher, dass Sie den CHalloMates Admin \"${admin.first_name} ${admin.last_name}\" wirklich ${admin.status == "inactive" ? "aktivieren" : "deaktivieren"} wollen?`' />
                        <Link style="cursor: pointer" class="dropdown-item" :href="
                          route('challo-mates-admins.resend-invitaion', {
                            challo_mates_admin: admin.id,
                          })
                        " method="put" as="submit" v-if="
                hasPermission('challo_mates_admin.edit') &&
                (admin.status == 'new' || admin.status == 'pending')
              ">
                        {{ admin.status == "new" ? "Einladung senden" : "Einladung erneut senden" }}
                        </Link>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { trans } from "laravel-vue-i18n";
import { reactive, ref } from "@vue/reactivity";
import ToggleUserStatus from "../Components/ToggleUserStatus.vue";
import UserStatus from "../../../Components/Status/UserStatus.vue";

const props = defineProps({
    admin: {
        type: Object,
        required: true,
    },
});

const showDropdown = ref(false);

const option = reactive({
    expand: false,
});
</script>

<style lang="scss" scoped>
.content {
    .td {
        font-family: "Poppins";
        font-style: normal;
        font-size: 16px;
        line-height: 24px;
        color: #135f84;
        font-weight: inherit;
        word-break: break-word;
    }
}

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
