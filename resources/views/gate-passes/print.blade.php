<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; }

        .page { padding: 20px; }

        .copy {
            border: 2px solid #000;
            padding: 15px 18px;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .pass-num {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            font-family: monospace;
            margin-bottom: 8px;
        }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .two-col td { font-size: 9px; padding: 0; }
        .lbl { font-weight: bold; font-size: 9px; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 100px; }

        .field { margin-bottom: 4px; font-size: 9px; }
        .field .lbl { font-weight: bold; }
        .field .val {
            border-bottom: 1px solid #000;
            display: block;
            min-height: 14px;
            padding: 1px 4px;
        }

        .approved {
            margin-top: 15px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .approved .pre { font-size: 9px; margin-bottom: 15px; }
        .approved .name {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 3px;
            display: inline-block;
            min-width: 160px;
        }
        .approved .pos { font-size: 9px; }
    </style>
</head>
<body>
<div class="page">
    @include('partials.letterhead')
    <div class="copy">

        <div class="title">Vehicle Pass</div>
        <div class="pass-num">{{ $gatePass->pass_number }}</div>

        <table class="two-col">
            <tr>
                <td><span class="lbl">Control No.:</span> <span class="underline">{{ $gatePass->pass_number }}</span></td>
                <td style="text-align:right;"><span class="lbl">Date:</span> <span class="underline">{{ $gatePass->created_at->format('m/d/Y') }}</span></td>
            </tr>
        </table>

        <table class="two-col" style="margin-bottom:8px;">
            <tr>
                <td><span class="lbl">LR:</span> <span class="underline">{{ $gatePass->lr_number ?? '' }}</span></td>
                <td><span class="lbl">PR:</span> <span class="underline">{{ $gatePass->pr_number ?? '' }}</span></td>
            </tr>
        </table>

        <div class="field"><span class="lbl">Driver's Name:</span><span class="val">{{ strtoupper($gatePass->driver_name) }}</span></div>
        <div class="field"><span class="lbl">Requested by:</span><span class="val">{{ strtoupper($gatePass->requestedBy?->name ?? '') }}</span></div>
        <div class="field"><span class="lbl">Vehicle Type:</span><span class="val">{{ strtoupper($gatePass->vehicle_type) }}</span></div>
        <div class="field"><span class="lbl">Plate Number:</span><span class="val">{{ strtoupper($gatePass->plate_number) }}</span></div>
        <div class="field"><span class="lbl">Destination:</span><span class="val">{{ strtoupper($gatePass->destination) }}</span></div>

        <table class="two-col">
            <tr>
                <td width="50%">
                    <div class="field">
                        <span class="lbl">Time of departure:</span>
                        <span class="val">{{ $gatePass->time_of_departure ? $gatePass->time_of_departure->format('h:i A') : '' }}</span>
                    </div>
                </td>
                <td width="50%">
                    <div class="field">
                        <span class="lbl">Time of arrival:</span>
                        <span class="val">{{ $gatePass->time_of_arrival ? $gatePass->time_of_arrival->format('h:i A') : '' }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="field"><span class="lbl">Passengers:</span><span class="val">{{ strtoupper($gatePass->passengers ?? '') }}</span></div>
        <div class="field"><span class="lbl">Purpose:</span><span class="val">{{ strtoupper($gatePass->purpose) }}</span></div>

        <div class="approved">
            <div class="pre">Approved by:</div>
            <div class="name">{{ $gatePass->approvedBy?->name ?? 'ENGR. JOEL JOHN M. ULEP' }}</div>
            <div class="pos">{{ $gatePass->approvedBy?->position ?? 'General Manager' }}</div>
        </div>

    </div>
</div>
</body>
</html>
