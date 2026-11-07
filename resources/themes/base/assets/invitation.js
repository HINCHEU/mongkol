// Behaviour shared by every theme: child themes import this file with their own CSS.
import Alpine from 'alpinejs';
import { fallingPetals } from '../../../js/petals.js';

const root = document.documentElement;
const KM_DIGITS = '០១២៣៤៥៦៧៨៩';
const khmerDigits = (value) => String(value).replace(/\d/g, (d) => KM_DIGITS[d]);

Alpine.store('lang', {
    current: root.lang,
    set(lang) {
        this.current = root.lang = lang;
        try {
            localStorage.setItem(root.dataset.langKey, lang);
        } catch {
            // Private mode or blocked storage: the choice just isn't remembered.
        }
    },
    digits(value) {
        return this.current === 'km' ? khmerDigits(value) : String(value);
    },
});

Alpine.store('toast', {
    message: '',
    timer: null,
    show(message) {
        this.message = message;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => (this.message = ''), 2500);
    },
});

// The envelope's one tap opens it, starts the music and fires the open beacon (§8).
Alpine.data('envelope', (beaconUrl) => ({
    opening: false,
    open() {
        if (this.opening) return;
        this.opening = true;
        window.dispatchEvent(new CustomEvent('invitation-open'));
        if (beaconUrl) navigator.sendBeacon?.(beaconUrl);
        setTimeout(() => {
            root.classList.add('is-open');
            this.$el.remove();
        }, 1200);
    },
}));

Alpine.data('music', () => ({
    playing: false,
    init() {
        // Played inside the envelope's tap, so browsers allow the sound.
        window.addEventListener('invitation-open', () => this.play());
        document.addEventListener('visibilitychange', () => document.hidden && this.pause());
    },
    play() {
        this.$refs.audio.play().then(() => (this.playing = true)).catch(() => (this.playing = false));
    },
    pause() {
        this.$refs.audio.pause();
        this.playing = false;
    },
    toggle() {
        this.playing ? this.pause() : this.play();
    },
}));

Alpine.data('countdown', (target) => ({
    left: null,
    init() {
        this.tick();
        this.timer = setInterval(() => this.tick(), 1000);
    },
    destroy() {
        clearInterval(this.timer);
    },
    tick() {
        const ms = new Date(target) - Date.now();
        if (ms <= 0) {
            this.left = null;
            clearInterval(this.timer);
            return;
        }
        const s = Math.floor(ms / 1000);
        this.left = { days: Math.floor(s / 86400), hours: Math.floor(s / 3600) % 24, minutes: Math.floor(s / 60) % 60, seconds: s % 60 };
    },
    get started() {
        return this.left === null;
    },
}));

// On the wedding day, mark the next ceremony from the guest's own clock (§9.7).
Alpine.data('timeline', () => ({
    init() {
        const now = Date.now();
        const items = [...this.$el.querySelectorAll('[data-start]')];
        const next = items.find((li) => new Date(li.dataset.start) > now);
        if (next && new Date(next.dataset.start).toDateString() === new Date(now).toDateString()) {
            next.classList.add('is-next');
        }
    },
}));

Alpine.data('dock', () => ({
    hidden: false,
    last: 0,
    init() {
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            this.hidden = y > this.last && y > 120;
            this.last = y;
        }, { passive: true });
    },
}));

Alpine.data('copy', (text, done) => ({
    async copy() {
        try {
            await navigator.clipboard.writeText(text);
        } catch {
            const field = Object.assign(document.createElement('textarea'), { value: text });
            document.body.append(field);
            field.select();
            document.execCommand('copy');
            field.remove();
        }
        Alpine.store('toast').show(done);
    },
}));

// RSVP and wishes post JSON to `${url}/rsvp` or `${url}/wishes`; no url (demo, editor preview) only shows the form.
// `website` is a honeypot: hidden from people, filled in by bots, and the server drops what it carries.
const postForm = async (url, body) => {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(body),
    });
    if (!response.ok) throw new Error(String(response.status));
    return response.json();
};

Alpine.data('rsvp', (url, earlier) => ({
    attending: earlier?.attending ?? null,
    party_size: earlier?.party_size || 1,
    message: earlier?.message ?? '',
    name: '',
    website: '',
    state: earlier ? 'sent' : null, // null | sending | sent | error | demo
    async send() {
        if (!url) return (this.state = 'demo');
        this.state = 'sending';
        try {
            await postForm(`${url}/rsvp`, { attending: this.attending, party_size: this.party_size, message: this.message, name: this.name, website: this.website });
            this.state = 'sent';
        } catch {
            this.state = 'error';
        }
    },
}));

Alpine.data('wishes', (url, list, guestName) => ({
    list,
    name: '',
    message: '',
    website: '',
    state: null, // null | sending | sent | pending | error | demo
    async send() {
        if (!url) return (this.state = 'demo');
        this.state = 'sending';
        try {
            const { shown } = await postForm(`${url}/wishes`, { name: this.name, message: this.message, website: this.website });
            if (shown) this.list.unshift({ name: guestName || this.name, message: this.message.trim() });
            this.state = shown ? 'sent' : 'pending';
            this.message = '';
        } catch {
            this.state = 'error';
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();

// Reveal each section once, when 15% of it is on screen (§8).
const observer = new IntersectionObserver((entries) => {
    for (const entry of entries) {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    }
}, { threshold: 0.15 });
document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

// Theme option "petals": the layout adds the canvas only when the couple turned them on.
const petalsCanvas = document.querySelector('canvas.petals');
if (petalsCanvas && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) fallingPetals(petalsCanvas);
