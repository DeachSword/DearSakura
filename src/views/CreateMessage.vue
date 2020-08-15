<template>
  <b-overlay :show="token==null ? true : false" variant="dark"
          opacity="0.85"
          blur="2px"
          spinner-variant="white"
          spinner-type="grow"
          rounded="sm"
        >
    <createMsg :token="token"></createMsg>
    <template v-slot:overlay>
        <div class="text-center">
          <b-icon icon="person-circle" font-scale="3" animation="cylon"></b-icon>
          <p id="cancel-label">您需要 登入 才能發布訊息</p>
          <b-button
            href="https://www.deachsword.com/serverbot/sso"
            variant="outline-info"
            size="sm"
            v-b-tooltip.hover 
            :title="'點擊登入'"
          >
            LOGIN
          </b-button>
        </div>
      </template>
  </b-overlay>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import createMsg from '@/components/createMsg'

export default {
  components: { createMsg },
  data() {
    return {
      token: null
    }
  },
  beforeDestroy() {
    this.removeTitle(this)
  },
  created() {
    if(this.token === null){
      this.$axios.get('https://dearsakura.deachsword.com/api/issuetoken')
        .then((response) => {
          this.token = response.data
        })
        .catch(function (error) {
        });
    }
  },
  watch: {
    title: {
      immediate: true,
      handler(title) {
        if (title) {
          this.updateTitle([this, title])
        }
      }
    }
  },
  computed: {
    title() {
      return this.$t('create.title')
    }
  },
  methods: {
    ...mapState(['lang']),
    ...mapMutations(['removeTitle', 'updateTitle']),
    track () {
      this.$ga.screenview('DearSakura/Create')
    }
  },
  metaInfo: {
    meta: [
      { vmid: 'og:description', property: 'og:description', content: 'Send her a message...just hope she can receive it, it will be a kind of salvation for me' } , 
      { vmid: 'description', name: 'description', content: 'Send her a message...just hope she can receive it, it will be a kind of salvation for me' }  
    ]
  }
}
</script>