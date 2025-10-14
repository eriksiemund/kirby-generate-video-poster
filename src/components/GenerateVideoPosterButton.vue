<template>
    <div class="k-generate-video-poster">
        <k-button
        class="k-generate-video-poster-button"
        v-bind="$props"
        icon="image"
        variant="filled"
        @click="handleGenerateVideoPoster"
        >Generate Video Poster</k-button>
    </div>
</template>

<script>
export default {
    extends: "k-button",
    props: {
        pageid          : String,
        basename        : String,
        extension       : String,
        url             : String,
        isposterrequired: String,
        posterfieldname : String
    },
    methods: {
        async handleGenerateVideoPoster () {
            const requiredProps = ['pageid', 'basename', 'extension', 'url', 'isposterrequired']

            const invalidProps = requiredProps.filter(key => {
                const value = this[key]
                return value === undefined || value === null || value === ''
            })

            if (invalidProps.length > 0) {
                console.warn('(Generate Video Poster) Invalid props:', invalidProps.join(', '))
                return
            }

            let video = document.querySelector('.k-file-preview-frame video')
            let isVideoCreated = false

            if (!video) {
                isVideoCreated    = true

                video             = document.createElement('video')
                video.src         = this.url
                video.crossOrigin = 'anonymous'
                video.muted       = true
                video.playsInline = true

                await new Promise(resolve => {
                    video.addEventListener('loadedmetadata', resolve, { once: true })
                })
                
                video.currentTime = 0
                
                await new Promise(resolve => {
                    video.addEventListener('seeked', resolve, { once: true })
                })
            }
            
            const canvas        = document.createElement('canvas')
                  canvas.width  = video.videoWidth
                  canvas.height = video.videoHeight
            const ctx           = canvas.getContext('2d')
            
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
            
            canvas.toBlob(async blob => {
                try {
                    const formatCurrentTime = (time) => {
                        const seconds = Math.floor(time)
                        const hundredths = Math.round((time % 1) * 100)
                        const hundredthsPadded = hundredths.toString().padStart(2, '0')
                        return `${seconds}_${hundredthsPadded}`
                    }

                    const videoFilename = this.basename + '.' + this.extension
                    const posterFilename = this.basename + '_' + formatCurrentTime(video.currentTime) + '.png'

                    const formData = new FormData();
                    formData.append('pageId', this.pageid)
                    formData.append('videoFilename', videoFilename)
                    formData.append('posterFilename', posterFilename)
                    formData.append('posterFile', blob, posterFilename) 
                    formData.append('posterFieldname', this.posterfieldname)
                    
                    const response = await fetch('/generate-video-poster/upload', {
                        method: 'POST',
                        body: formData
                    })
                    
                    if (!response.ok) throw new Error('(Generate Video Poster) Upload failed - Response Status: ' + response.status)

                    const data = await response.json()
                    
                    if (data.success) this.$reload()
                    
                } catch (err) {
                    console.error('(Generate Video Poster) Upload failed - Error:', err)
                } finally {
                    if (isVideoCreated) {
                        video.src = ''
                        video.removeAttribute('src')
                        video.load()
                        video.remove()
                    }
                    canvas.width = 0
                    canvas.height = 0
                }
                
            }, 'image/png')
        }
    },
    mounted() {
        if (parseInt(this.isposterrequired) === 1) this.handleGenerateVideoPoster()
    },
}
</script>