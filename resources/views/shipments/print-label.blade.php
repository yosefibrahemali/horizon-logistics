<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Shipping Label</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @page {
            size: 100mm 150mm;
            margin: 5mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            
        }

        .label {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .header img {
            max-height: 40px;
            margin-bottom: 4px;
        }

        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 9pt;
        }

        .box {
            border: 1px solid #aaa;
            border-radius: 6px;
            padding: 6px;
            width: 48%;
        }

        .box h4 {
            margin: 0 0 4px;
            font-size: 9pt;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }

        .details {
            margin: 8px 0;
            border: 1px solid #aaa;
            border-radius: 6px;
            padding: 6px;
        }

        .barcode {
            text-align: center;
            margin: 10px 0;
        }

        .barcode img {
            width: 180px;
            height: 50px;
        }

       .icons {
            text-align: center;
            margin-top: 10px;
        }

        .icons i {
            font-size: 45px;   /* حجم مناسب للملصق */
            margin: 0 8px;
            color: black;      /* تقدر تغير للون أحمر لو تبيه */
        }


    </style>
</head>
<body onload="window.print()">
    <div class="label">
        <div class="header" style="display: flex; align-items: center; gap: 15px;">
            <img src="{{ asset('logo.png') }}" alt="Horizon logistics" style="height: 40px; margin-right: 10px; standard-width: auto;">
            <h2 style="margin: 0; font-size: 28px;color:rgb(132, 41, 41);">Shipping Post</h2>
        </div>



        <div class="info">
            <div><strong>Order No:</strong> {{ $shipment->id }}</div>
            <div><strong>Date:</strong> {{ $shipment->created_at->format('d/m/Y') }}</div>
        </div>

        <div class="info">
            <div class="box">
                <h4>From:</h4>
                <p>{{ $shipment->sender->name }}</p>
                {{-- <p>{{ $shipment->sender_address }}</p> --}}
                {{-- <p>{{ $shipment->origin_city }}</p> --}}
                <p>{{ $shipment->sender_phone }}</p>
            </div>
            <div class="box">
                <h4>To:</h4>
                <p>{{ $shipment->receiver_name }}</p>
                <p>{{ $shipment->receiver_address }}</p>
                <p>{{ $shipment->receiver_city }}</p>
                <p>{{ $shipment->receiver_phone }}</p>
            </div>
        </div>

        <div class="details">
            <p><strong>Weight:</strong> {{ $shipment->weight ?? 'N/A' }} kg</p>
            <p><strong>Description:</strong> {{ $shipment->shipment_description ?? 'No description' }}</p>
        </div>

        <div class="barcode">
           <center>{!! $barcode !!}</center> 
            <p><strong>Tracking #:</strong> {{ $shipment->tracking_number }}</p>
        </div>

       <div class="icons">
            <i class="fa-solid fa-umbrella" title="Keep Dry"></i>
            <i class="fa-solid fa-recycle" title="Recycle"></i>
            @if ($shipment->allowed_to_open_and_testing === 1)
            <i class="fa-solid fa-arrow-right-arrow-left"></i>
            @endif
            @if ($shipment->is_fragile === 1)
            <i class="fa-solid fa-wine-glass-empty" title="Fragile"></i>
            @endif
        </div>
    </div>
</body>
</html>
