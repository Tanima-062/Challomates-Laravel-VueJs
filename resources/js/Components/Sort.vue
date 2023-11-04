<template>
    <svg
        :width="width"
        height="12"
        viewBox="0 0 8 12"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        @click="sort()"
    >
        <path
            d="M1.53033 7.46967C1.23744 7.17678 0.762563 7.17678 0.46967 7.46967C0.176777 7.76256 0.176777 8.23744 0.46967 8.53033L1.53033 7.46967ZM4 11L3.46967 11.5303C3.76256 11.8232 4.23744 11.8232 4.53033 11.5303L4 11ZM7.53033 8.53033C7.82322 8.23744 7.82322 7.76256 7.53033 7.46967C7.23744 7.17678 6.76256 7.17678 6.46967 7.46967L7.53033 8.53033ZM6.46967 4.53033C6.76256 4.82322 7.23744 4.82322 7.53033 4.53033C7.82322 4.23744 7.82322 3.76256 7.53033 3.46967L6.46967 4.53033ZM4 1L4.53033 0.46967C4.23744 0.176777 3.76256 0.176777 3.46967 0.46967L4 1ZM0.46967 3.46967C0.176777 3.76256 0.176777 4.23744 0.46967 4.53033C0.762563 4.82322 1.23744 4.82322 1.53033 4.53033L0.46967 3.46967ZM0.46967 8.53033L3.46967 11.5303L4.53033 10.4697L1.53033 7.46967L0.46967 8.53033ZM4.53033 11.5303L7.53033 8.53033L6.46967 7.46967L3.46967 10.4697L4.53033 11.5303ZM7.53033 3.46967L4.53033 0.46967L3.46967 1.53033L6.46967 4.53033L7.53033 3.46967ZM3.46967 0.46967L0.46967 3.46967L1.53033 4.53033L4.53033 1.53033L3.46967 0.46967Z"
            :fill="active ? '#003577' : fill"
        />
    </svg>
</template>

<script>
    export default {
        props: {
            fill: {
                default: "#A0A0A0",
                type: String,
            },
            keyName: {
                type: String,
                required: true,
            },
            routeName: {
                type: String,
                required: true,
            },
            params: {
                type: Object,
                default: {},
            },
            width: {
                type: [String, Number],
                default: 8,
            },
        },
        data() {
            return {
                active: false,
                sort_direction: "ASC",
            };
        },
        mounted() {
            this.activeSort();
        },
        methods: {
            activeSort() {
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                this.active =
                    searchParams.hasOwnProperty("order_by") &&
                    searchParams["order_by"] === this.keyName;
            },

            getQuery(){
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                const query = {
                    ...this.params,
                    ...searchParams,
                    _query: { order_by: this.keyName, direction: this.sort_direction }
                }


                if(searchParams.hasOwnProperty('query')){
                    query._query.query = searchParams.query;
                }

                return query;
            },

            sort() {
                this.sort_direction == "ASC"
                    ? (this.sort_direction = "DESC")
                    : (this.sort_direction = "ASC");

                let query = this.getQuery()

                this.$inertia.visit(
                    route(this.routeName, {...query}
                    ),
                    {
                        method: "get",
                        data: {},
                        replace: false,
                        preserveState: true,
                        preserveScroll: true,
                    }
                );
            },
        },
        watch: {
            "$page.url": function (newUrl, oldUrl) {
                this.activeSort();
            },
        },
    };
</script>

<style lang="scss" scoped>
    svg {
        cursor: pointer;
    }
</style>
