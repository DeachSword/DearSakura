<template>
    <b-card bg-variant="dark" border-variant="white" text-variant="white" style="width: auto;margin: 0.625rem;">
        <template v-slot:header>
            <code>To {{message.to}}</code>
            
            <b-button
            v-if="isLogin"
            variant="outline-danger"
            pill
            right
            v-b-tooltip.hover.bottom
            title="收藏(beta)"
            size="sm"
            class="favourites-btn">
            <b-icon icon="heart"></b-icon>
            </b-button>
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
                    <span class="message-footer-header__value-name">0</span>
                </span>
                <b-button variant="primary" class="message-footer-header__value" v-b-modal="isLogin ? 'modal-msg-rating_' + message.id : null"
                    :title="isLogin ? null : '登入後即可評價訊息!'" v-b-tooltip.hover.bottom>{{$t('message.rating')}}
                    <span class="message-footer-header__value-icon">
                        <b-icon icon="star-fill"></b-icon>
                    </span>
                    <span class="message-footer-header__value-name">{{message.rating}}</span>
                </b-button>
                <b-modal centered :id="'modal-msg-rating_' + message.id" title="Submit Rating" @show="ratingData.rating=-1" @ok="rateMessage(message.id)" :ok-disabled="ratingData.rating != -1 ? false : true">
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
  data() {
    return {
        infoMsg: null,
        errorMsg: null,
        canRating: false,
        ratingData: {
            id: -1,
            rating: -1
        }
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
            message.isRated = true
            message.selfRating = this.ratingData.rating
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
    }
  }
}
</script>