<x-app-layout>
    <style>
        .import-page {
            background: #f3f4f6;
            min-height: calc(100vh - 80px);
            padding: 40px 24px 56px;
        }

        .import-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .import-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 36px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .import-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .import-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 28px;
        }

        .import-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
            max-width: 760px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-label {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
        }

        .form-select,
        .form-control {
            width: 100%;
            min-height: 54px;
            border: 1px solid #d1d5db;
            border-radius: 16px;
            box-shadow: none !important;
            font-size: 15px;
            padding: 12px 16px;
            background: #fff;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.14) !important;
        }

        .action-bar {
            margin-top: 34px;
            display: flex;
            justify-content: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .action-form {
            margin: 0;
        }

        .btn-import-secondary,
        .btn-import-primary {
            min-width: 180px;
            height: 50px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-import-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-import-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-import-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-import-primary:hover {
            background: #1d4ed8;
        }

        .btn-import-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .preview-box,
        .success-box,
        .error-box,
        .info-box {
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .preview-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .success-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .preview-columns {
            margin: 10px 0 0;
            padding-left: 18px;
        }

        .preview-columns li {
            margin-bottom: 4px;
        }

        .preview-valid {
            margin-top: 12px;
            color: #166534;
            font-weight: 600;
        }

        .preview-invalid {
            margin-top: 12px;
            color: #b91c1c;
            font-weight: 600;
        }

        .preview-missing {
            margin-top: 8px;
            padding-left: 18px;
            color: #b91c1c;
        }

        .file-status {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 12px 14px;
            color: #334155;
            font-size: 14px;
        }

        .file-status strong {
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .import-card {
                padding: 24px 18px;
                border-radius: 18px;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .action-form {
                width: 100%;
            }

            .btn-import-secondary,
            .btn-import-primary {
                width: 100%;
                min-width: auto;
            }
        }
    </style>

    <div class="import-page">
        <div class="import-wrapper">
            <div class="import-card">

                <div class="import-title">Import mobilnosti</div>

                <div class="import-subtitle">
                    Učitaj Excel fajl i ZIP dokumentaciju za automatski unos postojećih mobilnosti.
                </div>

                @if(session('success'))
                    <div class="success-box">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-box">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-box">
                        <strong>Došlo je do greške:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="error-box">
                        <strong>Preskočeni redovi:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($excelTmpPath) || !empty($zipTmpPath))
                    <div class="info-box">
                        <strong>Fajlovi su privremeno sačuvani.</strong><br>
                        Nije potrebno ponovo birati fajlove prije importa osim ako želiš da ih zamijeniš novim.
                    </div>
                @endif

                @if(isset($previewData) && $previewData)
                    <div class="preview-box">
                        <div><strong>Preview uspješan</strong></div>
                        <div>Broj redova u Excel fajlu: {{ $previewData['row_count'] }}</div>
                        <div>ZIP fajl: {{ $previewData['zip_name'] }}</div>

                        @if(!empty($previewData['header']))
                            <div class="mt-2"><strong>Kolone iz Excel fajla:</strong></div>
                            <ul class="preview-columns">
                                @foreach($previewData['header'] as $column)
                                    <li>{{ $column ?: '(prazna kolona)' }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($previewData['is_valid_template'])
                            <div class="preview-valid">
                                Excel template je ispravan.
                            </div>

                            <div class="mt-3">
                                <div><strong>Validni redovi:</strong> {{ $previewData['valid_rows'] }}</div>
                                <div><strong>Nevalidni redovi:</strong> {{ $previewData['invalid_rows'] }}</div>
                            </div>

                            @if(!empty($previewData['row_errors']))
                                <div class="preview-invalid mt-4">
                                    Greške po redovima:
                                </div>
                                <div class="mt-2 space-y-3">
                                    @foreach($previewData['row_errors'] as $rowError)
                                        <div class="bg-white/60 border border-red-200 rounded-xl p-3">
                                            <div class="font-semibold text-red-700">
                                                Red {{ $rowError['row'] }}
                                                @if($rowError['index'])
                                                    — {{ $rowError['index'] }}
                                                @endif
                                                @if($rowError['student'])
                                                    — {{ $rowError['student'] }}
                                                @endif
                                            </div>
                                            <ul class="preview-missing">
                                                @foreach($rowError['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="preview-invalid">
                                Nedostaju obavezne kolone:
                            </div>
                            <ul class="preview-missing">
                                @foreach($previewData['missing_columns'] as $column)
                                    <li>{{ $column }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Podaci za import
                    </h2>

                    <a href="{{ route('adminDashboardShow') }}"
                       class="inline-flex items-center text-lg font-semibold text-gray-900 hover:text-indigo-600 transition">
                        <span class="mr-2">←</span>
                        Nazad na mobilnosti
                    </a>
                </div>

                <form id="preview-form" method="POST" action="{{ route('admin.mobility.import.preview') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="import-form-grid">
                        <div class="field-group">
                            <label for="fakultet_id" class="field-label">Izaberi fakultet</label>
                            <select name="fakultet_id" id="fakultet_id" class="form-select" required>
                                <option value="">-- Izaberi fakultet --</option>
                                @foreach($fakulteti as $fakultet)
                                    <option value="{{ $fakultet->id }}"
                                        {{ old('fakultet_id', $selectedFakultetId ?? null) == $fakultet->id ? 'selected' : '' }}>
                                        {{ $fakultet->naziv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(!empty($excelTmpPath))
                            <input type="hidden" name="excel_tmp_path" value="{{ $excelTmpPath }}">
                            <div class="field-group">
                                <label class="field-label">Excel fajl</label>
                                <div class="file-status">
                                    <strong>Excel fajl je privremeno sačuvan.</strong><br>
                                    Ako želiš drugi fajl, izaberi novi ispod.
                                </div>
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="excel_file"
                                    class="form-control"
                                    accept=".xlsx,.xls"
                                >
                            </div>
                        @else
                            <div class="field-group">
                                <label for="excel_file" class="field-label">Excel fajl</label>
                                <input
                                    type="file"
                                    name="excel_file"
                                    id="excel_file"
                                    class="form-control"
                                    accept=".xlsx,.xls"
                                    required
                                >
                            </div>
                        @endif

                        @if(!empty($zipTmpPath))
                            <input type="hidden" name="zip_tmp_path" value="{{ $zipTmpPath }}">
                            <div class="field-group">
                                <label class="field-label">ZIP fajl sa dokumentacijom</label>
                                <div class="file-status">
                                    <strong>ZIP fajl je privremeno sačuvan.</strong><br>
                                    Ako želiš drugi fajl, izaberi novi ispod.
                                </div>
                                <input
                                    type="file"
                                    name="zip_file"
                                    id="zip_file"
                                    class="form-control"
                                    accept=".zip"
                                >
                            </div>
                        @else
                            <div class="field-group">
                                <label for="zip_file" class="field-label">ZIP fajl sa dokumentacijom</label>
                                <input
                                    type="file"
                                    name="zip_file"
                                    id="zip_file"
                                    class="form-control"
                                    accept=".zip"
                                    required
                                >
                            </div>
                        @endif
                    </div>
                </form>

                <div class="action-bar">
                    <button
                        type="submit"
                        form="preview-form"
                        class="btn-import-secondary"
                    >
                        Provjeri
                    </button>

                    <form method="POST" action="{{ route('admin.mobility.import.store') }}" class="action-form">
                        @csrf
                        <input type="hidden" name="fakultet_id" value="{{ old('fakultet_id', $selectedFakultetId ?? '') }}">
                        <input type="hidden" name="excel_tmp_path" value="{{ $excelTmpPath ?? '' }}">
                        <input type="hidden" name="zip_tmp_path" value="{{ $zipTmpPath ?? '' }}">

                        <button
                            type="submit"
                            class="btn-import-primary"
                            {{ empty($excelTmpPath) || empty($zipTmpPath) ? 'disabled' : '' }}
                        >
                            Importuj mobilnosti
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>