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
        .grid td { width: 33.33%; vertical-align: top; padding: 3px; }

        .copy { border: 1px solid #000; padding: 6px 8px; }

        /* Letterhead */
        .lh-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        .lh-table td { padding: 0; vertical-align: middle; }
        .lh-logo { width: 25px; text-align: center; }
        .lh-logo img { width: 22px; height: 22px; object-fit: contain; }
        .lh-text { text-align: center; padding: 0 3px; }
        .lh-text .org { font-size: 6.5px; font-weight: bold; line-height: 1.2; }
        .lh-text .sub { font-size: 5.5px; color: #333; }

        .title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 4px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .two-col td { font-size: 7px; padding: 0; }
        .lbl { font-weight: bold; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 50px; }

        .field { margin-bottom: 2px; font-size: 7px; }
        .field .lbl { font-weight: bold; }
        .field .val { border-bottom: 1px solid #000; display: block; min-height: 10px; padding: 0 2px; }

        .permission { font-size: 6px; font-style: italic; margin-bottom: 2px; }

        .checkbox-row { margin-bottom: 2px; font-size: 7px; }
        .checkbox { display: inline-block; width: 7px; height: 7px; border: 1px solid #000; text-align: center; line-height: 7px; font-size: 6px; margin-right: 1px; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .sig-table td { text-align: center; padding: 0 2px; font-size: 7px; }
        .sig-name { font-weight: bold; font-size: 7px; border-top: 1px solid #000; padding-top: 2px; margin-top: 10px; }
        .sig-title { font-size: 6px; color: #555; }
    </style>
</head>
<body>

@php
    $sealPath = public_path('images/tmcwd-seal.png');
    $bpPath   = public_path('images/bagong-pilipinas.png');
    $sealB64  = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
    $bpB64    = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
    $chunks   = $passSlips->chunk(3);
@endphp

@foreach($chunks as $chunk)
<div class="page">
    @php
        $items = $chunk->values();
        while ($items->count() < 3) { $items->push(null); }
    @endphp

    <table class="grid">
        <tr>
            @foreach([0, 1, 2] as $i)
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
                                <div class="sub">Quintana Executive Homes, Brgy. San Agustin</div>
                                <div class="sub">City of Trece Martires</div>
                                <div class="sub">(046) 419-2664 / 0917-805-0391</div>
                            </td>
                            <td class="lh-logo">
                                @if($bpB64)
                                    <img src="data:image/png;base64,{{ $bpB64 }}">
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="title">P A S S &nbsp; S L I P</div>

                    <table class="two-col">
                        <tr>
                            <td><span class="lbl">Date:</span> <span class="underline">{{ $slip->created_at->format('m/d/Y') }}</span></td>
                        </tr>
                    </table>

                    <div style="font-weight:bold; font-size:7px; margin-bottom:1px;">Name of employee/s</div>
                    <div style="border-bottom:1px solid #000; min-height:18px; margin-bottom:2px; font-size:7px; padding:1px 2px;">
                        {{ $slip->employee->name }}
                    </div>
                    <div style="font-size:6px; margin-bottom:2px;">Encoded by: {{ $slip->encodedBy?->name ?? '—' }}</div>

                    <div class="permission">Permission is required to leave the office during office hour.</div>

                    <div class="checkbox-row">
                        <span class="checkbox">{{ $slip->pass_type === 'official_business' ? '✓' : '' }}</span> OFFICIAL &nbsp;
                        <span class="checkbox">{{ $slip->pass_type === 'personal' ? '✓' : '' }}</span> PERSONAL
                    </div>

                    <div class="field"><span class="lbl">DESTINATION:</span><span class="val">{{ strtoupper($slip->destination) }}</span></div>
                    <div class="field"><span class="lbl">PURPOSE:</span><span class="val">{{ strtoupper($slip->purpose) }}</span></div>
                    <div class="field"><span class="lbl">ACTUAL TIME:</span><span class="val">{{ $slip->time_out ? \Carbon\Carbon::parse($slip->time_out)->format('h:i A') : '' }}</span></div>
                    <div class="field"><span class="lbl">EXPECTED TIME TO BE BACK:</span><span class="val">{{ $slip->expected_return ? \Carbon\Carbon::parse($slip->expected_return)->format('h:i A') : '' }}</span></div>

                    <table class="sig-table">
                        <tr>
                            <td>
                                <div class="sig-name">{{ $slip->employee->name }}</div>
                                <div class="sig-title">Prepared by</div>
                            </td>
                            <td>
                                <div class="sig-name">{{ $slip->approvedBy?->name ?? 'MR. DANILO S. BENEDITO' }}</div>
                                <div class="sig-title">{{ $slip->approvedBy?->position ?? 'Division Manager' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif
            </td>
            @endforeach
        </tr>
    </table>
</div>
@endforeach

</body>
</html>
