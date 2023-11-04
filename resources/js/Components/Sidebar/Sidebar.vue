<template>
    <!-- Sidebar  -->
    <nav id="sidebar" class="h-auto">
        <div class="sidebar-header bg-background px-10 py-3    ">
            <img src="/images/logo.png" style="max-width: 100%; height: auto" />
        </div>

        <ul class="sidebar__nav bg-gradient-to-t from-white to-background px-10 py-3  pt-10   ">
            <SidebarMenuItem routeName="dashboard" activeText="Dashboard">
                <DashboardIcon class="w-8" />
                <span class="link-text">
                    {{ "Dashboard" }}
                </span>
            </SidebarMenuItem>

            <SidebarMenuItem routeName="challo-mates-admins.index" activeText="ChalloMatesAdmin/"
                v-if="hasAnyPermissions(['challo_mates_admin.view', 'challo_mates_admin.create',])">
                <AvatarIcon class="w-8" />
                <span class="link-text">
                    {{ "CHalloMates Admins" }}
                </span>
            </SidebarMenuItem>
            <SidebarMenuItem routeName="package.index" activeText="Package/"
                v-if="hasAnyPermissions(['package.view', 'package.create'])">
                <PackagesIcon class="w-8" />
                <span class="line-text">
                    {{ "Pakete" }}
                </span>
            </SidebarMenuItem>
            <SidebarMenuItem routeName="marketing-fees.index" activeText="MarketingFees/">
                <MarketingFeesIcon class="w-8" />
                <span class="line-text">
                    {{ "Marketinggebühren" }}
                </span>
            </SidebarMenuItem>
            <SidebarMenuItem routeName="company-consultants.index" activeText="CompanyConsultants/"
                v-if="hasAnyPermissions(['company_consultants.view', 'company_consultants.create'])">
                <CompanyConsultants class="w-8" />
                <span class="line-text">
                    {{ "Company Consultants" }}
                </span>
            </SidebarMenuItem>

            <SidebarMenuItem routeName="sales-partner.index" activeText="SalesPartner">
                <DistributorAndContactIcon class="w-8" />
                <span class="line-text">
                    {{ "Vertriebspartner & Verträge" }}
                </span>
            </SidebarMenuItem>

            <SidebarMenuItem routeName="mobile-app-users.index" activeText="MobileAppUser/">
                <MobileAppUserIcon class="w-8" />
                <span class="line-text">
                    {{ "Mobile App-Benutzer" }}
                </span>
            </SidebarMenuItem>

            <SidebarMenuItem route-name="store-visits.index" active-text="StoreVisits/">
                <StoreIcon class="w-8" />
                <span class="line-text">
                    Store-Besuche
                </span>
            </SidebarMenuItem>

            <SidebarMenuItem routeName="coins-credit.index" activeText="Coins/">
                <CoinsIcon class="w-8" />
                <span class="line-text">
                    Coins
                </span>
            </SidebarMenuItem>
            <SidebarMenuItem routeName="boosters.index" activeText="Boosters/">
                <BoosterIcon class="w-8" />
                <span class="line-text">
                    {{ "Boosters" }}
                </span>
            </SidebarMenuItem>


            <SidebarMenuItem
                routeName="sweepstakes.index"
                activeText="Sweepstakes/"
                :related-comp="['Participations', 'Raffles', 'Jackpot']"
                v-if="hasAnyPermissions(['sweepstakes.view', 'participation.view', 'raffles.view', 'jackpot.view'])"
            >
                <SweepstakesIcon class="w-8" />
                <span class="line-text">
                    {{ "Gewinnspiele" }}
                </span>
            </SidebarMenuItem>
        </ul>
    </nav>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import DashboardIcon from "../../Components/Icons/Dashboard";
import AvatarIcon from "../Icons/Avatar.vue";
import PackagesIcon from "../Icons/Packages.vue";
import SidebarMenuItem from './SidebarMenuItem.vue'
import MarketingFeesIcon from '../Icons/MarketingFee.vue'
import CompanyConsultants from '../Icons/CompanyConsultant.vue'
import DistributorAndContactIcon from '../Icons/DistributorAndContracts.vue'
import MobileAppUserIcon from '../Icons/MobileAppUser.vue'
import BoosterIcon from '../Icons/Booster.vue'
import SweepstakesIcon from '../Icons/Sweepstake.vue'
import StoreIcon from "../Icons/Store.vue";
import CoinsIcon from "../Icons/Coins.vue"

</script>

<style lang="scss" scoped>
</style>
