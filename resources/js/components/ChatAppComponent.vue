<template>
    <div class="card">
        <div class="card-header">Dashboard</div>

        <div class="card-body">
            <div class="border chat" id="chat">
                <template v-for="message in AllMessage">
                    <div v-if="userID === message.id" class="pl-2">
                        <h6 class="w-100 text-left pl-2 m-0" style="margin-bottom: 4px !important;" v-if="message.showName">{{message.name}}</h6>
                        <div class="d-flex w-100" style="margin-bottom: 1px">
                            <p class="p-2 maxWidth m-0 border border-secondary text-white border-radius bg-secondary">
                                {{message.message}}
                            </p>
                            <div class="d-flex align-items-end p-2">
                                <p style="color: grey;margin: 0">{{message.time}}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="pr-2">
                        <h6 class="w-100 text-right pr-2 m-0" style="margin-bottom: 4px !important;" v-if="message.showName">{{message.name}}</h6>
                        <div class="d-flex justify-content-end w-100" style="margin-bottom: 1px">
                            <div class="d-flex align-items-end p-2">
                                <p style="color: grey;margin: 0">{{message.time}}</p>
                            </div>
                            <p class="p-2 maxWidth m-0 border border-primary text-white border-radius bg-primary">
                                {{message.message}}
                            </p>
                        </div>
                    </div>
                </template>
                <div v-if="typing !== ''" class="p-2">
                    <span class="badge badge-pill badge-info text-white" style="font-size: 15px;">{{typing}}</span>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex">
            <input v-on:keyup.enter="sendMessage" v-model="message" class="form-control mr-3" placeholder="type your message" />
            <button v-on:click="sendMessage" class="btn btn-primary">send</button>
        </div>
    </div>

</template>

<script>
    import axios from 'axios';
    export default {
        name: "ChatAppComponent",
        props:['user_id'],
        data(){
            return{
                message:'',
                AllMessage:[],
                email:'',
                name:'',
                id:'',
                showName:true,
                userID:null,
                currentUser:[],
                typing:null,
                time:null
            }
        },
        methods:{
            sendMessage:function () {
                if(this.message !== ''){
                    axios.post('/send', {message: this.message, user_id: this.user_id}).then((response) => {
                        this.message = '';
                    }).catch((error) => {
                        console.log(error);
                    });
                }
            },
            getTime:function () {
                let time = new Date();
                return time.getHours() + ":" + time.getMinutes()
            }
        },
        created(){
            this.userID = Number(this.user_id);
            axios.get('/user').then((response) => {
                // console.log(response);
                this.currentUser = response.data;
            }).catch((error) => {
                console.log(error);
            });
        },
        watch:{
            message:function () {
                Echo.private('chat')
                    .whisper('typing', {
                        message: this.message,
                        user:this.currentUser.user.name,
                    });
            }
        },
        mounted() {
            let scroll = $('#chat');
            scroll.animate({
                scrollTop: scroll.get(0).scrollHeight
            }, 1000);
            Echo.private('chat').listen('ChatEvent', (e) => {
                console.log(e);
                this.showName = this.id !== e.user.id;
                this.email = e.user.email;
                this.name = e.user.name;
                this.id = e.user.id;
                this.time = this.getTime();
                this.AllMessage.push({
                    id: e.user.id,
                    name:e.user.name,
                    email:e.user.email,
                    message:e.message,
                    showName:this.showName,
                    time:this.time
                });
            }).listenForWhisper('typing', (e) => {
                if(e.message !== ''){
                    this.typing = e.user + ' typing ....';
                    // console.log(e.user + ' typing ....');
                }else {
                    this.typing = '';
                }
            });
            Echo.join('chat').here((users) => {
                console.log(users)
            }).joining((user) => {
                console.log(user.user);
            }).leaving((user) => {
                console.log(user.user);
            });
        }
    }
</script>

<style scoped>
.chat{
    height: 300px;
    overflow-x: auto;
}
.border-radius{
    border-radius: 20px;
}
.sender{
    /*flex-direction: column;*/
}
.maxWidth{
    max-width: 75%;
}
</style>