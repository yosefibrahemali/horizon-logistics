<div>
    @if (session()->has('success'))
        <div class="mb-4 px-4 py-2 bg-green-500 text-white rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 px-4 py-2 bg-red-500 text-white rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    <div class="container py-8">
   <header class="flex flex-col md:flex-row items-center justify-between mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 tracking-tight mb-4 md:mb-0">
            لوحة تحكم التوصيل
        </h1>
        <button id="openAddShipmentBtn" class="button-primary w-full md:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
            إضافة شحنة جديدة / مسح ضوئي
        </button>
    </header>

    <!-- Add Shipment Modal -->
    <div id="addShipmentModal" class="modal-overlay hidden">
        <div class="modal-content text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">إضافة شحنة جديدة</h3>
            <div class="mb-4">
                <label for="trackingNumber" class="block text-sm font-medium text-gray-700 mb-2">أدخل رقم التتبع</label>
                <input type="text" wire:model.defer="trackingNumber" id="trackingNumber" placeholder="رقم التتبع" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" />
            </div>
            <p class="text-gray-500 mb-4">أو مسح ضوئي بالكاميرا</p>
            <div id="qr-reader" class="w-full mb-4"></div>
            <div class="flex justify-between space-x-2 space-x-reverse">
                <button wire:click="addShipmentByTracking" id="confirmAddBtn" class="w-1/2 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">إضافة</button>
                <button id="cancelAddBtn" class="w-1/2 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400">إلغاء</button>
            </div>
        </div>
    </div>


    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('addShipmentModal');
            const trackingInput = document.getElementById('trackingNumber');
            const qrReaderDiv = document.getElementById('qr-reader');
            const openBtn = document.getElementById('openAddShipmentBtn');
            const cancelBtn = document.getElementById('cancelAddBtn');
            let html5QrcodeScanner;

            // فتح المودال بالزر
            openBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                startScanner();
            });

            // إغلاق المودال
            cancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                stopScanner();
            });

            function startScanner() {
                html5QrcodeScanner = new Html5Qrcode("qr-reader");
                Html5Qrcode.getCameras().then(cameras => {
                    if(cameras && cameras.length) {
                        html5QrcodeScanner.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: 250 },
                            (decodedText, decodedResult) => {
                                trackingInput.value = decodedText; // ملء رقم التتبع
                                stopScanner();
                            },
                            (errorMessage) => console.warn(errorMessage)
                        ).catch(err => console.error(err));
                    }
                }).catch(err => console.error(err));
            }

            function stopScanner() {
                if(html5QrcodeScanner) {
                    html5QrcodeScanner.stop().catch(() => {});
                }
            }

            // مثال: زر الإضافة يمكن إرسال الرقم مباشرة بالـ AJAX أو Form
            document.getElementById('confirmAddBtn').addEventListener('click', () => {
                const trackingNumber = trackingInput.value.trim();
                if(!trackingNumber) {
                    alert('الرجاء إدخال رقم التتبع!');
                    return;
                }
                // هنا يمكنك استدعاء AJAX أو Livewire action مباشرة
                console.log('إضافة شحنة برقم تتبع:', trackingNumber);
                modal.classList.add('hidden');
                stopScanner();
            });
        });
    </script>


    <main class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Current Shipment -->
        <div class="lg:col-span-1 card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-700">الشحنة الحالية</h2>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        @if($currentShipment)
                            @if($currentShipment->status == 'shipment_recived') bg-yellow-100 text-yellow-800
                            @elseif($currentShipment->status == 'on_way') bg-blue-100 text-blue-800
                            @elseif($currentShipment->status == 'delivered') bg-green-100 text-green-800
                            @elseif($currentShipment->status == 'returned') bg-red-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif
                        @else bg-gray-200 text-gray-800
                        @endif
                    ">
                        @if($currentShipment)
                            @if($currentShipment->status == 'shipment_recived') جاهز للتوصيل
                            @elseif($currentShipment->status == 'on_way') خارج للتوصيل
                            @elseif($currentShipment->status == 'delivered') تم التوصيل
                            @elseif($currentShipment->status == 'returned') استرجاع الشحنة
                            @else {{$currentShipment->status}} @endif
                        @else
                            لا يوجد
                        @endif
                    </span>

            </div>

            @if($currentShipment)
            <div class="space-y-4 text-gray-600">
                <p><span class="font-medium">رقم الشحنة:</span> {{ $currentShipment->id }}</p>
                <p><span class="font-medium">رقم تتبع الشحنة:</span> {{ $currentShipment->tracking_number }}</p>

                <p><span class="font-medium">العميل:</span> {{ $currentShipment->receiver_name }}</p>
                <p><span class="font-medium">العنوان:</span> <a href="{{$currentShipment->address}}">{{ $currentShipment->address ?? "N/A" }}</a></p>
                <p><span class="font-medium">رقم هاتف المستلم:</span> {{ $currentShipment->receiver_phone }}</p>
                <p><span class="font-medium">استلم من الزبون:</span> {{ number_format($currentShipment->total_cost) }}د.ل</p>
            </div>

           <div class="mt-8 flex flex-col space-y-4">
                @if($currentShipment->status != 'on_way' && $currentShipment->status != 'delivered')
                    <button wire:click="updateStatus('{{ $currentShipment->id }}','on_way')" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600">
                        خارج للتوصيل
                    </button>
                @endif

                @if($currentShipment->status != 'delivered')
                    <button wire:click="updateStatus('{{ $currentShipment->id }}','delivered')" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600">
                        تم التوصيل
                    </button>
                @endif

                @if($currentShipment->status != 'returned' && $currentShipment->status != 'delivered')
                    <button wire:click="updateStatus('{{ $currentShipment->id }}','returned')" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600">
                        استرجاع الشحنة
                    </button>
                @endif
            </div>

            @else
            <p class="text-gray-500 text-center mt-4">لا توجد شحنة حالية.</p>
            @endif
        </div>

        <!-- Shipments Table -->
        <div class="lg:col-span-2 card">
            <h2 class="text-2xl font-bold text-gray-700 mb-6">قائمة الشحنات</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الشحنة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم هاتف المستلم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($shipments as $shipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $shipment->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $shipment->receiver_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <a href="tel:{{ $shipment->receiver_phone }}" class="text-blue-500 hover:underline flex items-center space-x-1 rtl:space-x-reverse">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7-1 1 2 2 1-1 7 3v2a1 1 0 01-1 1h-2a17 17 0 01-12-12V5a1 1 0 011-1z" />
                                    </svg>
                                    {{ $shipment->receiver_phone }}
                                </a>
                            </td>


                            <td class="px-6 py-4 text-sm font-semibold
                                @if($shipment->status == 'shipment_recived') text-yellow-500
                                @elseif($shipment->status == 'on_way') text-blue-500
                                @else text-green-500 @endif
                            ">
                                @if($shipment->status == 'shipment_recived')
                                    تم استلام الشحنة
                                @elseif($shipment->status == 'on_way')
                                    خارج للتوصيل
                                @elseif($shipment->status == 'delivered')
                                    تم التوصيل
                                @elseif($shipment->status == 'returned')
                                    تم استرجاع الشحنة
                                @else
                                    {{ $shipment->status }}
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex items-center space-x-2 space-x-reverse table-cell-actions">
                                    {{-- <button wire:click="updateStatus('{{ $shipment->id }}','خارج للتوصيل')" class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full hover:bg-blue-200">تحديث</button> --}}
                                    <button wire:click="setCurrent('{{ $shipment->id }}')" class="px-3 py-1 bg-gray-100 text-gray-800 text-xs rounded-full hover:bg-gray-200">تعيين كـ حالية</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
</div>

<!-- Notification -->
<script>
    window.addEventListener('notify', event => {
        const message = event.detail.message;
        const type = event.detail.type;
        alert(message); // يمكنك استبدالها بمودال أو Toast أكثر أناقة
    });
</script>


</div>
