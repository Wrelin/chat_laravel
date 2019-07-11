<template>
    <div class="container">
        <hr>
        <div class="row">
            <div class="col-sm-8">
                <div class="form-control mh-100" style="overflow: auto; height: 200px">
                    <div v-for="message in messages">
                        <span v-if="message.type == 'image'">{{message.body}}
                            <a :href="message.path" download>
                                <img :src="message.path" height="100" alt="image">
                            </a>
                        </span>
                        <span v-else-if="message.type == 'file'">{{message.body}}
                            <a :href="message.path" download>{{message.path}}</a>
                        </span>
                        <span v-else v-html="message.body"></span>
                    </div>
                </div>
                <hr>
                <input type="text" class="form-control" v-model="textMessage" @keyup.enter="sendMessage"
                       @keydown="actionUser">
                <span v-if="isActive">{{isActive.name}} печатает...</span>
            </div>
            <div class="col-sm-4">
                <select class="form-control" v-model="userSelect" size="5">
                    <option v-for="user in users" :value="user.id">{{user.name}} {{user.status}}</option>
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <input type="file" value="Файл" @change="fileInputChange">
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user',
            'users',
            'databaseMessages'
        ],
        data() {
            return {
                messages: [],
                textMessage: '',
                isActive: false,
                typingTimer: false,
                userSelect: false,
            }
        },
        computed: {
            commonChannel() {
                return window.Echo.join('chat-action.*');
            },
            privateChannel() {
                return window.Echo.join('chat-action.' + this.user.id);
            },
            eventChannel() {
                return window.Echo.join('chat-action.' + this.userSelect);
            },
        },
        mounted() {
            this.databaseMessages.forEach((data) => {
                let message = {
                    body: this.getFormattedDate(data.date) + ' : ' + this.getStringWithLinks(data.body),
                    type: data.type,
                    path: data.path,
                }
                this.messages.push(message)
            });
            this.commonChannel
                .here((activeUsers) => {
                    this.users.forEach((user) => {
                        activeUsers.forEach((activeUser) => {
                            if (user.id == activeUser.id) {
                                user.status = 'Онлайн';
                                return
                            }
                            user.status = 'Не в чате';
                        })
                    });
                    this.$forceUpdate();
                })
                .joining((newUser) => {
                    let isExists = false;
                    this.users.forEach((user) => {
                        if (user.id == newUser.id) {
                            user.status = 'Онлайн';
                            isExists = true
                            return
                        }
                    })
                    if (!isExists) {
                        newUser.status = 'Онлайн'
                        this.users.push(newUser)
                    }
                    this.$forceUpdate();
                })
                .leaving((leaveUser) => {
                    this.users.forEach((user) => {
                        if (user.id == leaveUser.id) {
                            user.status = 'Не в сети';
                            return
                        }
                    })
                    this.$forceUpdate();
                })
                .listen('PrivateChat', ({data}) => {
                    let message = {
                        body: this.getFormattedDate(data.date) + ' : ' + this.getStringWithLinks(data.body),
                        type: data.type,
                        path: data.path,
                    }
                    this.messages.push(message)
                    this.isActive = false
                })
                .listenForWhisper('typing', (data) => {
                    this.isActive = data
                    if (this.typingTimer) clearTimeout(this.typingTimer);
                    this.typingTimer = setTimeout(() => {
                        this.isActive = false
                    }, 2000)
                })
            this.privateChannel
                .listen('PrivateChat', ({data}) => {
                    let message = {
                        body: this.getFormattedDate(data.date) + ' : ' + this.getStringWithLinks(data.body),
                        type: data.type,
                        path: data.path,
                    }
                    this.messages.push(message)
                    this.isActive = false
                })
                .listenForWhisper('typing', (data) => {
                    this.isActive = data
                    if (this.typingTimer) clearTimeout(this.typingTimer);
                    this.typingTimer = setTimeout(() => {
                        this.isActive = false
                    }, 2000)
                });

        },
        methods: {
            sendMessage() {
                if (!this.userSelect) this.userSelect = '*';

                let fullMessage = this.user.name + ' : ' + this.textMessage
                let date = new Date();
                axios.post('/messages', {
                    body: fullMessage,
                    date: date.valueOf(),
                    user_to: this.userSelect,
                    user_from: this.user.id,
                    type: 'text',
                    path: '',
                })
                let message = {
                    body: this.getFormattedDate(date) + ' : ' + this.getStringWithLinks(fullMessage),
                    type: 'text',
                    path: ''
                }
                this.messages.push(message)
                this.textMessage = ''
            },
            actionUser() {
                if (!this.userSelect || this.userSelect == '*') {
                    this.commonChannel.whisper('typing', {
                        name: this.user.name
                    });
                } else {
                    this.eventChannel.whisper('typing', {
                        name: this.user.name
                    });
                }
            },
            getFormattedDate(time) {
                let date = new Date(time)
                let strDate = "";
                let today = new Date();

                let yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);

                let beforeYesterday = new Date();
                beforeYesterday.setDate(beforeYesterday.getDate() - 2);

                if (today.getDate() == date.getDate()) {
                    strDate = "Сегодня";
                } else if (yesterday.getDate() == date.getDate()) {
                    strDate = "Вчера";
                } else if (beforeYesterday.getDate() == date.getDate()) {
                    strDate = "Позавчера";
                } else {
                    strDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                }

                strDate += ' в ' + ('0' + (date.getHours() + 1)).slice(-2) + ":" + ('0' + date.getMinutes()).slice(-2);

                return strDate;
            },
            getStringWithLinks($message) {
                let re = /([^\"=]{2}|^)((https?|ftp):\/\/\S+[^\s.,> )\];'\"!?])/;
                let subst = '$1<a href="$2" target="_blank">$2</a>';
                return $message.replace(re, subst);
            },
            fileInputChange() {
                let file = event.target.files[0]
                let form = new FormData()
                form.append('file', file)

                axios.post('/file/upload', form).then((response) => {
                    if (!this.userSelect) this.userSelect = '*';

                    let fullMessage = this.user.name
                    let date = new Date();
                    let type = file.type.includes('image') ? 'image' : 'file'
                    axios.post('/messages', {
                        body: fullMessage,
                        date: date.valueOf(),
                        user_to: this.userSelect,
                        user_from: this.user.id,
                        type: type,
                        path: response.data.path,
                    })
                    let message = {
                        body: this.getFormattedDate(date) + ' : ' + this.getStringWithLinks(fullMessage),
                        type: type,
                        path: response.data.fullPath
                    }
                    this.messages.push(message)
                })
            }
        }
    }
</script>
