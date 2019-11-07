<template>
    <div>
        <div v-if="privateBrowsing">Please visit this website in non-incognito mode.</div>
        <div v-else>
            <div class="w-full bg-pink p-2 flex fixed h-12">
                <div class="flex-1">
                    <object class="h-full" data="/images/outsite_logo.svg" type="image/svg+xml"></object>
                </div>
                <button :class="['p-2', 'text-pink', 'text-sm'].concat(formActive ? ['bg-pink-lighter'] : ['bg-white'])" @click="formActive = !formActive">Request a Song</button>
            </div>
            <div class="triangle fixed"></div>

            <div class="pt-12"></div>

            <request-form v-if="formActive" class="bg-white mb-1" @requestSubmitted="addRequest"></request-form>

            <now-playing :requests="requests"></now-playing>

            <div class="w-full bg-pink uppercase tracking-wide text-sm font-bold block p-2">Requests and Votes</div>

            <requests-list :requests="requests"></requests-list>
        </div>
    </div>
</template>

<script>
    import BrowsingModeDetector from 'js-detect-incognito-private-browsing'

    import RequestForm from './RequestForm'
    import RequestsList from './RequestsList'
    import NowPlaying from './NowPlaying'
    import Request from '../Request'

    export default {
        data() {
            return {
                formActive: false,
                requests: [],
                privateBrowsing: false,
            }
        },
        components: {
            'request-form': RequestForm,
            'requests-list': RequestsList,
            'now-playing': NowPlaying
        },
        mounted() {
            const BrowsingModeDetector = new window.BrowsingModeDetector()
            BrowsingModeDetector.do(browsingInIncognitoMode => { this.privateBrowsing = browsingInIncognitoMode })

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
