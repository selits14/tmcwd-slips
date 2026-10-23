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

        .copy { border: 2px solid #000; padding: 6px 8px; }

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
            letter-spacing: 2px;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }

        .pass-num { text-align: center; font-size: 9px; font-weight: bold; font-family: monospace; margin-bottom: 3px; }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .two-col td { font-size: 7px; padding: 0; }
        .lbl { font-weight: bold; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 55px; }

        .field { margin-bottom: 2px; font-size: 7.5px; }
        .field .lbl { font-weight: bold; }
        .field .val { border-bottom: 1px solid #000; display: block; min-height: 10px; padding: 0 2px; }

        .approved { margin-top: 5px; text-align: center; border-top: 1px solid #ccc; padding-top: 3px; }
        .approved .pre { font-size: 7px; margin-bottom: 8px; }
        .approved .name { font-weight: bold; font-size: 8px; border-top: 1px solid #000; padding-top: 2px; display: inline-block; min-width: 110px; }
        .approved .pos { font-size: 7px; }
    </style>
</head>
<body>

@php
    $sealPath = public_path('images/tmcwd-seal.png');
    $bpPath   = public_path('images/bagong-pilipinas.png');
    $sealB64  = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
    $bpB64    = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
    $chunks   = $gatePasses->chunk(4);
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
                @php $pass = $items[$i]; @endphp
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

                    <div class="title">Vehicle Pass</div>
                    <div class="pass-num">{{ $pass->pass_number }}</div>

                    <table class="two-col">
                        <tr>
                            <td><span class="lbl">Control No.:</span> <span class="underline">{{ $pass->pass_number }}</span></td>
                            <td><span class="lbl">Date:</span> <span class="underline">{{ $pass->created_at->format('m/d/Y') }}</span></td>
                        </tr>
                    </table>
                    <table class="two-col" style="margin-bottom:3px;">
                        <tr>
                            <td><span class="lbl">LR:</span> <span class="underline">{{ $pass->lr_number ?? '' }}</span></td>
                            <td><span class="lbl">PR:</span> <span class="underline">{{ $pass->pr_number ?? '' }}</span></td>
                        </tr>
                    </table>

                    <div class="field"><span class="lbl">Driver's Name:</span><span class="val">{{ strtoupper($pass->driver_name) }}</span></div>
                    <div class="field"><span class="lbl">Requested by:</span><span class="val">{{ strtoupper($pass->requestedBy?->name ?? '') }}</span></div>
                    <div class="field"><span class="lbl">Vehicle Type:</span><span class="val">{{ strtoupper($pass->vehicle_type) }}</span></div>
                    <div class="field"><span class="lbl">Plate Number:</span><span class="val">{{ strtoupper($pass->plate_number) }}</span></div>
                    <div class="field"><span class="lbl">Destination:</span><span class="val">{{ strtoupper($pass->destination) }}</span></div>

                    <table class="two-col">
                        <tr>
                            <td><div class="field"><span class="lbl">Departure:</span><span class="val">{{ $pass->time_of_departure ? $pass->time_of_departure->format('h:i A') : '' }}</span></div></td>
                            <td><div class="field"><span class="lbl">Arrival:</span><span class="val">{{ $pass->time_of_arrival ? $pass->time_of_arrival->format('h:i A') : '' }}</span></div></td>
                        </tr>
                    </table>

                    <div class="field"><span class="lbl">Passengers:</span><span class="val">{{ strtoupper($pass->passengers ?? '') }}</span></div>
                    <div class="field"><span class="lbl">Purpose:</span><span class="val">{{ strtoupper($pass->purpose) }}</span></div>

                    <div class="approved">
                        <div class="pre">Approved by:</div>
                        <div class="name">{{ $pass->approvedBy?->name ?? 'ENGR. JOEL JOHN M. ULEP' }}</div>
                        <div class="pos">{{ $pass->approvedBy?->position ?? 'General Manager' }}</div>
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
