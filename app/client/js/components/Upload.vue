<template>
  <form @submit.prevent="onUpload" method="POST" enctype="multipart/form-data">
    <div id="uploadPhoto">
      <p>Share your dogs' experience.</p>
      <input
        style="display: none"
        type="file" @change="onFileChanged"
      ref="fileInput">
      <button @click="$refs.fileInput.click()">Select your Photo</button>
      <button type="submit">Upload!</button>

      <div id="output" ref="outputMessage" class="message"></div>
    </div>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      selectedFile: null,
      imageURL: null,

    };
  },
  methods: {
    onFileChanged (event) {
      this.selectedFile = event.target.files[0];
    },
    onUpload() {
      const output = this.$refs.outputMessage;
      // upload file, get it from this.selectedFile
      const formData = new FormData()
      formData.append('selectedFile', this.selectedFile, this.selectedFile.name);
      const config = {
        headers: { 'content-type': 'multipart/form-data' },
        onUploadProgress: uploadEvent => {
          const percentCompleted = ('Upload Progress:'+ Math.round((uploadEvent.loaded/uploadEvent.total)*100)+'%');
        }
      };
      axios.post('photoUpload', formData, config)
        .then(response => {
          output.className = 'message';
          output.innerHTML = response.data;
        })
        .catch(error => {
          output.className = 'message text-danger';
          output.innerHTML = error.message;
        });

        this.$emit('selectedFile',formData, selectedFile);
    }
  },
};
</script>

<style>
  #uploadPhoto {
    position: relative;
    top: 20px;
    left: 20px;
    min-height: 40px;
    min-width: 100px;
    background-color: #eeeeee;
    padding: 10px 20px;
  }
  .message{
    position: relative;
  }
  .text-danger{
    color: red;
  }
  /*#filters label {
    display: block;
    height: 42px;
    line-height: 42px;
  }
  #filters label img, #filters label span {
    display: inline-block;
    width: 32px;
    height: 32px;
    vertical-align: middle;
    margin-bottom: 6px;
  }*/
</style>
