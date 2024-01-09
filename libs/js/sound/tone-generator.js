class PlayerWorklet extends AudioWorkletProcessor {
    constructor() {
        super();
        this.port.onmessage = this.onmessage.bind(this);
        this.playing = false;
		this.freq = 440;
		this.index = 0;
		this.sampleRate = 48000;
    }

    onmessage(e) {
        if (e.data.type === 'play') {
            // Toggle between playing and silence
            this.playing = !this.playing;
			this.phase = 0;
			this.freq = 440;
        }
        if (e.data.type === 'freq') {
            // Toggle between playing and silence
            this.freq = e.data.value;
        }
    }

    process(inputs, outputs) {
        const output = outputs[0];
        const channel = output[0];

        for (let i = 0; i < channel.length; ++i) {
            if (this.playing) {
                //channel[i] = Math.sin(this.phase);
				channel[i] = 0.5 * Math.sin(this.freq * 2 * Math.PI * (this.index / this.sampleRate));
				//this.phase += 0.1;
				this.index += 1;
            } else {
                channel[i] = 0;
            }
        }

        return true;
    }
}

registerProcessor('tone-worklet', PlayerWorklet);