<template>
    <div>
        <div v-for="(request, index) in sortedRequests" :class="['flex', 'h-18'].concat(request.owner ? ['bg-yellow-dark'] : ['']).concat(index % 2 == 0 ? [] : ['bg-grey-darker'])">
            <div class="flex-1 flex p-2">
                    <img v-if="request.image" :src="request.image" class="mr-2">
                    <div class="flex-1">
                        <span class="block font-bold">{{ request.track }}</span>
                        {{ request.artist }}
                    </div>
            </div>

            <div v-if="request.allowedToVote" @click="request.upvote()" :class="['text-lg', 'font-bold', 'flex', 'items-center', 'text-center', 'leading-none', 'w-10'].concat(index % 2 == 0 ? ['bg-black'] : ['bg-grey-darkest'])">
                <div class="flex-1">
                    <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 5 20 10"><path d="M10.707 7.05L10 6.343 4.343 12l1.414 1.414L10 9.172l4.243 4.242L15.657 12z"/></svg>
                    {{ request.votes }}
                </div>
            </div>

            <div v-else class="text-lg font-bold flex items-center text-center w-10">
                <div class="flex-1">{{ request.votes }}</div>
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
