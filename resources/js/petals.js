/**
 * Falling petals on a full-screen canvas: pink lotus, white jasmine and golden romduol (ផ្កាធ្លាក់).
 * Shared by the landing page and the invitation (theme option "petals").
 * Returns { set(on) } to pause and clear, or resume.
 */
export function fallingPetals(canvas) {
    const ctx = canvas.getContext('2d');
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);
    let active = true;

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });

    const petalTypes = ['lotus', 'jasmine', 'romduol'];
    const petals = [];
    const count = Math.min(Math.floor(width / 45), 26); // calm, gentle density

    class Petal {
        constructor() {
            this.reset(true);
        }

        reset(initial = false) {
            this.x = Math.random() * width;
            this.y = initial ? Math.random() * height : -30;
            this.size = 12 + Math.random() * 14;
            this.type = petalTypes[Math.floor(Math.random() * petalTypes.length)];
            this.speedY = 0.6 + Math.random() * 0.9;
            this.speedX = -0.3 + Math.random() * 0.6;
            this.rotation = Math.random() * 360;
            this.rotSpeed = (Math.random() - 0.5) * 1.5;
            this.sway = Math.random() * 100;
            this.swaySpeed = 0.015 + Math.random() * 0.02;
            this.opacity = 0.7 + Math.random() * 0.25;
            this.scaleX = 1;
        }

        update() {
            this.y += this.speedY;
            this.sway += this.swaySpeed;
            this.x += Math.sin(this.sway) * 0.8 + this.speedX;
            this.rotation += this.rotSpeed;
            this.scaleX = Math.cos(this.sway * 1.5);

            if (this.y > height + 30 || this.x < -40 || this.x > width + 40) {
                this.reset();
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate((this.rotation * Math.PI) / 180);
            ctx.scale(this.scaleX, 1);
            ctx.globalAlpha = this.opacity;

            if (this.type === 'lotus') {
                // Soft pink lotus petal (ស្រទាប់ផ្កាឈូក)
                const grad = ctx.createLinearGradient(0, -this.size, 0, this.size);
                grad.addColorStop(0, '#FFA8BA');
                grad.addColorStop(0.6, '#E85B81');
                grad.addColorStop(1, '#8C1B35');

                ctx.beginPath();
                ctx.moveTo(0, -this.size);
                ctx.bezierCurveTo(this.size * 0.7, -this.size * 0.3, this.size * 0.8, this.size * 0.5, 0, this.size);
                ctx.bezierCurveTo(-this.size * 0.8, this.size * 0.5, -this.size * 0.7, -this.size * 0.3, 0, -this.size);
                ctx.fillStyle = grad;
                ctx.fill();
            } else if (this.type === 'romduol') {
                // Golden romduol petal (ផ្ការំដួលមាស)
                const grad = ctx.createLinearGradient(-this.size * 0.5, -this.size, this.size * 0.5, this.size);
                grad.addColorStop(0, '#FFE8A3');
                grad.addColorStop(0.5, '#D4AF37');
                grad.addColorStop(1, '#96741F');

                ctx.beginPath();
                ctx.moveTo(0, -this.size * 0.9);
                ctx.quadraticCurveTo(this.size * 0.7, -this.size * 0.2, this.size * 0.4, this.size * 0.8);
                ctx.quadraticCurveTo(0, this.size * 0.5, -this.size * 0.4, this.size * 0.8);
                ctx.quadraticCurveTo(-this.size * 0.7, -this.size * 0.2, 0, -this.size * 0.9);
                ctx.fillStyle = grad;
                ctx.fill();
            } else {
                // Fragrant white jasmine blossom (ផ្កាម្លិះ) with a gold centre
                ctx.fillStyle = '#FFFFFF';
                ctx.beginPath();
                for (let i = 0; i < 5; i++) {
                    const angle = (i * Math.PI * 2) / 5;
                    ctx.arc(Math.cos(angle) * (this.size * 0.55), Math.sin(angle) * (this.size * 0.55), this.size * 0.25, 0, Math.PI * 2);
                }
                ctx.fill();
                ctx.fillStyle = '#E5C04A';
                ctx.beginPath();
                ctx.arc(0, 0, this.size * 0.15, 0, Math.PI * 2);
                ctx.fill();
            }

            ctx.restore();
        }
    }

    for (let i = 0; i < count; i++) {
        petals.push(new Petal());
    }

    function animate() {
        if (active) {
            ctx.clearRect(0, 0, width, height);
            for (const p of petals) {
                p.update();
                p.draw();
            }
        }
        requestAnimationFrame(animate);
    }
    animate();

    return {
        set(on) {
            active = on;
            if (!on) ctx.clearRect(0, 0, width, height);
        },
    };
}
