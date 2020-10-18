<template>
    <b-card bg-variant="dark" border-variant="white" text-variant="white" style="margin: 0.625rem;" ref="msgBox">
        <template v-slot:header>
          <div class="ds-layout" style="flex-direction: row;">
            <code class="ds-layout__section--full">To {{message.to}}</code>
            <b-button
              variant="outline-danger"
              pill
              right
              v-b-tooltip.hover.bottom
              :title="message.has_favourited ? '取消收藏' : '收藏'"
              size="sm"
              @click="favourite()"
              v-show="isLogin"
            >
              <b-icon :icon="message.has_favourited ? 'heart-fill' : 'heart'"></b-icon>
            </b-button>
            <b-button
              variant="outline-dark"
              pill
              right
              v-b-tooltip.hover.bottom
              title="嵌入"
              size="sm"
              v-b-modal="'modal-msg-api2if_' + message.id">
                <b-icon icon="three-dots-vertical" variant="white"></b-icon>
            </b-button>
            <b-modal centered :id="'modal-msg-api2if_' + message.id" title="嵌入網址" @shown="doCopy()">
              <div>
                <b-input-group prepend="@" class="mb-2 mr-sm-2 mb-sm-0">
                  <b-input :id="'inputApi2If_' + message.id" :value="getApi2Iframe()" readonly></b-input>
                </b-input-group>
          </div>
            </b-modal>
          </div>
        </template>
        <b-card-text>
            <pre style="color: unset" text-variant="white" 
            v-b-tooltip.hover.bottom title="Message">{{message.message}}</pre>
        </b-card-text>

        <footer class="blockquote-footer text-right text-white"><cite 
            v-b-tooltip.hover.bottom title="作成者">{{message._from}}</cite> sub on {{message.createdTime}}
        </footer>
        
        <template v-slot:footer>
            <div>
                <span class="message-footer-header__value" title="收藏數" v-b-tooltip.hover.top>
                    <span class="message-footer-header__value-icon">
                        <b-icon icon="heart-fill"></b-icon>
                    </span>
                    <span class="message-footer-header__value-name">{{message.favourite_count}}</span>
                </span>
                <b-button variant="primary" class="message-footer-header__value" v-b-modal="isLogin ? 'modal-msg-rating_' + rndkey + message.id : null"
                    :title="isLogin ? null : '登入後即可評價訊息!'" v-b-tooltip.hover.bottom>{{$t('message.rating')}}
                    <span class="message-footer-header__value-icon">
                        <b-icon icon="star-fill"></b-icon>
                    </span>
                    <span class="message-footer-header__value-name">{{message.rating}}</span>
                </b-button>
                <b-modal centered :id="'modal-msg-rating_' + rndkey + message.id" title="Submit Rating" @show="ratingData.rating=-1" @ok="rateMessage(message.id)" :ok-disabled="ratingData.rating != -1 ? false : true">
                    <b-rating
                    v-if="isLogin || message.isRated"
                    icon-empty="heart"
                    icon-half="heart-half"
                    icon-full="heart-fill"
                    variant="danger"
                    stars="10"
                    :value="message.isRated ? message.selfRating : null"
                    show-value
                    :readonly="(message.isRated || !message.canRating)? true : false"
                    @change="ratingData.id=message.id, ratingData.rating=$event"
                    ></b-rating>
                </b-modal>
            </div>
            <b-alert variant="success" :show="infoMsg != null ? true : false">{{infoMsg}}</b-alert>
            <b-alert variant="danger" :show="errorMsg != null ? true : false">{{errorMsg}}</b-alert>
        </template>
    </b-card>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import Qs from 'qs'

export default {
  props: ['message'],
  data: function()  {
    return {
        infoMsg: null,
        errorMsg: null,
        canRating: false,
        ratingData: {
            id: -1,
            rating: -1
        },
        _height: -1,
        rndkey: this.rndStr(7)
    }
  },
  computed: {
    ...mapState(['profile', 'isLogin'])
  },
  methods: {
    rateMessage(msgId){
      if(msgId == this.ratingData.id){
        this.infoMsg = null
        this.errorMsg = null
        this.$axios.post('https://dearsakura.deachsword.com/api/rating', Qs.stringify({
          "msgId" : msgId,
          "rating" : this.ratingData.rating
        }))
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = `評分失敗: ${response.data.message}`
          }else{
            this.errorMsg = null
            this.message.rating = response.data.result
            this.message.isRated = true
            this.message.selfRating = this.ratingData.rating
            this.infoMsg = `評分成功!`
          }
        })
        .catch((error) => {
            console.log(error)
            this.errorMsg = `評分失敗: 請聯絡管理員`
        })
        try{this.$ga.event('DearSakura', { method: '訊息評分' })}catch (error) {
          //
        }
      }
    },
    favourite() {
      if(this.message.has_favourited){ //SO FOOL >:D
        this.$axios.delete(`https://dearsakura.deachsword.com/api/favourites`, { data: { msgId: this.message.id } })
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = `取消收藏失敗: ${response.data.message}`
          }else{
            this.errorMsg = null
            this.message.favourite_count = response.data.result.favourite_count
            this.message.has_favourited = false
          }
        })
        .catch((error) => {
            console.log(error)
            this.errorMsg = `取消收藏失敗...(|||ﾟдﾟ)`
        })
      }else{
        this.$axios.post(`https://dearsakura.deachsword.com/api/favourites`, Qs.stringify({
          "msgId" : this.message.id
        }))
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = `收藏失敗: ${response.data.message}`
          }else{
            this.errorMsg = null
            this.message.favourite_count = response.data.result.favourite_count
            this.message.has_favourited = true
          }
        })
        .catch((error) => {
            console.log(error)
            this.errorMsg = `收藏失敗...(|||ﾟдﾟ)`
        })
      }
      try{this.$ga.event('DearSakura', { method: '訊息收藏' })}catch (error) {}
    },
    matchHeight() {
      this.$data._height = this.$refs.msgBox.offsetHeight; //clientHeight
    },
    getApi2Iframe(){
      let base = `<iframe src="https://dearsakura.deachsword.com/api2/msg/${this.message.id}" width="100%" height="${this.$data._height}" style="border:none;overflow:hidden;min-height: ${this.$data._height}px;" scrolling="no" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>`
      return base
    },
    doCopy() {
      document.querySelector(`#inputApi2If_${this.message.id}`).select()
      document.execCommand('copy');
    },
    rndStr(len) {
    	let text = " "
    	let chars = "abcdefghijklmnopqrstuvwxyz0123456789"
    
      for( let i=0; i < len; i++ ) {
				text += chars.charAt(Math.floor(Math.random() * chars.length))
      }

			return text
		}
  },
  mounted () {
    this.matchHeight()
  }
}
</script>