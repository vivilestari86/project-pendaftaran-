<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Uji Kompetensi</title>
    <style>
        body {
            margin: 0;
            padding: 32px;
            background: #f4f7fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        .card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #1d4ed8;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(23, 32, 51, 0.12);
        }

        .card-header {
            padding: 28px 32px;
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #ffffff;
        }

        .card-header h1 {
            margin: 0 0 6px;
            font-size: 30px;
        }

        .card-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.92;
        }

        .card-body {
            padding: 32px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .item {
            padding: 16px 18px;
            border: 1px solid #d7e1f0;
            border-radius: 12px;
            background: #f8fbff;
        }

        .item-label {
            margin-bottom: 6px;
            color: #5b6880;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .item-value {
            font-size: 19px;
            font-weight: 700;
        }

        .full {
            grid-column: 1 / -1;
        }

        .card-footer {
            padding: 20px 32px 28px;
            color: #48556c;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>Kartu Uji Kompetensi</h1>
            <p>Polindra Portal</p>
        </div>

        <div class="card-body">
            <div class="grid">
                <div class="item">
                    <div class="item-label">Nomor Ujian</div>
                    <div class="item-value">{{ $examCard['exam_number'] }}</div>
                </div>
                <div class="item">
                    <div class="item-label">Nama Peserta</div>
                    <div class="item-value">{{ $user->name }}</div>
                </div>
                <div class="item">
                    <div class="item-label">Email</div>
                    <div class="item-value">{{ $user->email }}</div>
                </div>
                <div class="item">
                    <div class="item-label">Nomor HP</div>
                    <div class="item-value">{{ $user->phone_number }}</div>
                </div>
                <div class="item full">
                    <div class="item-label">Nama Ujian</div>
                    <div class="item-value">{{ $examCard['exam']->name }}</div>
                </div>
                <div class="item">
                    <div class="item-label">Tanggal Ujian</div>
                    <div class="item-value">
                        {{ $examCard['exam']->start_date?->format('d/m/Y') }}
                        @if ($examCard['exam']->end_date && ! $examCard['exam']->end_date->isSameDay($examCard['exam']->start_date))
                            - {{ $examCard['exam']->end_date->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
                <div class="item">
                    <div class="item-label">Sesi</div>
                    <div class="item-value">
                        Sesi {{ $examCard['session']->order }} |
                        {{ $examCard['session']->start_time?->format('H:i') }} - {{ $examCard['session']->end_time?->format('H:i') }}
                    </div>
                </div>
                <div class="item">
                    <div class="item-label">Ruangan</div>
                    <div class="item-value">{{ $examCard['room']->name }}</div>
                </div>
                <div class="item">
                    <div class="item-label">Nomor Kursi</div>
                    <div class="item-value">{{ $examCard['seat_number'] }}</div>
                </div>
                <div class="item full">
                    <div class="item-label">Status</div>
                    <div class="item-value">Terverifikasi dan Terjadwal</div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            Kartu ini dihasilkan otomatis oleh sistem. Simpan file ini dan tunjukkan kepada panitia saat pelaksanaan ujian.
        </div>
    </div>
</body>
</html>
