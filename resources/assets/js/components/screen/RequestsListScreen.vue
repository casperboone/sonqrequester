<template>
    <div class="text-3xl">
        <div v-for="(request, index) in sortedRequests" :class="['flex'].concat(request.playingNow ? ['bg-green'] : ['']).concat(request.playingNext ? ['bg-blue'] : ['']).concat(request.owner ? ['bg-yellow-dark'] : ['']).concat(index % 2 == 0 ? [] : ['bg-grey-darker'])">
            <div class="flex-1 flex p-4">
                <img v-if="request.image" :src="request.image" class="mr-4 h-32">
                <div class="flex-1">
                    <div class="flex flex-col h-full">
                        <div class="flex-1">
                            <span class="block font-bold">{{ request.track }}</span>
                            {{ request.artist }}
                        </div>
                        <slot name="description" v-bind:request="request"></slot>
                    </div>
                </div>
            </div>

            <slot name="actions" v-bind:request="request"></slot>

            <div :class="['font-bold', 'flex', 'items-center', 'text-center', 'leading-none', 'w-32', 'text-4xl'].concat(index % 2 == 0 ? ['bg-black'] : ['bg-grey-darkest'])">
                <div class="flex-1">
                    {{ request.votes }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['requests', 'retainNowPlaying'],
        computed: {
            sortedRequests() {
                return this.requests
                    .sort((a, b) => {
                        if (b.votes > a.votes) {
                            return 1
                        } else if (b.votes < a.votes) {
                            return -1
                        } else {
                            return a.id - b.id
                        }
                    })
                    .filter(request => this.retainNowPlaying || !request.playingNow)
                    .filter(request => this.retainNowPlaying ||  !request.playingNext)
                    .slice(0, 4)
            }
        },
    }
</script>
