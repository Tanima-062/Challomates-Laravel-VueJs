<template>
    <label v-if="showLabel" class="form-label block mb-[5px] font-ropa font-normal text-16" for="password">{{ $t(label)
        }}*</label>
    <div class="password-input toggleable relative">
        <input :id="id" class="challo__input text-15 text-tints-5" :class="[{ error: error || hasError, 'input-field-error' : show_error }, attrs.classList]"
               :type="type" :value="attrs.modelValue" @input="(e) => emit('update:modelValue', e.target.value)"
               :placeholder="placeholder" v-bind="attrs" />

        <div v-show="toggleable" class="
        auth__card--form__toggle-icon
        absolute
        top-[50%]
        right-[3%]
        translate-y-[-50%]
      " style="cursor: pointer" @click="type == 'password' ? type = 'text' : type = 'password'">
            <eye-close v-show="type == 'password'" />
            <eye-open v-show="type != 'password'" />
        </div>
    </div>

    <div v-if="error && showError" class="form-error">
        {{ error }}
    </div>
</template>

<script setup>
import { ref } from "@vue/reactivity";
import { useAttrs } from "@vue/runtime-core";
import EyeClose from "../Icons/EyeClose.vue";
import EyeOpen from "../Icons/EyeOpen.vue";

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue"]);
const props = defineProps({
    label: {
        type: String,
        default: "Password",
    },
    id: {
        type: String,
        default: "password"
    },

    placeholder: {
        type: String,
        default: "Enter password",
    },

    toggleable: {
        type: Boolean,
        default: true,
    },

    error: {
        type: [String, Object, Array],
        required: false,
    },

    show_error: {
        type: Boolean,
        default: false,
    },

    showError: {
        type: Boolean,
        default: true,
    },

    showLabel: {
        type: Boolean,
        default: true,
    },
    hasError: {
        type: Boolean,
        default: false,
    }

});
const type = ref("password");

</script>

<style lang="scss" scoped>
.error {
    border-color: #c81717;
}

.input-field-error {
    border-color: #ff0000;
}

.form-error {
    font-family: "Poppins";
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
    font-family: "Poppins", sans-serif;
    color: #1aa1e4;
}
</style>
