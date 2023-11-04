<template>
  <div class="datepicker">
    <v-date-picker
      v-model="dateForm.date"
      is-range
      title-position="left"
      @dayclick="dayclick"
      :attributes="datepickerOptions.attrs"
      color="green"
      :key="compoenentKey"
    >
        <template v-slot="{ inputValue, inputEvents }">
            <div class="filter-date-range-picker shadow-md rounded-b-md">
                <input
                    id="date"
                    class="challo__input"

                    :value="inputValue.start"
                    v-on="inputEvents.start"
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
                    id="date"
                    class="challo__input"

                    :value="inputValue.end"
                    v-on="inputEvents.end"
                />

                <button class="clear-btn challo__btn" @click.prevent="clearDate">Löschen</button>

            </div>
        </template>
    </v-date-picker>
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
  },
  setup(props) {
    const compoenentKey = ref(0)
    const dateForm = reactive({
      date: {
        start: new Date(),
        end: new Date(),
      },
      is_default: true,
    });

    const start_date_key = computed(()=>props.keyPrefix ? `${props.keyPrefix}_start_date` : 'start_date')
    const end_date_key = computed(()=>props.keyPrefix ? `${props.keyPrefix}_end_date` : 'end_date')

    const datepickerOptions = reactive({
      attrs: [
        {
          key: "today",
          highlight: "red",
          dates: new Date(),
        },
      ],
    });

    const setDateFromUrl = () => {
      let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
      );

      let start = null;
      let end = null;

      if (searchParams.hasOwnProperty(start_date_key.value)) {
        start = searchParams[start_date_key.value];
      }
      if (searchParams.hasOwnProperty(end_date_key.value)) {
        end = searchParams[end_date_key.value];
      }

      if (start && end) {
        dateForm.date.start = start;
        dateForm.date.end = end;
        dateForm.is_default = false;
      }

      if (start && !end) {
        dateForm.date.start = start;
        dateForm.date.end = start;
        dateForm.is_default = false;
      }

      if (end && !start) {
        dateForm.date.end = end;
        dateForm.date.start = end;
        dateForm.is_default = false;
      }
    };

    setDateFromUrl();

    watch(
      () => dateForm.date,
      _debounce(function () {
        dateForm.is_default = false;
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

    const formatDate = (date) => {
      return new Date(date).toISOString().slice(0, 10);
    };

    const dayclick = (e) => {

    };

    const buildQueryParams = () => {
      let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
      );

      if (!dateForm.is_default) {
        searchParams[start_date_key.value] = formatDate(dateForm.date.start);
        searchParams[end_date_key.value] = formatDate(dateForm.date.end);
      }else {
          if(searchParams.hasOwnProperty(start_date_key.value)) {
              delete searchParams[start_date_key.value];
          }
          if(searchParams.hasOwnProperty(end_date_key.value)) {
              delete searchParams[end_date_key.value];
          }
      }

      if (searchParams.hasOwnProperty("page")) {
        delete searchParams["page"];
      }

      return searchParams;
    };



    const clearDate = () => {
        // dateForm.date.start = new Date(),
        // dateForm.date.end =  new Date(),
        compoenentKey.value++;
        dateForm.is_default = true;
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

    return {
      dateForm,
      dayclick,
      datepickerOptions,
      clearDate,
      compoenentKey
    };
  },
};
</script>

<style lang="scss" scoped>
.datepicker {
  position: absolute;
  left: 0;
  top: 27px;
}

.filter-date-range-picker {
    display: flex;
    justify-content: space-between;


}

.filter-date-range-picker{
    display: flex;
    justify-content: space-between;
    width: 450px;
    background-color: white;
    padding: 10px;
    position: absolute;
    top: 4px;
    // border: 1px solid #E2E2E2;
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
