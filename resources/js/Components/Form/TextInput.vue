<template>
    <div :class="$attrs.class">
        <label v-if="label" class="form-label block mb-[8px]" :class="this.$attrs.labelClass" :for="id">{{ $t(label)
        }}</label>
        <input :id="id" ref="input" v-bind="{ ...$attrs }" class="challo__input"
            :class="[{ 'border-error': (error || show_error) }, classLists]" :type="type" :value="modelValue"
            :placeholder="placeholder" @input="changeInput" :style="$attrs.inputStyle" :required="required" />
        <div v-if="show_error && error" class="form-error">{{ error }}</div>
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
        show_error: {
            type: Boolean,
            default: false,
        },
        required: {
            type: Boolean,
            default: false
        },
        error: String,
        label: String,
        modelValue: [String, Number],
        placeholder: String,
        classLists: {
            type: String,
            default: "",
        },
        hasError: {
            type: Boolean,
            default: false,
        }
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
            this.$emit('update:modelValue', e.target.value)
            this.$emit('clearError')
        }
    },
};
</script>


<style lang="scss" scoped>

.form-error {
    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 18px;
    display: flex;
    align-items: center;
    color: #FF0000;
    margin-top: 5px;
}

// .form-label {
//     font-style: normal;
//     font-size: 15px;
//     color: #1AA1E4;
// }
</style>
