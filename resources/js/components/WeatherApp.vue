<template>
  <div class="text-white mb-8">
    <div class="places-input text-gray-800">
      <input
        type="search"
        id="address"
        class="w-full py-4 px-4 rounded-lg outline-none"
        placeholder="Enter your city here..."
        v-on:keyup.enter="addLocation"
      />
    </div>
    <div
      class="weather-container font-sans w-128 max-w-lg overflow-hidden rounded-lg bg-gray-900 shadow-lg mt-4"
    >
      <div class="current-weather flex items-center justify-between px-6 py-8">
        <span
          v-if="loading"
          class="flex items-center justify-center"
          style="width: 100%"
        >
          <loader-component
            class="flex items-center justify-center"
          ></loader-component>
          <span>{{ message }}</span>
        </span>
        <div class="flex items-center" v-if="loading == false">
          <div v-if="currentTemperature.actual">
            <div class="text-6xl font-semibold">
              {{ currentTemperature.actual }}°C
            </div>
            <div>Feels like {{ currentTemperature.feels }}°C</div>
          </div>
          <div class="mx-5">
            <div class="font-semibold">{{ currentTemperature.summary }}</div>
            <div v-if="location.name">
              {{ location.name }}, {{ location.country }}
            </div>
          </div>
        </div>
        <div v-if="loading == false" class="ml-6">
          <img
            v-if="currentTemperature.icon"
            :src="currentTemperature.icon"
            alt="Icon"
          />
        </div>
      </div>
      <div
        class="future-weather text-sm bg-gray-800 px-6 py-8 overflow-hidden"
        v-if="loading == false"
      >
        <div
          v-for="(day, index) in daily"
          :key="day.date_epoch"
          class="flex items-center"
          :class="{ 'mt-8': index > 0 }"
        >
          <div class="w-1/6 text-lg text-gray-200">
            {{ toDayOfWeek(day.date_epoch) }}
          </div>
          <div class="w-4/6 px-4 flex items-center">
            <div>
              <img :src="day.day.condition.icon" alt="Icon" />
            </div>
            <div class="ml-3">{{ day.day.condition.text }}</div>
          </div>
          <div class="w-1/6 text-right">
            <div>{{ Math.round(day.day.maxtemp_c) }}°C</div>
            <div>{{ Math.round(day.day.mintemp_c) }}°C</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// const places = require("places.js");
const moment = require("moment");
export default {
  mounted() {
    if (this.location.user) {
      this.fetchData();
    } else {
      this.message = "Waiting for location...";
      this.loading = true;
    }

    // var placesAutocomplete = places({
    //   appId: "",
    //   apiKey: "",
    //   container: document.querySelector("#address"),
    // }).configure({
    //   type: "city",
    //   aroundLatLngViaIP: false,
    // });

    // var $address = document.querySelector("#address-value");
    // placesAutocomplete.on("change", (e) => {
    //   console.log(e.suggestion);
    //   $address.textContent = e.suggestion.value;

    //   this.location.user = `${e.suggestion.name}`;
    // });

    // placesAutocomplete.on("clear", () => {
    //   $address.textContent = "none";
    // });
  },

  data() {
    return {
      message: "",
      loading: true,
      currentTemperature: {
        actual: "",
        feels: "",
        summary: "",
        icon: "",
      },
      daily: [],
      location: {
        country: "",
        name: "",
        user: "",
        time: "",
      },
    };
  },

  methods: {
    addLocation(e) {
      e.preventDefault();
      if (e.keyCode === 13) {
        this.loading = true;
        this.location.user = `${e.target.value.toLowerCase()}`;
        this.fetchData();
      }
    },
    fetchData() {
      try {
        this.message = "Please wait we check this location...";
        fetch(`/api/weather?location=${this.location.user}`)
          .then((res) => res.json())
          .then((data) => {
            console.log(data);
            this.loading = false;

            this.currentTemperature.actual = Math.round(data.current.temp_c);
            this.currentTemperature.feels = Math.round(
              data.current.feelslike_c
            );
            this.currentTemperature.summary = data.current.condition.text;
            this.currentTemperature.icon = data.current.condition.icon;
            this.location.country = data.location.country;
            this.location.name = data.location.name;
            this.location.time = moment(data.location.localtime);

            this.daily = data.forecast.forecastday;
          });
      } catch (error) {
        this.message = "Sorry we have server error.";
        console.log(error);
      }
    },
    toDayOfWeek(timestamp) {
      const newDate = new Date(timestamp * 1000);
      const days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

      return days[newDate.getDay()];
    },
  },
};
</script>
