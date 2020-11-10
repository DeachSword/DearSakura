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
    <!-- 最受喜歡 -->
    <section id="recent-posts-3" class="widget mt-3">
        <h3>Most Favorites</h3>
        <b-card-group columns v-if="favorites.length > 0">
          <message v-for="(d, i) in favorites" :key="'mf_' + i" :message="d"></message>
        </b-card-group>
        <div v-else>
          Nothing...
        </div>
    </section>
    <!-- 最高評價 -->
    <section id="recent-posts-3" class="widget mt-3">
        <h3>Top Rated</h3>
        <b-card-group columns v-if="rated.length > 0">
          <message v-for="(d, i) in rated" :key="'tr_' + i" :message="d"></message>
        </b-card-group>
        <div v-else>
          Nothing...
        </div>
    </section>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import message from '@/components/Message.vue'

export default {
  data(){
    return {
      search: null,
      favorites: [],
      rated: []
    }
  },
  components: { message },
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