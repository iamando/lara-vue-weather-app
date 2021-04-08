require("./bootstrap");
window.Vue = require("vue").default;

Vue.component(
    "weatherapp-component",
    require("./components/WeatherApp.vue").default
);
Vue.component("loader-component", require("./components/Loader.vue").default);

const app = new Vue({
    el: "#app"
});
