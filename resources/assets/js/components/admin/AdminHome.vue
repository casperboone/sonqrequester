<template>
    <div class="text-white">
        <notifications />

        <div class="w-full bg-blue uppercase tracking-wide text-sm font-bold block p-2">Karafun Connection</div>
        <div class="text-black border-blue border-l border-b border-r mb-4 p-2 flex">
            <div class="flex-1">
                Host:
                <input v-model="karafunSettings.host" type="text" class="block w-full p-3 h-10 bg-grey-lighter" />
                Channel:
                <input v-model="karafunSettings.channel" type="text" placeholder="e.g. 769024" class="block w-full p-3 h-10 bg-grey-lighter" />
                <button @click="connectKarafun()" class="bg-green text-white rounded p-2 mt-2">Connect</button>
                <span>Status: {{(karafun && karafun.isAuthenticated()) ? 'Connected' : 'Disconnected' }}</span>
            </div>
            <div v-if="karafun" class="ml-4 w-1/3">
                <div v-for="item in karafun.queue" class="border-b mb-2 flex">
                    <div class="flex-1">
                        <div class="font-bold">{{ item.title }}</div>
                        {{ item.artist }}
                    </div>
                    <div v-if="karafun.status.songPlaying.songId == item.songId">
                        Playing
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full bg-pink uppercase tracking-wide text-sm font-bold block p-2">Add a request manually</div>
        <request-form class="border-pink border-l border-b border-r mb-1" @requestSubmitted="addRequest"></request-form>

        <now-playing :requests="requests"></now-playing>

        <div class="w-full bg-pink uppercase tracking-wide text-sm font-bold block p-2">Requests and Votes</div>

        <requests-list class="bg-grey-darkest" :requests="requests" :retain-now-playing="true">
            <template slot="description" slot-scope="slotProps">
                <span class="text-xs">Requested by {{ slotProps.request.name }}</span>
            </template>
            <template slot="actions" slot-scope="slotProps">
                <div class="flex items-center">
                    <button class="bg-white rounded p-2 mr-2" @click="removeNameOfRequest(slotProps.request)">Remove Name</button>
                    <button class="bg-white rounded p-2 mr-2" @click="archiveRequest(slotProps.request)">Archive</button>
                    <button class="bg-green text-white rounded p-2 mr-2" @click="markRequestAsNowPlaying(slotProps.request)">Mark as playing</button>
                    <button class="bg-blue text-white rounded p-2 mr-2" @click="markRequestAsNext(slotProps.request)">Mark as next</button>
                </div>
            </template>
        </requests-list>
    </div>
</template>

<script>
    import RequestForm from '../RequestForm'
    import RequestsList from '../RequestsList'
    import NowPlaying from '../NowPlaying'
    import Request from '../../Request'
    import KarafunRemote from '../../Karafun'

    export default {
        data() {
            return {
                formActive: false,
                requests: [],
                karafunSettings: {
                    host: 'https://www.karafun.nl/',
                    channel: '769024'
                },
                karafun: undefined
            }
        },
        components: {
            'request-form': RequestForm,
            'requests-list': RequestsList,
            'now-playing': NowPlaying,
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

                if (this.karafun && this.requests.length <=2) {
                    console.log('called by add')
                    this.syncKarafunQueue()
                }

                window.scrollTo(0, document.body.scrollHeight)
            },
            showSuccessNotification(message) {
                this.$notify({
                    type: 'success',
                    title: 'Action succesfully executed',
                    text: message
                })
            },
            showErrorNotification() {
                this.$notify({
                    type: 'error',
                    title: 'Something went wrong',
                    text: 'Action could not be executed'
                })
            },
            removeNameOfRequest(request) {
                request.removeName()
                    .then(response => this.showSuccessNotification('The name of the author is now ' + response.data.data.name))
                    .catch(() => this.showErrorNotification())
            },
            archiveRequest(request) {
                request.archive()
                    .then(() => this.showSuccessNotification('The request has now been archived'))
                    .catch(() => this.showErrorNotification())
            },
            markRequestAsNowPlaying(request) {
                request.markAsNowPlaying()
                    .then(response => this.showSuccessNotification(response.data.data.track + ' has been marked as now playing'))
                    .catch(() => this.showErrorNotification())
            },
            markRequestAsNext(request) {
                request.markAsNext()
                    .then(response => this.showSuccessNotification(response.data.data.track + ' has been marked as next'))
                    .catch(() => this.showErrorNotification())
            },
            connectKarafun() {
                this.karafun = new KarafunRemote(this.karafunSettings.host, this.karafunSettings.channel)
                this.karafun.connect("songrequester-" + Math.ceil(Math.random() * 10000))
            },
            syncKarafunQueue() {
                if (this.karafun.queue.length <= 1) {
                    const sorted = this.requests
                        .sort((a, b) => {
                            if (b.votes > a.votes) {
                                return 1
                            } else if (b.votes < a.votes) {
                                return -1
                            } else {
                                return a.id - b.id
                            }
                        })
                        .filter(request => !request.playingNow && !request.playingNext)

                    if (sorted.length > 0) {
                        const request = sorted[0]
                        this.karafun.addToQueue(request.trackId, request.name)
                        this.markRequestAsNext(request)
                    }
                }

                if (this.karafun.queue.length > 0) {
                    const karafunNowPlaying = this.karafun.queue[0]

                    const correspondingRequest = this.requests.find(request => request.trackId == karafunNowPlaying.songId)

                    const currentlyPlayingAccordingToSongRequester = this.requests.find(request => request.playingNow)
                    if (correspondingRequest !== currentlyPlayingAccordingToSongRequester) {
                        this.archiveRequest(currentlyPlayingAccordingToSongRequester)
                    }

                    if (correspondingRequest) {
                        this.markRequestAsNowPlaying(correspondingRequest)
                    }
                }
            }
        },
        watch: {
            'karafun.queue': function (queue, oldQueue) {
                console.log('called by watch')
                this.syncKarafunQueue()
            }
        },
    }
</script>
