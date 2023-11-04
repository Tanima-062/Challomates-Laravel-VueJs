<template>
    <div class="status">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="5" cy="5" r="5" :fill="fillColor" />
        </svg>
        <span class="status-tooltip">{{ $t(statusText) }}</span>
    </div>
</template>

<script>
export default {
    props: {
        status: {
            type: String,
            required: true,
        },
    },
    computed: {
        fillColor() {
            const status_color = { 'new': '#1AA1E4', 'pending': '#05655A','registration pending': '#05655A', 'active': '#06CEB5', 'expired': '#102327', 'canceled': '#FFA013', 'posted': '#FFD698' }
            return status_color[this.status] ?? "#FF0000"
        },

        statusText() {
            const status_de = { 'active': 'Aktiv', 'inactive': 'Inaktiv', 'pending': 'Anstehend', 'registration pending': 'Registrierung pendent', 'new': 'Neu', 'canceled': 'Abgebrochen', 'expired': 'Abgelaufen', 'posted': 'Posted' }
            return status_de[this.status] || this.status;
        }
    },
};
</script>

<style lang="scss" scoped>
.status {
    position: relative;

    .status-tooltip {
        position: absolute;
        top: 0;
        left: 25px;
        display: none;
        padding: 5px 10px;
        border-radius: 4px;
        background-color: black;
        color: white;
        z-index: 999;
        font-size: 14px;
        line-height: 16px;
        // font-weight: 700;
        text-align: center;
        min-width: 70px;
        word-break: normal;

        animation: .8s ease-in-out;

        font-family: 'Ropa Sans';
        font-style: normal;
        font-weight: 400;
        // font-size: 14px;
        // line-height: 18px;

        // color: #135F84;


        &::before {


            content: "";
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 5px 7.5px 5px 0;
            border-color: transparent black transparent transparent;
            display: inline-block;
            position: absolute;
            left: -7px;
            top: 50%;
            transform: translateY(-50%);
        }
    }

}

svg:hover+.status-tooltip {
    display: block;
}
</style>
