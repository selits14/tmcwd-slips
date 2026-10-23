<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; }

        .page { padding: 20px; }

        .copy {
            border: 1px solid #000;
            padding: 15px 18px;
            max-width: 100%;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .two-col td { font-size: 9px; padding: 0; }
        .lbl { font-weight: bold; }
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 100px;
        }

        .to-from-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .to-from-table td { padding: 2px 0; font-size: 10px; }
        .to-from-table .tlbl { font-weight: bold; width: 65px; }

        .please { font-style: italic; font-size: 9px; margin-bottom: 6px; }
        .last-refuel { font-size: 9px; margin-bottom: 8px; }

        .plate-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        .plate-table td { border: 1px solid #000; padding: 6px 8px; }
        .cell-lbl { font-size: 8px; color: #555; }
        .cell-val { font-weight: bold; font-size: 14px; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .sig-table td { padding: 3px 5px; font-size: 9px; vertical-align: bottom; }
        .sig-lbl { font-weight: bold; width: 70px; }
        .sig-line {
            border-bottom: 1px solid #000;
            min-height: 16px;
            padding: 1px 2px;
        }

        .approved {
            margin-top: 12px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .approved .pre  { font-size: 9px; font-weight: bold; margin-bottom: 15px; }
        .approved .name { font-weight: bold; font-size: 11px; border-top: 1px solid #000; padding-top: 3px; display: inline-block; min-width: 160px; }
        .approved .pos  { font-size: 9px; }
    </style>
</head>
<body>
<div class="page">

@include('partials.letterhead')
    <div class="copy">

        <div class="title">G A S &nbsp; S L I P</div>

        {{-- Control No / Date --}}
        <table class="two-col">
            <tr>
                <td><span class="lbl">CONTROL NO.&nbsp;</span><span class="underline">{{ $gasSlip->slip_number }}</span></td>
                <td style="text-align:right;"><span class="lbl">DATE&nbsp;</span><span class="underline">{{ $gasSlip->created_at->format('m/d/Y') }}</span></td>
            </tr>
        </table>

        {{-- To / From / Subject --}}
        <table class="to-from-table">
            <tr>
                <td class="tlbl">To:</td>
                <td>{{ $gasSlip->gas_station }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size:9px;">Inocencio, TMC</td>
            </tr>
            <tr>
                <td class="tlbl">From:</td>
                <td>TMC Water District</td>
            </tr>
            <tr>
                <td class="tlbl">Subject:</td>
                <td>{{ $gasSlip->fuel_type }}</td>
            </tr>
        </table>

        <div class="please">
            Please issue {{ strtolower($gasSlip->fuel_type) }} to TMCWD Vehicle
        </div>

        <div class="last-refuel">
            Last refuel on: <span class="underline">
                {{ $gasSlip->last_refuel_date ? \Carbon\Carbon::parse($gasSlip->last_refuel_date)->format('M d, Y') : '' }}
            </span>
        </div>

        {{-- Plate / Fuel --}}
        <table class="plate-table">
            <tr>
                <td>
                    <div class="cell-lbl">Plate Number</div>
                    <div class="cell-val">{{ $gasSlip->vehicle->plate_number }}</div>
                </td>
                <td>
                    <div class="cell-lbl">{{ $gasSlip->fuel_type }} / Liters</div>
                    <div class="cell-val">{{ $gasSlip->requested_liters !== null ? number_format((float) $gasSlip->requested_liters, 2) : 'Not specified' }}</div>
                </td>
            </tr>
        </table>

        {{-- Signatures --}}
        <div style="font-weight:bold; font-size:10px; margin-bottom:4px;">Requested by</div>
        <table class="sig-table">
            <tr>
                <td class="sig-lbl">Name:</td>
                <td>
                    <div class="sig-line">{{ $gasSlip->requestedBy->name }}</div>
                </td>
                <td style="width:100px;">
                    <div style="font-size:8px;">Signature:</div>
                    <div class="sig-line"></div>
                </td>
            </tr>
            <tr>
                <td class="sig-lbl">Issued by:</td>
                <td colspan="2">
                    <div class="sig-line"></div>
                    <div style="text-align:center; font-size:8px;">Gas Slip Custodian</div>
                </td>
            </tr>
        </table>
        <div style="font-size:8px; margin-top:2px; margin-bottom:6px;">Encoded by: {{ $gasSlip->encodedBy?->name ?? '—' }}</div>

        <div class="approved">
            <div class="pre">Approved by:</div>
            <div class="name">{{ $gasSlip->approvedBy?->name ?? 'ENGR. JOEL JOHN M. ULEP' }}</div>
            <div class="pos">{{ $gasSlip->approvedBy?->position ?? 'General Manager' }}</div>
        </div>

    </div>
</div>
</body>
</html>
