import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('mobileMenu', () => ({
    open: false,
}));

Alpine.data('helpFaq', () => ({
    query: '',
    items: [
        {
            question: 'How do I submit a report?',
            answer: 'Register or log in, open Report Issue, fill out the form, validate your postcode, place the map pin, and submit.',
        },
        {
            question: 'How do I track my issue?',
            answer: 'Go to My Reports and open the report to see the current status and timeline.',
        },
        {
            question: 'What do the statuses mean?',
            answer: 'Submitted means received, In review means under assessment, Resolved means completed, and Rejected means it could not be progressed.',
        },
    ],
    get filteredFaqs() {
        const search = this.query.trim().toLowerCase();

        if (!search) {
            return this.items;
        }

        return this.items.filter((item) => {
            const haystack = `${item.question} ${item.answer}`.toLowerCase();
            return haystack.includes(search);
        });
    },
}));

Alpine.data('helpBot', () => ({
    input: '',
    loading: false,
    messages: [
        { role: 'bot', text: 'Hi. Ask me anything about using CivicEase, such as reporting an issue, tracking reports, map location help, or admin review.' },
    ],
    async send() {
        const text = this.input.trim();
        if (!text || this.loading) return;

        this.messages.push({ role: 'user', text });
        this.loading = true;
        this.input = '';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const history = this.messages
                .slice(-4)
                .filter((message) => message.role === 'user' || message.role === 'bot')
                .map((message) => ({
                    role: message.role === 'bot' ? 'assistant' : 'user',
                    text: message.text,
                }));

            const response = await fetch('/help/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    messages: history.slice(0, -1),
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                const message = data?.errors?.message?.[0] || 'The chatbot is unavailable right now. Make sure LM Studio is running and try again.';
                this.messages.push({ role: 'bot', text: message });
                return;
            }

            this.messages.push({ role: 'bot', text: data.reply || 'I could not generate a reply just now. Please try again.' });
        } catch (error) {
            this.messages.push({ role: 'bot', text: 'The chatbot could not connect to the local LM Studio server. Start LM Studio and try again.' });
        } finally {
            this.loading = false;
        }
    },
}));

document.addEventListener('DOMContentLoaded', () => {
    const postcodeButton = document.querySelector('[data-postcode-lookup]');
    const mapElement = document.getElementById('report-map');
    const imageInput = document.getElementById('image');
    const imagePreviewWrapper = document.querySelector('[data-report-image-preview-wrapper]');
    const imagePreview = document.querySelector('[data-report-image-preview]');
    const cameraOpenButton = document.querySelector('[data-camera-open]');
    const cameraHideButton = document.querySelector('[data-camera-hide]');
    const cameraPanel = document.querySelector('[data-camera-panel]');
    const cameraPermissionStep = document.querySelector('[data-camera-permission-step]');
    const cameraRequestButton = document.querySelector('[data-camera-request]');
    const cameraCancelButton = document.querySelector('[data-camera-cancel]');
    const cameraLive = document.querySelector('[data-camera-live]');
    const cameraVideo = document.querySelector('[data-camera-video]');
    const cameraCanvas = document.querySelector('[data-camera-canvas]');
    const cameraCaptureButton = document.querySelector('[data-camera-capture]');
    const cameraStopButton = document.querySelector('[data-camera-stop]');
    const cameraStatus = document.querySelector('[data-camera-status]');
    const communityFilterForm = document.querySelector('[data-community-filter-form]');
    let currentPreviewUrl = null;
    let cameraStream = null;
    const maxCameraDimension = 1600;

    const clearPreviewUrl = () => {
        if (currentPreviewUrl) {
            URL.revokeObjectURL(currentPreviewUrl);
            currentPreviewUrl = null;
        }
    };

    const showImagePreview = (file) => {
        if (!imagePreviewWrapper || !imagePreview || !file) return;

        clearPreviewUrl();
        currentPreviewUrl = URL.createObjectURL(file);
        imagePreview.src = currentPreviewUrl;
        imagePreviewWrapper.classList.remove('hidden');
    };

    const updateCameraStatus = (message) => {
        if (cameraStatus) {
            cameraStatus.textContent = message;
        }
    };

    const stopCameraStream = () => {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => track.stop());
            cameraStream = null;
        }

        if (cameraVideo) {
            cameraVideo.srcObject = null;
        }
    };

    if (imageInput) {
        imageInput.addEventListener('change', () => {
            const [file] = imageInput.files || [];
            if (file) {
                showImagePreview(file);
            }
        });
    }

    if (cameraOpenButton && cameraPanel) {
        const showPermissionStep = () => {
            cameraPanel.classList.remove('hidden');
            cameraHideButton?.classList.remove('hidden');
            cameraPermissionStep?.classList.remove('hidden');
            cameraLive?.classList.add('hidden');
            updateCameraStatus('Camera access has not started yet. Choose "Allow and open camera" to continue.');
        };

        const hideCameraPanel = () => {
            stopCameraStream();
            cameraPanel.classList.add('hidden');
            cameraHideButton?.classList.add('hidden');
            cameraPermissionStep?.classList.remove('hidden');
            cameraLive?.classList.add('hidden');
            updateCameraStatus('');
        };

        cameraOpenButton.addEventListener('click', () => {
            if (!navigator.mediaDevices?.getUserMedia) {
                cameraPanel.classList.remove('hidden');
                cameraHideButton?.classList.remove('hidden');
                cameraPermissionStep?.classList.add('hidden');
                cameraLive?.classList.add('hidden');
                updateCameraStatus('Camera capture is not available in this browser. You can still upload an image from your device.');
                return;
            }

            showPermissionStep();
        });

        cameraHideButton?.addEventListener('click', hideCameraPanel);
        cameraCancelButton?.addEventListener('click', hideCameraPanel);
        cameraStopButton?.addEventListener('click', () => {
            stopCameraStream();
            cameraPermissionStep?.classList.remove('hidden');
            cameraLive?.classList.add('hidden');
            updateCameraStatus('Camera stopped. You can reopen it if you still need to capture a photo.');
        });

        cameraRequestButton?.addEventListener('click', async () => {
            if (!navigator.mediaDevices?.getUserMedia || !cameraVideo) {
                updateCameraStatus('Camera capture is not available in this browser.');
                return;
            }

            updateCameraStatus('Requesting camera permission from your browser...');

            try {
                stopCameraStream();

                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                    },
                    audio: false,
                });

                cameraVideo.srcObject = cameraStream;
                cameraPermissionStep?.classList.add('hidden');
                cameraLive?.classList.remove('hidden');
                updateCameraStatus('Camera is ready. Capture a photo when the preview looks right.');
            } catch (error) {
                cameraPermissionStep?.classList.remove('hidden');
                cameraLive?.classList.add('hidden');

                if (error && typeof error === 'object' && 'name' in error && error.name === 'NotAllowedError') {
                    updateCameraStatus('Camera permission was denied. You can try again or upload an image from your device.');
                } else if (error && typeof error === 'object' && 'name' in error && error.name === 'NotFoundError') {
                    updateCameraStatus('No camera was found on this device. You can upload an image instead.');
                } else {
                    updateCameraStatus('Camera access failed. Please try again or upload an image from your device.');
                }
            }
        });

        cameraCaptureButton?.addEventListener('click', () => {
            if (!cameraVideo || !cameraCanvas || !imageInput || !cameraStream) {
                updateCameraStatus('Camera is not ready yet.');
                return;
            }

            const width = cameraVideo.videoWidth;
            const height = cameraVideo.videoHeight;

            if (!width || !height) {
                updateCameraStatus('The camera preview is still loading. Try again in a moment.');
                return;
            }

            const scale = Math.min(1, maxCameraDimension / Math.max(width, height));
            const targetWidth = Math.max(1, Math.round(width * scale));
            const targetHeight = Math.max(1, Math.round(height * scale));

            cameraCanvas.width = targetWidth;
            cameraCanvas.height = targetHeight;

            const context = cameraCanvas.getContext('2d');
            if (!context) {
                updateCameraStatus('Unable to capture a frame from the camera.');
                return;
            }

            context.drawImage(cameraVideo, 0, 0, targetWidth, targetHeight);

            cameraCanvas.toBlob((blob) => {
                if (!blob) {
                    updateCameraStatus('Unable to capture the photo. Please try again.');
                    return;
                }

                const file = new File([blob], `report-camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                imageInput.files = transfer.files;
                imageInput.dispatchEvent(new Event('change', { bubbles: true }));
                stopCameraStream();
                cameraPermissionStep?.classList.remove('hidden');
                cameraLive?.classList.add('hidden');
                updateCameraStatus('Photo captured and attached to your report. You can capture again or submit the form.');
            }, 'image/jpeg', 0.82);
        });

        window.addEventListener('beforeunload', stopCameraStream);
    }

    if (postcodeButton && mapElement && window.L) {
        let map = window.L.map(mapElement).setView([54.5, -3], 6);
        let marker = null;

        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const postcodeInput = document.getElementById('postcode');
        const postcodeLookupLatInput = document.getElementById('postcode_lookup_latitude');
        const postcodeLookupLngInput = document.getElementById('postcode_lookup_longitude');
        const statusBox = document.getElementById('postcode-status');
        const postcodeError = document.getElementById('postcode-error');
        const postcodeErrorClasses = ['border-red-300', 'text-red-900', 'focus:border-red-500', 'focus:ring-red-500'];

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const showPostcodeStatus = (message, tone = 'neutral') => {
            statusBox.textContent = message;
            statusBox.classList.remove('text-slate-500', 'text-green-700', 'text-red-600');

            if (tone === 'success') {
                statusBox.classList.add('text-green-700');
            } else if (tone === 'error') {
                statusBox.classList.add('text-red-600');
            } else {
                statusBox.classList.add('text-slate-500');
            }
        };

        const clearPostcodeErrorState = () => {
            postcodeInput?.classList.remove(...postcodeErrorClasses);
            if (postcodeError) {
                postcodeError.textContent = '';
                postcodeError.classList.add('hidden');
            }
        };

        const showPostcodeErrorState = (message) => {
            postcodeInput?.classList.add(...postcodeErrorClasses);
            if (postcodeError) {
                postcodeError.textContent = message;
                postcodeError.classList.remove('hidden');
            } else {
                showPostcodeStatus(message, 'error');
            }
        };

        const clearLookupCoordinates = () => {
            if (postcodeLookupLatInput) postcodeLookupLatInput.value = '';
            if (postcodeLookupLngInput) postcodeLookupLngInput.value = '';
        };

        const setMarker = (lat, lng) => {
            if (!marker) {
                marker = window.L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    latInput.value = position.lat.toFixed(7);
                    lngInput.value = position.lng.toFixed(7);
                });
            } else {
                marker.setLatLng([lat, lng]);
            }

            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
            map.setView([lat, lng], 16);
        };

        const setLookupCoordinates = (lat, lng) => {
            if (postcodeLookupLatInput) postcodeLookupLatInput.value = Number(lat).toFixed(7);
            if (postcodeLookupLngInput) postcodeLookupLngInput.value = Number(lng).toFixed(7);
        };

        postcodeInput?.addEventListener('input', () => {
            clearLookupCoordinates();
            clearPostcodeErrorState();
            showPostcodeStatus('Use a UK postcode to place the map.');
        });

        map.on('click', (event) => {
            setMarker(event.latlng.lat, event.latlng.lng);
        });

        postcodeButton.addEventListener('click', async () => {
            const postcode = postcodeInput.value.trim();
            if (!postcode) {
                showPostcodeErrorState('Enter a postcode first.');
                showPostcodeStatus('Enter a postcode first.', 'error');
                return;
            }

            clearPostcodeErrorState();
            showPostcodeStatus('Checking postcode...');

            try {
                const response = await fetch(`https://api.postcodes.io/postcodes/${encodeURIComponent(postcode)}`);
                const data = await response.json();

                if (!response.ok || !data.result) {
                    clearLookupCoordinates();
                    showPostcodeErrorState('Please enter a valid UK postcode.');
                    showPostcodeStatus('Postcode not found. Please check and try again.', 'error');
                    return;
                }

                setLookupCoordinates(data.result.latitude, data.result.longitude);
                setMarker(data.result.latitude, data.result.longitude);
                clearPostcodeErrorState();
                showPostcodeStatus('Postcode found. You can drag the pin or click the map to refine the location.', 'success');
            } catch (error) {
                clearLookupCoordinates();
                showPostcodeErrorState('Please enter a valid UK postcode.');
                showPostcodeStatus('Lookup failed. You can still place the pin manually within the supported area and submit.', 'error');
            }
        });
    }

    const communityMap = document.getElementById('community-map');
    if (communityMap && window.L) {
        const reports = JSON.parse(communityMap.dataset.reports || '[]');
        const map = window.L.map(communityMap).setView([54.5, -3], 6);
        const resultsBox = document.querySelector('[data-community-map-results]');
        const emptyBox = document.querySelector('[data-community-map-empty]');
        const markers = [];
        const totalReportCount = Number(resultsBox?.dataset.totalReportCount || reports.length);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const escapeHtml = (value) => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
        const formatStatus = (status) => status
            .split('_')
            .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
        const formatDate = (dateValue) => new Date(dateValue).toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });

        const renderCommunityMap = () => {
            markers.forEach((marker) => marker.remove());
            markers.length = 0;

            if (resultsBox) {
                resultsBox.textContent = `Showing ${reports.length} of ${totalReportCount} resolved public reports.`;
            }

            if (emptyBox) {
                emptyBox.classList.toggle('hidden', reports.length !== 0);
            }

            if (!reports.length) {
                map.setView([54.5, -3], 6);
                return;
            }

            const bounds = [];

            reports.forEach((report) => {
                const marker = window.L.marker([report.latitude, report.longitude]).addTo(map);
                marker.bindPopup(
                    `<div class="space-y-1"><strong>${escapeHtml(report.title)}</strong><div>${escapeHtml(report.category)}</div><div>Status: ${escapeHtml(formatStatus(report.status))}</div><div>Reported: ${escapeHtml(formatDate(report.created_at))}</div></div>`
                );
                markers.push(marker);
                bounds.push([report.latitude, report.longitude]);
            });

            if (reports.length === 1) {
                map.setView(bounds[0], 15);
                return;
            }

            map.fitBounds(bounds, { padding: [40, 40] });
        };

        if (reports.length) {
            renderCommunityMap();
        } else {
            renderCommunityMap();
        }
    }

    if (communityFilterForm) {
        const filterInputs = communityFilterForm.querySelectorAll('[data-community-filter]');

        filterInputs.forEach((input) => {
            input.addEventListener('change', () => {
                communityFilterForm.requestSubmit();
            });
        });
    }

    const detailMap = document.getElementById('report-detail-map');
    if (detailMap && window.L) {
        const lat = parseFloat(detailMap.dataset.lat);
        const lng = parseFloat(detailMap.dataset.lng);
        const map = window.L.map(detailMap).setView([lat, lng], 15);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        window.L.marker([lat, lng]).addTo(map);
    }
});

Alpine.start();
