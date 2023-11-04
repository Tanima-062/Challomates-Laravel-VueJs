<template>
    <div :class="[$attrs.class]" style="width: 100%">
        <label v-if="label" class="form-label" :for="id">{{ label }}</label>
        <div class="phone_input"
             :style="styles"
             :class="{ 'input-field-error' : ( show_error || ( !is_valid && phone_number.length > 0 ) ), 'valid-number' : ( is_valid && !show_error), 'number-exist' : number_exist, }"
        >
            <CountryCode v-model="country_code" />
            <div class="borders">
                <p>|</p>
            </div>
            <input id="phone_number" class="phone__number__input " type="text" v-model="phone_number"
                placeholder="11 111 11 11" style="border: none" :disabled="!country_code"
                @input="(event) => $emit('update:phone_number', event.target.value.replace(/[^0-9]/g, ''))" />
        </div>
    </div>
</template>

<script>
import { v4 as uuid } from "uuid";
// import CountryCode from './CountryCode.vue'
import CountryCode from '../../../Components/CountryCode'

import parsePhoneNumber from 'libphonenumber-js'

export default {
    inheritAttrs: false,
    components: {
        CountryCode
    },
    props: {
        id: {
            type: String,
            default() {
                return `text-input-${uuid()}`;
            },
        },
        show_error: {
            type: Boolean,
            default: false,
        },
        number_exist: {
            type: Boolean,
            default: false,
        },
        error: [String, Boolean],
        label: String,
        country_code: null,
        phone_number: null,
    },
    watch: {
        country_code() {
            this.$emit('update:phone_number', this.phone_number)
            this.$emit('updated', this.phone_number);
        },
        phone_number() {
            this.$emit('update:country_code', this.country_code)
            this.$emit('updated', this.country_code);
        }
    },
    computed: {
        styles() {
            return ( (this.error || !this.is_valid ) && this.show_error ) ? "border: 1px solid #C81717;" : (this.is_valid === true ? "border: 1px solid ##16a34a;" : "border: 1px solid #C8C8C8;");
        },
        is_valid() {
            if (!this.phone_number) return false;

            const phoneNumber = parsePhoneNumber(this.phone_number, this.country_code.toUpperCase())
            if (phoneNumber && phoneNumber.isValid()) {
                return true;
            }
            return false;
        }
    },
};
</script>

<style lang="scss" >
.phone_input {
    // width: 100%;
    display: flex;
    align-items: center;

    box-sizing: border-box;
    border-radius: 48px;
    background-color: white;
    border: 1px solid #C2C5C6;

    &.input-field-error {
        border: 1px solid #ff0000 !important;
    }

    &.valid-number {
        border: 1px solid #079507 !important;
    }

    input.phone__number__input::-webkit-outer-spin-button,
    input.phone__number__input::-webkit-inner-spin-button {
        appearance: none;
    }

    input.phone__number__input {
        background: #fff;
        border: 1px solid #c8c8c8;
        box-sizing: border-box;
        color: #323232;
        display: block;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        height: 40px;
        line-height: 24px;
        outline: none;
        padding: 3.75px 7.5px;
        width: 60%;
        font-family: 'Ropa Sans',serif;
        border-left: none;
        border-radius: 0 5px 5px 0;

        &:focus {
            // border: 1px solid $tints-4;
        }
    }

    .borders {
        width: 5px;
        background-color: white;
        height: 40px;
        // border-top: 1px solid $tones-1;
        // border-bottom: 1px solid $tones-1;
        display: flex;
        align-items: center;

        padding-right: 10px;
        padding-left: 10px;

        p {
            color: #abbecc;
            margin: 0;
            transform: scale(.5, 1.6);
        }
    }
}

.form-label {
    font-family: 'Poppins',serif;
    font-style: normal;
    // font-weight: 700;
    font-size: 16px;
    line-height: 24px;
}

.invalid-number {
    border: 1px solid red;
}

.valid-number {
    border: 1px solid green;
}
</style>
