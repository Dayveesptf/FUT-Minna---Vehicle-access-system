<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Officer · Checkpoint</p>
        <h1 class="font-display text-2xl font-semibold">Scan Vehicle QR Code</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-5 mb-5 max-w-md">
        <label class="field-label">Current Gate</label>
        <select id="gate-select" class="field-input">
            <option value="">Select a gate to begin…</option>
            @foreach($gates as $gate)
                <option value="{{ $gate->id }}">{{ $gate->gate_name }} — {{ $gate->location }}</option>
            @endforeach
        </select>
        @if($gates->isEmpty())
            <p class="field-error">No active gate points configured. Ask an admin to add one first.</p>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-6 items-start">
        <div class="card p-6">
            <div class="mb-5 pb-5" style="border-bottom: 1px solid var(--line);">
                <label class="field-label">Manual QR Entry (fallback)</label>
                <div class="flex gap-2">
                    <input type="text" id="manual-code" placeholder="Paste QR payload value" class="field-input font-mono-id">
                    <button id="manual-submit" class="btn btn-primary" style="white-space: nowrap;">Verify</button>
                </div>
            </div>

            <div id="reader" style="width: 100%;"></div>
            <p class="text-xs text-black/40 mt-3 text-center">Point the camera at the vehicle's QR label.</p>
        </div>

        <div id="result-panel" class="card p-8 flex items-center justify-center min-h-[300px]">
            <div class="text-center">
                <div class="barrier-divider" style="width: 60px; margin: 0 auto 12px;"></div>
                <p class="text-black/40 text-sm">Select a gate, then scan.</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const resultPanel = document.getElementById('result-panel');
        const gateSelect = document.getElementById('gate-select');
        let isProcessing = false;

        function verifyCode(code) {
            if (isProcessing) return;

            if (!gateSelect.value) {
                resultPanel.innerHTML = `<p class="text-sm text-red-600 text-center">Select a gate before scanning.</p>`;
                return;
            }

            isProcessing = true;

            fetch("{{ route('officer.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ qr_code: code, gate_point_id: gateSelect.value }),
            })
            .then(res => res.json())
            .then(data => {
                renderResult(data);
                setTimeout(() => { isProcessing = false; }, 1500);
            })
            .catch(() => {
                resultPanel.innerHTML = `<p class="text-sm text-red-600 text-center">Something went wrong. Try again.</p>`;
                isProcessing = false;
            });
        }

        function renderResult(data) {
            let color, label;
            if (!data.success) {
                color = '#C23B3B'; label = 'Not Recognized';
            } else if (data.access === 'denied') {
                color = '#C23B3B'; label = 'Access Denied';
            } else if (data.access === 'exit') {
                color = '#E8A63C'; label = 'Exit Recorded';
            } else {
                color = '#1B7A4D'; label = 'Access Granted';
            }

            const vehicleInfo = data.vehicle ? `
                <p class="font-mono-id text-lg font-semibold mt-2">${data.vehicle.plate_number}</p>
                <p class="text-sm text-black/50">${data.owner.first_name} ${data.owner.last_name} — ${data.vehicle.vehicle_brand} ${data.vehicle.vehicle_model}</p>
            ` : '';

            resultPanel.innerHTML = `
                <div class="text-center">
                    <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 16px; border-radius:999px; background:${color}1a; color:${color}; font-weight:600; font-size:14px;">
                        ${label}
                    </div>
                    ${vehicleInfo}
                    <p class="text-xs text-black/40 mt-3">${data.message}</p>
                </div>
            `;
        }

        document.getElementById('manual-submit').addEventListener('click', () => {
            const code = document.getElementById('manual-code').value.trim();
            if (code) verifyCode(code);
        });

        document.getElementById('manual-code').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('manual-submit').click();
            }
        });

        document.getElementById('reader').insertAdjacentHTML('afterbegin', '<p style="font-size:12px; color:rgba(20,22,28,0.4); text-align:center; padding:12px;" id="cam-loading">Starting camera…</p>');

        const scanner = new Html5Qrcode("reader");
        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 220 },
            (decodedText) => {
                verifyCode(decodedText);
            },
            (errorMessage) => { /* ignore per-frame scan failures */ }
        ).then(() => {
            document.getElementById('cam-loading')?.remove();
        }).catch(err => {
            document.getElementById('cam-loading')?.remove();
            resultPanel.innerHTML = `<p class="text-sm text-red-600 text-center">Camera access failed: ${err}</p>`;
        });
    </script>
</x-app-layout>
