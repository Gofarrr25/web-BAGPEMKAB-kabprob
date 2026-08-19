<div id="password-strength-wrapper" class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between mb-1.5">
        <span class="text-xs font-bold text-gray-700">Kekuatan Password:</span>
        <span id="strength-label" class="text-xs font-bold text-gray-400">Belum Diisi</span>
    </div>
    
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-3 overflow-hidden">
        <div id="strength-meter" class="h-2 rounded-full transition-all duration-300 w-0 bg-gray-300"></div>
    </div>
    
    <!-- Checklist Indikator Real-time -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px]">
        <div id="req-length" class="flex items-center text-gray-400 font-semibold transition-colors">
            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i> Minimal 8 Karakter
        </div>
        <div id="req-upper" class="flex items-center text-gray-400 font-semibold transition-colors">
            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i> Minimal 1 Huruf Besar (A-Z)
        </div>
        <div id="req-lower" class="flex items-center text-gray-400 font-semibold transition-colors">
            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i> Minimal 1 Huruf Kecil (a-z)
        </div>
        <div id="req-number" class="flex items-center text-gray-400 font-semibold transition-colors">
            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i> Minimal 1 Angka (0-9)
        </div>
        <div id="req-special" class="flex items-center text-gray-400 font-semibold transition-colors sm:col-span-2">
            <i class="fas fa-times-circle mr-1.5 text-xs text-gray-400"></i> Minimal 1 Karakter Khusus (!@#$%^&*)
        </div>
    </div>
</div>

<script>
function attachPasswordStrengthMeter(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('input', function() {
        const val = input.value;
        
        const hasLength = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSpecial = /[^A-Za-z0-9]/.test(val);

        updateReq('req-length', hasLength);
        updateReq('req-upper', hasUpper);
        updateReq('req-lower', hasLower);
        updateReq('req-number', hasNumber);
        updateReq('req-special', hasSpecial);

        let score = 0;
        if (hasLength) score++;
        if (hasUpper) score++;
        if (hasLower) score++;
        if (hasNumber) score++;
        if (hasSpecial) score++;

        const meter = document.getElementById('strength-meter');
        const label = document.getElementById('strength-label');

        if (!meter || !label) return;

        if (val.length === 0) {
            meter.style.width = '0%';
            meter.className = 'h-2 rounded-full transition-all duration-300 bg-gray-300';
            label.textContent = 'Belum Diisi';
            label.className = 'text-xs font-bold text-gray-400';
            return;
        }

        switch (score) {
            case 1:
            case 2:
                meter.style.width = '25%';
                meter.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                label.textContent = 'Sangat Lemah';
                label.className = 'text-xs font-bold text-red-600';
                break;
            case 3:
                meter.style.width = '50%';
                meter.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
                label.textContent = 'Sedang';
                label.className = 'text-xs font-bold text-orange-600';
                break;
            case 4:
                meter.style.width = '75%';
                meter.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                label.textContent = 'Kuat';
                label.className = 'text-xs font-bold text-yellow-600';
                break;
            case 5:
                meter.style.width = '100%';
                meter.className = 'h-2 rounded-full transition-all duration-300 bg-green-600';
                label.textContent = 'Sangat Kuat';
                label.className = 'text-xs font-bold text-green-600';
                break;
        }
    });
}

function updateReq(elementId, isValid) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const icon = el.querySelector('i');
    
    if (isValid) {
        el.className = 'flex items-center text-green-600 font-bold transition-colors';
        if (icon) icon.className = 'fas fa-check-circle mr-1.5 text-xs text-green-600';
    } else {
        el.className = 'flex items-center text-gray-400 font-semibold transition-colors';
        if (icon) icon.className = 'fas fa-times-circle mr-1.5 text-xs text-gray-400';
    }
}
</script>
