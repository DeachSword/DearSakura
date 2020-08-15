<template>
  <div>
    <p>{{$t('create.p1')}}</p>
    <b-form-input
      name="_from"
      v-model="$data._from"
      type="text"
      :placeholder="$t('create.inputTip._from')"
    ></b-form-input>
    <br />
    <transition mode="in-out">
      <b-form-input
        name="to"
        v-if="$data._from"
        v-model="to"
        type="text"
        :placeholder="$t('create.inputTip.to')"
      ></b-form-input>
    </transition>
    <br />
    <transition mode="in-out">
      <b-form-textarea
        name="message"
        v-if="to" 
        v-model="message"
        :placeholder="$t('create.inputTip.message')"
        rows="3"
        max-rows="10"
        no-resize
      ></b-form-textarea>
    </transition>
    <br />
    <b-button type="submit" variant="primary"  v-if="canSub" @click="createMessage">{{$t('create.title')}}</b-button>
    <p class="form-text text-danger" v-if="errorMsg != null">{{'ERROR!'}}: {{errorMsg}}</p>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import Qs from 'qs'


export default {
  props: ['token'],
  data() {
    return { 
      _from: '',
      to: '',
      message: '',
      errorMsg: null,
      canSub: false
    }
  },
  computed: {
    CM() {
      return this.message
    }
  },
  watch: {
    CM :{
      handler: function (msg) {
        this.errorMsg = null
        if(this.$data._from && this.$data._from.trim().length == 0){
          this.errorMsg = '不得為空'
        }else if(this.$data.to && this.$data.to.trim().length == 0){
          this.errorMsg = '不得為空'
        }else if(msg){
          if(msg.trim().length > 10){
              this.canSub = true
              return
          }else{
            this.errorMsg = '訊息至少10個字'
          }
        }
        this.canSub = false
      }
    }
  },
  methods: {
    ...mapState(['lang']),
    createMessage() {
      if(!this.canSub) return this.errorMsg = '不允許'
      this.$ga.event('DearSakura', { method: '創建訊息' })

      this.$axios.post('/api/init.php', Qs.stringify({
          'act': 'DearSakura',
          'data': {
              "_from" : this.$data._from.trim(),
              "to" : this.to.trim(),
              "msg" : this.message.trim()
          },
          'token': this.token
        }))
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = response.data.message
          }else{
            this.$router.push(`/?to=${this.to}`)
          }
        })
        .catch(function (error) {
          console.log(error)
        });
    }
  },
  beforeDestroy() {
    clearTimeout(this.timer);
  }
}
</script>

<style>

</style>
