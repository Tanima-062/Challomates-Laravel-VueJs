<template>
    <div :class="$attrs.class">
        <label v-if="label.length > 0" class="form-label block mb-[8px]" :class="this.$attrs.labelClass" :for="id">{{ label }}</label>
        <div class="relative">
            <button type="button" v-if="buttonPrefixText" class="
          absolute
          h-10
          flex
          items-center
          px-5
          rounded-l-full
          text-gray-3
          font-ropa
          pointer-events-none
        "
                    :class="this.$attrs.prefixClass"
            >
                {{ buttonPrefixText }}
            </button>
            <input 
            :id="id" 
            ref="input" 
            v-bind="{ ...$attrs }" 
            class="challo__input text-gray-3" 
            :class="[{ 'error': error, 'pl-20': buttonPrefixText, 'pr-20': buttonSuffixText, 'input-field-error' : show_error }, classLists]" 
            :type="type" 
            :value="modelValue" 
            :placeholder="placeholder" 
            @input="changeInput" 
            :style="$attrs.inputStyle" 
            @keypress="onlyNumber($event)"/>
            <button
                type="button"
                v-if="buttonSuffixText"
                class="
          absolute
          h-10
          flex
          items-center
          px-5
          rounded-r-full
          text-gray-3
          font-ropa
          top-0
          right-0
          pointer-events-none
        "
                :class="this.$attrs.suffixClass"
            >
                {{ buttonSuffixText }}
            </button>
        </div>
<!--        <div v-if="show_error && error" class="form-error">{{ error }}</div>-->
    </div>
</template>

<script>
import { v4 as uuid } from "uuid";

export default {
    inheritAttrs: false,
    props: {
        id: {
            type: String,
            default() {
                return `text-input-${uuid()}`;
            },
        },
        type: {
            type: String,
            default: "text",
        },
        inputType: {
            type: String,
            default: "text",
        },
        show_error: {
            type: Boolean,
            default: false,
        },
        error: String,
        label: {
            type: String,
            default: ""
        },
        buttonPrefixText: String,
        buttonSuffixText: String,
        modelValue: [String, Number],
        placeholder: String,
        classLists: {
            type: String,
            default: "",
        },
    },
    emits: ["update:modelValue", "clearError"],
    methods: {
        focus() {
            this.$refs.input.focus();
        },
        select() {
            this.$refs.input.select();
        },
        setSelectionRange(start, end) {
            this.$refs.input.setSelectionRange(start, end);
        },
        changeInput(e) {
            this.$emit("update:modelValue", e.target.value);
            this.$emit("clearError");
        },
        
        onlyNumber(event) {
            let keyCode = event.keyCode || event.which;
            return ( this.inputType === 'number' ) ? ( keyCode > 47 && keyCode < 58 ) ? true : event.preventDefault() : true;
        }
    },
};
</script>

<style scoped>


input.challo__input::-webkit-outer-spin-button,
input.challo__input::-webkit-inner-spin-button {
    appearance: none;
}

.input-field-error {
    border-color: #ff0000;
}

.error {
    border-color: #c81717;
}

.form-error {
    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 18px;
    display: flex;
    align-items: center;
    color: #ff0000;
    margin-top: 5px;
}

.form-label {
    font-style: normal;
    font-size: 15px;
    color: #1aa1e4;
}
</style>
