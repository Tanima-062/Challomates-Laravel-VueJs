<template>
  <div class="form__tooltip" ref="form_tooltip">
    <svg
      @click="toggle"
      width="12"
      height="12"
      viewBox="0 0 12 12"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      style="cursor: pointer"
    >
      <path
        d="M5.5 3.505C5.5 3.78114 5.72386 4.005 6 4.005C6.27614 4.005 6.5 3.78114 6.5 3.505H5.5ZM6.5 3.5C6.5 3.22386 6.27614 3 6 3C5.72386 3 5.5 3.22386 5.5 3.5H6.5ZM5.5 8.5C5.5 8.77614 5.72386 9 6 9C6.27614 9 6.5 8.77614 6.5 8.5H5.5ZM6.5 5C6.5 4.72386 6.27614 4.5 6 4.5C5.72386 4.5 5.5 4.72386 5.5 5H6.5ZM6.5 3.505V3.5H5.5V3.505H6.5ZM6.5 8.5L6.5 5H5.5L5.5 8.5H6.5ZM10 6C10 8.20914 8.20914 10 6 10V11C8.76142 11 11 8.76142 11 6H10ZM6 10C3.79086 10 2 8.20914 2 6H1C1 8.76142 3.23858 11 6 11V10ZM2 6C2 3.79086 3.79086 2 6 2V1C3.23858 1 1 3.23858 1 6H2ZM6 2C8.20914 2 10 3.79086 10 6H11C11 3.23858 8.76142 1 6 1V2Z"
        fill="#1AA1E4"
      />
    </svg>
    <div class="content" v-show="show">
      <p>Das Passwort muss folgende Bedingungen erfüllen:</p>
      <ul>
        <li v-if="resetPassword">Kein zuvor verwendetes Passwort</li>
        <li>Mindestens 8 Zeichen</li>
        <li>Mindestens 1 Grossbuchstabe</li>
        <li>Mindestens 1 Kleinbuchstabe</li>
        <li>Mindestens 1 Sonderzeichen</li>
        <li>Mindestens 1 Zahl</li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";

export default {
  props: {
    resetPassword: {
      type: Boolean,
      default: false,
    },
  },
  setup() {
    const show = ref(false);
    const form_tooltip = ref();

    const outside_click_hide = (e) => {
      if (
        e.target !== form_tooltip.value &&
        !form_tooltip.value.contains(e.target)
      ) {
        show.value = !show.value;
        document.removeEventListener("click", outside_click_hide);
      }
    };

    const toggle = (e) => {
      e.stopImmediatePropagation();

      if (show.value) {
        show.value = !show.value;
        document.removeEventListener("click", outside_click_hide);
      } else {
        show.value = !show.value;
        document.addEventListener("click", outside_click_hide);
      }
    };

    return {
      show,
      form_tooltip,
      toggle,
    };
  },
};
</script>

<style lang="scss" scoped>
.form__tooltip {
  position: relative;

  svg {
    position: absolute;
    // margin-top: 0.5rem;
    margin-left: 2px;
    margin-top: 2px;
    z-index: 2;
  }

  .content {
    background-color: #ffffff;
    padding: 1.5rem;
    box-sizing: border-box;
    width: 387px;
    position: absolute;
    border: 1px solid #ffba49;
    border-radius: 5px;
    font-family: Lato;
    font-size: 12px;
    font-weight: 400;
    line-height: 18px;
    letter-spacing: 0em;
    text-align: left;
    color: #787878;

    p {
    //   font-size: 12px;
    //   font-weight: 400;
    //   line-height: 18px;
    //   letter-spacing: 0em;
    //   text-align: left;
    //   color: #787878;
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;

        /* Gray / 3 */

        color: #787878;

    }

    ul {
      font-size: 12px;
      line-height: 18px;
      padding-left: 18px;
      list-style: none;

      li {
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        letter-spacing: 0em;
        text-align: left;
        color: #787878;
        // background: red;
      }

      li::before {
        content: "\2022"; /* Add content: \2022 is the CSS Code/unicode for a bullet */
        color: #787878; /* Change the color */
        font-weight: bold; /* If you want it to be bold */
        display: inline-block; /* Needed to add space between the bullet and the text */
        width: 16px; /* Also needed for space (tweak if needed) */
        margin-left: -12px; /* Also needed for space (tweak if needed) */
        font-size: 12px;
      }
    }

    p {
      margin: 0px;
      color: #787878;
    }

    z-index: 1;
  }
}
</style>
