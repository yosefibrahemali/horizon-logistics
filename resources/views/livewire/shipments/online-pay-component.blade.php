<div>
    <div class="background-dots"></div>

    <div class="max-w-4xl w-full mx-auto p-6 md:p-10 bg-white rounded-3xl shadow-2xl z-10 lg:flex lg:space-x-10">

        <!-- Left side: Shipment Details -->
        <div class="lg:w-1/2 mb-8 lg:mb-0">
            <!-- Logo section -->
            <div class="flex items-center mb-6">
                <!-- Placeholder for a logo -->
                <div class="w-12 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-lg font-bold mr-3">
                    <img src="/logo.png" alt="">
                </div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight" style="color: brown;">Horizon Logistics</h1>
            </div>

            <div class="space-y-3 text-sm text-gray-600">
                <p class="flex justify-between items-center">
                    <span class="font-medium">معرف الطلب:</span> 
                    <span id="orderId">#{{$shipment->id}}</span>
                </p>
                <p class="flex justify-between items-center">
                    <span class="font-medium">رقم التتبع:</span> 
                    <span id="orderId">{{$shipment->tracking_number}}</span>
                </p>
                <p class="flex justify-between items-center">
                    <span class="font-medium">عنوان الشحن:</span> 
                    <span id="shippingAddress">{{$shipment->city->name}}</span>
                </p>
                <hr class="border-gray-200">

                <p class="flex justify-between items-center">
                    <span class="font-medium">قيمة الشحنة:</span> 
                    <span id="subtotal" class="font-bold text-gray-800">
                        د.ل{{ number_format($shipment->shipment_cost, 2) }}
                    </span>
                </p>
                <p class="flex justify-between items-center">
                    <span class="font-medium">رسوم الشحن:</span> 
                    <span id="shippingFee" class="font-bold text-gray-800">
                        د.ل{{ number_format($shipment->shipping_cost, 2) }}
                    </span>
                </p>
                <p class="flex justify-between items-center">
                    <span class="font-medium">الضرائب:</span> 
                    <span id="tax" class="font-bold text-gray-800">
                        د.ل{{ number_format($shipment->tax, 2) }}
                    </span>
                </p>
            </div>

            <hr class="border-gray-300 my-4">

            <div class="flex justify-between items-center font-bold text-xl text-blue-600">
                <span>المبلغ الإجمالي:</span>
                <span id="totalAmount">
                       
                    د.ل{{ number_format($shipment->total_cost, 2) }}
                </span>
            </div>

        </div>

        <!-- Right side: Payment Form -->
       <div class="lg:w-1/2 flex items-center justify-center">
            <div id="paymentForm" class="space-y-6 w-full max-w-sm text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">طريقة الدفع</h2>
               @if($shipment->payment_status === "unpayed")
                    <button type="button" id="paymentButton"
                        class="glow-button w-full px-4 py-3 bg-gradient-to-r from-red-600 to-orange-500 text-white font-semibold rounded-xl shadow-lg hover:from-red-700 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:ring-opacity-50 transition-all duration-300 relative" onclick="startPayment()">

                        <!-- نص الزر العادي -->
                        
                        <span id="paymentText">
                            ادفع الآن - {{$shipment->total_cost}} د.ل . (البطاقات المحلية)
                        </span>
                        
                       


                        <!-- Loader -->
                        <span id="paymentLoader" class="hidden flex items-center justify-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>جارٍ تحميل بوابة 
                            الدفع...
                            </span>
                        </span>
                    </button>
                @else
                    <button type="button" id="paymentButton"
                        class="glow-button w-full px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50 transition-all duration-300 relative">

                        <!-- نص الزر -->
                        <span id="paymentText">
                              .....تمت عملية الدفع بنجاح
                        </span>

                    </button>

                    
                @endif

                 




            </div>
        </div>
    </div>

    <!-- Message box for success/error -->
    <div id="messageBox" class="fixed inset-x-0 bottom-4 flex justify-center z-50 hidden">
        <div id="messageText" class="px-6 py-3 rounded-lg shadow-lg text-white font-medium"></div>
    </div>


     <script>

            const shipmentTotal = {{ $shipment->total_cost * 1000 }}; // تحويل المبلغ إلى مليمات



            
            // دالة للحصول على التاريخ والوقت الحالي
            function getCurrentDateTime() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                return `${year}${month}${day}${hours}${minutes}${seconds}`;
            }

            // دالة لتوليد مرجع فريد للتاجر
            function generateMerchantReference() {
                return 'REF-' + Math.floor(Math.random() * 1000000);
            }

            // دالة لتوليد Secure Hash
            function generateSecureHash(amount, dateTimeLocalTrxn, merchantReference) {
                const secretKey = "3a488a89b3f7993476c252f017c488bb";// orginl "31643564383937632D356564342D343539362D383033622D623839393566383364643138";
                // const stringToHash = `Amount=${amount}&DateTimeLocalTrxn=${dateTimeLocalTrxn}&MerchantId=10040898689&MerchantReference=${merchantReference}&TerminalId=88529158`;

                const stringToHash = `Amount=${amount}&DateTimeLocalTrxn=${dateTimeLocalTrxn}&MerchantId=10081014649&MerchantReference=${merchantReference}&TerminalId=99179395`;

                const hash = CryptoJS.HmacSHA256(stringToHash, CryptoJS.enc.Hex.parse(secretKey));
                return hash.toString(CryptoJS.enc.Hex).toUpperCase();
            }
            

            // بدء عملية الدفع
            function startPayment() {
                const paymentButton = document.getElementById('paymentButton');
                const paymentText = document.getElementById('paymentText');
                const paymentLoader = document.getElementById('paymentLoader');

                paymentText.classList.add('hidden');
                paymentLoader.classList.remove('hidden');
                paymentButton.disabled = true;

                const amount = @json($shipment->total_cost * 1000); // مليمات صحيحة
                const dateTimeLocalTrxn = getCurrentDateTime();
                const merchantReference = generateMerchantReference();
                const secureHash = generateSecureHash(amount, dateTimeLocalTrxn, merchantReference);

                if (typeof Lightbox !== 'undefined' && Lightbox.Checkout) {
                    Lightbox.Checkout.configure = {
                        MID: "10081014649",
                        TID: "99179395",
                        AmountTrxn: amount,
                        MerchantReference: merchantReference,
                        TrxDateTime: dateTimeLocalTrxn,
                        SecureHash: secureHash,
                        completeCallback: function(data) {
                            console.log("تم الدفع بنجاح:", data);

                            // إرسال حدث Livewire لتحديث الحالة
                            Livewire.emit('paymentCompleted', @json($shipment->id), data);

                            paymentText.classList.remove('hidden');
                            paymentLoader.classList.add('hidden');
                            paymentButton.disabled = false;
                            alert("تم الدفع بنجاح!");
                        },
                        errorCallback: function(error) {
                                Livewire.emit('paymentCompleted', @json($shipment->id), error);

                            console.error("حدث خطأ أثناء الدفع:", error);
                            alert("حدث خطأ أثناء الدفع: " + error.message);
                            paymentText.classList.remove('hidden');
                            paymentLoader.classList.add('hidden');
                            paymentButton.disabled = false;
                        },
                        cancelCallback: function() {
                            Livewire.emit('paymentCompleted', @json($shipment->id));

                            console.log("تم إلغاء الدفع");
                            alert("تم إلغاء الدفع.");
                            paymentText.classList.remove('hidden');
                            paymentLoader.classList.add('hidden');
                            paymentButton.disabled = false;
                        }
                    };

                    Lightbox.Checkout.showLightbox();
                } else {
                    console.error("مكتبة Lightbox غير موجودة أو غير مُهيأة.");
                    paymentText.classList.remove('hidden');
                    paymentLoader.classList.add('hidden');
                    paymentButton.disabled = false;
                }
            }


    </script>
    {{-- @if($moamalat)

    @endif --}}
       
</div>