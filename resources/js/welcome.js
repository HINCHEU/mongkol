// Landing page: falling petals with the On/Off button at the bottom left.
import { fallingPetals } from './petals.js';

const canvas = document.getElementById('petals-canvas');
const button = document.getElementById('petals-toggle-btn');
const label = document.getElementById('petals-label');

if (canvas) {
    const petals = fallingPetals(canvas);
    let active = true;

    button?.addEventListener('click', () => {
        active = !active;
        petals.set(active);
        label.textContent = active ? 'ផ្កាប្រសិទ្ធពរ (On)' : 'ផ្កាប្រសិទ្ធពរ (Off)';
        button.classList.toggle('opacity-60', !active);
    });
}
