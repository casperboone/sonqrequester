<template>
    <div>
        <div v-for="(request, index) in sortedRequests" :class="['p-2', 'flex'].concat(index % 2 == 0 ? ['bg-grey-dark'] : [])">
            <div class="flex-1">
                <span class="block font-bold">{{ request.track }}</span>
                {{ request.artist }}
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
        data() {
            return {
                'requests': []
            }
        },
        mounted() {
            axios.get('/requests')
                .then(response => this.requests = response.data.data.map(data => new Request(data)))
        },
        computed: {
            sortedRequests() {
                return this.requests.sort((a, b) => b.votes - a.votes)
            }
        }
    }

    class Request {
        constructor(data) {
            this.id = data.id
            this.name = data.name
            this.track = data.track
            this.artist = data.artist
            this.votes = data.votes
            this.allowedToVote = data.allowed_to_vote
        }

        upvote() {
            this.votes++
            this.allowedToVote = false

            axios.post('/requests/' + this.id + '/upvote')
                .catch(() => {
                    this.votes--
                    this.allowedToVote = true
                })
        }
    }
</script>
