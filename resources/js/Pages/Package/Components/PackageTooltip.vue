<template>
    <div class="package__tooltip" ref="package_tooltip">
        <svg
            @click="toggle"
            :id="toolTipId"
            width="12"
            height="12"
            viewBox="0 0 12 12"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            style="cursor: pointer"
        >
            <path
                d="M5.5 3.505C5.5 3.78114 5.72386 4.005 6 4.005C6.27614 4.005 6.5 3.78114 6.5 3.505H5.5ZM6.5 3.5C6.5 3.22386 6.27614 3 6 3C5.72386 3 5.5 3.22386 5.5 3.5H6.5ZM5.5 8.5C5.5 8.77614 5.72386 9 6 9C6.27614 9 6.5 8.77614 6.5 8.5H5.5ZM6.5 5C6.5 4.72386 6.27614 4.5 6 4.5C5.72386 4.5 5.5 4.72386 5.5 5H6.5ZM6.5 3.505V3.5H5.5V3.505H6.5ZM6.5 8.5L6.5 5H5.5L5.5 8.5H6.5ZM10 6C10 8.20914 8.20914 10 6 10V11C8.76142 11 11 8.76142 11 6H10ZM6 10C3.79086 10 2 8.20914 2 6H1C1 8.76142 3.23858 11 6 11V10ZM2 6C2 3.79086 3.79086 2 6 2V1C3.23858 1 1 3.23858 1 6H2ZM6 2C8.20914 2 10 3.79086 10 6H11C11 3.23858 8.76142 1 6 1V2Z"
                fill="#787878"
            />
        </svg>
        <div class="content" v-show="show">
            <p>Im 1. Jahr gratis</p>
        </div>
    </div>
</template>


<script>
    import { ref } from "vue";

    export default {
        props: {
            toolTipId: {
                type: [String, Number],
                required: true,
            },
        },
        setup() {
            const show = ref(false);
            const package_tooltip = ref();
            let element;

            const outside_click_hide = (e) => {
                if (
                    e.target !== package_tooltip.value &&
                    !package_tooltip.value.contains(e.target)
                ) {
                    show.value = !show.value;
                    element.classList.remove( 'active' );
                    document.removeEventListener("click", outside_click_hide);
                }
            };

            const toggle = (e) => {
                e.stopImmediatePropagation();
                element = (e.target.tagName === 'svg') ? e.target : e.target.parentElement;

                if (show.value) {
                    show.value = !show.value;
                    element.classList.remove( 'active' );
                    document.removeEventListener("click", outside_click_hide);
                } else {
                    document.dispatchEvent( new Event( 'click' ) );
                    show.value = !show.value;
                    element.classList.add( 'active' );
                    document.addEventListener("click", outside_click_hide);
                }
            };

            return {
                show,
                package_tooltip,
                toggle,
            };
        },
    };
</script>

<style lang="scss" scoped>
    .package__tooltip {
        position: relative;

        svg {
            position: absolute;
            z-index: 1;
            right: 0;
            top: -1.3rem;

            &.active {
                z-index: 3;
            }
        }

        .content {
            background-color: #ffffff;
            box-sizing: border-box;
            width: 10rem;
            position: absolute;
            border: 1px solid #ffba49;
            border-radius: 5px;
            font-family: Lato,serif;
            font-size: 12px;
            font-weight: 400;
            line-height: 18px;
            letter-spacing: 0em;
            text-align: left;
            color: #787878;
            top: -1.35rem;
            left: 6.65rem;
            z-index: 2;

            p {
                font-size: 12px;
                padding: 1.5rem 0;
                font-weight: 400;
                line-height: 18px;
                letter-spacing: 0em;
                text-align: left;
                color: #787878;
                margin: 0 auto;
                color: #787878;
            }
        }
    }
</style>
