<template>
<div class="mt-5" id="add-review" v-if="!reviewSubmitted">
    <h3 class="font-bold text-2xl mb-6">Ervaring met deze leverancier?</h3>
    <hr>
    <div class="py-4" v-if="user">
        <div class=" mt-2 lg:flex">
            <UserCard :author="user" />
            <div class="w-full flex-1">
                <form @submit.prevent="submitReview" method="POST" enctype="multipart/form-data">

                    <textarea name="body" id="" rows="5" v-model="body"
                        class="block w-full border-2 border-gray-300 p-3 rounded"></textarea>

                        <img :src="imageUrl" alt="" v-if="image" class="mt-4 rounded w-32">
           
                    <div class="mb-3 flex justify-between">
                      <AddReviewStars @ratingChanged="setRating"/>
                          <button type="button" class="block" @click="addImage = !addImage">Afbeelding toevoegen</button>
                    </div>

                  <div class="mb-3" v-if="addImage">
                        <input type="file" name="image" accept="image/*" @change="uploadImage">
                    </div>
        

                    <p class="text-sm text-red-400" v-if="error">{{error}}</p>
             
                    <button type="submit" class="button ml-auto mt-3">Review plaatsen</button>
                </form>
            </div>


        </div>
    </div>
    <p class="py-3" v-else>Log in om deze leverancier te beoordelen.</p>
</div>
</template>

<script>
import UserCard from './UserCard';
import {reviewBus} from '../../app.js';
import AddReviewStars from './AddReviewStars.vue';

export default {
  props: {
    urlPath: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      body: '',
      rating: 1,
      error: '',
      addImage: false,
      image: null, 
      reviewSubmitted: false,
    }
  },
  components: {
    UserCard,
    AddReviewStars
  },
  computed: {
    user() {
     return currentUser;
    },
    imageUrl() {
      return URL.createObjectURL(this.image);
    }
  },
  methods: {
    submitReview() {
      if (!this.body) {
        return this.error = 'Schrijf kort waarom je deze score geeft.';
      }
      const formData = new FormData();

      if (this.image) {
        formData.append('image', this.image);
      }

      formData.append('rating', this.rating);
      formData.append('body', this.body);

      axios.post(this.urlPath, formData).then(() => reviewBus.$emit('review_added', this.review));
      this.reviewSubmitted = true;
    },
    uploadImage(e) {
      this.imageUploaded = true;
      this.image = e.target.files[0];
    },
    setRating(rating) {
      this.rating = rating;
    }
  }
}
</script>

<style>

</style>
