<template>
  <div v-if="reviews.length">
    <h3 class="font-bold text-2xl mt-6 mb-4">Uitgelichte reviews</h3>
      <div v-if="reviews.length">
        <ul class="list showcased">
      <ShowcasedReview v-for="review in reviews" :review="review" :currentUserIsOwner="currentUserIsOwner" :key="review.id" />
        </ul>
      </div>
  </div>
</template>

<script>
import ShowcasedReview from './review/ShowcasedReview.vue';
export default {
  components: {
    ShowcasedReview
  },
  props: {
    businessSlug:  {
      type: String,
      required: true,
    },
    currentUserIsOwner: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      reviews: []
    }
  }, 
  mounted() {
    axios.get(`/businesses/${this.businessSlug}/review/showcased`).then(res => {
      this.reviews = res.data.showcasedReviews;
    })
  }
}
</script>

<style>
  .showcased li {
    border: solid 1px goldenrod;
    padding: 30px;
  }
</style>
