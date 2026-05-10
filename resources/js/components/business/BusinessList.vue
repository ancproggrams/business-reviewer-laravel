<template>
<div>
    <div>
        <input class="supplier-search" id="search-input" v-model="search_input" type="text" placeholder="Zoek leverancier, AI-oplossing of branche" @keyup="keyPressed(search_input)">
    </div>
    <br>
    <div style="position:relative;min-height:100px">
        <div v-bind:class="[{loading: listLoading}]"></div>
        <BusinessCard v-for="business in businesses" :key="business.id" :business="business"></BusinessCard>
        <div class="empty-suppliers" v-if="!listLoading && businesses.length === 0">
            <h2>Nog geen leveranciers gevonden</h2>
            <p>Word de eerste AI-leverancier op EyAy of pas je zoekopdracht aan.</p>
            <a href="/businesses/create">Bedrijf aanmelden</a>
        </div>
    </div>
</div>
</template>
<style >
.loading{
    position: absolute;
    top:0;
    left: 0;
    z-index: 9;
    width: 100%;
    height:100%;
    display: block;
    background: black;
    opacity: .2;
    background: black url("https://upload.wikimedia.org/wikipedia/commons/b/b9/Youtube_loading_symbol_1_(wobbly).gif") center center no-repeat;
    background-size:40px 40px;
}
.supplier-search {
    width: 100%;
    min-height: 46px;
    border-radius: 999px;
    border: 1px solid var(--eyay-line);
    background: rgba(255,255,255,.82);
    color: var(--eyay-ink);
    padding: 0 18px;
}
.empty-suppliers {
    padding: 34px;
    border-radius: 28px;
    border: 1px dashed var(--eyay-line);
    background: rgba(223,242,236,.42);
    color: var(--eyay-muted);
}
.empty-suppliers h2 {
    margin: 0 0 8px;
    color: var(--eyay-ink);
    font-size: 1.45rem;
}
.empty-suppliers a {
    display: inline-flex;
    margin-top: 14px;
    color: var(--eyay-green-dark);
    font-weight: 760;
}
</style>
<script>
import BusinessCard from "./BusinessCard";
export default{
    components:{
        BusinessCard,
    },
    data(){
        return{
            businesses:[],
            search_input:'',
            listLoading:false,
        }
    },
    created(){
        this.fetchAll();
    },
    methods:{
        fetchAll(search_input){
            this.listLoading = true;
            axios.get(window.location.href,{
                headers:{
                    Accept:'application/json',
                },
                params:{
                    search:search_input,
                }
            }).then(res=>{
                this.listLoading = false;
                this.businesses = res.data;
            }).catch(error=>{
                this.listLoading = false;
                console.log(error);
            })
        },
        keyPressed(search_input){
            if(search_input.length>3 || search_input.length == 0){
                this.fetchAll(search_input);
            }
        }
    }
}
</script>
