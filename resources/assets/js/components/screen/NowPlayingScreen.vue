<template>
    <div class="flex">
        <div v-if="nowPlayingRequest" class="bg-green-dark w-full text-xl mb-3">
           <span class="uppercase tracking-wide text-4xl text-center text-sm font-bold block p-2">Now Playing</span>

           <div class="flex bg-green p-4 text-3xl">
               <img v-if="nowPlayingRequest.image" :src="nowPlayingRequest.image" class="h-48 mr-4">
               <div class="flex flex-col flex-1">
                   <div class="flex-1">
                       <span class="block font-bold">{{ nowPlayingRequest.track }}</span>
                       {{ nowPlayingRequest.artist }}
                   </div>
                   <span class="block text-3xl font-bold bg-green-dark px-3 py-2 mt-2 uppercase">
                        Requested by {{ nowPlayingRequest.name }}
                    </span>
               </div>
           </div>
        </div>

        <div v-if="comingUpRequest" class="bg-blue-dark w-full text-xl mb-3">
            <span class="uppercase tracking-wide text-4xl text-center font-bold block p-2">Coming Up Next</span>

            <div class="flex bg-blue p-4 text-3xl">
                <img v-if="comingUpRequest.image" :src="comingUpRequest.image" class="h-48 mr-4">
                <div class="flex flex-col flex-1">
                    <div class="flex-1">
                        <span class="block font-bold">{{ comingUpRequest.track }}</span>
                        {{ comingUpRequest.artist }}
                    </div>
                    <span class="block text-3xl font-bold bg-blue-dark px-3 py-2 mt-2 uppercase">
                        Requested by {{ comingUpRequest.name }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['requests'],
        computed: {
            nowPlayingRequest() {
                return this.requests.filter(request => request.playingNow).pop()
            },
            comingUpRequest() {
                return this.requests.filter(request => request.playingNext).pop()
            }
        }
    }
</script>
