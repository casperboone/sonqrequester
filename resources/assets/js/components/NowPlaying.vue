<template>
    <div>
       <div v-if="nowPlayingRequest" class="bg-green-dark w-full text-xl">
           <span class="uppercase tracking-wide text-sm font-bold block p-2">Now Playing</span>

           <div class="flex bg-green p-2">
               <img v-if="nowPlayingRequest.image" :src="nowPlayingRequest.image" class="h-24 mr-2">
               <div class="flex flex-col flex-1">
                   <div class="flex-1">
                       <span class="block font-bold">{{ nowPlayingRequest.track }}</span>
                       {{ nowPlayingRequest.artist }}
                   </div>
                   <span class="block text-xs font-bold bg-green-dark px-2 py-1 mt-2 uppercase">
                        Requested by {{ nowPlayingRequest.name }}
                    </span>
               </div>
           </div>
       </div>

        <div v-if="comingUpRequest" class="bg-blue-dark w-full mt-2">
            <span class="uppercase tracking-wide text-sm font-bold block p-2">Coming Up Next</span>

            <div class="flex bg-blue p-2">
                <img v-if="comingUpRequest.image" :src="comingUpRequest.image" class="mr-2">
                <div class="flex flex-col flex-1">
                    <div class="flex-1">
                        <span class="block font-bold">{{ comingUpRequest.track }}</span>
                        {{ comingUpRequest.artist }}
                    </div>
                    <span class="block text-xs font-bold bg-blue-dark text-white px-2 py-1 mt-2 uppercase">
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
                return this.requests.filter(request => request.artist == 'Nirvana').pop()
            },
            comingUpRequest() {
                return this.requests.filter(request => request.artist == 'blink-182').pop()
            }
        }
    }
</script>
