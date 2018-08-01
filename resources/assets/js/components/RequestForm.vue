<template>
    <div class="p-2">
        <input type="text" v-model="name" placeholder="Your name" class="block w-full p-3 bg-grey-lighter">
        <input type="text" v-model="track" placeholder="Track title" class="block w-full mt-2 p-3 bg-grey-lighter">
        <input type="text" v-model="artist" placeholder="Artist" class="block w-full mt-2 p-3 bg-grey-lighter">
        <button @click="submit" class="block w-full mt-2 p-3 bg-pink text-white font-bold">Submit</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                name: '',
                track: '',
                artist: ''
            }
        },
        methods: {
            submit() {
                axios.post("/requests", {name: this.name, track: this.track, artist: this.artist})
                    .then(response => {
                        this.name = ''
                        this.track = ''
                        this.artist = ''

                        this.$emit("requestSubmitted")
                    })
            }
        }
    }
</script>
