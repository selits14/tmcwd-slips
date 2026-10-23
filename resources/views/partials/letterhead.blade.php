@php
    $sealPath   = public_path('images/tmcwd-seal.png');
    $bpPath     = public_path('images/bagong-pilipinas.png');
    $sealB64    = file_exists($sealPath) ? base64_encode(file_get_contents($sealPath)) : null;
    $bpB64      = file_exists($bpPath)   ? base64_encode(file_get_contents($bpPath))   : null;
    $sealExt    = 'png';
@endphp

<table style="width:100%; border-collapse:collapse; margin-bottom:8px; border-bottom:3px double #000; padding-bottom:6px;">
    <tr>
        {{-- Left Logo --}}
        <td style="width:70px; text-align:center; vertical-align:middle;">
            @if($sealB64)
                <img src="data:image/{{ $sealExt }};base64,{{ $sealB64 }}"
                     style="width:60px; height:60px; object-fit:contain;">
            @else
                <div style="width:60px; height:60px; border:1px solid #ccc; display:inline-block;"></div>
            @endif
        </td>

        {{-- Center Text --}}
        <td style="text-align:center; vertical-align:middle; padding:0 10px;">
            <div style="font-size:8px; font-family:Arial;">Republic of the Philippines</div>
            <div style="font-size:13px; font-weight:bold; font-family:Arial; line-height:1.3;">
                TRECE MARTIRES CITY WATER DISTRICT
            </div>
            <div style="font-size:8px; font-family:Arial;">(LWUA CCC NO. 574)</div>
            <div style="font-size:7.5px; font-family:Arial; margin-top:2px;">
                Quintana Executive Homes, Brgy. San Agustin, City of Trece Martires
            </div>
            <div style="font-size:7.5px; font-family:Arial;">
                Contact No. (046) 419-2664 / 0917-805-0391
            </div>
            <div style="font-size:7.5px; font-family:Arial;">
                Email Add.: tmcwd@yahoo.com / customercare.tmcwd@gmail.com
            </div>
        </td>

        {{-- Right Logo --}}
        <td style="width:70px; text-align:center; vertical-align:middle;">
            @if($bpB64)
                <img src="data:image/{{ $sealExt }};base64,{{ $bpB64 }}"
                     style="width:60px; height:60px; object-fit:contain;">
            @else
                <div style="width:60px; height:60px; border:1px solid #ccc; display:inline-block;"></div>
            @endif
        </td>
    </tr>
</table>