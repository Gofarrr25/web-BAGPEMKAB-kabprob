class AccessibilityControl {
    constructor() {
        this.state = {
            fontSize: 100,
            highlightTitles: false,
            highlightLinks: false,
            dyslexiaFont: false,
            letterSpacing: 0,
            lineHeight: 0,
            fontWeight: 0,
            contrast: 'normal', // normal, dark, light, high
            saturation: 'normal', // normal, high, low, monochrome
            readingGuide: false,
            stopAnimations: false,
            largeCursor: false,
        };
        
        this.init();
    }

    init() {
        this.loadState();
        this.bindEvents();
        this.applyState();
        this.createReadingGuide();
    }

    loadState() {
        const saved = localStorage.getItem('accessibilityState');
        if (saved) {
            this.state = { ...this.state, ...JSON.parse(saved) };
            this.updateUI();
        }
    }

    saveState() {
        localStorage.setItem('accessibilityState', JSON.stringify(this.state));
        this.applyState();
        this.updateUI();
    }

    updateUI() {
        // Update font size text
        const fontSizeEl = document.getElementById('acc-font-size-val');
        if (fontSizeEl) fontSizeEl.textContent = this.state.fontSize + '%';

        // Helper to toggle active classes on buttons
        const toggleBtnClass = (id, isActive) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            if (isActive) {
                btn.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
                btn.classList.remove('border-gray-200');
            } else {
                btn.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200');
                btn.classList.add('border-gray-200');
            }
        };

        toggleBtnClass('btn-sorot-judul', this.state.highlightTitles);
        toggleBtnClass('btn-sorot-tautan', this.state.highlightLinks);
        toggleBtnClass('btn-font-disleksia', this.state.dyslexiaFont);
        
        toggleBtnClass('btn-jarak-huruf', this.state.letterSpacing > 0);
        toggleBtnClass('btn-tinggi-baris', this.state.lineHeight > 0);
        toggleBtnClass('btn-ketebalan-font', this.state.fontWeight > 0);

        toggleBtnClass('btn-kontras-gelap', this.state.contrast === 'dark');
        toggleBtnClass('btn-kontras-terang', this.state.contrast === 'light');
        toggleBtnClass('btn-kontras-tinggi', this.state.contrast === 'high');

        toggleBtnClass('btn-saturasi-tinggi', this.state.saturation === 'high');
        toggleBtnClass('btn-saturasi-rendah', this.state.saturation === 'low');
        toggleBtnClass('btn-monokrom', this.state.saturation === 'monochrome');

        toggleBtnClass('btn-panduan-membaca', this.state.readingGuide);
        toggleBtnClass('btn-hentikan-animasi', this.state.stopAnimations);
        toggleBtnClass('btn-kursor-besar', this.state.largeCursor);
    }

    applyState() {
        const html = document.documentElement;
        const body = document.body;

        // Font Size
        html.style.fontSize = (this.state.fontSize / 100) * 16 + 'px';

        // Highlight Titles
        if (this.state.highlightTitles) {
            body.classList.add('acc-highlight-titles');
        } else {
            body.classList.remove('acc-highlight-titles');
        }

        // Highlight Links
        if (this.state.highlightLinks) {
            body.classList.add('acc-highlight-links');
        } else {
            body.classList.remove('acc-highlight-links');
        }

        // Dyslexia Font
        if (this.state.dyslexiaFont) {
            body.classList.add('acc-dyslexia');
        } else {
            body.classList.remove('acc-dyslexia');
        }

        // Letter Spacing
        body.classList.remove('acc-letter-spacing-1', 'acc-letter-spacing-2', 'acc-letter-spacing-3');
        if (this.state.letterSpacing > 0) {
            body.classList.add('acc-letter-spacing-' + this.state.letterSpacing);
        }

        // Line Height
        body.classList.remove('acc-line-height-1', 'acc-line-height-2', 'acc-line-height-3');
        if (this.state.lineHeight > 0) {
            body.classList.add('acc-line-height-' + this.state.lineHeight);
        }

        // Font Weight
        body.classList.remove('acc-font-weight-1', 'acc-font-weight-2', 'acc-font-weight-3');
        if (this.state.fontWeight > 0) {
            body.classList.add('acc-font-weight-' + this.state.fontWeight);
        }

        // Contrast
        body.classList.remove('acc-contrast-dark', 'acc-contrast-light', 'acc-contrast-high');
        if (this.state.contrast !== 'normal') {
            body.classList.add('acc-contrast-' + this.state.contrast);
        }

        // Saturation
        body.classList.remove('acc-saturation-high', 'acc-saturation-low', 'acc-saturation-monochrome');
        if (this.state.saturation !== 'normal') {
            body.classList.add('acc-saturation-' + this.state.saturation);
        }

        // Reading Guide
        const guide = document.getElementById('acc-reading-guide');
        if (guide) {
            guide.style.display = this.state.readingGuide ? 'block' : 'none';
        }

        // Stop Animations
        if (this.state.stopAnimations) {
            body.classList.add('acc-stop-animations');
        } else {
            body.classList.remove('acc-stop-animations');
        }

        // Large Cursor
        if (this.state.largeCursor) {
            body.classList.add('acc-large-cursor');
        } else {
            body.classList.remove('acc-large-cursor');
        }
    }

    createReadingGuide() {
        if (!document.getElementById('acc-reading-guide')) {
            const guide = document.createElement('div');
            guide.id = 'acc-reading-guide';
            guide.style.position = 'fixed';
            guide.style.left = '0';
            guide.style.right = '0';
            guide.style.height = '10px';
            guide.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            guide.style.borderTop = '2px solid red';
            guide.style.borderBottom = '2px solid red';
            guide.style.zIndex = '999999';
            guide.style.pointerEvents = 'none';
            guide.style.display = 'none';
            document.body.appendChild(guide);

            document.addEventListener('mousemove', (e) => {
                if (this.state.readingGuide) {
                    guide.style.top = (e.clientY - 5) + 'px';
                }
            });
        }
    }

    reset() {
        this.state = {
            fontSize: 100,
            highlightTitles: false,
            highlightLinks: false,
            dyslexiaFont: false,
            letterSpacing: 0,
            lineHeight: 0,
            fontWeight: 0,
            contrast: 'normal',
            saturation: 'normal',
            readingGuide: false,
            stopAnimations: false,
            largeCursor: false,
        };
        this.saveState();
    }

    bindEvents() {
        // Toggle Panel
        const toggleBtn = document.getElementById('btn-accessibility-toggle');
        const panel = document.getElementById('accessibility-panel');
        const closeBtn = document.getElementById('btn-acc-close');

        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', () => {
                panel.classList.toggle('-translate-x-full');
            });
        }

        if (closeBtn && panel) {
            closeBtn.addEventListener('click', () => {
                panel.classList.add('-translate-x-full');
            });
        }

        // Reset
        const resetBtn = document.getElementById('btn-acc-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.reset());
        }

        // Font Size
        document.getElementById('btn-acc-font-minus')?.addEventListener('click', () => {
            if (this.state.fontSize > 50) {
                this.state.fontSize -= 10;
                this.saveState();
            }
        });
        document.getElementById('btn-acc-font-plus')?.addEventListener('click', () => {
            if (this.state.fontSize < 200) {
                this.state.fontSize += 10;
                this.saveState();
            }
        });

        // Toggles
        document.getElementById('btn-sorot-judul')?.addEventListener('click', () => {
            this.state.highlightTitles = !this.state.highlightTitles;
            this.saveState();
        });
        document.getElementById('btn-sorot-tautan')?.addEventListener('click', () => {
            this.state.highlightLinks = !this.state.highlightLinks;
            this.saveState();
        });
        document.getElementById('btn-font-disleksia')?.addEventListener('click', () => {
            this.state.dyslexiaFont = !this.state.dyslexiaFont;
            this.saveState();
        });

        // Cyclers (0 -> 1 -> 2 -> 3 -> 0)
        document.getElementById('btn-jarak-huruf')?.addEventListener('click', () => {
            this.state.letterSpacing = (this.state.letterSpacing + 1) % 4;
            this.saveState();
        });
        document.getElementById('btn-tinggi-baris')?.addEventListener('click', () => {
            this.state.lineHeight = (this.state.lineHeight + 1) % 4;
            this.saveState();
        });
        document.getElementById('btn-ketebalan-font')?.addEventListener('click', () => {
            this.state.fontWeight = (this.state.fontWeight + 1) % 4;
            this.saveState();
        });

        // Radio groups (Contrast)
        const setContrast = (val) => {
            this.state.contrast = this.state.contrast === val ? 'normal' : val;
            this.saveState();
        };
        document.getElementById('btn-kontras-gelap')?.addEventListener('click', () => setContrast('dark'));
        document.getElementById('btn-kontras-terang')?.addEventListener('click', () => setContrast('light'));
        document.getElementById('btn-kontras-tinggi')?.addEventListener('click', () => setContrast('high'));

        // Radio groups (Saturation)
        const setSaturation = (val) => {
            this.state.saturation = this.state.saturation === val ? 'normal' : val;
            this.saveState();
        };
        document.getElementById('btn-saturasi-tinggi')?.addEventListener('click', () => setSaturation('high'));
        document.getElementById('btn-saturasi-rendah')?.addEventListener('click', () => setSaturation('low'));
        document.getElementById('btn-monokrom')?.addEventListener('click', () => setSaturation('monochrome'));

        // Tools
        document.getElementById('btn-panduan-membaca')?.addEventListener('click', () => {
            this.state.readingGuide = !this.state.readingGuide;
            this.saveState();
        });
        document.getElementById('btn-hentikan-animasi')?.addEventListener('click', () => {
            this.state.stopAnimations = !this.state.stopAnimations;
            this.saveState();
        });
        document.getElementById('btn-kursor-besar')?.addEventListener('click', () => {
            this.state.largeCursor = !this.state.largeCursor;
            this.saveState();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.accessibilityControl = new AccessibilityControl();
});
