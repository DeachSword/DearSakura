<template>
  <div>
    <findMsg :to="to"></findMsg>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import findMsg from '@/components/findMsg'

export default {
  props: ['to'],
  components: { findMsg },
  methods: {
    ...mapMutations(['setApiState']),
    ...mapState(['lang'])
  },
  created() {
    this.setApiState(false)
  },
}
</script>