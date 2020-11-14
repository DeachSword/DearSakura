<template>
  <div class="about">
    <h1>{{$t('about.title')}}</h1>
    <p>{{$t('about.p1')}}<br />{{$t('about.p2')}}</p>
    <p>FROM_Sakura</p>
    <p>{{$t('about.p3')}}<br />{{$t('about.p4')}}</p>
    <div>
      <br>
      <br>
      <br><p>
      反正還有空間, 就在這裡讓我測試翻譯吧! 可以吧? 可以對吧?<br /><span>{{$i18n.locale}} - {{ $t('debug') }}</span>
      <br />雖然根本沒啥好翻譯的, 但還是開源翻譯檔給update囉~ <a href="https://github.com/DeachSword/DearSakura">GITHUB</a></p>
    </div>
  </div>
</template>

<script>
import { mapMutations } from 'vuex'

export default {
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
  beforeDestroy() {
    this.removeTitle(this)
  },
  computed: {
    title() {
      return "About"
    }
  },
  async serverPrefetch() {
    this.updateTitle([this, this.title])
  },
  methods: {
    ...mapMutations(['removeTitle', 'updateTitle']),
    track() {
      this.$ga.screenview({
        page: '/about',
        title: 'DearSakura/About',
        location: window.location.href
      })
    }
  },
  metaInfo() {
    return {
      meta: [
        { vmid: 'og:description', property: 'og:description', content: this.$t('about.description') } , 
        { vmid: 'description', name: 'description', content: this.$t('about.description') }  
      ]
    }
  }
}
</script>

