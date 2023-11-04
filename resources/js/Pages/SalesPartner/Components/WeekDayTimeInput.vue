<template>
    <div
        class="weekday-table rounded-[24px] border-[1px] w-2/3 bg-[#ffffff80] overflow-hidden"
        :class="[attrs.error ? 'border-error' : 'border-gray-corner']"
    >
        <div
            class="head border-b-[1px] border-gray-corner"
            :class="{ 'bg-white': editable }"
        >
            <div class="row flex divide-x-2 divide-gray-corner">
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        classLists="border-gray-corner"
                        :value="allSelected ? 1 : 0"
                        :selected="allSelected"
                        @toggle="toggleAllSelect"
                    />
                </div>
                <div class="column py-2 text-gray-3 text-15 font-semibold font-poppins w-[26%] !border-l-[0px]">
                    {{ editable ? 'Alle auswählen' : '' }}</div>
                <div class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins">Von</div>
                <div class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins">Bis</div>
                <div class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins">Von</div>
                <div class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins">Bis</div>
            </div>
        </div>
        <div class="body divide-y-[1px] divide-gray-corner">

            <!-- monday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.monday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.monday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.monday"
                        @toggle="toggleDayStatus('monday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.monday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Montag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.monday?.first_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.monday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.monday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.monday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.monday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.monday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.monday?.last_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.monday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.monday.last_start_time"
                        :requiredTime="attrs.modelValue.monday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.monday?.last_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.monday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.monday.last_end_time"
                        :requiredTime="attrs.modelValue.monday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- tuesday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.tuesday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.tuesday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.tuesday"
                        @toggle="toggleDayStatus('tuesday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.tuesday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Dienstag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.tuesday?.first_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.tuesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.tuesday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.tuesday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.tuesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.tuesday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.tuesday?.last_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.tuesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.tuesday.last_start_time"
                        :requiredTime="attrs.modelValue.tuesday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.tuesday?.last_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.tuesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.tuesday.last_end_time"
                        :requiredTime="attrs.modelValue.tuesday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- wednesday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.wednesday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.wednesday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.wednesday"
                        @toggle="toggleDayStatus('wednesday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.wednesday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Mittwoch</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.wednesday?.first_start_time
                        }}
                    </p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.wednesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.wednesday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.wednesday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.wednesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.wednesday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.wednesday?.last_start_time
                        }}
                    </p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.wednesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.wednesday.last_start_time"
                        :requiredTime="attrs.modelValue.wednesday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.wednesday?.last_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.wednesday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.wednesday.last_end_time"
                        :requiredTime="attrs.modelValue.wednesday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- thursday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.thursday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.thursday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.thursday"
                        @toggle="toggleDayStatus('thursday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.thursday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Donnerstag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.thursday?.first_start_time
                        }}
                    </p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.thursday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.thursday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.thursday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.thursday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.thursday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.thursday?.last_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.thursday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.thursday.last_start_time"
                        :requiredTime="attrs.modelValue.thursday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.thursday?.last_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.thursday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.thursday.last_end_time"
                        :requiredTime="attrs.modelValue.thursday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- friday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.friday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.friday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.friday"
                        @toggle="toggleDayStatus('friday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.friday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Freitag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.friday?.first_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.friday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.friday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.friday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.friday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.friday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.friday?.last_start_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.friday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.friday.last_start_time"
                        :requiredTime="attrs.modelValue.friday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.friday?.last_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.friday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.friday.last_end_time"
                        :requiredTime="attrs.modelValue.friday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- saturday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.saturday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.saturday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.saturday"
                        @toggle="toggleDayStatus('saturday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.saturday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Samstag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.saturday?.first_start_time
                        }}
                    </p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.saturday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.saturday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >{{
                        attrs.modelValue.saturday?.first_end_time
                        }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.saturday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.saturday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.saturday?.last_start_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.saturday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.saturday.last_start_time"
                        :requiredTime="attrs.modelValue.saturday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.saturday?.last_end_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.saturday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.saturday.last_end_time"
                        :requiredTime="attrs.modelValue.saturday.last_start_time ? true : false"
                    />
                </div>
            </div>

            <!-- sunday -->
            <div
                class="row flex divide-x-2 divide-gray-corner"
                :class="{ 'bg-white': attrs.modelValue.sunday && editable }"
            >
                <div
                    v-if="editable"
                    class="column py-2 w-[6%] flex justify-center items-center"
                >
                    <CheckBox
                        :value="attrs.modelValue.sunday ? 1 : 0"
                        classLists="border-gray-corner"
                        :selected="attrs.modelValue.sunday"
                        @toggle="toggleDayStatus('sunday')"
                    />
                </div>
                <div
                    class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa"
                    :class="[!editable ? 'px-3' : '', !editable || !attrs.modelValue.sunday ? 'text-gray-3' : 'text-tints-5']"
                >
                    Sonntag</div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.sunday?.first_start_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.sunday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.sunday.first_start_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.sunday?.first_end_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.sunday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.sunday.first_end_time"
                        :requiredTime="true"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.sunday?.last_start_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.sunday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.sunday.last_start_time"
                        :requiredTime="attrs.modelValue.sunday.last_end_time ? true : false"
                    />
                </div>
                <div class="column w-[17%]">
                    <p
                        class="text-tints-5 text-18x px-5 py-2"
                        v-if="!editable"
                    >
                        {{ attrs.modelValue.sunday?.last_end_time }}</p>
                    <TimeInput
                        class="h-full"
                        inputClass="py-2 px-5"
                        v-if="attrs.modelValue.sunday && editable"
                        placeholder="00:00"
                        v-model="attrs.modelValue.sunday.last_end_time"
                        :requiredTime="attrs.modelValue.sunday.last_start_time ? true : false"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { toRaw, ref, computed } from '@vue/reactivity';
import { onUpdated } from 'vue';
import { watch } from '@vue/runtime-core';
import CheckBox from '../../../Components/Form/CheckBox.vue';
import { useAttrs } from 'vue'
import TimeInput from '../../../Components/Form/TimeInput.vue';

const attrs = useAttrs()
const emit = defineEmits(['update:modelValue', 'changed'])

defineProps({
    editable: {
        type: Boolean,
        default: true,
    }
})
const allSelected = computed(() => {
    return Object.keys(attrs.modelValue).length == 7
})

const toggleAllSelect = () => {
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
    let opening_hours = { ...toRaw(attrs.modelValue) }
    if(allSelected.value) {
        days.forEach(day => {
            if (!attrs.modelValue[day] || (!attrs.modelValue[day].first_start_time && !attrs.modelValue[day].first_end_time && !attrs.modelValue[day].last_start_time && !attrs.modelValue[day].last_end_time)) {
                delete opening_hours[day]
            }
        })
    }

    days.forEach(day => {
        if (!attrs.modelValue[day]) {
            let opening_times = {
                day,
                first_start_time: null,
                first_end_time: null,
                last_start_time: null,
                last_end_time: null,
            }
            opening_hours[day] = opening_times
        }
    });
    emit('update:modelValue', opening_hours)
}
const toggleDayStatus = (day, status = null) => {
    let opening_hours = { ...toRaw(attrs.modelValue) }

    let opening_times = {
        day: null,
        first_start_time: null,
        first_end_time: null,
        last_start_time: null,
        last_end_time: null,
    }
    if (status == null) {
        if (opening_hours[day]) {
            delete opening_hours[day]
        } else {
            opening_times.day = day
            opening_hours[day] = opening_times
        }
    }
    emit('update:modelValue', opening_hours)
}
</script>
