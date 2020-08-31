<template>
    <div>{"code": 200, "message": "debug!"}</div>
</template>
<script>
    import { mapMutations } from 'vuex'
    export default {
        methods: {
            ...mapMutations(['setApiState']),
        },
        created() {
            this.setApiState(true)
        }
    }
</script>