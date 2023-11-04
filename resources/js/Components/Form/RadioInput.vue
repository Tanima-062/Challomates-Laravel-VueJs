<template>
  <div
    class="checkbox-wrapper d-flex flex-row align-items-center"
    style="gap: 5px"
  >
    <label class="challo__form--checkbox" :for="id" @click="change">
      <div
        :id="id"
        class="check"
        :class="{ active: checked, inactive: !checked }"
      ></div>
      <p class="label">
        {{ label }}
      </p>
    </label>
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
        return `radio-input-${uuid()}`;
      },
    },
    clickable: {
      type: Boolean,
      default: true,
    },
    error: String,
    label: {
      type: String,
      required: true,
    },
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
        this.$emit("update:modelValue", this.checked);
      }
    },
  },
};
</script>


<style lang="scss" scoped>
.challo__form--checkbox {
  padding: 0;
  margin: 0;
  display: flex;
  align-items: center;
}
.check {
  width: 24px;
  height: 24px;
  background-color: #abbecc;
  border-radius: 50%;
  border: 3px solid white;
  outline: 1px solid #abbecc;
  margin-right: 10px;
  cursor: pointer;
}
.check.active {
  background-color: #005f9f;
  outline: 1px solid #005f9f;
}

.label {
  font-family: 'Poppins', sans-serif;
  font-style: normal;
  font-weight: 700;
  font-size: 16px;
  line-height: 19px;
  color: #003577;
  margin: 0;
  padding: 0;
  cursor: pointer;
}
</style>
