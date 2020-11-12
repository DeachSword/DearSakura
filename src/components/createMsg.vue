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
    <transition mode="in-out" v-if="message">
      <b-form-tags input-id="tags" v-model="tags" class="mb-2" separator=" ">
        <template v-slot="{ tags,
       inputAttrs, inputHandlers, disabled, addTag, removeTag }">
          <b-input-group aria-controls="my-custom-tags-list">
          <input
            v-bind="inputAttrs"
            v-on="inputHandlers"
            :placeholder="$t('create.inputTip.tag.customTagInput')"
            class="form-control">
          <b-input-group-append>
            <b-button @click="addTag()" variant="primary">{{$t('create.inputTip.tag.addBtn')}}</b-button>
          </b-input-group-append>
        </b-input-group>
        <ul
          id="my-custom-tags-list"
          class="list-unstyled d-inline-flex flex-wrap mb-0"
          aria-live="polite"
          aria-atomic="false"
          aria-relevant="additions removals"
        >
          <b-card
            v-for="tag in tags"
            :key="tag"
            :id="`my-custom-tags-tag_${tag.replace(/\s/g, '_')}_`"
            tag="li"
            class="mt-1 mr-1"
            body-class="py-1 pr-2 text-nowrap"
          >
            <strong>{{ tag }}</strong>
            <b-button
              @click="removeTag(tag)"
              variant="link"
              size="sm"
              :aria-controls="`my-custom-tags-tag_${tag.replace(/\s/g, '_')}_`"
            >{{$t('create.inputTip.tag.removeBtn')}}</b-button>
          </b-card>
        </ul>
        <b-dropdown size="sm" variant="outline-secondary" block menu-class="w-100">
            <template v-slot:button-content>
              <b-icon icon="tag-fill"></b-icon>{{$t('create.inputTip.tag.recommend')}}
            </template>
            <b-dropdown-form @submit.stop.prevent="() => {}">
              <b-form-group
                label-for="tag-search-input"
                :label="$t('create.inputTip.tag.searchTagsLabel')"
                label-cols-md="auto"
                class="mb-0"
                label-size="sm"
                :description="searchDesc"
              >
                <b-form-input
                  v-model="tag_search"
                  id="tag-search-input"
                  type="search"
                  size="sm"
                  autocomplete="off"
                 ></b-form-input>
              </b-form-group>
            </b-dropdown-form>
            <b-dropdown-divider></b-dropdown-divider>
            <b-dropdown-item-button
              v-for="option in availableOptions"
              :key="option"
              @click="onOptionClick({ option, addTag })"
            >
              {{ option }}
            </b-dropdown-item-button>
            <b-dropdown-text v-if="availableOptions.length === 0">
              {{$t('create.inputTip.tag.searchTagsNoAvailableTags')}}
            </b-dropdown-text>
          </b-dropdown>            
          </template>

      </b-form-tags>
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
      tags: null,
      tag_options: ['sinoalice'],
      tag_search: '',
      tags: [],
      errorMsg: null,
      canSub: false
    }
  },
  computed: {
    CM() {
      return this.message
    },
    criteria() {
      // Compute the search criteria
      return this.tag_search.trim().toLowerCase()
    },
    availableOptions() {
      const criteria = this.criteria
      // Filter out already selected options
      const options = this.tag_options.filter(opt => this.tags.indexOf(opt) === -1)
      if (criteria) {
        // Show only options that match criteria
        return options.filter(opt => opt.toLowerCase().indexOf(criteria) > -1);
      }
      // Show all options available
      return options
    },
    searchDesc() {
      if (this.criteria && this.availableOptions.length === 0) {
        return this.$t('create.inputTip.tag.searchTagsNotFound')
      }
      return ''
    }
  },
  watch: {
    CM :{
      handler: function (msg) {
        this.errorMsg = null
        if(this.token == null) return
        if(this.$data._from && this.$data._from.trim().length == 0){
          this.errorMsg = '不得為空'
        }else if(this.$data.to && this.$data.to.trim().length == 0){
          this.errorMsg = '不得為空'
        }else if(msg){
          if(msg.trim().length > 10){
              this.canSub = true
              return
          }else{
            this.errorMsg = this.$t('create.inputTip.msgTextNotEnough')
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
      try{this.$ga.event('DearSakura', { method: '創建訊息' })}catch{}

      this.$axios.post('/api/init.php', Qs.stringify({
          'act': 'DearSakura',
          'data': {
              "_from" : this.$data._from.trim(),
              "to" : this.to.trim(),
              "msg" : this.message.trim(),
              "tag" : this.tags
          },
          'token': this.token
        }))
        .then((response) => {
          if(response.data.message !== "success"){
            this.errorMsg = response.data.message
          }else{
            this.$router.push(`/message/${this.to}`)
          }
        })
        .catch((error) => {
          this.errorMsg = 'Server error, 請聯絡管理員'
        });
    },
    onOptionClick({ option, addTag }) {
      addTag(option)
      this.tag_search = ''
    }
  },
  beforeDestroy() {
    clearTimeout(this.timer);
  }
}
</script>

<style>

</style>
