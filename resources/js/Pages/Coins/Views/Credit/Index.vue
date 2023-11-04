    <template>
    <h3 class="font-poppins text-18xl text-primary-3 font-semibold mb-4">Coins</h3>
    <Tabs />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page placeholder="Nach Store-Namen, Mobile App-Benutzer Vor- oder Nachnamen suchen"
                        routeName="coins-credit.index" />
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div class="challo__filter flex-1" :class="{ 'bg-white': option.showFilter }">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />
                    <div class="filters flex justify-start" v-show="option.showFilter">

                        <div class="filter-menu mr-auto">
                            <SingleCheckboxFilter label="Mobile App-Benutzer" column="mobile_app_users" labelKey="name"
                                valueKey="value" :options="filterData.mobile_app_users?.data"
                                routeName="coins-credit.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_users')"
                                @instant-update="(key, silently = false) => getFilterOptions(key, silently)"
                                :active="option.current_open_filter_menu == 'mobile_app_users'"
                                @toggle="toggleCurrentOpenFilterMenu('mobile_app_users')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu mr-auto">
                            <MultiCheckboxFilter label="Store" column="sales_partners" labelKey="name" valueKey="value"
                                :options="filterData.sales_partners?.data" routeName="coins-credit.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('sales_partners')"
                                @instant-update="(key) => getFilterOptions(key)"
                                :active="option.current_open_filter_menu == 'sales_partners'"
                                @toggle="toggleCurrentOpenFilterMenu('sales_partners')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu mr-auto">
                            <DateRangeFilter routeName='coins-credit.index' label="Datum" />
                        </div>
                    </div>
                </div>
                <EntriesPerPage :target="'coins-credit.index'" />
            </div>
        </div>
        <template v-if="true">
            <Table>
                <template #thead>
                    <div class="flex items-start gap-2 w-[15%]">
                        <span>Mobile App-Benutzer</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index"
                            key-name="mobile_app_user_id" />
                    </div>

                    <div class="flex items-start gap-2 w-[15%]">
                        <span>Store</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index" key-name="sales_partner_id" />
                    </div>

                    <div class="flex items-start gap-2 w-[25%]">
                        <span>Aufenthalt</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index" key-name="check_in_time" />
                    </div>

                    <div class="flex items-start gap-2 w-[15%]">
                        <span>Umsatz-Coins</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index" key-name="posting_coins" />
                    </div>

                    <div class="flex items-start gap-2 w-[10%]">
                        <span>Posting-Coins</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index" key-name="coin" />
                    </div>

                    <div class="flex items-start gap-2 w-[15%]">
                        <span>Total Coins</span>
                        <Sort class="mt-[5px] ml-[10px]" route-name="coins-credit.index" key-name="total_coins" />
                    </div>

                    <div class="td w-[5%]"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="credit in credits.data" :credit="credit" />
                </template>
            </Table>
            <pagination :paginationData="credits" v-if="credits.data.length > 0"/>
        </template>
        <EmptyPage v-if="credits.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import SearchInPage from "../../../../Components/SearchInPage.vue";
import FilterButton from "../../../../Components/Filters/FilterButton.vue";
import Tabs from "../../Components/Tabs.vue";
import Table from "../../../../Components/Tables/Table.vue";
import EmptyPage from "../../../../Components/EmptyPage.vue";
import Pagination from "../../../../Components/Pagination.vue";
import Sort from "../../../../Components/Sort.vue";
import MultiCheckboxFilter from "../../../../Components/Filters/MultiCheckboxFilter.vue";
import SingleCheckboxFilter from "../../../../Components/Filters/SingleCheckboxFilter.vue";
import DateRangeFilter from "../../../../Components/Filters/DateRangeFilter.vue";
import EntriesPerPage from "../../../../Components/EntriesPerPage.vue";
import axios from "axios";
import TableBodyRow from "./Components/TableBodyRow.vue";

const props = defineProps({
    credits: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    mobile_app_users: {
        data: [],
    },
    sales_partners: {
        data: [],
    }
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu, silently = false) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));

    searchParams.column = menu;

    axios.get(route("coins-credit.filter.data", { ...searchParams })).then((res) => {
        filterData[menu]["data"] = res.data;
    });

    if (silently == true)
        return;

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu == menu) {
        option.current_open_filter_menu = null;
    } else {
        if (filterData.hasOwnProperty(menu) && !filterData[menu]["loaded"]) {
            getFilterOptions(menu);
        } else {
            option.current_open_filter_menu = menu;
        }
    }
};

const closeCurrentOptionFilterMenu = (e) => {
    if (option.current_open_filter_menu == e) {
        option.current_open_filter_menu = null;
    }
};
</script>
