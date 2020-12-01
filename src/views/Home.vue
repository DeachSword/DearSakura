<template>
  <div>
    {{$t('home.text.p1')}}<br />{{$t('home.text.p2')}}<br />{{$t('home.text.p3')}}<br /><br /><br />
    {{$t('home.text.p4')}}
     <b-form-input
      v-model="search"
      type="text"
      name="name"
      :placeholder="$t('home.find.inputTip')"
      @keyup.enter="makeFind"
    ></b-form-input>
    <br />
    <!-- 最近留言 -->
    <section id="recent-posts-3" class="widget mt-3">
        <h3>New Comments</h3>
        <div v-if="comments == null">加載中...ε≡ﾍ( ´∀`)ﾉ</div>
        <messageList :messages="comments" v-else-if="comments.length"></messageList>
        <div v-else>Nothing</div>
    </section>
    <!-- 最受喜歡 -->
    <section id="recent-posts-3" class="widget mt-3">
        <h3>Most Favorites</h3>
        <div v-if="comments == null">加載中...ε≡ﾍ( ´∀`)ﾉ</div>
        <messageList :messages="favorites" v-else-if="favorites.length"></messageList>
        <div v-else>Nothing</div>
    </section>
    <!-- 最高評價 -->
    <section id="recent-posts-3" class="widget mt-3">
        <h3>Top Rated</h3>
        <div v-if="comments == null">加載中...ε≡ﾍ( ´∀`)ﾉ</div>
        <messageList :messages="rated" v-else-if="rated.length"></messageList>
        <div v-else>Nothing</div>
    </section>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import messageList from '@/components/MessageList.vue'

export default {
  data(){
    return {
      search: null,
      favorites: null,
      rated: null,
      comments: null
    }
  },
  components: { messageList },
  methods: {
    ...mapState(['lang'])
  },
  created() {
    this.getMyPageInfo();
    if(this.$route.query.to !== undefined){
      this.$router.push({ path: `/message/${this.$route.query.to}` })
      //this.search = this.$route.query.to
    }
  },
  methods: {
    ...mapMutations(['setUsers']),
    getMyPageInfo() {
      this.$axios.post('https://dearsakura.deachsword.com/api/get_my_page_info_for_sakura_11', null)
        .then((response) => {
          if(response.data.message !== "success"){
            console.log('[GetMyPageInfo][error]' + response.data.message);
          }else{
            this.favorites = response.data.result.favorites
            this.rated = response.data.result.rated
            this.comments = response.data.result.comments
            this.setUsers(response.data.result.users)
          }
        })
        .catch(function (error) {
          console.log('[GetMyPageInfo][error]' + error);
        });
    },
    makeFind() {
      this.$router.push({ path: `/message/${this.search}` })
    }
  }
}
</script>
<style>
.messages__items-row {
    display: flex;
    width: 100%;
}
.messages__item {
  width: 100%;
}
</style>