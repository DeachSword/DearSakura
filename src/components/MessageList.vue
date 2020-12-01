<template>
    <b-card-group deck v-if="messages.length > 0">
        <div v-for="(d, i) in availableMessages" class="messages__items-row">
            <message v-for="(md, mi) in d" :key="rndStr(5) + '_' + mi" :message="md"></message>
        </div>
    </b-card-group>
</template>

<script>
    import { mapMutations } from 'vuex'

    import message from '@/components/Message.vue'
    export default {
        props: ['messages'],
        data() {
            return {
                divideCount: 2
            }
        },
        components: { message },
        computed: {
            availableMessages() {
                return this.divideArray(this.messages, this.divideCount)
            }
        },
        methods: {
            divideArray (array, count) {
                const length = Math.ceil(array.length / count)
                return new Array(length).fill().map((_, i) =>
                    array.slice(i * count, (i + 1) * count)
                )
            },
            rndStr(len) {
                let text = ""
                let chars = "abcdefghijklmnopqrstuvwxyz0123456789"
            
                for( let i=0; i < len; i++ ) {
                            text += chars.charAt(Math.floor(Math.random() * chars.length))
                }

                return text
            },
            onResize(w) {
                var w = window.innerWidth;
                if(w >= 992){
                    this.divideCount = 2
                }
                else{
                    this.divideCount = 1
                }
            }
        },
        created() {
            window.addEventListener("resize", this.onResize);
        },
        destroyed() {
            window.removeEventListener("resize", this.onResize);
        }
    }
</script>