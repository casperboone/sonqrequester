<template>
    <div class="p-2">
        <div v-if="errorMessage" class="bg-red p-2 mb-3" v-html="'<strong>Oops.</strong> ' + errorMessage"></div>

        <input type="text" v-model="name" placeholder="Your name" class="block w-full p-3 h-10 bg-grey-lighter">
        <div class="flex mt-2">
            <div class="flex-1">
                <input type="text" v-model="track" placeholder="Track title" class="block w-full h-10 p-3 bg-grey-lighter"
                       @focus="showSuggestions = true"
                       @blur="showSuggestions = false"
                       @click="searchTracks()"
                >

                <div v-if="showSuggestions" class="text-black">
                    <div v-for="suggestion in suggestions" class="h-12 my-2 flex" @click="selectSuggestion(suggestion)">
                        <img :src="suggestion.image" class="h-12">
                        <div class="flex-1 pl-2">
                            <span class="block font-bold">{{ suggestion.track }}</span>
                            {{ suggestion.artist }}
                        </div>
                    </div>
                </div>

                <input type="text" v-model="artist" placeholder="Artist" class="block w-full mt-2 p-3 bg-grey-lighter">
            </div>
            <div>
                <img v-if="image" :src="image" class="h-20 ml-2">
            </div>
        </div>
        <button @click="submit" class="block w-full mt-2 p-3 bg-pink text-white font-bold">Submit</button>
    </div>
</template>

<script>
    import Request from '../Request'

    export default {
        data() {
            return {
                name: '',
                track: '',
                artist: '',
                image: '',
                showSuggestions: false,
                suggestions: [],
                errorMessage: ''
            }
        },
        methods: {
            submit() {
                this.errorMessage = ""

                axios.post("/requests", {name: this.name, track: this.track, artist: this.artist, image: this.image})
                    .then(response => {
                        this.track = ''
                        this.artist = ''
                        this.image = ''

                        this.$emit("requestSubmitted", new Request(response.data.data))
                    })
                    .catch(error => {
                        if (error.response.data.message !== undefined) {
                            this.errorMessage = error.response.data.message

                            if (error.response.data.errors !== undefined) {
                                this.errorMessage +=
                                    [].concat.apply([], Object.values(error.response.data.errors)).map(msg => "<li>" + msg + "</li>").join('')
                            }
                        } else {
                            this.errorMessage = error.message
                        }
                    })
            },
            searchTracks() {
                axios.post("/search", {query: this.track})
                    .then(response => this.suggestions = response.data)
            },
            selectSuggestion(suggestion) {
                this.track = suggestion.track
                this.artist = suggestion.artist
                this.image = suggestion.image

                this.clearSuggestions()
            },
            clearSuggestions() {
                this.showSuggestions = false
                this.suggestions = []
            }
        },
        watch: {
            track: _.debounce(function () {
                this.searchTracks()
            }, 400)
        }
    }
</script>
