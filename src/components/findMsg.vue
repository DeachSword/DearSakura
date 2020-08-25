<template>
  <div>
    <b-form-input
      v-model="search"
      type="text"
      name="name"
      :placeholder="$t('home.find.inputTip')"
      @keyup="finds"
    ></b-form-input>
    <small v-if="infoMsg != null" class="form-text text-info">{{infoMsg}}</small>
    <small class="form-text text-danger" v-if="errorMsg != null">{{'ERROR!'}}: {{errorMsg}}</small>
    <b-form-text v-if="searched">{{'Result'}}: {{messages.length}}</b-form-text>
    <progress class="progress is-info" max="100" v-if="loading"></progress>
    <hr v-if="messages.length">
    <b-card-group columns>
      <message v-for="(d, i) in messages" :key="i" :message="d" :index="i"></message>
    </b-card-group>
    <b-jumbotron v-if="messages.length == 0 && searched && !loading && search"  bg-variant="dark" border-variant="white" text-variant="white">
    <template v-slot:header>噓...你只需要發送!</template>

    <template v-slot:lead>
      哇喔!<br />
      這裡是一片新天地! 還沒有人傳遞訊息給 {{search}}<br />
      趕快發送訊息給 {{search}}, 總有一天它肯定能夠看到你的訊息
    </template>

    <hr class="my-4">

    <p>
      即使是給過去/未來的自己, 肯定、一定能夠傳達到的! <br />
      不管當時在做些甚麼, 儘管只是種自我滿足, 抑或想彌補些甚麼<br />
      那些都會在這那些都會在這<span class="blockquote-footer text-right text-white">留下紀錄</span>
    </p>

    <b-button-group>
        <b-button variant="primary" to="/create">{{$t('create.title')}}</b-button>
        <b-button variant="info" href="/about">{{$t('about.title')}}</b-button>
    </b-button-group>
  </b-jumbotron>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import axios from 'axios'
import Qs from 'qs'

import message from '@/components/Message.vue'

export default {
  props: ['to'],
  data() {
    return { 
      search: '',
      searched: false,
      messages: [],
      timer: '',
      loading: false,
      errorMsg: null,
      infoMsg: null
    }
  },
  components: { message },
  created() {
    if(this.$route.query.to !== undefined){
      this.$router.push({ path: `/message/${this.$route.query.to}` })
      //this.search = this.$route.query.to
    }else if(this.to !== undefined){
      this.search = this.to
    }else if(this.$route.fullPath == '/message/'){
      this.$router.replace({ path: `/` })
      this.$router.replace({ path: `/message` })
    }
  },
  computed: {
    ...mapState(['profile', 'isLogin']),
    finds() {
      if (!this.search) {
        return []
      }
      return []
    },
    findsV2() {
      console.log('findsV2 is not init on public version')
      return []
    },
    title() {
      if(this.to !== undefined){
        return decodeURIComponent(this.to)
      }
    }
  },
  watch: {
    title: {
      immediate: true,
      handler(title) {
        if (title) {
          this.updateTitle([this, `To ${title}`])
        }else{
          this.removeTitle(this)
          if(this.searched){
            this.search = null
            this.messages = []
          }
        }
      }
    },
    finds :{
      deep: true,
      handler: function () {
        var k = this.search
        if(!k || k === null || k.length == 0){
          this.infoMsg = null
          this.loading = false;
          return
        }
        this.infoMsg = `準備搜尋: ${k}`
        k = k.toLocaleLowerCase()
        this.loading = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => { this.findMessage(k)}, 5000)
      }
    }
  },
  methods: {
    ...mapMutations(['removeTitle', 'updateTitle']),
    ...mapState(['lang']),
    findMessage(name) {
      const _name = this.search != null ? this.search.toLocaleLowerCase() : null
      if(name !== _name || _name === null) return
      this.infoMsg = `正在搜尋: ${name}`;
      this.$axios.get('https://dearsakura.deachsword.com/api/getmessage/' + encodeURIComponent(name))
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = response.data.message
          }else{
            this.errorMsg = null
            this.infoMsg = `搜尋成功!`
            this.messages = response.data.result
          }
          this.pushRouter()
        })
        .catch((error) => {
          console.log(error)
          this.pushRouter()
        })
        .finally(() => {
          //mobile is not work...
        });
        try{this.$ga.event('DearSakura', { method: '搜尋訊息' })}catch (error) {
          //
        }
    },
    pushRouter(){
      this.$router.push({ path: `/message/${this.search}` })
      this.loading = false;
      clearTimeout(this.timer);
      this.searched = true
    }
  },
  beforeDestroy() {
    this.removeTitle(this)
    clearTimeout(this.timer);
  },
  metaInfo() {
    if (this.search) return {
      meta: [
        { vmid: 'og:description', name: 'og:description', content: `Are you ${decodeURIComponent(this.search)}? Hope you can receive these messages` } , 
        { vmid: 'description', name: 'description', content: `Are you ${decodeURIComponent(this.search)}? Hope you can receive these messages` }  
      ]
    }
  }
}
</script>

<style>

.progress {
    -moz-appearance: none;
    -webkit-appearance: none;
    border: none;
    border-radius: 290486px;
    display: block;
    height: 1rem;
    overflow: hidden;
    padding: 0;
    width: 100%
}

.progress::-webkit-progress-bar {
    background-color: #ededed
}

.progress::-webkit-progress-value {
    background-color: #3298dc
}

.progress::-moz-progress-bar {
    background-color: #3298dc
}

.progress::-ms-fill {
    background-color: #3298dc;
    border: none
}

.progress:indeterminate {
    animation-duration: 1.5s;
    animation-iteration-count: infinite;
    animation-name: moveIndeterminate;
    animation-timing-function: linear;
    background-color: #ededed;
    background-image: linear-gradient(90deg,#3298dc 30%,#ededed 0);
    background-position: 0 0;
    background-repeat: no-repeat;
    background-size: 150% 150%
}

.progress:indeterminate::-webkit-progress-bar {
    background-color: transparent
}

.progress:indeterminate::-moz-progress-bar {
    background-color: transparent
}
@keyframes moveIndeterminate {
    0% {
        background-position: 200% 0
    }

    to {
        background-position: -200% 0
    }
}
.favourites-btn {
  top: 8px;
  right: 8px;
  position: absolute;
}

.message-footer-header__value {
    font-size: 12px;
    font-weight: 500;
    margin-right: 10px;
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,.75);
}

.message-footer-header__value--has-favourites {
    background: hsla(var(--hsl-b6),.25);
    padding: 2px 5px;
    border-radius: 4px
}

.message-footer-header__value--has-favourites:hover {
    background: hsla(var(--hsl-b6),.5)
}

.message-footer-header__value-icon {
    padding-right: 2px;
    color: #fff;
}

.message-footer-header__value-icon--favourites:active,.message-footer-header__value-icon--favourites:hover,.message-footer-header__value-icon--favourites:link,.message-footer-header__value-icon--favourites:visited {
    text-decoration: none;
    color: #fff
}

.message-footer-header__value-icon--favourites:hover {
    color: #cc5288
}

.message-footer-header__value-icon--favourited:hover,.message-footer-header__value-icon--favourited:link {
    color: #b17
}

</style>
