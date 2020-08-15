<template>
  <div>
    吶、還記得那時候嗎？<br />我有好多好多……想對妳說的話<br />啊～啊、希望有天妳能夠收到這些訊息—<br /><br /><br />
    現在，輸入妳吶令人熟悉的名子吧！也許某個人也有話想對你說！
    <findMsg></findMsg>
  </div>
</template>

<script>
import { mapState } from 'vuex'

import findMsg from '@/components/findMsg'

export default {
  components: { findMsg },
  methods: {
    ...mapState(['lang'])
  },
  created() {
  },
}
</script>