<template>
    
</template>

<script>

import {Howl, Howler} from 'howler';

export default {

    data () {
        return { 
            playing:[],
            current_sound:"",
            sounds_url:[
                {name : "ting", url:"/sounds/ting.mp3"},
            ]

        }
    },

    methods: {

        play(name,sound,loop = false) {
            const audio = new Howl({
                src: [sound],
                loop:loop,
                sound:1
            });
            audio.play();
            if (loop = true)
            {
                this.playing.push({name:name,audio:audio});
            }
            
        },

        async playSound(event)
        {
           const sound = this.sounds_url.filter(e=>event.name == e.name)[0];
        //    console.log("here");
            if (!sound) return;
            {
                await this.stopAll();
                this.play(sound.name,sound.url,event.loop);
            }
        },

        stopSound(name)
        {
            const sounds = this.playing.filter(e=>name == e.name);
            if (sounds)
            {
                sounds.forEach(sound => {
                    sound.audio.stop(); //for assurance
                    sound.audio.unload();
                });
                this.playing = this.playing.filter(e=>name != e.name);
            }
        },

        async stopAll(){
            const sounds = this.playing;
            if (sounds)
            {
                await sounds.forEach(sound => {
                    sound.audio.stop(); //for assurance
                    sound.audio.unload();
                });
                this.playing = [];
            }
        }
    },

    created(){
        const self = this;
        this.emitter.off("play_sound");
        this.emitter.on('play_sound', event => {
            this.playSound(event);
        });

        this.emitter.off("stop_sound");
        this.emitter.on('stop_sound', name => {
            this.stopSound(name);
        });

        this.emitter.off("stop_sound");
        this.emitter.on('stop_sound_all', name => {
            this.stopAll();
        });
    },

    beforeDestroy(){
        // alert("here");
        this.stopAll();
    },

    mounted() {
        
        // const self = this;
        // this.emitter.on('play_sound', event => {
        //     this.playSound(event);
        // });

        // this.emitter.on('stop_sound', name => {
        //     this.stopSound(name);
        // });

    },

    watch:{
      
    }
}

</script>