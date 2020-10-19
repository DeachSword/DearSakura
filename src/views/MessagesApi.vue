<template>
    <b-overlay :show="searched == false ? true : false" rounded="sm">
        <div v-if="message != null">
            <message :message="message" style="width: auto;margin: unset;"></message>
        </div>
        <div v-else>
            <client-only>
                <b-card bg-variant="dark" text-variant="white" title="Message Not Found">
                    <b-card-text>
                        {{errMsg}}
                    </b-card-text>
                    <b-button href="/" variant="primary">查看其他訊息</b-button>
                </b-card>
            </client-only>
        </div>
    </b-overlay>
</template>
<script>
    import { mapMutations } from 'vuex'

    import message from '@/components/Message.vue'
    export default {
        props: ['msgId'],
        data() {
            return {
                'message': null,
                'errMsg': '該訊息不存在或是已被刪除',
                'searched': false
            }
        },
        components: { message },
        methods: {
            ...mapMutations(['setApiState']),
            findMessage() {
                this.$axios.get('https://dearsakura.deachsword.com/api/getmessage?msgid=' + this.msgId)
                .then((response) => {
                    if(response.data.message == "success"){
                        if(response.data.result.length > 0){
                            this.message = response.data.result[0]
                        }
                    }else{
                        this.errMsg = response.data.message
                    }
                })
                .catch((error) => {
                    console.log(error)
                })
                .finally(() => {
                    this.searched = true;
                });
                try{this.$ga.event('DearSakura', { method: '搜尋訊息' })}catch (error) {}
            },
        },
        created() {
            this.setApiState(true)
            this.findMessage()
        }
    }
</script>