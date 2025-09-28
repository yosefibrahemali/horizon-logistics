

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        /* Gradient background for the main card */
        .payment-card {
            background-image: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
        }

        /* Custom glow for the payment button */
        .glow-button {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .glow-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px brown;
        }

        /* For the animated background */
        .background-dots {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .input-field {
            transition: all 0.2s;
        }
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }
    </style>
</head>
<body class="bg-gray-100">

    @livewire('shipments.online-pay-component', ['shipment' => $shipment])<body>

  


    @livewireScripts

   

    <script src="https://tnpg.moamalat.net:6006/js/lightbox.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

</body>
</html>