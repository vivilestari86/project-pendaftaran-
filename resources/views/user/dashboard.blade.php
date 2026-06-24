@php
    $uploadedCount = collect(array_keys($documents))
        ->filter(fn (string $key): bool => $uploadedDocuments->has($key))
        ->count();
    $isDocumentComplete = $uploadedCount === count($documents);

    $statuses = [
        ['title' => 'Registrasi', 'description' => 'Selesai pada 24 Okt 2024', 'state' => 'done'],
        [
            'title' => 'Upload Dokumen',
            'description' => $isDocumentComplete ? 'Semua dokumen sudah diupload' : "{$uploadedCount}/" . count($documents) . ' dokumen diupload',
            'state' => $isDocumentComplete ? 'done' : 'current',
        ],
        ['title' => 'Verifikasi Admin', 'description' => 'Menunggu validasi dokumen', 'state' => $isDocumentComplete ? 'current' : 'pending'],
        ['title' => 'Uji Kompetensi', 'description' => 'Tahap uji kompetensi akhir', 'state' => 'pending'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard User - Polindra Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
</head>
<body>
    <div class="portal-shell">
        <aside class="portal-sidebar">
            <a class="brand" href="{{ route('user.dashboard') }}" aria-label="Polindra Portal">
                <img class="brand-logo" src="{{ asset('images/logo_polindra.jpg') }}" alt="Logo Polindra">
                <span>Polindra Portal</span>
            </a>

            <nav class="sidebar-nav" aria-label="Navigasi utama">
                <a href="{{ route('user.dashboard') }}" class="nav-link active">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z"/></svg>
                    Dashboard
                </a>
            </nav>

            <div class="sidebar-bottom">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h8v2H7v14h6v2H5V3Zm11.6 4.6L21 12l-4.4 4.4-1.4-1.4 2-2H10v-2h7.2l-2-2 1.4-1.4Z"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="portal-main">
            <header class="topbar">
                <h1>Halo, {{ auth()->user()->name }}! Mari lengkapi pendaftaranmu.</h1>
            </header>

            <div class="content-grid">
                <form class="documents-panel" aria-labelledby="required-documents-title">
                    @csrf

                    @if (session('success'))
                        <div class="upload-alert success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="upload-alert error">{{ $errors->first() }}</div>
                    @endif

                    <div class="panel-heading">
                        <div>
                            <div class="title-row">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 3 19 9v12H5V3h8Zm0 2H7v14h10V10h-4V5Zm-1 7 3 3h-2v3h-2v-3H9l3-3Z"/></svg>
                                <h2 id="required-documents-title">Dokumen Wajib</h2>
                            </div>
                            <p>Pastikan semua file terlihat jelas dan mudah dibaca.</p>
                        </div>
                        <span class="status-badge">Perlu Tindakan</span>
                    </div>

                    <div class="document-grid">
                        @foreach ($documents as $key => $document)
                            @php
                                $uploadedDocumentList = $uploadedDocuments->get($key, collect());
                                $uploadedDocument = $uploadedDocumentList->first();
                                $isMultiple = $document['multiple'] ?? false;
                            @endphp
                            <article class="document-card {{ ($document['wide'] ?? false) ? 'document-card-wide' : '' }}">
                                <div class="document-card-top">
                                    <span class="doc-icon {{ $document['tone'] }}">
                                        @switch($document['icon'])
                                            @case('id-card')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5V5Zm2 2v10h10V7H7Zm2 2h3v3H9V9Zm5 0h2v2h-2V9Zm-5 5h7v1.6H9V14Z"/></svg>
                                                @break
                                            @case('graduation')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 4 10 5-10 5L2 9l10-5Zm-5 8.2 5 2.5 5-2.5V16c-1.2 1.3-2.9 2-5 2s-3.8-.7-5-2v-3.8Z"/></svg>
                                                @break
                                            @case('identity')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4V6Zm2 2v8h12V8H6Zm2 2h4v4H8v-4Zm6 0h3v1.5h-3V10Zm0 3h3v1.5h-3V13Z"/></svg>
                                                @break
                                            @case('medical')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3h6v4h4v14H5V7h4V3Zm2 2v4H7v10h10V9h-4V5h-2Zm0 6h2v2h2v2h-2v2h-2v-2H9v-2h2v-2Z"/></svg>
                                                @break
                                            @case('camera')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5h6l1.5 2H20v12H4V7h3.5L9 5Zm3 5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm0 2a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/></svg>
                                                @break
                                            @default
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18H6V3Zm2 2v14h8V5H8Zm2 3h4v2h-4V8Zm0 4h4v2h-4v-2Z"/></svg>
                                        @endswitch
                                    </span>
                                    <span class="file-rule">
                                        {{ $document['format'] }} (Maks 5MB{{ $isMultiple ? ', 2 file' : '' }})
                                    </span>
                                </div>
                                <h3>{{ $document['title'] }}</h3>
                                <p>{{ $document['description'] }}</p>
                                <div class="uploaded-files" data-uploaded-files {{ $uploadedDocumentList->isEmpty() ? 'hidden' : '' }}>
                                    @foreach ($uploadedDocumentList as $savedDocument)
                                        <a href="{{ route('user.documents.show', $savedDocument) }}" class="uploaded-file" target="_blank" rel="noopener">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.5 15.2-3-3 1.4-1.4 1.6 1.6 5.6-5.6 1.4 1.4-7 7Z"/></svg>
                                            {{ $savedDocument->original_name }}
                                        </a>
                                    @endforeach
                                </div>
                                <div
                                    class="document-dropzone"
                                    data-upload-url="{{ route('user.documents.upload', $key) }}"
                                    data-document-title="{{ $document['title'] }}"
                                >
                                    <input
                                        class="dropzone-input"
                                        type="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        aria-label="Upload {{ $document['title'] }}"
                                    >
                                    <div class="dropzone-message">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v8h3l-4 4-4-4h3V5Zm-5 14h12v2H6v-2Z"/></svg>
                                        <span>{{ $uploadedDocument ? 'Ganti File' : 'Upload File' }}</span>
                                        <small>PDF, JPG, PNG</small>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="submit-row">
                        <button type="button" class="submit-button" data-complete-documents>
                            Kirim Dokumen
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 5 7 7-7 7-1.4-1.4 4.6-4.6H3v-2h14.2l-4.6-4.6L14 5Z"/></svg>
                        </button>
                    </div>
                </form>

                <aside class="status-panel" aria-labelledby="application-status-title">
                    <section class="status-card">
                        <div class="title-row side-title">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4V4Zm2 2v12h12V6H6Zm2 8h2v2H8v-2Zm3-4h2v6h-2v-6Zm3 2h2v4h-2v-4Z"/></svg>
                            <h2 id="application-status-title">Status Pendaftaran</h2>
                        </div>

                        <div class="timeline">
                            @foreach ($statuses as $status)
                                <div class="timeline-item {{ $status['state'] }}">
                                    <span class="timeline-dot" aria-hidden="true">
                                        @if ($status['state'] === 'done')
                                            <svg viewBox="0 0 24 24"><path d="m9.5 15.2-3-3 1.4-1.4 1.6 1.6 5.6-5.6 1.4 1.4-7 7Z"/></svg>
                                        @endif
                                    </span>
                                    <div>
                                        <h3>{{ $status['title'] }}</h3>
                                        <p>{{ $status['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="help-box">
                            <div class="help-heading">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4a8 8 0 0 0-8 8v5h4v-6H6.1A6 6 0 0 1 18 11h-2v6h4v-5a8 8 0 0 0-8-8Zm-2 14h5v2h-5v-2Z"/></svg>
                                <h3>Butuh Bantuan?</h3>
                            </div>
                            <p>Hubungi bagian rekrutmen untuk kendala teknis atau klarifikasi dokumen.</p>
                            <a href="#" class="guideline-link">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4h2v8l3-3 1.4 1.4L12 15.8l-5.4-5.4L8 9l3 3V4ZM5 18h14v2H5v-2Z"/></svg>
                                Unduh Panduan
                            </a>
                            <div class="help-email">Email: hrd@rsud-reg.com</div>
                        </div>
                    </section>
                </aside>
            </div>

            <footer class="portal-footer">
                <div class="footer-copy">Polindra Portal © 2026. All rights reserved.</div>
                <div class="footer-links">
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat Layanan</a>
                </div>
            </footer>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const completeButton = document.querySelector('[data-complete-documents]');

        const renderUploadedFile = (card, uploadedDocument) => {
            const uploadedFiles = card.querySelector('[data-uploaded-files]');
            const link = document.createElement('a');
            const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

            link.href = uploadedDocument.url;
            link.className = 'uploaded-file';
            link.target = '_blank';
            link.rel = 'noopener';
            icon.setAttribute('viewBox', '0 0 24 24');
            icon.setAttribute('aria-hidden', 'true');
            path.setAttribute('d', 'm9.5 15.2-3-3 1.4-1.4 1.6 1.6 5.6-5.6 1.4 1.4-7 7Z');
            icon.appendChild(path);
            link.appendChild(icon);
            link.append(uploadedDocument.name);

            uploadedFiles.replaceChildren(link);
            uploadedFiles.hidden = false;
        };

        const setDropzoneState = (dropzoneElement, state, message) => {
            const label = dropzoneElement.querySelector('.dropzone-message span');

            dropzoneElement.classList.remove('is-uploading', 'is-success', 'is-error');

            if (state) {
                dropzoneElement.classList.add(state);
            }

            label.textContent = message;
        };

        const parseUploadError = (response) => {
            if (typeof response === 'string') {
                return response;
            }

            if (response?.errors) {
                const firstError = Object.values(response.errors).flat()[0];

                if (firstError) {
                    return firstError;
                }
            }

            return response?.message || 'Upload gagal. Coba pilih file lain.';
        };

        const initNativeDropzone = (dropzoneElement) => {
            const card = dropzoneElement.closest('.document-card');
            const message = dropzoneElement.querySelector('.dropzone-message span');
            const originalLabel = message.textContent;
            const input = dropzoneElement.querySelector('.dropzone-input');

            dropzoneElement.setAttribute('role', 'button');
            dropzoneElement.setAttribute('tabindex', '0');

            const uploadFile = async (file) => {
                const formData = new FormData();
                formData.append('file', file);

                setDropzoneState(dropzoneElement, 'is-uploading', 'Mengupload...');
                input.disabled = true;

                const response = await fetch(dropzoneElement.dataset.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin',
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(parseUploadError(payload));
                }

                setDropzoneState(dropzoneElement, 'is-success', 'Upload Berhasil');
                renderUploadedFile(card, payload.document);
                input.value = '';
                input.disabled = false;
            };

            const handleFile = (file) => {
                if (!file) {
                    return;
                }

                uploadFile(file).catch((error) => {
                    setDropzoneState(dropzoneElement, 'is-error', error.message);
                    input.value = '';
                    input.disabled = false;

                    window.setTimeout(() => {
                        setDropzoneState(dropzoneElement, '', originalLabel);
                    }, 3000);
                });
            };

            dropzoneElement.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    input.click();
                }
            });
            input.addEventListener('change', () => handleFile(input.files[0]));
            dropzoneElement.addEventListener('dragover', (event) => {
                event.preventDefault();
                dropzoneElement.classList.add('dz-drag-hover');
            });
            dropzoneElement.addEventListener('dragleave', () => {
                dropzoneElement.classList.remove('dz-drag-hover');
            });
            dropzoneElement.addEventListener('drop', (event) => {
                event.preventDefault();
                dropzoneElement.classList.remove('dz-drag-hover');
                handleFile(event.dataTransfer.files[0]);
            });
        };

        const initDropzoneJs = (dropzoneElement) => {
            const card = dropzoneElement.closest('.document-card');
            const message = dropzoneElement.querySelector('.dropzone-message span');
            const originalLabel = message.textContent;

            new Dropzone(dropzoneElement, {
                url: dropzoneElement.dataset.uploadUrl,
                paramName: 'file',
                maxFiles: 1,
                maxFilesize: 5,
                acceptedFiles: '.pdf,.jpg,.jpeg,.png',
                uploadMultiple: false,
                disablePreviews: true,
                clickable: true,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                dictInvalidFileType: 'Format file harus PDF, JPG, atau PNG.',
                dictFileTooBig: 'Ukuran file maksimal 5MB.',
                init() {
                    this.on('addedfile', () => {
                        setDropzoneState(dropzoneElement, 'is-uploading', 'Mengupload...');
                    });

                    this.on('success', (file, response) => {
                        setDropzoneState(dropzoneElement, 'is-success', 'Upload Berhasil');
                        renderUploadedFile(card, response.document);
                        this.removeAllFiles(true);
                    });

                    this.on('error', (file, response) => {
                        setDropzoneState(dropzoneElement, 'is-error', parseUploadError(response));
                        this.removeAllFiles(true);

                        window.setTimeout(() => {
                            setDropzoneState(dropzoneElement, '', originalLabel);
                        }, 3000);
                    });
                },
            });
        };

        document.querySelectorAll('.document-dropzone').forEach((dropzoneElement) => {
            initNativeDropzone(dropzoneElement);
        });

        completeButton.addEventListener('click', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
