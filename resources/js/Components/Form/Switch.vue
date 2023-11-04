<template>
    <div
        class="checkbox-wrapper flex items-center rounded-full mr-4"
    >
        <label class="challo__form--switch relative">
            <div
                class="slider round rounded-full"
                @click="change"
                :class="{ active: checked, inactive: !checked, disabled : !clickable }"
            ></div>
        </label>
        <span v-if="label?.trim().length > 0" class="text-tints-5  ml-2">{{ label }}</span>
        <p v-if="statusText" class="status ml-4">
            {{ checked ? "Aktiv" : "Inaktiv" }}
        </p>
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
                return `checkbox-input-${uuid()}`;
            },
        },
        statusText: {
            type: Boolean,
            default() {
                return true;
            },
        },
        clickable: {
            type: Boolean,
            default: true,
        },
        error: String,
        label: String,
        value: [String, Number, Boolean],
    },
    data() {
        return {
            checked: false,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.checked = this.value;
        });
    },
    methods: {
        change: function () {
            if (this.clickable) {
                this.checked = !this.checked;
                this.$emit('change', this.checked)
                this.$emit("update:modelValue", this.checked);
            }
        },
    },
};
</script>


<style lang="scss" scoped>
.vebo__form--switch {
    display: inline-block;
    height: 16px;
    margin: 0;
    position: relative;
    width: 32px;
}

.challo__form--switch .slider {
    background-color: #ccc;
    bottom: 0;
    cursor: pointer;
    height: 16px;
    left: 0;
    // position: absolute;
    right: 0;
    top: 0;
    transition: .4s;
    width: 32px;
}

.slider.round.active {
    background-color: #1AA1E4;
    &::before {
        transform: translateX(15px);
    }
}
.slider.round.inactive {
    background-color: #ccc;
    &::before {
        transform: translateX(0px);
    }
}

.status {
    font-family: 'Poppins', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;

    //   color: #323232;
}
.slider.round.inactive:before {
    transform: translateX(0);
}

.challo__form--switch .slider.round:before {
    border-radius: 50%;
}

.challo__form--switch .slider:before {
    background-color: #fff;
    bottom: 3.5px;
    content: "";
    height: 10px;
    left: 4px;
    position: absolute;
    transition: .4s;
    width: 10px;
}

.disabled {
    cursor: not-allowed !important;
}

</style>
