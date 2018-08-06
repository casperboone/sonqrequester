<template>
    <div class="text-white">
        <notifications />

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

    export default {
        data() {
            return {
                formActive: false,
                requests: [],
            }
        },
        components: {
            'request-form': RequestForm,
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
            }
        }
    }
</script>
