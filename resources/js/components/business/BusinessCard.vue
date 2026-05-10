<template>
    <div>
        <a :href="'businesses/'+ business.slug ">
            <div class="business-cards">
                <div class="image-container">
                    <img :src="imageUrl">
                </div>
                <div class="right-side ml-3 ">
                    <h2 class="font-bold text-xl">{{ business.name }}</h2>
                    <BusinessCategories v-for="category in business.categories" :category="category" :key="category.id"></BusinessCategories>
                    <StarRating :rating="business.average_review" :createdAt="business.created_at"  :small="true" /> 
                    <p>{{ business.description }}</p>
                </div>
            </div>
            
        </a>
    </div>
</template>
<style>

</style>
<script>
import BusinessCategories from "./BusinessCategories";
import StarRating from "../StarRating";
export default({
    props:['business'],
    components:{
        BusinessCategories,
        StarRating,
    },
    computed:{
        imageUrl(){
            if (this.business.front_image.startsWith('images/')) {
                return `${window.location.origin}/${this.business.front_image}`;
            }

            return window.location.origin+'/storage/'+this.business.front_image;
        }
    }
})
</script>
