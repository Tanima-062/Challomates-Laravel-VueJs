<template>
    <div class="challo__card__tabs  -mt-5">
        <div class="challo__card__tabs--top">
            <h4 class="text-primary-3 font-poppins text-18xl font-semibold w-2/3 mr-10 ">Gewinnspiele</h4>
            <Link
                class="challo__btn btn-block btn-warms flex justify-center items-center w-1/3 py-[6.58px] h-10"
                v-if="hasPermission('jackpot.view')"
                :href="route('jackpot.index')"
            >
                <SweepStakeIcon class="mr-2.5" /> CHF {{ jackpotAmount }}
            </Link>
        </div>
        <div class="challo__card__tabs--bottom">
            <Link v-if="hasPermission('sweepstakes.view')" :href="route('sweepstakes.index')" class="challo__card__tabs--item w-1/3" :class="{active: activeTab === 'sweepstakes'}">Gewinnspiele</Link>
            <Link v-if="hasPermission('participation.view')" :href="route('participation.index')" class="challo__card__tabs--item w-1/3" :class="{active: activeTab === 'participations'}">Teilnahmen</Link>
            <Link v-if="hasPermission('raffles.view')" :href="route('raffles.index')" class="challo__card__tabs--item w-1/3" :class="{active: activeTab === 'raffles'}">Verlosungen</Link>
        </div>
        <div class="challo__card__tab--menus"></div>
    </div>
</template>

<script>
    import SweepStakeIcon from '../../../Components/Icons/Sweepstake.vue'
    import { Link } from "@inertiajs/inertia-vue3";
    export default {
        components: {
            SweepStakeIcon,
            Link
        },
        props: {
            activeTab: {
                type: String,
                default: 'raffles'
            },
            jackpotAmount: {
                type: [Number, String],
                default: 0
            }
        }
    }
</script>

<style lang="scss" scoped>
    .challo__card__tabs--item {
        font-weight: normal;

        &.active {
            font-weight: 600;
        }
    }
    .active {
        pointer-events: none;
    }
</style>
