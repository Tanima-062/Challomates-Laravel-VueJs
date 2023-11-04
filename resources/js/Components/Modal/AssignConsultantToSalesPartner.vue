<template>
    <div class="modal__confirmation flex flex-col" :class="attrs['class']" v-bind:style="attrs['style']">
        <h5 class="title">Company Consultant zuteilen</h5>
        <p class="description mb-8 text-tints-5">
            Bitte wählen Sie den Company Consultant aus,
            welcher für alle von Ihnen selektierten Vertriebspartner
            zugeteilt werden soll.
        </p>
        <div class="select-consultant">
            <label for="consultant" class="form-label font-semibold mb-2">Company Consultant</label>
            <SearchSelect :options="company_consultants" :searchable="['prefix_id', 'first_name', 'last_name']"
                placeholder="Nach der ID, dem Vor- oder Nachnamen des Consultants suchen" labelKey="name" valueKey="id"
                v-model="selected_company_consultant" />
        </div>
        <div class="btns flex justify-between">
            <button class="yes challo__btn btn-block  w-1/2 mr-4"
                :class="[selected_company_consultant ? 'bg-primary-1' : 'bg-gray-corner cursor-not-allowed']"
                @click="selected_company_consultant ? emit('assign', selected_company_consultant) : null">
                Zuteilen
            </button>
            <button class="no challo__btn btn-outline-primary  btn-block w-1/2 ml-4" style="color: #44B8F1"
                @click="emit('cancel')">
                Abbrechen
            </button>
        </div>
    </div>
</template>

<script setup>
import { useAttrs } from "@vue/runtime-core";
import { ref } from "vue";
import SearchSelect from "../Form/SearchSelect.vue";

const attrs = useAttrs();
const emit = defineEmits(['assign', 'cancel']);
defineProps({
    company_consultants: {
        type: [Object, Array],
        require: true,
    }
});
const selected_company_consultant = ref(undefined)

</script>

<script>
export default {
    inheritAttrs: false,
};
</script>

<style lang="scss" scoped>
.challo__modal .overlay {
    position: absolute;
}

.modal__confirmation {
    width: 600px;
    padding: 32px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 4px 4px rgb(0 0 0 / 10%);
    height: fit-content;
    border-radius: 24px;
    top: 50%;
    border: 1px solid #FFBA49;

}

.title {
    font-family: 'Poppins';
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 27px;
    text-align: center;
    color: #1AA1E4;
    margin-bottom: 32px;
}

.description {
    font-family: 'Poppins';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    // display: flex;
    // align-items: center;
    // text-align: center;
    color: #135F84;
    text-align: center;
}

.btns {
    margin-top: 32px;
}
</style>

