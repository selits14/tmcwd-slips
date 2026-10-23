<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; }

        .page { padding: 15px; page-break-after: always; }
        .page:last-child { page-break-after: avoid; }

        /* Letterhead */
        .lh-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border-bottom: 3px double #000; padding-bottom: 5px; }
        .lh-table td { padding: 0; vertical-align: middle; }
        .lh-logo { width: 65px; text-align: center; }
        .lh-logo img { width: 58px; height: 58px; object-fit: contain; }
        .lh-text { text-align: center; padding: 0 8px; }
        .lh-text .org { font-size: 12px; font-weight: bold; }
        .lh-text .sub { font-size: 8px; }

        .report-title { text-align: center; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }

        .vehicle-info { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .vehicle-info td { font-size: 9px; padding: 2px 0; }
        .vehicle-info .lbl { font-weight: bold; width: 110px; }
        .vehicle-info .val { border-bottom: 1px solid #000; min-width: 120px; display: inline-block; }

        table.mrot { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.mrot th {
            border: 1px solid #000;
            padding: 3px 5px;
            background: #f0f0f0;
            text-align: center;
            font-size: 8px;
        }
        table.mrot td {
            border: 1px solid #ccc;
            padding: 2px 5px;
            font-size: 8px;
        }
        table.mrot .total-row { font-weight: bold; background: #f9f9f9; }

        .certify { font-size: 8px; font-style: italic; margin: 10px 0 5px; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .sig-table td { text-align: center; padding: 0 20px; font-size: 8px; }
        .sig-line { border-top: 1px solid #000; padding-top: 3px; margin-top: 20px; }
        .sig-name { font-weight: bold; font-size: 9px; }
        .sig-title { font-size: 8px; }

        .note { font-size: 7px; color: #555; margin-top: 10px; font-style: italic; }
    </style>
</head>
<body>

@php
    $sealPath = public_path('images/tmcwd-seal.png');
    $bpPath   = public_path('images/bagong-pilipinas.png');
    $sealB64  = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
    $bpB64    = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
@endphp

@foreach($grouped as $vehicleId => $slips)
@php
    $vehicle = $slips->first()->vehicle;
    $driver  = $slips->first()->driver;
@endphp
<div class="page">

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
        Monthly Report of Official Travel<br>
        <span style="font-size:10px;">{{ strtoupper($monthLabel) }}</span>
    </div>

    {{-- Vehicle / Driver Info --}}
    <table class="vehicle-info">
        <tr>
            <td class="lbl">Vehicle Plate No:</td>
            <td><span class="val">{{ $vehicle->plate_number }}</span></td>
            <td style="width:30px;"></td>
            <td class="lbl">Driver's Name:</td>
            <td><span class="val">{{ strtoupper($driver->name) }}</span></td>
        </tr>
    </table>

    {{-- MROT Table --}}
    <table class="mrot">
        <thead>
            <tr>
                <th>DATE</th>
                <th>Distance Traveled<br>(in Kms)</th>
                <th>Gasoline Consumed<br>(in Liters)</th>
                <th>Oil Used<br>(in Liters)</th>
                <th>Slip No.</th>
                <th>Remarks / Destination</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slips as $slip)
            <tr>
                <td>{{ strtoupper($slip->created_at->format('F j, Y')) }}</td>
                <td style="text-align:center;">{{ number_format($slip->distance_travelled ?? 0, 2) }}</td>
                <td style="text-align:center;">{{ number_format($slip->fuel_used ?? 0, 2) }}</td>
                <td style="text-align:center;">{{ number_format($slip->oil_used ?? 0, 2) }}</td>
                <td style="text-align:center;">{{ $slip->slip_number }}</td>
                <td>{{ $slip->places_to_visit }}</td>
            </tr>
            @endforeach

            {{-- Empty rows to fill space --}}
            @for($i = $slips->count(); $i < 25; $i++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor

            {{-- Totals --}}
            <tr class="total-row">
                <td style="text-align:right; font-weight:bold;">TOTALS</td>
                <td style="text-align:center;">{{ number_format($slips->sum('distance_travelled'), 2) }}</td>
                <td style="text-align:center;">{{ number_format($slips->sum('fuel_used'), 2) }}</td>
                <td style="text-align:center;">{{ number_format($slips->sum('oil_used'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    {{-- Certification --}}
    <div class="certify">
        I hereby certify to the correctness of the above statements and that the motor vehicle
        was used strictly official business only.
    </div>

    {{-- Signatures --}}
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line">
                    <div class="sig-name">{{ strtoupper($driver->name) }}</div>
                    <div class="sig-title">(Driver's Name and Signature)</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div style="font-size:8px; margin-bottom:15px;">Approved by:</div>
                    <div class="sig-name">ENGR. EDVAN RHEY O. REYES</div>
                    <div class="sig-title">Admin. Services Chief</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="note">
        NOTE: This report should be accomplished in triplicate of which, supported by the originals of duly
        accomplished Driver's Record of travel (Form A) should be submitted, thru the Administrative
        Officer V or his equivalent, to the auditor concerned.
    </div>

</div>
@endforeach

</body>
</html>