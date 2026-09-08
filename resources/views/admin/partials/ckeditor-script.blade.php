<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>
<style>
    /* Mengatur jarak antar paragraf (Enter) default mirip MS Word */
    .ck-editor__editable p {
        margin-bottom: 8px;
        margin-top: 0;
    }

    /* Custom UI untuk Dropdown Line Spacing ala MS Word */
    .ck-custom-spacing-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid var(--ck-color-base-border);
        border-radius: var(--ck-border-radius);
        box-shadow: var(--ck-drop-shadow);
        width: 250px;
        z-index: 1000;
        padding: 4px 0;
    }
    .ck-custom-spacing-menu .ck-spacing-item {
        display: block;
        width: 100%;
        text-align: left;
        padding: 6px 36px;
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 13px;
        color: #333;
        font-family: inherit;
        position: relative;
        transition: background 0.1s;
    }
    .ck-custom-spacing-menu .ck-spacing-item:hover {
        background: var(--ck-color-button-default-hover-background);
    }
    .ck-custom-spacing-menu .ck-spacing-item.active-item::before {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        left: 12px;
        color: #475569;
    }
    
    .ck.ck-toolbar {
        flex-wrap: wrap !important;
    }
    
    /* Memaksa dropdown Font Size agar ukurannya tetap ringkas (tidak menjadi raksasa atau terlalu kecil) */
    .ck-dropdown__panel .ck-list__item .ck-button__label[style*="font-size"],
    .ck-dropdown__panel .ck-list__item .ck-button__label[style*="font-family"] {
        font-size: 13px !important;
        line-height: 1.5 !important;
    }
</style>
<script>
function createCkEditor(selector, customPlaceholder) {
    const targetElement = document.querySelector(selector);
    if (!targetElement) return;


    function CustomSpacingPlugin(editor) {
        const elements = ['paragraph', 'heading1', 'heading2', 'heading3', 'heading4', 'heading5', 'heading6', 'listItem'];
        
        elements.forEach(el => {
            if (editor.model.schema.isRegistered(el)) {
                editor.model.schema.extend(el, { allowAttributes: ['lineHeight', 'marginTop', 'marginBottom'] });
            }
        });

        const attributes = [
            { model: 'lineHeight', view: 'line-height' },
            { model: 'marginTop', view: 'margin-top' },
            { model: 'marginBottom', view: 'margin-bottom' }
        ];

        editor.conversion.for('downcast').add(dispatcher => {
            attributes.forEach(attr => {
                elements.forEach(el => {
                    dispatcher.on(`attribute:${attr.model}:${el}`, (evt, data, conversionApi) => {
                        if (!conversionApi.consumable.consume(data.item, evt.name)) return;
                        
                        const viewElement = conversionApi.mapper.toViewElement(data.item);
                        if (!viewElement) return;

                        if (data.attributeNewValue) {
                            conversionApi.writer.setStyle(attr.view, data.attributeNewValue, viewElement);
                        } else {
                            conversionApi.writer.removeStyle(attr.view, viewElement);
                        }
                    });
                });
            });
        });

        attributes.forEach(attr => {
            editor.conversion.for('upcast').attributeToAttribute({
                view: { styles: { [attr.view]: /.*/ } },
                model: {
                    key: attr.model,
                    value: viewElement => viewElement.getStyle(attr.view)
                }
            });
        });
        
        // Handle legacy classes
        const legacyClasses = {
            'ck-line-spacing-10': '1.0',
            'ck-line-spacing-115': '1.15',
            'ck-line-spacing-15': '1.5',
            'ck-line-spacing-20': '2.0'
        };
        
        for (const [cls, val] of Object.entries(legacyClasses)) {
            editor.conversion.for('upcast').attributeToAttribute({
                view: { classes: cls },
                model: { key: 'lineHeight', value: val }
            });
        }
    }

    return CKEDITOR.ClassicEditor.create(targetElement, {
        extraPlugins: [ CustomSpacingPlugin ],
        toolbar: {
            items: [
                'sourceEditing', 'fullScreen', '|',
                'undo', 'redo', '|',
                'findAndReplace', 'selectAll', '|',
                'heading', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'code', 'removeFormat', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'outdent', 'indent', '|',
                'link', 'uploadImage', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                'specialCharacters', 'horizontalLine', 'pageBreak'
            ],
            shouldNotGroupWhenFull: true
        },
        placeholder: customPlaceholder || 'Ketikkan konten artikel / dokumen halaman di sini...',
        alignment: {
            options: ['left', 'center', 'right', 'justify']
        },
        list: { properties: { styles: true, startIndex: true, reversed: true } },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraf Biasa' },
                { model: 'heading1', view: 'h1', title: 'Heading 1 (Judul Utama)' },
                { model: 'heading2', view: 'h2', title: 'Heading 2 (Sub Judul)' },
                { model: 'heading3', view: 'h3', title: 'Heading 3 (Judul Bagian)' },
                { model: 'heading4', view: 'h4', title: 'Heading 4 (Sub Bagian)' },
                { model: 'heading5', view: 'h5', title: 'Heading 5 (Judul Kecil)' },
                { model: 'heading6', view: 'h6', title: 'Heading 6 (Keterangan)' }
            ]
        },
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Calibri, sans-serif',
                'Georgia, serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        fontSize: {
            options: [ 8, 9, 10, 11, 12, 14, 'default', 16, 18, 20, 24, 28, 32 ],
            supportAllValues: true
        },
        htmlSupport: { 
            allow: [
                { 
                    name: /^(p|h[1-6]|ul|ol|li|table|tbody|thead|tfoot|tr|th|td|blockquote|div|span|strong|em|i|b|u|s|a|img|figure|figcaption)$/, 
                    attributes: true, 
                    classes: true, 
                    styles: true 
                }
            ] 
        },
        htmlEmbed: { showPreviews: true },
        link: { addTargetToExternalLinks: true, defaultProtocol: 'https://' },
        image: {
            toolbar: [
                'imageTextAlternative',
                'toggleImageCaption',
                '|',
                'imageStyle:inline',
                'imageStyle:alignLeft',
                'imageStyle:alignCenter',
                'imageStyle:alignRight',
                '|',
                'resizeImage'
            ],
            styles: [
                'full',
                'alignLeft',
                'alignCenter',
                'alignRight'
            ],
            resizeOptions: [
                { name: 'resizeImage:original', label: 'Original', value: null },
                { name: 'resizeImage:50', label: '50%', value: '50' },
                { name: 'resizeImage:75', label: '75%', value: '75' }
            ],
            insert: { type: 'auto' }
        },
        table: {
            contentToolbar: [
                'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'
            ]
        },
        mediaEmbed: { previewsInData: true },
        simpleUpload: {
            uploadUrl: "{{ route('admin.ckeditor.upload') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        },
        removePlugins: [
            'CKBox', 'CKFinder', 'EasyImage', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
            'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template', 'DocumentOutline',
            'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced', 'Style'
        ]
    }).then(editor => {
        // --- INJEKSI CUSTOM UI DROPDOWN MICROSOFT WORD SPACING ---
        const toolbar = editor.ui.view.toolbar.element;
        const dropdownHtml = `
            <div class="ck ck-dropdown ck-custom-spacing-wrapper" style="position: relative;">
                <button type="button" class="ck ck-button ck-off ck-custom-spacing-btn" title="Line and Paragraph Spacing" tabindex="-1">
                    <span class="ck ck-icon" style="display: flex; align-items: center; justify-content: center; gap: 3px; color: var(--ck-color-button-default-icon);">
                        <i class="fas fa-arrows-alt-v" style="font-size: 11px;"></i>
                        <i class="fas fa-bars" style="font-size: 13px;"></i>
                    </span>
                    <span class="ck-spacing-btn-label" style="font-size: 13px; font-weight: 600; color: #334155; margin: 0 4px; min-width: 85px; text-align: left;">Spacing: 1.15</span>
                    <span class="ck ck-dropdown__arrow">
                        <svg viewBox="0 0 10 10"><path d="M.941 4.523a.75.75 0 1 1 1.06-1.06l3.006 3.005 3.005-3.005a.75.75 0 1 1 1.06 1.06l-3.535 3.536a.75.75 0 0 1-1.06 0L.941 4.523z"></path></svg>
                    </span>
                </button>
                <div class="ck-custom-spacing-menu">
                    <button type="button" class="ck-spacing-item" data-action="line" data-val="1.0">1.0</button>
                    <button type="button" class="ck-spacing-item" data-action="line" data-val="1.15">1.15</button>
                    <button type="button" class="ck-spacing-item" data-action="line" data-val="1.5">1.5</button>
                    <button type="button" class="ck-spacing-item" data-action="line" data-val="2.0">2.0</button>
                    <div style="height: 1px; background: var(--ck-color-base-border); margin: 4px 0;"></div>
                    <button type="button" class="ck-spacing-item" id="btn-space-before" data-action="space-before">Add Space Before Paragraph</button>
                    <button type="button" class="ck-spacing-item" id="btn-space-after" data-action="space-after">Remove Space After Paragraph</button>
                </div>
            </div>
        `;

        const alignBtnGroup = toolbar.querySelector('.ck-dropdown[data-cke-tooltip-text="Text alignment"]') || toolbar.querySelector('.ck-toolbar__items');
        if (alignBtnGroup && alignBtnGroup.classList.contains('ck-dropdown')) {
            alignBtnGroup.insertAdjacentHTML('afterend', dropdownHtml);
        } else {
            toolbar.insertAdjacentHTML('beforeend', dropdownHtml);
        }

        // --- INJEKSI CUSTOM UI BUTTON FILE ATTACHMENT ---
        const btnAttachHtml = `
            <button type="button" class="ck ck-button ck-off ck-custom-attach-btn" title="Sisipkan Lampiran (PDF/DOCX/Excel/ZIP)" tabindex="-1" style="margin-left: 5px;">
                <span class="ck ck-icon" style="display: flex; align-items: center; justify-content: center; gap: 3px; color: var(--ck-color-button-default-icon);">
                    <svg viewBox="0 0 20 20" style="width: 14px; height: 14px; fill: currentColor;" xmlns="http://www.w3.org/2000/svg"><path d="M4 1.5A1.5 1.5 0 0 1 5.5 0h5.879a1.5 1.5 0 0 1 1.06.44l4.122 4.12A1.5 1.5 0 0 1 17 5.622V18.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 18.5v-17zM5.5 1.5v17h11V6H11V1.5H5.5zM12 1.5V5h3.5L12 1.5z"/></svg>
                    <span style="font-size: 11px; font-weight: bold; margin-left:2px; font-family:sans-serif;">Attach</span>
                </span>
            </button>
        `;
        toolbar.insertAdjacentHTML('beforeend', btnAttachHtml);
        const btnAttach = toolbar.querySelector('.ck-custom-attach-btn');
        btnAttach.addEventListener('click', (e) => {
            e.preventDefault();
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z';
            input.onchange = async (ev) => {
                const file = ev.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('upload', file);
                try {
                    const response = await fetch("{{ route('admin.ckeditor.upload') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.uploaded) {
                        const ext = file.name.split('.').pop().toLowerCase();
                        const html = `<a href="${data.url}" class="document-attachment" data-ext="${ext}" target="_blank">${file.name}</a>`;
                        editor.model.change(writer => {
                            const viewFragment = editor.data.processor.toView(html);
                            const modelFragment = editor.data.toModel(viewFragment);
                            editor.model.insertContent(modelFragment);
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'Upload Gagal', text: data.error?.message || 'Gagal mengunggah file.', confirmButtonColor: '#3085d6'});
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire({icon: 'error', title: 'Upload Gagal', text: 'Terjadi kesalahan saat mengunggah file.', confirmButtonColor: '#3085d6'});
                }
            };
            input.click();
        });


        const wrapper = toolbar.querySelector('.ck-custom-spacing-wrapper');
        const btn = wrapper.querySelector('.ck-custom-spacing-btn');
        const menu = wrapper.querySelector('.ck-custom-spacing-menu');
        const labelBtn = wrapper.querySelector('.ck-spacing-btn-label');
        
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isVisible = menu.style.display === 'block';
            document.body.click(); 
            
            if (!isVisible) {
                menu.style.display = 'block';
                btn.classList.add('ck-on');
                updateMenuState();
            }
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                menu.style.display = 'none';
                btn.classList.remove('ck-on');
            }
        });

        function updateMenuState() {
            const blocks = Array.from(editor.model.document.selection.getSelectedBlocks());
            if (!blocks.length) return;
            
            const block = blocks[0];
            
            let currentLineHeight = block.getAttribute('lineHeight') || '1.15';
            if (currentLineHeight === '1' || currentLineHeight === '1.0' || currentLineHeight === '1.00') {
                currentLineHeight = '1.0';
            }
            if (currentLineHeight === '2' || currentLineHeight === '2.0' || currentLineHeight === '2.00') {
                currentLineHeight = '2.0';
            }
            
            const currentMarginTop = block.getAttribute('marginTop') || '0px';
            const currentMarginBottom = block.getAttribute('marginBottom') || '8px';

            labelBtn.innerText = 'Spacing: ' + currentLineHeight;

            menu.querySelectorAll('.ck-spacing-item').forEach(el => el.classList.remove('active-item'));
            
            const activeBtn = menu.querySelector(`[data-val="${currentLineHeight}"]`);
            if (activeBtn) activeBtn.classList.add('active-item');

            const btnBefore = menu.querySelector('#btn-space-before');
            if (currentMarginTop !== '0px' && currentMarginTop !== '') {
                btnBefore.innerText = 'Remove Space Before Paragraph';
                btnBefore.dataset.state = 'remove';
            } else {
                btnBefore.innerText = 'Add Space Before Paragraph';
                btnBefore.dataset.state = 'add';
            }

            const btnAfter = menu.querySelector('#btn-space-after');
            // Jika marginBottom diset explicit ke angka selain 8px / 0px (misal 12pt)
            if (currentMarginBottom !== '8px' && currentMarginBottom !== '0px' && currentMarginBottom !== '') { 
                btnAfter.innerText = 'Remove Space After Paragraph';
                btnAfter.dataset.state = 'remove';
            } else {
                btnAfter.innerText = 'Add Space After Paragraph';
                btnAfter.dataset.state = 'add';
            }
        }

        editor.model.document.selection.on('change:range', () => {
            updateMenuState();
        });

        menu.querySelectorAll('.ck-spacing-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const action = item.dataset.action;
                const state = item.dataset.state;
                
                editor.model.change(writer => {
                    const blocks = Array.from(editor.model.document.selection.getSelectedBlocks());
                    
                    blocks.forEach(block => {
                        // Hanya terapkan pada block yang mendukung inline styling
                        if (/^(paragraph|heading[1-6]|listItem)$/.test(block.name)) {
                            if (action === 'line') {
                                writer.setAttribute('lineHeight', item.dataset.val, block);
                                if (item.dataset.val === '1.0') {
                                    // Sesuai request: 1.0 tanpa margin paragraf
                                    writer.setAttribute('marginBottom', '0px', block);
                                }
                            } else if (action === 'space-before') {
                                writer.setAttribute('marginTop', state === 'add' ? '12pt' : '0px', block);
                            } else if (action === 'space-after') {
                                writer.setAttribute('marginBottom', state === 'add' ? '12pt' : '0px', block);
                            }
                        }
                    });
                });
                
                menu.style.display = 'none';
                btn.classList.remove('ck-on');
                editor.editing.view.focus();
                updateMenuState();
            });
        });
        
        // --- INJEKSI AUTO-PARAGRAPH SETELAH GAMBAR ---
        editor.model.document.on('change:data', () => {
            const changes = editor.model.document.differ.getChanges();
            let lastInsertedImage = null;

            for (const change of changes) {
                if (change.type === 'insert' && (change.name === 'imageBlock' || change.name === 'imageInline' || change.name === 'image')) {
                    const node = change.position.nodeAfter;
                    if (node && (node.name === 'imageBlock' || node.name === 'imageInline' || node.name === 'image')) {
                        lastInsertedImage = node;
                    }
                }
            }

            if (lastInsertedImage) {
                editor.model.enqueueChange(writer => {
                    const nextNode = lastInsertedImage.nextSibling;
                    let paragraphToSelect = null;

                    if (!nextNode || nextNode.name !== 'paragraph') {
                        paragraphToSelect = writer.createElement('paragraph');
                        writer.insert(paragraphToSelect, lastInsertedImage, 'after');
                    } else {
                        paragraphToSelect = nextNode;
                    }

                    if (paragraphToSelect) {
                        writer.setSelection(writer.createPositionAt(paragraphToSelect, 0));
                    }
                });
                editor.editing.view.focus();
            }
        });

        setTimeout(updateMenuState, 500);

    }).catch(error => {
        console.error('CKEditor Init Error:', error);
    });
}
</script>
