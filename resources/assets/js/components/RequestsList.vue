<template>
    <div>
        <div v-for="(request, index) in sortedRequests" :class="['p-2', 'flex'].concat(request.owner ? ['bg-yellow-dark'] : ['']).concat(index % 2 == 0 ? [] : ['bg-grey-dark'])">
            <div class="flex-1">
                <div class="flex">
                    <img v-if="request.image" :src="request.image" class="mr-2">
                    <div class="flex-1">
                        <span class="block font-bold">{{ request.track }}</span>
                        {{ request.artist }}
                    </div>
                </div>
            </div>
            <div class="text-lg font-bold flex items-center">
                <div>
                    {{ request.votes }}
                    <span v-show="request.allowedToVote" @click="request.upvote()">UP</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['requests'],
        computed: {
            sortedRequests() {
                return this.requests.sort((a, b) => b.votes - a.votes)
            }
        },
    }
</script>
