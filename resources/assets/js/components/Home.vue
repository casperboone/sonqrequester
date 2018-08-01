<template>
    <div>
        <div class="w-full bg-pink p-2 flex fixed h-12">
            <div class="flex-1">
                <object class="h-full" data="/images/outsite_logo.svg" type="image/svg+xml"></object>
            </div>
            <button :class="['p-2', 'text-pink', 'text-sm'].concat(formActive ? ['bg-pink-lighter'] : ['bg-white'])" @click="formActive = !formActive">Request a Song</button>
        </div>
        <div class="triangle fixed"></div>

        <div class="pt-12"></div>

        <request-form v-if="formActive" class="bg-white" @requestSubmitted="addRequest"></request-form>
        <requests-list :requests="requests"></requests-list>
    </div>
</template>

<script>
    import RequestForm from './RequestForm'
    import RequestsList from './RequestsList'
    import Request from '../Request'

    export default {
        data() {
            return {
                formActive: false,
                requests: [],
            }
        },
        components: {
            'request-form': RequestForm,
            'requests-list': RequestsList
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
