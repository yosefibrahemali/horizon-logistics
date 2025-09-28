<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم التوصيل</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            padding: 1rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
        }
        .button-primary {
            transition: all 0.2s;
        }
        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .table-cell-actions button {
            transition: all 0.2s;
        }
        .table-cell-actions button:hover {
            transform: translateY(-1px);
        }
        /* Custom dot background */
        .background-dots {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }
        .modal-content {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
            width: 90%;
            max-width: 500px;
        }
    </style>
</head>
<body class="bg-gray-100 text-right">
   


    @livewire('delivery-men.index-component', ['shipments' => $shipments, 'currentShipment' => $currentShipment])
    {{-- <div class="background-dots"></div>

    <div class="container py-8">
        <header class="flex flex-col md:flex-row items-center justify-between mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 tracking-tight mb-4 md:mb-0">
                لوحة تحكم التوصيل
            </h1>
            <button id="addShipmentBtn" class="button-primary w-full md:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
                إضافة شحنة جديدة / مسح ضوئي
            </button>
        </header>

        <main class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Current Shipment Widget -->
            <div class="lg:col-span-1 card">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-700">الشحنة الحالية</h2>
                    <span id="currentStatus" class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-full">لا يوجد</span>
                </div>
                <div id="currentShipmentDetails" class="space-y-4 text-gray-600">
                    <!-- This section will be populated by your backend -->
                    <p class="text-gray-500 text-center">يرجى تحديد شحنة حالية.</p>
                </div>
                <div id="currentShipmentActions" class="mt-8 flex flex-col space-y-4 ">
                    <!-- These action buttons will be handled by your backend -->

                    <button id="outForDeliveryBtn" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600">
                        خارج للتوصيل
                    </button>
                    <button id="deliveredBtn" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600">
                        تم التوصيل
                    </button>
                </div>
            </div>

            <!-- Shipments Table -->
            <div class="lg:col-span-2 card">
                <h2 class="text-2xl font-bold text-gray-700 mb-6">قائمة الشحنات</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الشحنة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="shipmentsTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Your / loop will go here to populate the table rows -->
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">SHP-001</td>
                                <td class="px-6 py-4 text-sm text-yellow-500 font-semibold">جاهز للتوصيل</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex items-center space-x-2 space-x-reverse table-cell-actions">
                                        <button class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full hover:bg-blue-200">
                                            تحديث
                                        </button>
                                        <button class="px-3 py-1 bg-gray-100 text-gray-800 text-xs rounded-full hover:bg-gray-200">
                                            تعيين كـ حالية
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Shipment Modal -->
    <div id="addShipmentModal" class="modal-overlay hidden">
        <div class="modal-content text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">إضافة شحنة جديدة</h3>
            <div class="mb-4">
                <label for="trackingNumber" class="block text-sm font-medium text-gray-700 mb-2">أدخل رقم التتبع</label>
                <input type="text" id="trackingNumber" placeholder="رقم التتبع" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" />
            </div>
            <p class="text-gray-500 mb-4">أو</p>
            <button id="scanButton" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 mb-4">
                مسح ضوئي بالكاميرا
            </button>
            <div class="flex justify-between space-x-2 space-x-reverse">
                <button id="confirmAddBtn" class="w-1/2 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    إضافة
                </button>
                <button id="cancelAddBtn" class="w-1/2 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- Message box -->
    <div id="messageBox" class="fixed inset-x-0 bottom-4 flex justify-center z-50 hidden">
        <div id="messageText" class="px-6 py-3 rounded-lg shadow-lg text-white font-medium"></div>
    </div> --}}

</body>
</html>