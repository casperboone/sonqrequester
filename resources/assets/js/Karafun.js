import io from 'socket.io-client'

class SocketClient {
    constructor(host, channel) {
        this.channel = channel
        this.host = host
        this.socket = null
        this.socketId = null
        this.events = {}
    }

    connect(login) {
        this.login = login
        this.socket = io(this.host, {
            'multiplex': false,
            'transports': ['polling'],
            'query': {
                'remote': 'kf' + this.channel
            }
        })
        this.socket.on('connect', () => this._onConnected())
        this.socket.on('die', () => this.socket.disconnect())
        this.socket.on('serverUnreachable', () => {})
        this.socket.on("loginAlreadyTaken", () => console.log('loginAlreadyTaken'))

        this.socket.on('status', status => this.emit('status', status))
        this.socket.on('preferences', preferences => console.log(preferences))
        this.socket.on('permissions', permissions => console.log(permissions))
        this.socket.on('queue', queue => this.emit('queue', queue))

        this.socket.on('close', console.log)
        this.socket.on('error', console.log)
        this.socket.on('disconnect', console.log)

        this.socket.on("logout", () => this.emit('logout'))
    }

    send(event, params) {
        this.socket.emit(event, params)
    }

    close() {
        if (this.socket) {
            this.socket.disconnect()
        }
    }

    on(event, listener) {
        if (typeof(this.events[event]) !== 'object') {
            this.events[event] = []
        }
        this.events[event].push(listener)
    }

    emit(event, value) {
        if (typeof(this.events[event]) === 'object') {
            this.events[event].forEach(listener => listener(value))
        }
    }

    _onConnected() {
        const auth = {
            login: this.login,
            channel: this.channel,
            role: 'participant',
            app: 'karafun',
            socket_id: this.socketId
        }
        this.socket.emit("authenticate", auth, () => {
            this.socketId = this.socket.id
            this.emit('authenticated', this)
        })
    }
}

const PlayerState = {
    idle: 0,
    infoscreen: 1,
    loading: 2,
    paused: 3,
    playing: 4
}

class PlayerStatus {
    constructor(client) {
        this.state = PlayerState.loading
        this.pitch = 0
        this.tempo = 0
        this.volume = 100
        this.volumeBv = 100
        this.volumeLd = {}
        this.volumeMic = 100
        this.songPlaying = {artist: "", id: 0, queueId: "", singer: "", songId: 0, status: "", title: ""}
        this.client = client
        this.client.on('status', status => this._updateStatus(status))
    }

    _updateStatus(status) {
        this.state = PlayerState[status.state] !== undefined ? PlayerState[status.state] : PlayerState.idle
        this.pitch = status.pitch
        this.tempo = status.tempo
        this.volume = status.volume
        this.volumeBv = status.volumeBv
        this.volumeLd = this._parseLeadsVolume(status.volumeLd)
        this.volumeMic = status.volumeMic !== undefined ? Math.floor(status.volumeMic) : 0
        this.songPlaying = status.songPlaying
    }

    _parseLeadsVolume(volumes) {
        const volumeLd = {}
        for (const key in volumes) {
            const currentLd = {
                key: key,
                value: volumes[key]
            }
            const search = key.match(/^ld_(.+)\.(.+)$/)
            if (search) {
                currentLd.name = search[1]
            } else if (key.length > 1) {
                currentLd.name = key
            }
            volumeLd[key] = currentLd
        }
        return volumeLd
    }
}

export default class Remote {
    constructor(host, channel) {
        this.client = new SocketClient(host,channel)
        this.status = new PlayerStatus(this.client)

        this.authenticated = false
        this.client.on('authenticated', () => {
            this.authenticated = true
        })
        this.queue = []
        this.client.on('queue', queue => {
            this.queue = queue
        })
    }

    connect(login) {
        this.client.connect(login)
    }

    isAuthenticated() {
        return this.authenticated
    }

    addToQueue(songId, singer, position = 99999) {
        this.client.send('queueAdd', {
            songId: parseInt(songId),
            pos: position,
            singer: singer
        })
    }
}
//
// const karafun = new Remote("https://www.karafun.nl", "769024")
//
// karafun.connect("Casper28892")
//
// setInterval(() => console.log(karafun.status), 5000)
//
// karafun.client.send('queueAdd', {
//     songId: parseInt(14466),
//     pos: 99999,
//     singer: "Woeee dit komt uit een node scriptje 2"
// })
