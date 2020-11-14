<template>
  <div v-if="!isApi">
    <div id="app" v-if="!isApi" class="ds-layout">
      <div class="ds-layout__section--full">
        <b-navbar toggleable="lg" type="dark" variant="dark">
          <b-navbar-brand to="/">DearSakura</b-navbar-brand>

          <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

          <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
              <b-nav-item to="/create">{{$t('create.title')}}</b-nav-item>
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">

              <b-nav-item-dropdown :text="currentLang" right>
                <b-dropdown-item  v-for="[lang, name] in availableLang" :key="lang" @click="updateLang(lang, $event)" :href="`?lang=${lang}`">
                  {{name}}
                </b-dropdown-item>
                <b-dropdown-divider></b-dropdown-divider>
                <b-dropdown-item to="/about" id="about">{{'About'}}</b-dropdown-item>
              </b-nav-item-dropdown>
            </b-navbar-nav>

            <b-button class="avatar avatar--nav2 js-current-user-avatar js-click-menu js-user-login--menu js-user-header avatar--guest js-click-menu--active" v-b-tooltip.hover :title="profile!=null ? profile.profile.displayName : $t('home.login.tipLogin')" :href="profile!=null ? '#' : 'https://www.deachsword.com/serverbot/sso'" :style="profile!=null ? `background-image:url('${profile.profile.pictureUrl}');` : ''"></b-button>
          </b-collapse>
        </b-navbar>
        <section class="section" @click="closeMenu">
          <div class="container">
            <router-view></router-view>
          </div>
        </section>
      </div>
      <footer class="footer">
        <div class="footer__row">
          <a class="footer__link" href="https://github.com/DeachSword/DearSakura">{{$t('home.footer.source')}}</a>
          <a class="footer__link" href="https://www.facebook.com/DeachSword.tw/">FaceBook</a>
        </div>
        <div class="footer__row">
            DearSakura by <a class="footer__link" href="https://www.deachsword.com">DeachSword</a> 2020-{{ new Date().getFullYear() }}
        </div>
      </footer>
    </div>
  </div>
  <div v-else>
    <router-view></router-view>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import i18n from '@/lang'

const langs = {}
Object.keys(i18n.messages).map(function(objectKey, index) {
    var value = i18n.messages[objectKey]['tran']['local'];
    if(value == '') value = objectKey;
    langs[objectKey] = value;
});


export default {
  data() {
    return { 
      menu: false, 
      url: null, 
      description: null
    }
  },
  mounted() {
    this.url = window.location.href
  },
  computed: {
    ...mapState(['lang','cTitle', 'profile', 'isLogin', 'isApi']),
    currentUrl() {
      if(this.url != null) this.$ga.screenview({
        appName: 'DearSakura',
        screenName: `DearSakura/${this.$route.name}`,
      })
      return this.url
    },
    currentLang() {
      return langs[this.lang]
    },
    availableLang() {
      return Object.entries(langs)
        .filter(([lang]) => lang !== this.lang)
    }
  },
  watch: {
    currentUrl: {
      immediate: true,
      handler(url) {
        //if(url != null) console.log('url:'+url)
      }
    }
  },
  created() {
    this.updateTitle([this, 'DearSakura'])
    this.$i18n.locale = this.lang
    this.description = this.$t('home.description')
    this.checkSession()
  },
  methods: {
    ...mapMutations(['setLang', 'updateTitle', 'setProfile', 'setLoginState']),
    updateLang(lang, e) {
      // don't redirect with control keys
      if (e.metaKey || e.altKey || e.ctrlKey || e.shiftKey) return
      // don't redirect when preventDefault called
      if (e.defaultPrevented) return
      // don't redirect on right click
      if (e.button !== undefined && e.button !== 0) return
      // https://github.com/vuejs/vue-router/blob/65de048ee9f0ebf899ae99c82b71ad397727e55d/src/components/link.js#L159-L164
      e.preventDefault()

      this.setLang(lang)
      //this.$router.push({ query: { lang } })
    },
    switchMenu() {
      this.menu = !this.menu
    },
    closeMenu() {
      this.menu = false
    },
    checkSession() {
      this.$axios.post('https://dearsakura.deachsword.com/api/session', null)
        .then((response) => {
          this.setProfile(response.data.result)
          this.setLoginState(true)
        })
        .catch(function (error) {
        });
    },
    checkSessionV2() {
      return this.$axios.post('/api/session', null)
        .then((response) => {
          return response.data.result
          console.log(this.profile.profile.displayName)
        })
        .catch(function (error) {
        });
    }
  },
  metaInfo() {
    return {
      meta: [
        { property: 'og:url', content: `https://dearsakura.deachsword.com${this.$route.fullPath}` },
        { property: 'og:title', content: this.cTitle },
        { vmid: 'og:description', property: 'og:description', content: this.description },
        { vmid: 'description', name: 'description', content: this.description }
      ]
    }
  }
}
</script>

<style lang="scss">
@import '~bootstrap';
@import '~bootstrap-vue';
</style>
<style>
  html, body, #app {
    min-height: 100vh;
    flex-direction: column!important;
    display: flex;
  }
  #app {
    text-shadow: 0 0.05rem 0.1rem rgba(0, 0, 0, .5);
    margin: 0;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    background-color: #333;
    color: #fff;
    color: #fff;
  }
  .ds-layout {
    display: flex;
    flex-direction: column;
    transition: opacity .2s ease-in-out,-webkit-filter .2s ease-in-out;
    transition: filter .2s ease-in-out,opacity .2s ease-in-out;
    transition: filter .2s ease-in-out,opacity .2s ease-in-out,-webkit-filter .2s ease-in-out;
  }
  .ds-layout__section {
    display: flex;
    flex-direction: column;
  }
  .ds-layout__section--full {
    flex: 1 0 auto;
  }
  code {
    background-color: unset;
  }
  .title, .subtitle {
    color: unset;
  }
  strong{
    color: unset;
  }
  .footer{
    background-color: hsl(var(--hsl-b5));
    color: hsl(var(--hsl-f1));
    font-size: 10px;
    display: flex;
    flex-direction: column;
    flex: none;
    padding: 10px 0 5px;
    margin-top: auto!important;
  }
  .footer__row {
    margin-bottom: 5px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
  }
  .footer__link {
    font-weight: 700;
    color: #fff;
    margin: 0 10px;
  }
  button.avatar:focus {
    outline: unset;
    outline: unset;
  }
  .comment-editor_submit-btn {
    font-size: 12px;
    flex: none;
    margin: 5px 2px;
    color: #fff;
    min-width: 80px;
    border-radius: 10000px;
    padding: 5px;
    float: right;
  }
  .avatar {
    outline: none;
    border: none;
    padding: 0;
    margin: 0;
    background: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    box-shadow: 0 1px 3px rgba(0,0,0,.25);
    border-radius: 4px;
    width: 70px;
    height: 70px;
    flex: none;
    background-color: #333;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: 50%;
    display: block;
  } 
  .avatar--nav2 {
    width: 60px;
    height: 60px;
    box-shadow: none;
    border-radius: 50%;
    overflow: hidden;
    transition: width .3s,height .3s;
}

  .avatar--guest {
      background-image: url(https://www.deachsword.com/web/img/nologinuser.jpg);
  }
  .avatar--nav2.js-click-menu--active:after, .avatar--nav2:hover:after {
    opacity: 1;
  }
  button, html input[type=button], input[type=reset], input[type=submit] {
      -webkit-appearance: button;
      cursor: pointer;
  }

  @media(min-width: 576px) {
    .card-columns {
        column-count: 2;
        column-gap: 1.25rem;
        orphans: 1;
        widows: 1
    }

    .card-columns .card {
        display: inline-block;
        width: 100%
    }
  }
  :root {
    --hsl-p:var(--base-hue),100%,50%;--hsl-h1:var(--base-hue),100%,70%;--hsl-h2:var(--base-hue),50%,45%;--hsl-c1:var(--base-hue),40%,100%;--hsl-c2:var(--base-hue),40%,90%;--hsl-l1:var(--base-hue),40%,80%;--hsl-l2:var(--base-hue),40%,75%;--hsl-l3:var(--base-hue),40%,70%;--hsl-l4:var(--base-hue),40%,50%;--hsl-d1:var(--base-hue),20%,35%;--hsl-d2:var(--base-hue),20%,30%;--hsl-d3:var(--base-hue),20%,25%;--hsl-d4:var(--base-hue),20%,20%;--hsl-d5:var(--base-hue),20%,15%;--hsl-d6:var(--base-hue),20%,10%;--hsl-f1:var(--base-hue),10%,60%;--hsl-b1:var(--base-hue),10%,40%;--hsl-b2:var(--base-hue),10%,30%;--hsl-b3:var(--base-hue),10%,25%;--hsl-b4:var(--base-hue),10%,20%;--hsl-b5:var(--base-hue),10%,15%;--hsl-b6:var(--base-hue),10%,10%;--colour-pink-hue:333;--hsl-pink-1:var(--colour-pink-hue),100%,70%;--hsl-pink-2:var(--colour-pink-hue),80%,60%;--hsl-pink-3:var(--colour-pink-hue),60%,50%;--colour-purple-hue:255;--hsl-purple-1:var(--colour-purple-hue),100%,70%;--hsl-purple-2:var(--colour-purple-hue),80%,60%;--hsl-purple-3:var(--colour-purple-hue),60%,50%;--colour-blue-hue:200;--hsl-blue-1:var(--colour-blue-hue),100%,70%;--hsl-blue-2:var(--colour-blue-hue),80%,60%;--hsl-blue-3:var(--colour-blue-hue),60%,50%;--colour-green-hue:125;--hsl-green-1:var(--colour-green-hue),100%,70%;--hsl-green-2:var(--colour-green-hue),80%,60%;--hsl-green-3:var(--colour-green-hue),60%,50%;--colour-lime-hue:90;--hsl-lime-1:var(--colour-lime-hue),100%,70%;--hsl-lime-2:var(--colour-lime-hue),80%,60%;--hsl-lime-3:var(--colour-lime-hue),60%,50%;--colour-orange-hue:45;--hsl-orange-1:var(--colour-orange-hue),100%,70%;--hsl-orange-2:var(--colour-orange-hue),80%,60%;--hsl-orange-3:var(--colour-orange-hue),60%,50%;--colour-red-hue:360;--hsl-red-1:var(--colour-red-hue),100%,70%;--hsl-red-2:var(--colour-red-hue),80%,60%;--hsl-red-3:var(--colour-red-hue),60%,50%;--colour-darkorange-hue:20;--hsl-darkorange-1:var(--colour-darkorange-hue),100%,70%;--hsl-darkorange-2:var(--colour-darkorange-hue),80%,60%;--hsl-darkorange-3:var(--colour-darkorange-hue),60%,50%
    }

 </style>
