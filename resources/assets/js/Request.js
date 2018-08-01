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