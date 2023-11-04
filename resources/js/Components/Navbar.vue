<template>
  <nav>
      <div class="navbar__search w-440">
          <input type="text" class=" challo__input search__input placeholder:text-tints-3" placeholder="Suche">
      </div>
      <div class="navbar__user flex justify-between items-center relative">
          <div class="navbar__user--icon text-tints-4">
              <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="18.5" cy="18.5" r="18.5" fill="white"/>
                    <path d="M28.0984 24.9825C27.9689 23.6266 27.4508 22.4134 26.6735 21.4856C26.3497 21.0574 25.702 21.0574 25.3782 21.4143C25.0543 21.7711 25.0543 22.4134 25.3782 22.7702C25.8963 23.4125 26.2849 24.1975 26.3497 25.1253C26.544 27.409 25.1838 29.3358 23.176 29.6927C22.9169 29.764 22.6579 29.764 22.3988 29.764C20.9739 29.764 19.6137 29.764 18.1888 29.764C17.9298 29.764 17.7354 29.8354 17.5411 30.0495C16.9582 30.6918 17.4116 31.7623 18.1888 31.7623C18.4479 31.7623 18.7717 31.7623 19.0308 31.7623C20.1319 31.7623 21.233 31.7623 22.2693 31.7623C22.7226 31.7623 23.1113 31.7623 23.5646 31.6195C26.4145 31.0486 28.3575 28.194 28.0984 24.9825ZM15.4685 21.4143C16.6344 21.4143 17.8002 21.4143 18.9661 21.4143C19.1604 21.4143 19.3547 21.4143 19.6137 21.4143C19.8728 21.4143 20.0671 21.3429 20.2614 21.1288C20.8443 20.4865 20.4557 19.416 19.6137 19.416C18.2536 19.416 16.8287 19.416 15.4685 19.416C12.4244 19.416 9.96321 22.1279 9.89844 25.4821C9.89844 27.0521 10.3518 28.4794 11.2586 29.5499C11.5824 29.9781 12.2301 29.9781 12.554 29.5499C12.8778 29.1931 12.9426 28.6222 12.6187 28.2653C12.1653 27.6944 11.9063 27.0521 11.7767 26.2671C11.3233 23.698 13.0073 21.4856 15.4685 21.4143Z" fill="currentColor"/>
                    <path d="M18.9646 6C15.9205 6 13.3945 8.78326 13.3945 12.1374C13.3945 15.563 15.8557 18.3462 18.9646 18.3462C22.0735 18.3462 24.5347 15.6344 24.5347 12.2088C24.5995 8.78326 22.0735 6 18.9646 6ZM19.0294 16.348C16.892 16.348 15.2081 14.4925 15.2081 12.2088C15.2081 9.85374 16.892 7.99824 18.9646 7.99824C21.0372 7.99824 22.786 9.85374 22.786 12.2088C22.786 14.4925 21.102 16.348 19.0294 16.348Z" fill="currentColor"/>
                </svg>
          </div>
          <div class="navbar__user--name flex flex-col mr-6 ml-2 text-tints-4 cursor-pointer"  @click="option.showDropwown = !option.showDropwown">
              <span class="full__name text-15">{{ auth_user.name }}</span>
              <span class="user__type text-12">{{ auth_user.user_type }}</span>
          </div>
          <div class="navbar__user--dropdown--icon cursor-pointer text-tints-4" @click="option.showDropwown = !option.showDropwown">
              <up-arrow  v-if="option.showDropwown" />
              <down-arrow v-if="!option.showDropwown" />
          </div>
          <div class="dropdown-menu absolute right-0 bg-background" v-show="option.showDropwown" v-click-away="()=> option.showDropwown = false">
              <ul>
                  <li>
                      <a @click.prevent="logout" href="" class="block px-4 py-2 text-tints-4 text-right">Abmelden</a>
                  </li>
              </ul>
          </div>
      </div>
  </nav>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import MenubarIcon from "./Icons/MenuBar.vue";
import UpArrow from "./Icons/UpArrow.vue";
import DownArrow from "./Icons/DownArrow.vue";
import { reactive } from "@vue/reactivity";
import { computed } from "@vue/runtime-core";
import AvatarIcon from "./Icons/Avatar.vue";

export default {
  components: {
    Link,
    MenubarIcon,
    UpArrow,
    DownArrow,
    AvatarIcon
  },
  setup() {
    const logout = () => {
      Inertia.post(route("logout"));
    };

    const option = reactive({
      showDropwown: false,
    });

    const show = computed(() => option.showDropwown);
    const closeDropdown = () => {
      option.showDropwown = false;
    };

    return {
      logout,
      option,
      show,
      closeDropdown,
    };
  },
};
</script>

<style lang="scss" scoped>
    .dropdown-menu {
        top: 50px;
        width: 150px;
    }
</style>
