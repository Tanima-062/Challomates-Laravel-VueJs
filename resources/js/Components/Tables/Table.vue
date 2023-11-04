<template>
    <div class="table">
        <div class="thead bg-white">
            <div class="tr">
                <div class="content">
                    <slot name="thead"></slot>
                </div>
            </div>
        </div>
        <div class="tbody">
            <slot name="tbody"></slot>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>

<style lang="scss" scoped>

</style>
