/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap.js");

window.Vue = require("vue");

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Busses
export const reviewBus = new Vue();

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component("modal", require("./components/Modal.vue").default);

Vue.component(
    "businessphotos",
    require("./components/BusinessPhotos.vue").default
);

Vue.component(
    "showcasedreviews",
    require("./components/ShowcasedReviews.vue").default
);

Vue.component(
    "add-review",
    require("./components/review/AddReview.vue").default
);


Vue.component("reviews", require("./components/review/Reviews.vue").default);


// Business section
Vue.component(
    "businesses",
    require("./components/business/BusinessList.vue").default);

Vue.mixin({
    methods: {
        getLocationUrl(section) {
            const fullUrl = window.location.pathname;

            switch (section) {
                case "business":
                    return fullUrl.split("/")[2];

                default:
                    return fullUrl;
            }
        },
        getAsset(location, name) {
            return `${window.location.origin}/${location}/${name}`;
        }
    }
});
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import { modalBus } from "./buses/buses.js";

const app = new Vue({
    el: "#app",
    methods: {
        openModal(modal) {
            modalBus.$emit("modal", modal);
            modalBus.modalIsOpened = true;
        }
    }
});

const navDropdownBtn = document.querySelector("#nav-dropdown");

if (navDropdownBtn) {
    navDropdownBtn.addEventListener("click", () => {
        document.querySelector("#dropdown-items").classList.toggle("shown");
    });

    window.addEventListener("click", e => {
        if (
            !document.querySelector("#dropdown-items").contains(e.target) &&
            !document.querySelector("#nav-dropdown").contains(e.target)
        ) {
            document.querySelector("#dropdown-items").classList.remove("shown");
        }
    });
}

// window.addEventListener("click", function(e) {
//     if (
//         document.querySelector(".show-search-dropdown") &&
//         !document.querySelector(".navigation").contains(e.target)
//     ) {
//         dropdownMenu.classList.remove("show");
//     }

//     if (
//         document.querySelector(".show-search-dropdown") &&
//         !document.querySelector(".show-search-dropdown").contains(e.target)
//     ) {
//         document
//             .querySelector(".main__content-search")
//             .classList.remove("show-search-dropdown");
//     }
// });
