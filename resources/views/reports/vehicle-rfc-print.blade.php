<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; }
        .page { padding: 15px; }

        /* Letterhead */
        .lh-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border-bottom: 3px double #000; padding-bottom: 5px; }
        .lh-table td { padding: 0; vertical-align: middle; }
        .lh-logo { width: 65px; text-align: center; }
        .lh-logo img { width: 58px; height: 58px; object-fit: contain; }
        .lh-text { text-align: center; padding: 0 8px; }
        .lh-text .org { font-size: 12px; font-weight: bold; }
        .lh-text .sub { font-size: 8px; }

        .report-title { text-align: center; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }

        table.rfc { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8px; }
        table.rfc th {
            border: 1px solid #000;
            padding: 3px 4px;
            background: #f0f0f0;
            text-align: center;
            font-size: 7.5px;
        }
        table.rfc td {
            border: 1px solid #ccc;
            padding: 3px 4px;
            text-align: center;
        }
        table.rfc .text-left { text-align: left; }
        table.rfc .excess-over  { color: red; font-weight: bold; }
        table.rfc .excess-under { color: green; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sig-table td { text-align: center; padding: 0 30px; font-size: 8px; }
        .sig-line { border-top: 1px solid #000; padding-top: 3px; margin-top: 25px; }
        .sig-name { font-weight: bold; font-size: 9px; }
        .sig-role { font-size: 8px; }
        .sig-label { font-size: 8px; color: #555; }
    </style>
</head>
<body>
<div class="page">

    @php
        $sealPath = public_path('images/tmcwd-seal.png');
        $bpPath   = public_path('images/bagong-pilipinas.png');
        $sealB64  = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
        $bpB64    = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
    @endphp

    {{-- Letterhead --}}
    <table class="lh-table">
        <tr>
            <td class="lh-logo">
                @if($sealB64)<img src="data:image/png;base64,{{ $sealB64 }}">@endif
            </td>
            <td class="lh-text">
                <div class="sub">Republic of the Philippines</div>
                <div class="org">TRECE MARTIRES CITY WATER DISTRICT</div>
                <div class="sub">(LWUA CCC NO. 574)</div>
                <div class="sub">Quintana Executive Homes, Brgy. San Agustin, City of Trece Martires, Cavite 4109</div>
            </td>
            <td class="lh-logo">
                @if($bpB64)<img src="data:image/png;base64,{{ $bpB64 }}">@endif
            </td>
        </tr>
    </table>

    <div class="report-title">
        Report of Fuel Consumption for the Month of {{ strtoupper($monthLabel) }}
    </div>

    {{-- RFC Table --}}
    <table class="rfc">
        <thead>
            <tr>
                <th rowspan="2" style="text-align:left;">Type of Vehicle</th>
                <th rowspan="2">Plate Number</th>
                <th rowspan="2">No. of Cylinder</th>
                <th colspan="2">Odometer Reading</th>
                <th rowspan="2">Total Distance Travelled (A)</th>
                <th rowspan="2">Total Fuel Used (B)</th>
                <th rowspan="2">Distance per Liter km/L (C = A/B)</th>
                <th rowspan="2">Normal Travel km/L (D)</th>
                <th rowspan="2">Total Liters + 10% Allowance (E = [A/D][1.1])</th>
                <th rowspan="2">Excess (B - E)</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Beginning</th>
                <th>Ending</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $data)
            <tr>
                <td class="text-left">{{ $data['vehicle']->description }}</td>
                <td>{{ $data['vehicle']->plate_number }}</td>
                <td>{{ $data['vehicle']->num_cylinders ?? '—' }}</td>
                <td>{{ $data['odometer_begin'] }}</td>
                <td>{{ $data['odometer_end'] }}</td>
                <td>{{ number_format($data['total_distance'], 2) }}</td>
                <td>{{ number_format($data['total_fuel'], 2) }}</td>
                <td>{{ number_format($data['km_per_liter'], 2) }}</td>
                <td>{{ number_format($data['normal_km'], 2) }}</td>
                <td>{{ number_format($data['allowed_liters'], 2) }}</td>
                <td class="{{ $data['excess'] > 0 ? 'excess-over' : 'excess-under' }}">
                    {{ number_format($data['excess'], 2) }}
                </td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="12" style="text-align:center; padding:10px; color:#999;">
                    No records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Signatures --}}
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line">
                    <div class="sig-label">Prepared by:</div>
                    <div class="sig-name">JONA GRACE N. LOYOLA</div>
                    <div class="sig-role">Admin Services Aide</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-label">Verified and Found Correct:</div>
                    <div class="sig-name">MR. LORENZO R. SISANTE</div>
                    <div class="sig-role">DIVISION MANAGER - CMD</div>
                </div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>