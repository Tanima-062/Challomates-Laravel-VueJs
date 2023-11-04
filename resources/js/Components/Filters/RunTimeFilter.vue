<template>
    <span class="filter-menu__label--title" @click="toggleOpen" v-html="filterPriceLabel()"></span>
        <div class="filter-option  absolute bg-white  min-w-max shadow-sm rounded-b-md" v-if="open" v-click-away="close">
            <ul class="filter-option__items max-h-[300px] overflow-y-auto ">
                <li v-for="option in options" :key="option[valueKey]"
                    class=" px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0">
                    <label @click="filter(option[valueKey])" class="cursor-pointer">
                        <span class="status" :class="`status-${option[valueKey].toLowerCase()}`"></span> {{
                                getText(option[valueKey])
                        }}
                    </label>
                </li>
            </ul>
        </div>
        <div class="filter-price-range  shadow-md rounded-b-md flex-col" v-if="openOther" v-click-away="close">
           <div class="flex items-center justify-between w-full">
             <input
                id="price_start"
                class="challo__input"
                :value="priceForm.price.start"
            />
            <span class="">
                <svg viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3"
                    />
                </svg>
            </span>
            <input
                id="price_end"
                class="challo__input"
                :value="priceForm.price.end"
            />

            <button class="clear-btn challo__btn" @click.prevent="clearPrice">{{ $t('clear') }}</button>
           </div>

            <div class="price-field">
                <input type="range" class="min-thumb" step=".01" :min="minValue" :max="maxValue"  v-model="priceForm.price.start">
                <input type="range" class="max-thumb" step=".01" :min="minValue" :max="maxValue"  v-model="priceForm.price.end">
            </div>
        </div>

</template>
<script>
    import { reactive, ref } from "@vue/reactivity";
    import { computed, watch } from "@vue/runtime-core";
    import { debounce as _debounce } from "lodash";
    import { Inertia } from "@inertiajs/inertia";
    export default {
        props: {
            columnKey: {
                type: String,
                required: true,
            },
            keyPrefix: {
                type: String,
                default: null,
            },
            routeName: {
                type: String,
                required: true,
            },
            options: {
                type: [Array, Object],
                default: [],
            },
            valueKey: {
                type: [String, Number],
                default: "id",
            },
            name: {
                type: [String, Number],
                default: "",
            },
            minValue: {
                type: [String, Number],
                default: 0,
            },
            maxValue: {
                type: [String, Number],
                default: 0,
            },
            title:{
                type: String,
                default: "Preis",
            },
        },
        setup(props, ctx) {

            const priceForm = reactive({
                price: {
                    start: null,
                    end: null,
                },
                is_default: true,
            });

            const start_price_key = computed(()=>props.keyPrefix ? `${props.keyPrefix}_start_time` : 'start_time')
            const end_price_key = computed(()=>props.keyPrefix ? `${props.keyPrefix}_end_time` : 'end_time')

            const setPriceFromUrl = () => {
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                let start = null;
                let end = null;


                if (searchParams.hasOwnProperty(start_price_key.value)) {
                    start = searchParams[start_price_key.value];
                }
                if (searchParams.hasOwnProperty(end_price_key.value)) {
                    end = searchParams[end_price_key.value];
                }

                if (start && end) {
                    console.log(start);
                    priceForm.price.start = start;
                    priceForm.price.end = end;
                }
            };

            setPriceFromUrl();

            watch(
                () => priceForm.price,
                _debounce(function () {
                    if(priceForm.price.start == null || priceForm.price.end == null) return

                    priceForm.is_default = false;
                    Inertia.visit(
                        route(props.routeName, {
                            ...buildQueryParams(),
                        }),
                        {
                            preserveScroll: true,
                            preserveState: true,
                        }
                    );
                }, 500),
                { deep: true }
            );

            const buildQueryParams = () => {
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                if (!priceForm.is_default) {
                    searchParams[start_price_key.value] = priceForm.price.start;
                    searchParams[end_price_key.value] = priceForm.price.end;
                }else {
                    if(searchParams.hasOwnProperty(start_price_key.value)) {
                        delete searchParams[start_price_key.value];
                    }
                    if(searchParams.hasOwnProperty(end_price_key.value)) {
                        delete searchParams[end_price_key.value];
                    }
                }

                if (searchParams.hasOwnProperty("page")) {
                    delete searchParams["page"];
                }

                return searchParams;
            };


            const filter = (price) => {
                if(price == 'other_preis'){
                    if(!priceForm.price.start && !priceForm.price.end){
                        priceForm.price.start = props.minValue;
                        priceForm.price.end = props.maxValue;
                    }
                    open.value = !open.value
                    openOther.value = !openOther.value;
                }else{
                    let prices = price.split("-");
                    priceForm.price.start = prices[0];
                    priceForm.price.end = prices[1];
                    open.value = !open.value;
                }
            }

            const getText = (price) => {
                if(price == 'other_preis'){
                    return 'Other '+props.title
                }else{
                    return price
                }
            }

            const clearPrice = () => {
                priceForm.price.start = null,
                priceForm.price.end =  null,
                priceForm.is_default = true;
                openOther.value = false;
                Inertia.visit(
                    route(props.routeName, {
                        ...buildQueryParams(),
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );

            }

            const open = ref(false)

            const openOther = ref(false)

            const toggleOpen = ()=> {
                open.value = !open.value
                openOther.value = false
            }
            const close = ()=> {
                open.value = false
                openOther.value = false
            }



            const filterPriceLabel = ()=> {
                if(priceForm.price.start && priceForm.price.end){
                    return `<span class="filter_dates">
                    ${priceForm.price.start} - ${priceForm.price.end}
                    </span>
                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.4697 6.53033C10.7626 6.82322 11.2374 6.82322 11.5303 6.53033C11.8232 6.23744 11.8232 5.76256 11.5303 5.46967L10.4697 6.53033ZM6 1L6.53033 0.469669C6.23744 0.176776 5.76256 0.176776 5.46967 0.469669L6 1ZM0.46967 5.46967C0.176777 5.76256 0.176777 6.23744 0.46967 6.53033C0.762563 6.82322 1.23744 6.82322 1.53033 6.53033L0.46967 5.46967ZM11.5303 5.46967L6.53033 0.469669L5.46967 1.53033L10.4697 6.53033L11.5303 5.46967ZM5.46967 0.469669L0.46967 5.46967L1.53033 6.53033L6.53033 1.53033L5.46967 0.469669Z" fill="#135F84"/>
                        </svg>
                `
                }
                return `${props.title}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="7"
                        viewBox="0 0 12 7"
                        fill="none"
                    >
                        <path
                        d="M1.53033 0.46967C1.23744 0.176777 0.762563 0.176777 0.46967 0.46967C0.176777 0.762563 0.176777 1.23744 0.46967 1.53033L1.53033 0.46967ZM6 6L5.46967 6.53033C5.76256 6.82322 6.23744 6.82322 6.53033 6.53033L6 6ZM11.5303 1.53033C11.8232 1.23744 11.8232 0.762563 11.5303 0.46967C11.2374 0.176777 10.7626 0.176777 10.4697 0.46967L11.5303 1.53033ZM0.46967 1.53033L5.46967 6.53033L6.53033 5.46967L1.53033 0.46967L0.46967 1.53033ZM6.53033 6.53033L11.5303 1.53033L10.4697 0.46967L5.46967 5.46967L6.53033 6.53033Z"
                        fill="currentColor"
                        />
                    </svg>
                `
            }
            return {
                priceForm,
                clearPrice,
                open,
                openOther,
                toggleOpen,
                close,
                filterPriceLabel,
                filter,
                getText
            };
        }
    }

</script>

<style lang="scss" scoped>
.price-field {
  position: relative;
  width: 100%;
  height: 36px;
  box-sizing: border-box;
  padding-top: 15px;
  padding-left: 0px;
  margin-bottom: 15px ;

}

/* Reset style for input range */

.price-field input[type=range] {
    position: absolute;
    appearance: none;
    background: linear-gradient(90deg,    #03D48E 25%, #01CFB6 50%, #08BAEC 75%, #008DB1 100%);
    width: 100%;
    height: 28px;
    outline: 0;
    box-sizing: border-box;
    border-radius: 50px;
    border:3px solid #C0C0C0 ;
    pointer-events: none;
    -webkit-appearance: none;
    padding: 2px;
}


.price-field input[type=range]:active,
.price-field input[type=range]:focus {
  outline: 0;
}

.price-field input[type=range]::-ms-track {
  width: 188px;
  height: 2px;
  border: 0;
  outline: 0;
  box-sizing: border-box;
  border-radius: 5px;
  pointer-events: none;
  background: transparent;
  color: red;
  border-radius: 5px;
}

/* Style toddler input range */

.max-thumb::-webkit-slider-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    margin-top: -6px;
    // margin-left: 15px;
}
.min-thumb::-webkit-slider-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    margin-top: -6px;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    // margin-left: 2px;
}

.max-thumb::-moz-range-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    margin-top: -6px;
    // margin-left: 15px;
}
.min-thumb::-moz-range-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    margin-top: -6px;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    // margin-left: 2px;
}

.max-thumb::-ms-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    margin-top: -6px;
    // margin-left: 15px;
}
.min-thumb::-ms-thumb {
  /* WebKit/Blink */
    position: relative;
    -webkit-appearance: none;
    margin-top: -6px;
    border: 0;
    outline: 0;
    border-radius: 50%;
    height: 14px;
    width: 14px;
    background-color: #fff;
    cursor: pointer;
    pointer-events: all;
    z-index: 100;
    // margin-left: 2px;
}

/* Style track input range */

.price-field input[type=range]::-webkit-slider-runnable-track {
  /* WebKit/Blink */
  width: 188px;
  height: 2px;
  cursor: pointer;
  border-radius: 5px;
  background: transparent;
  border: none;
}

.price-field input[type=range]::-moz-range-track {
  /* Firefox */
  width: 188px;
  height: 2px;
  cursor: pointer;
  background: #242424;
  border-radius: 5px;
}

.price-field input[type=range]::-ms-track {
  /* IE */
  width: 188px;
  height: 2px;
  cursor: pointer;
  background: #242424;
  border-radius: 5px;
}


.filter-price-range{
    display: flex;
    justify-content: space-between;
    background-color: white;
    padding: 10px;
    position: absolute;
    top: 27px;
    align-items: center;

    span {
        // width: 50px;
        margin-left: 5px;
        margin-right: 10px;
        color: currentColor;
        font-size: 36px;
        display: flex;
        align-items: center;
        svg {
            fill: currentColor;
            width: 18px;
            height: 18px;;
            stroke: #787878;;
        }

    }

    .clear-btn {
        outline: none;
        border: none;
        text-align: center;
        // background-color: rgb(167, 2, 2);

        font-weight: 700;

        padding: 7px 25px;
        // margin-left: 5px;;
        // border-radius: 5px;
        margin-left: 10px;
        font-family: 'Ropa Sans';


        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
        cursor: pointer;
    }

}
</style>
