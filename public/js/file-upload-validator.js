const CustomUploadAlert = {
    showError: function(elementOrId, message) {
        let container = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
        if (!container) return;

        this.removeError(container);

        const alertDiv = document.createElement('div');
        alertDiv.className = 'custom-file-error-alert mt-3 p-3.5 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 flex gap-3 items-start shadow-sm w-full max-w-full overflow-hidden';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle mt-0.5 text-red-500 text-base flex-shrink-0"></i>
            <div class="flex-1 min-w-0">
                <strong class="block text-red-700 mb-1 text-sm">File Ditolak</strong>
                <span class="block break-words leading-relaxed text-xs sm:text-sm text-red-600">${message}</span>
            </div>
        `;
        
        if (container.tagName === 'INPUT') {
            let parent = container.parentElement;
            if (parent.classList.contains('flex') || parent.tagName === 'LABEL') {
                parent = parent.parentElement;
            }
            if(parent) {
                parent.appendChild(alertDiv);
            } else {
                container.insertAdjacentElement('afterend', alertDiv);
            }
        } else {
            // Container biasa (seperti dropzone)
            container.appendChild(alertDiv);
        }
        
        // Auto hilangkan setelah 7 detik
        setTimeout(() => {
            if (alertDiv.parentNode) alertDiv.remove();
        }, 7000);
    },
    
    removeError: function(elementOrId) {
        let container = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
        if (!container) return;
        
        let parent = container;
        if (container.tagName === 'INPUT') {
            parent = container.parentElement;
            if (parent && (parent.classList.contains('flex') || parent.tagName === 'LABEL')) {
                parent = parent.parentElement;
            }
        }
        
        if(parent) {
            const alerts = parent.querySelectorAll('.custom-file-error-alert');
            alerts.forEach(a => a.remove());
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    function getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function validateFile(input) {
        if (!input.files || input.files.length === 0) {
            CustomUploadAlert.removeError(input);
            return true;
        }

        let hasError = false;
        let errorMessage = '';

        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            
            // 1. Ekstensi Terlarang Secara Global
            const ext = getFileExtension(file.name);
            const forbiddenExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'js', 'html', 'htm', 'json', 'exe', 'bat', 'cmd', 'sh', 'svg'];
            
            // SVG kadang diperbolehkan, tapi user bilang tolak "svg jika memang tidak diperbolehkan". 
            // Kita tolak saja jika tidak ada di accept.
            
            if (forbiddenExts.includes(ext)) {
                // cek apakah memang diizinkan secara eksplisit di accept
                const acceptAttr = input.getAttribute('accept');
                if (!(acceptAttr && (acceptAttr.includes('.'+ext) || acceptAttr.includes('image/svg+xml')))) {
                    hasError = true;
                    errorMessage = 'Jenis file skrip/sistem (.'+ext.toUpperCase()+') tidak diperbolehkan demi keamanan.';
                    break;
                }
            }

            // 2. Validasi Accept Attribute
            const accept = input.getAttribute('accept');
            if (accept) {
                const acceptArray = accept.split(',').map(item => item.trim().toLowerCase());
                let isAccepted = false;

                for (let j = 0; j < acceptArray.length; j++) {
                    const rule = acceptArray[j];
                    if (rule.startsWith('.')) {
                        if ('.' + ext === rule) {
                            isAccepted = true; break;
                        }
                    } else if (rule.endsWith('/*')) {
                        const typeGroup = rule.split('/')[0];
                        if (file.type.startsWith(typeGroup + '/')) {
                            isAccepted = true; break;
                        }
                    } else {
                        if (file.type === rule || (file.type === '' && ext === rule.split('/')[1])) { 
                            isAccepted = true; break;
                        }
                    }
                }
                
                if (!isAccepted) {
                    hasError = true;
                    let friendlyFormat = acceptArray.filter(r => r.startsWith('.')).map(r => r.toUpperCase().replace('.','')).join(', ');
                    if(!friendlyFormat) {
                        // Fallback to mime types, but ensure there is a space after comma for wrapping
                        friendlyFormat = acceptArray.join(', ');
                    }
                    errorMessage = 'Format file tidak sesuai.<br>Hanya <strong>' + friendlyFormat + '</strong> yang diperbolehkan.';
                    break;
                }
            }

            // 3. Validasi Size
            const maxSizeStr = input.getAttribute('data-max-size');
            let maxSize = 50 * 1024 * 1024; // Default 50MB
            if (maxSizeStr) {
                maxSize = parseInt(maxSizeStr) * 1024 * 1024;
            }
            if (file.size > maxSize) {
                hasError = true;
                errorMessage = 'Ukuran file terlalu besar.<br>Maksimal file adalah ' + (maxSize / 1024 / 1024) + 'MB.';
                break;
            }
        }

        if (hasError) {
            input.value = ''; // Reset input
            
            // Cegah event 'change' lebih lanjut agar script lain tidak menampilkan preview
            // (Note: This might not stop other listeners on the same element if they are attached earlier, 
            // but it stops default form submission visually).
            
            // Dispatch a custom event in case other scripts want to know it failed
            input.dispatchEvent(new CustomEvent('file-validation-failed'));
            
            CustomUploadAlert.showError(input, errorMessage);
            return false;
        } else {
            CustomUploadAlert.removeError(input);
            return true;
        }
    }

    // Attach to body with useCapture to catch it early!
    document.addEventListener('change', function(e) {
        if (e.target && e.target.tagName === 'INPUT' && e.target.type === 'file') {
            if(!validateFile(e.target)) {
                e.stopImmediatePropagation();
            }
        }
    }, true);
});
