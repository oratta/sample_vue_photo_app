<template>
    <p>システムエラーが発生しました！<br>code: {{ errorCode }}<br>message: {{ errorMessage }}</p>
</template>

<script>
    export default {
        computed: {
            errorCode(){
                return this.$store.state.error.code
            },
            errorMessage(){
                return this.$store.state.error.message
            }
        }
    }
</script>