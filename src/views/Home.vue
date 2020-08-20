<template>
  <div>
    {{$t('home.text.p1')}}<br />{{$t('home.text.p2')}}<br />{{$t('home.text.p3')}}<br /><br /><br />
    {{$t('home.text.p4')}}
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