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
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .two-col td { font-size: 9px; padding: 0; }
        .lbl { font-weight: bold; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 80px; }

        .employees-label { font-weight: bold; font-size: 9px; margin-bottom: 2px; }
        .employees-list {
            border-bottom: 1px solid #000;
            min-height: 30px;
            margin-bottom: 6px;
            font-size: 10px;
            padding: 2px 4px;
        }

        .permission { font-size: 8px; font-style: italic; margin-bottom: 6px; }

        .checkbox-row { margin-bottom: 6px; font-size: 9px; }
        .checkbox {
            display: inline-block;
            width: 10px; height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 8px;
            margin-right: 3px;
        }

        .field { margin-bottom: 5px; font-size: 9px; }
        .field .lbl { font-weight: bold; }
        .field .val {
            border-bottom: 1px solid #000;
            display: block;
            min-height: 14px;
            padding: 1px 4px;
        }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sig-table td { text-align: center; padding: 0 10px; font-size: 9px; }
        .sig-name {
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 20px;
        }
        .sig-title { font-size: 8px; color: #555; }
    </style>
</head>
<body>
<div class="page">
    @include('partials.letterhead')
    <div class="copy">

        <div class="title">P A S S &nbsp;&nbsp; S L I P</div>

        <table class="two-col">
            <tr>
                <td><span class="lbl">Date:</span> <span class="underline">{{ $passSlip->created_at->format('m/d/Y') }}</span></td>
            </tr>
        </table>

        <div class="employees-label">Name of employee/s</div>
        <div class="employees-list">
            {{ $passSlip->employee->name }}
            @if($passSlip->remarks)
                <br>{{ $passSlip->remarks }}
            @endif
        </div>
        <div style="font-size:8px; margin-bottom:6px;">Encoded by: {{ $passSlip->encodedBy?->name ?? '—' }}</div>

        <div class="permission">
            Permission is required to leave the office during office hour.
        </div>

        <div class="checkbox-row">
            <span class="checkbox">{{ $passSlip->pass_type === 'official_business' ? '✓' : '' }}</span> OFFICIAL &nbsp;&nbsp;&nbsp;
            <span class="checkbox">{{ $passSlip->pass_type === 'personal' ? '✓' : '' }}</span> PERSONAL
        </div>

        <div class="field">
            <span class="lbl">DESTINATION:</span>
            <span class="val">{{ strtoupper($passSlip->destination) }}</span>
        </div>
        <div class="field">
            <span class="lbl">PURPOSE:</span>
            <span class="val">{{ strtoupper($passSlip->purpose) }}</span>
        </div>
        <div class="field">
            <span class="lbl">ACTUAL TIME:</span>
            <span class="val">
                {{ $passSlip->time_out ? \Carbon\Carbon::parse($passSlip->time_out)->format('h:i A') : '' }}
            </span>
        </div>
        <div class="field">
            <span class="lbl">EXPECTED TIME TO BE BACK:</span>
            <span class="val">
                {{ $passSlip->expected_return ? \Carbon\Carbon::parse($passSlip->expected_return)->format('h:i A') : '' }}
            </span>
        </div>

        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-name">{{ $passSlip->employee->name }}</div>
                    <div class="sig-title">Prepared by</div>
                </td>
                <td>
                    <div class="sig-name">
                        {{ $passSlip->approvedBy?->name ?? 'MR. DANILO S. BENEDITO' }}
                    </div>
                    <div class="sig-title">
                        {{ $passSlip->approvedBy?->position ?? 'Division Manager' }}
                    </div>
                </td>
            </tr>
        </table>

    </div>
</div>
</body>
</html>
