<template>
<div>
        <h3 class="font-bold text-2xl mt-6 mb-4">Reviews ({{ count }})</h3>
        <hr>
    <ul class="mt-5">
        <div v-if="reviews.length">
            <Review
                v-for="review in reviews"
                :key="review.id"
                :review="review"
                :currentUserIsOwner="currentUserIsOwner"
            />
        </div>
        <p v-else class="text-gray-600">Nog geen reviews.</p>
    </ul>
    </div>
</template>

<script>
import Review from "./Review";
import {reviewBus} from '../../app.js';

export default {
    components: {
        Review
    },
    data() {
        return {
            loading: true,
            reviews: [],
            count: 0,
        };
    },
    props: ['currentUserIsOwner'],
    methods: {
        reviewAdded(review){
            this.reviews.push(review);
        },
        fetchReviews() {
            axios(`/businesses/${this.getLocationUrl("business")}/review`)
            .then(res => {
                this.reviews = res.data;
                this.loading = false;
                this.count = res.data.length;
            })
            .catch(err => {
                this.loading = false;
                this.error = "Reviews konden niet worden opgehaald.";
            });
        }
    },
    mounted() {
        this.fetchReviews();
        reviewBus.$on('review_added', this.fetchReviews);
    }
};
</script>

<style></style>
