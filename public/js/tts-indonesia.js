/**
 * Text-to-Speech (TTS) Bahasa Indonesia
 * - Otomatis membacakan sambutan saat halaman pertama kali dibuka (setelah interaksi pertama user).
 * - Otomatis membacakan teks yang di-blok/seleksi oleh pengguna.
 * - Menggunakan Web Speech API bawaan browser.
 * - Tidak menampilkan UI apapun.
 */
(function () {
    'use strict';

    // Cek apakah browser mendukung Speech Synthesis
    if (!('speechSynthesis' in window)) {
        console.warn('[TTS] Browser tidak mendukung Web Speech API.');
        return;
    }

    const synth = window.speechSynthesis;
    const WELCOME_MESSAGE = 'Selamat datang di website resmi Bagian Pemerintahan Kabupaten Probolinggo.';
    const WELCOME_KEY = 'tts_welcome_played';
    const MAX_TEXT_LENGTH = 1000; // Batas panjang teks yang dibacakan

    let indonesianVoice = null;
    let voicesLoaded = false;
    let userHasInteracted = false;

    // Pantau interaksi user untuk mengizinkan TTS (mencegah popup prompt dari browser)
    function unlockTTS() {
        userHasInteracted = true;
        document.removeEventListener('click', unlockTTS);
        document.removeEventListener('touchstart', unlockTTS);
        document.removeEventListener('keydown', unlockTTS);
    }
    document.addEventListener('click', unlockTTS, { once: true });
    document.addEventListener('touchstart', unlockTTS, { once: true });
    document.addEventListener('keydown', unlockTTS, { once: true });

    /**
     * Mencari voice Bahasa Indonesia yang tersedia di browser.
     */
    function loadVoices() {
        const voices = synth.getVoices();
        if (voices.length === 0) return;

        voicesLoaded = true;

        // Prioritas: cari voice id/id-ID, lalu ms/ms-MY sebagai fallback Melayu
        indonesianVoice = voices.find(v => v.lang === 'id-ID') ||
                          voices.find(v => v.lang.startsWith('id')) ||
                          voices.find(v => v.lang === 'ms-MY') ||
                          voices.find(v => v.lang.startsWith('ms')) ||
                          null;

        if (indonesianVoice) {
            console.log('[TTS] Voice ditemukan:', indonesianVoice.name, indonesianVoice.lang);
        } else {
            console.log('[TTS] Tidak ada voice Indonesia/Melayu, menggunakan default.');
        }
    }

    /**
     * Membacakan teks menggunakan TTS.
     */
    function speak(text) {
        // Cegah pembacaan jika user belum berinteraksi agar browser tidak memunculkan popup blokir
        if (!userHasInteracted) {
            console.log('[TTS] Menunggu interaksi user sebelum dapat memutar suara.');
            return;
        }

        if (!text || text.trim().length === 0) return;

        // Hentikan speech yang sedang berjalan
        synth.cancel();

        // Batasi panjang teks
        let cleanText = text.trim();
        if (cleanText.length > MAX_TEXT_LENGTH) {
            cleanText = cleanText.substring(0, MAX_TEXT_LENGTH);
        }

        const utterance = new SpeechSynthesisUtterance(cleanText);
        utterance.lang = 'id-ID';
        utterance.rate = 1.0;
        utterance.pitch = 1.0;
        utterance.volume = 1.0;

        if (indonesianVoice) {
            utterance.voice = indonesianVoice;
        }

        synth.speak(utterance);
    }

    /**
     * Membacakan pesan sambutan (hanya sekali per session).
     */
    function playWelcome() {
        // Cek apakah sudah pernah diputar di session ini
        if (sessionStorage.getItem(WELCOME_KEY)) return;

        sessionStorage.setItem(WELCOME_KEY, 'true');
        speak(WELCOME_MESSAGE);
    }

    /**
     * Handler untuk membacakan teks yang di-seleksi (dengan debounce).
     */
    let selectionTimeout = null;

    function handleTextSelection() {
        clearTimeout(selectionTimeout);

        selectionTimeout = setTimeout(function () {
            const selection = window.getSelection();
            const selectedText = selection ? selection.toString().trim() : '';

            if (selectedText.length > 2) {
                speak(selectedText);
            }
        }, 600); // Tunggu 600ms setelah selesai seleksi
    }

    // =============================================
    // Inisialisasi
    // =============================================

    // Load voices
    loadVoices();
    if (synth.onvoiceschanged !== undefined) {
        synth.onvoiceschanged = loadVoices;
    }

    // Bacakan sambutan setelah interaksi pertama user (kebijakan autoplay browser)
    let welcomePlayed = false;

    function onFirstInteraction() {
        if (welcomePlayed) return;
        welcomePlayed = true;

        // Pastikan voices sudah loaded
        if (!voicesLoaded) {
            loadVoices();
        }

        playWelcome();

        // Hapus listener setelah dipakai
        document.removeEventListener('click', onFirstInteraction);
        document.removeEventListener('touchstart', onFirstInteraction);
        document.removeEventListener('keydown', onFirstInteraction);
    }

    // Jika welcome belum pernah diputar di session ini, pasang listener
    if (!sessionStorage.getItem(WELCOME_KEY)) {
        document.addEventListener('click', onFirstInteraction, { once: false });
        document.addEventListener('touchstart', onFirstInteraction, { once: false });
        document.addEventListener('keydown', onFirstInteraction, { once: false });
    }

    // Pasang listener untuk seleksi teks
    document.addEventListener('mouseup', handleTextSelection);
    document.addEventListener('touchend', handleTextSelection);

    // =============================================
    // TTS untuk Menu & Submenu Navigasi
    // =============================================
    let hoverTimeout = null;
    
    function setupMenuTTS() {
        // Ambil elemen menu di header (link dan button dropdown)
        const menuItems = document.querySelectorAll('header nav a, header nav button, .dropdown-menu a');
        
        menuItems.forEach(item => {
            // Bacakan teks menu saat diklik (tanpa tambahan kata apapun)
            item.addEventListener('click', function() {
                clearTimeout(hoverTimeout);
                const text = (this.textContent || this.innerText).trim();
                if (text.length > 0) {
                    speak(text);
                }
            });

            item.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                // Delay 400ms agar tidak cerewet kalau cursor hanya numpang lewat
                hoverTimeout = setTimeout(() => {
                    // Bersihkan spasi kosong dan ambil teks murninya
                    const text = (this.textContent || this.innerText).trim();
                    if (text.length > 0) {
                        speak(text);
                    }
                }, 400); 
            });
            
            item.addEventListener('focus', function() {
                const text = (this.textContent || this.innerText).trim();
                if (text.length > 0) {
                    speak(text);
                }
            });
            
            item.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
            });
        });
    }

    // Eksekusi fungsi setupMenuTTS setelah DOM load atau langsung (karena dipanggil di akhir body)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMenuTTS);
    } else {
        setupMenuTTS();
    }

    // Bersihkan speech saat user meninggalkan halaman (gunakan pagehide sebagai ganti beforeunload/unload)
    window.addEventListener('pagehide', function () {
        synth.cancel();
    });

    console.log('[TTS] Text-to-Speech Bahasa Indonesia aktif (termasuk fitur hover Menu).');
})();
