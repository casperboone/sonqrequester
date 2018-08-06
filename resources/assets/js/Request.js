export default class Request {
    constructor(data) {
        this.id = data.id
        this.name = data.name
        this.track = data.track
        this.artist = data.artist
        this.image = data.image
        this.votes = data.votes
        this.allowedToVote = data.allowed_to_vote == undefined ? true : data.allowed_to_vote
        this.owner = data.owner == undefined ? false : data.owner
        this.playingNow = data.playing_now
        this.playingNext = data.playing_next
    }

    upvote() {
        this.votes++
        this.allowedToVote = false

        axios.post('/requests/' + this.id + '/upvote')
            .then(response => {
                this.allowedToVote = response.data.data.allowed_to_vote
            })
            .catch(() => {
                this.votes--
                this.allowedToVote = true
            })
    }

    removeName() {
        return axios.post('/requests/' + this.id + '/remove-name')
    }

    archive() {
        return axios.delete('/requests/' + this.id)
    }

    markAsNowPlaying() {
        return axios.post('/requests/' + this.id + '/play-now')
    }

    markAsNext() {
        return axios.post('/requests/' + this.id + '/play-next')
    }
}
