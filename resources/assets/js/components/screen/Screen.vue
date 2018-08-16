<template>
    <div>
        <div class="w-full bg-pink p-4 flex fixed h-24">
            <div class="flex-1">
                <object class="h-full" data="/images/outsite_logo.svg" type="image/svg+xml"></object>
            </div>
            <div class="p-4 text-white font-bold text-3xl">Song Requester</div>
        </div>
        <div class="triangle-large fixed"></div>

        <div class="pt-24"></div>

        <now-playing :requests="requests"></now-playing>

        <div class="w-full bg-pink text-center uppercase tracking-wide text-3xl font-bold block p-2">Requests and Votes</div>

        <requests-list :requests="requests"></requests-list>
    </div>
</template>

<script>
    import RequestsList from './RequestsListScreen'
    import NowPlaying from './NowPlayingScreen'
    import Request from '../../Request'

    export default {
        data() {
            return {
                formActive: false,
                requests: [],
            }
        },
        components: {
            'requests-list': RequestsList,
            'now-playing': NowPlaying
        },
        mounted() {
            this.updateRequests()
            window.setInterval(this.updateRequests, 10000)

            window.Echo.channel('song-requests')
                .listen("SongWasRequested", event => this.requests.push(new Request(event.request)))
                .listen("RequestGotVote", event =>
                    this.requests.find(request => request.id == event.request.id).votes = event.request.votes
                )
                .listen("RequestGotUpdated", event => this.processUpdatedRequest(new Request(event.request)))
                .listen("RequestWasArchived", event => this.requests = this.requests.filter(request => request.id != event.request.id))
        },
        methods: {
            updateRequests() {
                axios.get('/requests')
                    .then(response => this.requests = response.data.data.map(data => new Request(data)))
            },
            processUpdatedRequest(updatedRequest) {
                let allRequestsExceptUpdated = this.requests.filter(request => request.id != updatedRequest.id)

                if (updatedRequest.playingNow) {
                    allRequestsExceptUpdated.forEach(request => request.playingNow = false)
                }
                if (updatedRequest.playingNext) {
                    allRequestsExceptUpdated.forEach(request => request.playingNext = false)
                }

                this.requests = allRequestsExceptUpdated.concat(updatedRequest)
            },
            addRequest(request) {
                this.formActive = false

                this.requests.push(request)

                window.scrollTo(0, document.body.scrollHeight)
            }
        }
    }
</script>
