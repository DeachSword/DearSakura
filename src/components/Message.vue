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
              v-b-modal="'modal-msg-api2if_' + rndkey + message.id">
                <b-icon icon="three-dots-vertical" variant="white"></b-icon>
            </b-button>
            <b-modal centered :id="'modal-msg-api2if_' + rndkey + message.id" title="嵌入網址">
              <div>
                <b-input-group prepend="Iframe" class="mb-2 mr-sm-2 mb-sm-0">
                  <b-input :id="'inputApi2If_' + rndkey + message.id" :value="getApi2Iframe()" readonly></b-input>
                  <b-input-group-append>
                    <b-button variant="info" @click="doCopy('inputApi2If')"><b-icon icon="files"></b-icon></b-button>
                  </b-input-group-append>
                </b-input-group>
                <b-input-group prepend="URI" class="mb-2 mr-sm-2 mb-sm-0">
                  <b-input :id="'inputApi2Uri_' + rndkey + message.id" :value="getApi2Iframe(true)" readonly></b-input>
                  <b-input-group-append>
                    <b-button variant="info" @click="doCopy('inputApi2Uri')"><b-icon icon="files"></b-icon></b-button>
                  </b-input-group-append>
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
            <div class="ds-layout" style="flex-direction: row;">
              <div  class="ds-layout__section--full">
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
              <b-button variant="info" class="message-footer-header__value" v-b-modal="'modal-msg-comments_' + rndkey + message.id"><b-icon icon="chat-dots-fill"></b-icon>  {{message.comment_count}} 則留言</b-button>
              <div v-if="isApi">
                <b-button variant="outline-light" class="message-footer-header__value" :to="'/message/' + message.to">查看貼文串</b-button>
              </div>
              <b-modal centered :id="'modal-msg-comments_' + rndkey + message.id" size="xl" title="Comments"
                footer-bg-variant="dark"
                footer-text-variant="light">
                <div v-if="message.comment_count == 0">
                  沒有留言ヽ(･ω･｀)
                </div>
                <div v-else>
                  <b-card bg-variant="white" text-variant="dark" v-for="cmt in message.comments">
                    <b-avatar :src="getUserData(cmt.userId).pictureUrl"></b-avatar><b class="ml-2">{{getUserData(cmt.userId).displayName}}</b>
                    <b-card-text class="ml-5">
                      {{cmt.message}}
                      <div class="text-secondary">{{cmt.createdTime}}</div>
                    </b-card-text>
                  </b-card>
                </div>
                <template #modal-footer>
                  <div class="w-100">
                    <b-form-textarea
                      name="comment_message"
                      v-model="commentData.message"
                      placeholder="在此輸入新留言"
                      max-rows="5"
                      no-resize
                      :disabled="!isLogin"
                    ></b-form-textarea>
                    <b-button v-if="isLogin" pill class="comment-editor_submit-btn" :disabled="commentData.message == '' ? true : false" @click="submitComment('messageset', message.id, commentData.message)">發表</b-button>
                    <b-button v-else pill class="comment-editor_submit-btn" variant="success" href="https://www.deachsword.com/serverbot/sso">
                      <b-icon icon="lock-fill"></b-icon> 登入以留言
                    </b-button>
                  </div>
                </template>
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
        commentData: {
            message: ''
        },
        _height: -1,
        rndkey: this.rndStr(7)
    }
  },
  computed: {
    ...mapState(['profile', 'isLogin', 'isApi', 'users'])
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
    submitComment(a1, a2, a3) {
      this.commentData.message = '';
      this.$axios.post(`https://dearsakura.deachsword.com/api/comments`, Qs.stringify({
        "commentable_type": a1,
        "commentable_id": a2,
        "message": a3
      }))
      .then((response) => {
        if(response.data.message !== "success"){
          alert(`can't comment! \n\n${response.data.message}`)
        }else{
          console.log('comment success!')
          console.log(response.data)
        }
      })
      .catch((error) => {
          console.log(error)
          var err_code = error.response.status
          switch (err_code) {
            case 500:
              alert("server error, can't comment!")
              break;
            default:
              alert(error.response.data.message)
              break;
          }
      })
    },
    getUserData(mid) {
      if(mid in this.users) return this.users[mid];
      console.log(`[getUserData] user not found: ${mid}`)
      return {
        'displayName': '未知用戶',
        'pictureUrl': null
      }
    },
    matchHeight() {
      this.$data._height = this.$refs.msgBox.offsetHeight; //clientHeight
    },
    getApi2Iframe(urionly=false){
      let apiUri = `https://dearsakura.deachsword.com/api2/msg/${this.message.id}`
      if(urionly) return apiUri
      let base = `<iframe src="${apiUri}" width="100%" height="${this.$data._height}" style="border:none;overflow:hidden;min-height: ${this.$data._height}px;" scrolling="no" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>`
      return base
    },
    doCopy(key) {
      document.querySelector(`#${key}_${this.rndkey}${this.message.id}`).select()
      document.execCommand('copy');
    },
    rndStr(len) {
    	let text = ""
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