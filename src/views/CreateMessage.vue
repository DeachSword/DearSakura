<template>
  <div>
    <b-overlay :show="token==null ? true : false" variant="dark"
            opacity="0.85"
            blur="2px"
            spinner-variant="white"
            spinner-type="grow"
            rounded="sm"
          >
      <createMsg :token="token"></createMsg>
      <template v-slot:overlay>
          <div v-if="errorMsg == null">
            <div class="text-center">
              <b-icon icon="person-circle" font-scale="3" animation="cylon"></b-icon>
              <p id="cancel-label">{{$t('create.loginTip')}}</p>
              <b-button
                href="https://www.deachsword.com/serverbot/sso"
                variant="outline-info"
                size="sm"
                v-b-tooltip.hover 
                :title="$t('home.login.tipLogin')"
              >
                LOGIN
              </b-button>
            </div>
          </div>
          <div v-else>
            <div class="text-center">
              <b-icon variant="danger" icon="exclamation-circle-fill" font-scale="3"></b-icon>
              <p id="cancel-label">{{errorMsg}}</p>
            </div>
          </div>
        </template>
    </b-overlay>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import createMsg from '@/components/createMsg'

export default {
  components: { createMsg },
  data() {
    return {
      token: null,
      errorMsg: null,
    }
  },
  beforeDestroy() {
    this.removeTitle(this)
  },
  created() {
    if(this.token === null){
      this.$axios.get('https://dearsakura.deachsword.com/api/issuetoken')
        .then((response) => {
          if(response.data.message !== 'success'){
            this.errorMsg = response.data.message
          }else{
            this.token = response.data.result
          }
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