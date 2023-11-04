<template>
    <option :value="option.id">{{option[name]}} <a v-if="isShowLink" :href="link" target="_blank" class="text-gray-3 text-16 underline underline-offset-2 font-ropa float-right">{{title}}</a></option>
</template>
<script setup>
    const props = defineProps({
        option: {
            type: [Array,Object],
            required: true,
        },
        name:{
            type:String,
            required:true,
        },
        title:{
            type:String,
            default:''
        },
        isShowLink:{
            type:Boolean,
            default:false
        },
        link:{
            type: String,
            default:''
        }
    });
</script>
