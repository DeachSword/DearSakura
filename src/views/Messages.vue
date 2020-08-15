<template>
  <div>
    <findMsg :to="to"></findMsg>
  </div>
</template>

<script>
import { mapState } from 'vuex'

import findMsg from '@/components/findMsg'

export default {
  props: ['to'],
  components: { findMsg },
  methods: {
    ...mapState(['lang'])
  },
  created() {
  },
}
</script>