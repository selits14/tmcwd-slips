<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; }
        .page { padding: 15px; }

        /* ===== RFC ===== */
        .rfc-header { text-align: center; margin-bottom: 10px; }
        .rfc-header p { font-size: 9px; }
        .rfc-header h2 { font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 3px 0; }

        .rfc-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8px; }
        .rfc-table th {
            border: 1px solid #000;
            padding: 3px 4px;
            background: #f0f0f0;
            text-align: center;
            font-size: 7px;
        }
        .rfc-table td {
            border: 1px solid #999;
            padding: 3px 4px;
            text-align: center;
        }
        .rfc-table .text-left { text-align: left; }

        .sig-row { display: flex; gap: 40px; margin-top: 15px; }
        .sig-box { flex: 1; }
        .sig-line { border-top: 1px solid #000; padding-top: 3px; text-align: center; margin-top: 20px; }
        .sig-name { font-weight: bold; font-size: 9px; }
        .sig-title { font-size: 8px; }

        /* ===== Page break ===== */
        .page-break { page-break-before: always; padding-top: 15px; }

        /* ===== MROT ===== */
        .mrot-header { text-align: center; margin-bottom: 8px; }
        .mrot-header p { font-size: 9px; }
        .mrot-header h2 { font-size: 11px; font-weight: bold; text-transform: uppercase; }

        .mrot-info { display: flex; gap: 30px; margin-bottom: 8px; font-size: 9px; }
        .mrot-info .item { display: flex; gap: 4px; }
        .mrot-info .lbl { font-weight: bold; }
        .mrot-info .val { border-bottom: 1px solid #000; min-width: 100px; }

        .mrot-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8px; }
        .mrot-table th {
            border: 1px solid #000;
            padding: 3px 6px;
            background: #f0f0f0;
            text-align: center;
        }
        .mrot-table td {
            border: 1px solid #ccc;
            padding: 2px 6px;
            text-align: center;
        }
        .mrot-table .text-left { text-align: left; }
        .mrot-table .total-row { font-weight: bold; background: #f9f9f9; }

        .certify { font-size: 8px; font-style: italic; margin: 8px 0 4px; }
        .footer-note { font-size: 7px; margin-top: 8px; color: #555; }
    </style>
</head>
<body>
<div class="page">
    @include('partials.letterhead')
    {{-- ============= RFC ============= --}}
    <div class="rfc-header">
        <p>Republic of the Philippines</p>
        <h2>Trece Martires City Water District</h2>
        <p>Quintana Executive Homes, Trece Martires City, Cavite 4109</p>
        <br>
        <h2>REPORT OF FUEL CONSUMPTION FOR THE MONTH OF {{ strtoupper($monthLabel) }}</h2>
    </div>

    <table class="rfc-table">
        <thead>
            <tr>
                <th rowspan="2">Type of Vehicle</th>
                <th rowspan="2">Plate Number</th>
                <th rowspan="2">Driver</th>
                <th colspan="2">Odometer Reading</th>
                <th rowspan="2">Total Distance Travelled (A)</th>
                <th rowspan="2">Total Fuel Used (B)</th>
                <th rowspan="2">km/L (C=A/B)</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Beginning</th>
                <th>Ending</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $data)
            <tr>
                <td class="text-left">{{ $data['vehicle']->description }}</td>
                <td>{{ $data['vehicle']->plate_number }}</td>
                <td class="text-left">{{ $data['driver']->name }}</td>
                <td>—</td>
                <td>—</td>
                <td>{{ number_format($data['total_distance'], 2) }}</td>
                <td>{{ number_format($data['total_fuel'], 2) }}</td>
                <td>
                    @if($data['total_fuel'] > 0)
                        {{ number_format($data['total_distance'] / $data['total_fuel'], 2) }}
                    @else
                        —
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sig-row">
        <div class="sig-box">
            <div class="sig-line">
                Prepared by:<br>
                <span class="sig-name">JONA GRACE N. LOYOLA</span><br>
                <span class="sig-title">Admin Services Aide</span>
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line">
                Verified and Found Correct:<br>
                <span class="sig-name">MR. LORENZO R. SISANTE</span><br>
                <span class="sig-title">DIVISION MANAGER - CMD</span>
            </div>
        </div>
    </div>

    {{-- ============= MROT per vehicle ============= --}}
    @foreach($summary as $data)
    <div class="page-break">

        <div class="mrot-header">
            <p>Republic of the Philippines</p>
            <h2>Trece Martires City Water District</h2>
            <p>Quintana Executive Homes, Trece Martires City, Cavite 4109</p>
            <br>
            <h2>MONTHLY REPORT OF OFFICIAL TRAVEL</h2>
        </div>

        <div class="mrot-info">
            <div class="item">
                <span class="lbl">Vehicle Plate No:</span>
                <span class="val">{{ $data['vehicle']->plate_number }}</span>
            </div>
            <div class="item">
                <span class="lbl">Driver's Name:</span>
                <span class="val">{{ strtoupper($data['driver']->name) }}</span>
            </div>
        </div>

        <table class="mrot-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>Distance Traveled (km)</th>
                    <th>Gasoline Consumed (Liters)</th>
                    <th>Oil Used (Liters)</th>
                    <th>Slip No.</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['slips'] as $slip)
                <tr>
                    <td>{{ strtoupper($slip->created_at->format('F j, Y')) }}</td>
                    <td>{{ $slip->distance_travelled ? number_format($slip->distance_travelled, 2) : '' }}</td>
                    <td>{{ $slip->fuel_used ? number_format($slip->fuel_used, 2) : '' }}</td>
                    <td></td>
                    <td>{{ $slip->slip_number }}</td>
                    <td class="text-left">{{ $slip->places_to_visit }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>TOTALS</td>
                    <td>{{ number_format($data['total_distance'], 2) }}</td>
                    <td>{{ number_format($data['total_fuel'], 2) }}</td>
                    <td></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <div class="certify">
            I hereby certify to the correctness of the above statements and that the motor vehicle
            was used strictly official business only.
        </div>

        <div class="sig-row">
            <div class="sig-box">
                <div class="sig-line">
                    <span class="sig-name">{{ strtoupper($data['driver']->name) }}</span><br>
                    <span class="sig-title">(Driver's Name and Signature)</span>
                </div>
            </div>
            <div class="sig-box">
                <div class="sig-line">
                    Approved by:<br>
                    <span class="sig-name">ENGR. EDVAN RHEY O. REYES</span><br>
                    <span class="sig-title">Admin. Services Chief</span>
                </div>
            </div>
        </div>

        <div class="footer-note">
            NOTE: This report should be accomplished in triplicate of which, supported by the originals
            of duly accomplished Driver's Record of travel (Form A) should be submitted, thru the
            Administrative Officer V or his equivalent, to the auditor concerned.
        </div>

    </div>
    @endforeach

</div>
</body>
</html>