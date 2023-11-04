<template>
    <div class="status">
        <svg class="absolute" width="15" height="15" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="5" cy="5" r="5" :fill="fillColor"/>
        </svg>

        <svg v-if="published === 'not_published' && status !== 'drawn'" class="absolute top-[2.7px] left-[2.7px]" width="10" height="10" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="3" cy="3" r="2.5" fill="#FF0000" stroke="white"/>
        </svg>

        <span class="status-tooltip">{{ statusText }}</span>
    </div>
</template>

<script>
export default {
    props: {
        status: {
            type: String,
            required: true,
        },

        statusColor: {
            type: Object,
            default: {
                'active_published' : '#06CEB5',
                'finished_published' : '#FF0000',
                'drawn_published' : '#102327',
                'drawn_not_published' : '#102327'
            }
        },

        statusToolTip: {
            type: Object,
            default: {
                'active_published' : 'Aktiv (Publiziert)',
                'finished_published' : 'Beendet (Publiziert)',
                'drawn_published' : 'Ausgelost (Drawn)',
                'drawn_not_published' : 'Ausgelost (Drawn)',
            }
        }
    },
    computed: {
        fillColor() {
            return this.statusColor[this.status];
        },
        statusText(){
            return this.statusToolTip[this.status];
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
        background-color: #FFFFFF;
        color: #135F84;
        z-index: 999;
        font-size: 14px;
        line-height: 16px;
        // font-weight: 700;
        text-align: center;
        min-width: 70px;
        width: 160px;

        animation: .8s ease-in-out;

        font-family: 'Ropa Sans',serif;
        font-weight: 600;
        // font-size: 14px;
        // line-height: 18px;

        // color: #135F84;


        &::before {
            content: "";
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 5px 7.5px 5px 0;
            border-color: transparent #FFFFFF transparent transparent;
            display: inline-block;
            position: absolute;
            left: -7px;
            top: 50%;
            transform: translateY(-50%);
        }
    }

}

svg:hover + .status-tooltip {
    display: block;
}
</style>
