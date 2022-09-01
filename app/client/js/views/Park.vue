<template>
  <div>
    <div class="park" v-if="park">
      <a href="/" @click.exact.prevent="$router.push('/')">Close</a>
      <h2>{{ park.Title }}</h2>
      <ul class="park__features">
        <li>{{ park.FeatureOnOffLeash }}</li>
      </ul>
      <p class="park__notes">{{ park.Notes }}</p>
      <p class="park__provider">Managed by <strong>{{ park.Provider }}</strong></p>
    </div>
    <section>
      <Upload @click="onUpload" />
    </section>
  </div>
</template>

<script>
import Upload from '../components/Upload.vue';

export default {
  components: {
    Upload
},
  computed: {
    park() {
      return this.$store.state.parks.find(park => park.ID === parseInt(this.$route.params.id, 10));
    },
    parks() {
      return this.$store.state.parks;
    },
  },
};

</script>

<style>
  .park {
    padding: 20px;
  }
</style>
