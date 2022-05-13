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
      <Upload @onFileChanged="onFileChanged" />
    </section>
    <section class="viewGallery">
      <div class="row justify-content-center">
        <button class=".btn-viewGallery" @click="$refs.PhotoGallery.click()">View Photo Gallery</button>
        <GalleryList :images="images" ref="PhotoGallery"/>
      </div>
    </section>
  </div>
</template>

<script>
import Upload from '../components/Upload.vue';
import GalleryList from '../components/GalleryList.vue';

export default {
  components: {
    Upload,
    GalleryList
},
  computed: {
    park() {
      return this.$store.state.parks.find(park => park.ID === parseInt(this.$route.params.id, 10));
    },
    parks() {
      return this.$store.state.parks;
    },
  },
  methods:{
    onFileChanged(e) {
    }
  }
};

</script>

<style>
  .park {
    padding: 20px;
  }
  .viewGallery{
    padding-top: 26px;
  }
  .btn-viewGallery {
    position: relative;
    top: 20px;
    left: 20px;
    min-height: 40px;
    min-width: 100px;
    background-color: #eeeeee;
    padding: 10px 20px;
  }
</style>
