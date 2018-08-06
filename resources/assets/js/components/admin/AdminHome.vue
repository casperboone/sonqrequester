<template>
    <div class="text-white">
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
                    <button class="bg-white rounded p-2 mr-2">Remove Name</button>
                    <button class="bg-white rounded p-2 mr-2">Remove</button>
                    <button class="bg-green text-white rounded p-2 mr-2">Mark as next</button>
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
                );
        },
        methods: {
            updateRequests() {
                axios.get('/requests')
                    .then(response => this.requests = response.data.data.map(data => new Request(data)))
            },
            addRequest(request) {
                this.formActive = false

                this.requests.push(request)

                window.scrollTo(0, document.body.scrollHeight)
            }
        }
    }
</script>
