<template>
    <div class="chart-box flex flex-col bg-white p-4 rounded-[20px]">
        <div class="head flex items-center flex-col mb-4">
            <h3
                class="title text-primary-1 font-poppins font-semibold text-18xl mb-4"
                v-if="title"
            >
                {{ title }}
            </h3>
            <p class="text-primary-3 font-poppins">Total: {{ total ?? 0 }}</p>
        </div>
        <Pie
            :chart-options="chartOptions"
            :chart-data="chartData"
            :chart-id="chartId"
            :dataset-id-key="datasetIdKey"
            :plugins="[DataLabels]"
            :css-classes="cssClasses"
            :styles="styles"
            :width="width"
            :height="height"
        />
        <div class="labels flex flex-row justify-center gap-4 mt-5">
            <div
                v-for="(item, index) in chartData.labels"
                :key="item"
                class="labels-container flex flex-row items-center gap-2"
            >
                <div
                    class="box min-w-[8px] min-h-[8px]"
                    :style="{
                        background: chartData.datasets[0].backgroundColor[index],
                    }"
                ></div>
                <div class="label text-4">
                    {{ item }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Pie } from "vue-chartjs";
import DataLabels from "chartjs-plugin-datalabels";
// import staticColors from "../../colors.js"
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
} from "chart.js";
import { computed, ref } from "@vue/reactivity";

const staticColors = ['red', 'green', 'blue', 'orange']
ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    chartId: {
        type: String,
        default: "pie-chart",
    },
    datasetIdKey: {
        type: String,
        default: "label",
    },
    title: {
        required: false,
    },
    total: {
        required: false,
    },
    width: {
        type: Number,
        default: 300,
    },
    height: {
        type: Number,
        default: 300,
    },
    cssClasses: {
        default: "",
        type: String,
    },
    styles: {
        type: Object,
        default: () => { },
    },
    label: {
        type: Array,
        default: () => [],
    },
    colors: {
        type: Array,
        default: () => [],
    },
    textColors: {
        type: [String, Array],
        default: "white",
    },
    data: {
        type: Array,
        default: () => [],
    },
    showPercentage: {
        default: true,
    },
    dataLabelPrefix: {
        default: ""
    }
});

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
    },
});

const chartData = computed(() => {
    return {
        labels: props.label,
        datasets: [
            {
                datalabels: {
                    anchor: "end",
                    clamp: true,
                    align: "start",
                    formatter: (value, ctx) => {
                        if (props.showPercentage) {
                            const total = props.data.reduce((sum, cur) => (sum + (cur ? parseFloat(cur) : 0)), 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            const display = [`${props.dataLabelPrefix} ${value}`, `${percentage} %`];
                            return display;
                        }
                        return value;
                    },
                    color: props.textColors,
                    font: {
                        family: "Ubuntu",
                        style: "normal",
                        size: "16px",
                        weight: "700",
                    },
                },
                backgroundColor: props.colors.length ? props.colors : staticColors,
                data: props.data,
            },
        ],
    };
});
</script>

<style scoped>
#pie-chart label {
    display: block;
    color: red !important;
}
</style>
