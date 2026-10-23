<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 8px; color: #000; }

        .page { page-break-after: always; padding: 5px; }
        .page:last-child { page-break-after: avoid; }

        .grid { width: 100%; border-collapse: collapse; }
        .grid td { width: 50%; vertical-align: top; padding: 3px; }

        .copy { border: 1px solid #000; padding: 6px 8px; }

        /* Letterhead */
        .lh-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; border-bottom: 2px solid #000; padding-bottom: 3px; }
        .lh-table td { padding: 0; vertical-align: middle; }
        .lh-logo { width: 30px; text-align: center; }
        .lh-logo img { width: 28px; height: 28px; object-fit: contain; }
        .lh-text { text-align: center; padding: 0 4px; }
        .lh-text .org { font-size: 7.5px; font-weight: bold; line-height: 1.2; }
        .lh-text .sub { font-size: 6px; color: #333; }

        .title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 3px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .two-col td { font-size: 7px; padding: 0; }
        .lbl { font-weight: bold; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 60px; }

        .to-from-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .to-from-table td { padding: 1px 0; font-size: 7.5px; }
        .to-from-table .tlbl { font-weight: bold; width: 45px; }

        .please { font-style: italic; font-size: 7px; margin-bottom: 3px; }
        .last-refuel { font-size: 7px; margin-bottom: 4px; }

        .plate-table { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 5px; }
        .plate-table td { border: 1px solid #000; padding: 3px 4px; }
        .cell-lbl { font-size: 6px; color: #555; }
        .cell-val { font-weight: bold; font-size: 10px; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .sig-table td { padding: 1px 3px; font-size: 7px; vertical-align: bottom; }
        .sig-lbl { font-weight: bold; width: 50px; }
        .sig-line { border-bottom: 1px solid #000; min-height: 11px; }

        .approved { margin-top: 5px; text-align: center; border-top: 1px solid #ccc; padding-top: 3px; }
        .approved .pre  { font-size: 7px; font-weight: bold; margin-bottom: 8px; }
        .approved .name { font-weight: bold; font-size: 8px; border-top: 1px solid #000; padding-top: 2px; display: inline-block; min-width: 100px; }
        .approved .pos  { font-size: 7px; }
    </style>
</head>
<body>

@php
    $sealPath = public_path('images/tmcwd-seal.png');
    $bpPath   = public_path('images/bagong-pilipinas.png');
    $sealB64  = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
    $bpB64    = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
    $chunks   = $gasSlips->chunk(4);
@endphp

@foreach($chunks as $chunk)
<div class="page">
    @php
        $items = $chunk->values();
        while ($items->count() < 4) { $items->push(null); }
    @endphp

    <table class="grid">
        @foreach([[0,1],[2,3]] as $pair)
        <tr>
            @foreach($pair as $i)
            <td>
                @if($items[$i])
                @php $slip = $items[$i]; @endphp
                <div class="copy">

                    {{-- Letterhead --}}
                    <table class="lh-table">
                        <tr>
                            <td class="lh-logo">
                                @if($sealB64)
                                    <img src="data:image/png;base64,{{ $sealB64 }}">
                                @endif
                            </td>
                            <td class="lh-text">
                                <div class="sub">Republic of the Philippines</div>
                                <div class="org">TRECE MARTIRES CITY WATER DISTRICT</div>
                                <div class="sub">(LWUA CCC NO. 574)</div>
                                <div class="sub">Quintana Executive Homes, Brgy. San Agustin, City of Trece Martires</div>
                                <div class="sub">Contact No. (046) 419-2664 / 0917-805-0391</div>
                                <div class="sub">tmcwd@yahoo.com / customercare.tmcwd@gmail.com</div>
                            </td>
                            <td class="lh-logo">
                                @if($bpB64)
                                    <img src="data:image/png;base64,{{ $bpB64 }}">
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="title">G A S &nbsp; S L I P</div>

                    <table class="two-col">
                        <tr>
                            <td><span class="lbl">CONTROL NO.</span> <span class="underline">{{ $slip->slip_number }}</span></td>
                            <td style="text-align:right;"><span class="lbl">DATE</span> <span class="underline">{{ $slip->created_at->format('m/d/Y') }}</span></td>
                        </tr>
                    </table>

                    <table class="to-from-table">
                        <tr><td class="tlbl">To:</td><td>{{ $slip->gas_station }}</td></tr>
                        <tr><td></td><td style="font-size:6px;">Inocencio, TMC</td></tr>
                        <tr><td class="tlbl">From:</td><td>TMC Water District</td></tr>
                        <tr><td class="tlbl">Subject:</td><td>{{ $slip->fuel_type }}</td></tr>
                    </table>

                    <div class="please">Please issue {{ strtolower($slip->fuel_type) }} to TMCWD Vehicle</div>
                    <div class="last-refuel">Last refuel on: <span class="underline">{{ $slip->last_refuel_date ? \Carbon\Carbon::parse($slip->last_refuel_date)->format('M d, Y') : '' }}</span></div>

                    <table class="plate-table">
                        <tr>
                            <td><div class="cell-lbl">Plate Number</div><div class="cell-val">{{ $slip->vehicle->plate_number }}</div></td>
                            <td><div class="cell-lbl">{{ $slip->fuel_type }} / Liters</div><div class="cell-val">{{ $slip->requested_liters !== null ? number_format((float) $slip->requested_liters, 2) : 'Not specified' }}</div></td>
                        </tr>
                    </table>

                    <div style="font-weight:bold; font-size:7.5px; margin-bottom:2px;">Requested by</div>
                    <table class="sig-table">
                        <tr>
                            <td class="sig-lbl">Name:</td>
                            <td><div class="sig-line">{{ $slip->requestedBy->name }}</div></td>
                            <td style="width:50px;"><div style="font-size:6px;">Signature:</div><div class="sig-line"></div></td>
                        </tr>
                        <tr>
                            <td class="sig-lbl">Issued by:</td>
                            <td colspan="2"><div class="sig-line"></div><div style="text-align:center; font-size:6px;">Gas Slip Custodian</div></td>
                        </tr>
                    </table>
                    <div style="font-size:6px; margin-top:1px; margin-bottom:2px;">Encoded by: {{ $slip->encodedBy?->name ?? '—' }}</div>

                    <div class="approved">
                        <div class="pre">Approved by:</div>
                        <div class="name">{{ $slip->approvedBy?->name ?? 'ENGR. JOEL JOHN M. ULEP' }}</div>
                        <div class="pos">{{ $slip->approvedBy?->position ?? 'General Manager' }}</div>
                    </div>
                </div>
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>
</div>
@endforeach

</body>
</html>
