<template>
    <div class="challo__pagination">
        <Link class="paginate-btn doubble-previous" :href="paginationData.first_page_url"
            v-if="paginationData.current_page > 1" preserve-scroll preserve-state>
        <LeftArrow />
        <LeftArrow />
        </Link>

        <Link class="paginate-btn previous" :href="paginationData.prev_page_url" v-if="paginationData.prev_page_url"
            preserve-scroll preserve-state>
        <LeftArrow />
        <span>{{ $t("Vorherige") }}</span>
        </Link>
        <template v-for="(link, index) in paginationData.links">
            <Link class="paginate-btn nth-page" :class="{ active: link.active }" :key="`link-${index}`" :href="link.url"
                v-html="link.label" v-if="index != 0 && index != paginationData.links.length - 1" preserve-scroll preserve-state>
            </Link>
        </template>

        <Link class="paginate-btn forward" :href="paginationData.next_page_url" v-if="paginationData.next_page_url"
            preserve-scroll preserve-state>
        <span>{{ $t('Nächste') }}</span>
        <RightArrow />
        </Link>

        <Link class="paginate-btn dubble-forward" :href="paginationData.last_page_url"
            v-if="paginationData.current_page < paginationData.last_page" preserve-scroll preserve-state>
        <RightArrow />
        <RightArrow />
        </Link>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import RightArrow from "./Icons/RightArrow.vue";
import LeftArrow from "./Icons/LeftArrow.vue";

export default {
    components: {
        Link,
        RightArrow,
        LeftArrow,
    },
    props: {
        paginationData: {
            type: Object,
            required: true,
        },

    },
};
</script>

<style lang="scss" scoped>
.challo__pagination {
    margin-top: 10px;
    margin-bottom: 10px;
}

.challo__pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    justify-content: center;
    max-width: 100%;
}

.active {
    pointer-events: none;
}

.disabled {
    pointer-events: none;
}

.paginate-btn {
    font-family: 'Poppins', sans-serif;
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 17px;

    text-align: center;
}

.challo__pagination .paginate-btn {
    align-items: center;
    background: #e5ebef;
    border: 1px solid #abbecc;
    border-radius: 5px;
    box-sizing: border-box;
    color: #135F84;
    display: flex;
    gap: 4px;
    justify-content: center;
    padding: 9px 14px;
}

.challo__pagination .active {
    background-color: #7292a9;
    color: #eff3f6;
}
</style>
