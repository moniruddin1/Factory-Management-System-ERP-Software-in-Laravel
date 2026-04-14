<x-app-layout>
    <div class="py-8 bg-gray-100 dark:bg-slate-900 min-h-screen font-sans flex justify-center no-print">
        <div class="w-full max-w-[850px]">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('purchases.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-sm font-medium">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-md font-medium">
                    <i class="fa-solid fa-print mr-1"></i> Print Invoice
                </button>
            </div>

            <div class="bg-white shadow-2xl rounded-xl overflow-hidden p-6 relative" id="webPreview">
            </div>
        </div>
    </div>

    <div class="print-only-wrapper" id="printPreview">
    </div>

    <style>
        .print-only-wrapper { display: none; }

        /* Classic Invoice Styles */
        .classic-invoice {
            width: 750px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 15px;
            color: #161a1e;
            font-family: Arial, sans-serif;
            position: relative;
            background: #fff;
            overflow: hidden;
        }

        /* Watermark */
        .watermark-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
            user-select: none;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0.10;
        }
        .watermark-bg img {
            width: 40%;
            max-height: 800px;
            object-fit: contain;
            filter: grayscale(100%);
        }
        .watermark-bg span {
            font-size: 120px;
            font-weight: 900;
            text-transform: uppercase;
            transform: rotate(-45deg);
            white-space: nowrap;
            color: #e5e7eb;
        }

        /* Content Z-Index */
        .classic-content { position: relative; z-index: 10; }

        .c-header h1 { text-align: center; text-transform: uppercase; margin: 0; font-size: 24px; font-weight: bold;}
        .c-address, .c-contact { text-align: center; font-size: 14px; margin-bottom: 5px; }

        /* Table Resets to fix print borders */
        .c-table { width: 100%; font-size: 13px; line-height: 1.2; margin-bottom: 15px; border-collapse: collapse;}

        /* এখানে হিডেন বর্ডারগুলো জোরপূর্বক হাইড করা হয়েছে */
        .c-table:not(.dotted-table) th,
        .c-table:not(.dotted-table) td,
        .c-table:not(.dotted-table) tr {
            border: none !important;
            padding: 3px 5px;
        }

        /* Dotted Table specifically for Items */
        .dotted-table { border: 1px dotted #000 !important; }
        .dotted-table th, .dotted-table td { border: 1px dotted #000 !important; padding: 6px; }
        .dotted-table th { text-align: center; font-weight: bold; }

        .c-outstanding-table { width: 100%; border-collapse: collapse; }
        .c-outstanding-table td { padding: 3px 5px; border: none !important; }
        .c-outstanding-table tr { border: none !important; }

        /* Helper for Print text wrapping */
        .nowrap { white-space: nowrap !important; }

        /* Print Media Query */
        @media print {
            .no-print { display: none !important; }
            .print-only-wrapper { display: block; }

            /* পেজ সাইজ এবং মার্জিন সেট করা হলো */
            @page {
                margin: 10mm;
                size: A4 portrait;
            }

            body, html {
                margin: 0 !important;
                padding: 0 !important;
                background-color: #fff !important;
                width: 100% !important; /* 800px এর বদলে 100% দেওয়া হলো */
                min-width: auto !important;
            }

            ::-webkit-scrollbar { display: none; }

            .classic-invoice {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important; /* প্রিন্টে আউটার বর্ডার না রাখাই ভালো দেখায় */
                padding: 0 !important;
                box-shadow: none;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid;
            }

            /* প্রিন্টে টেবিলের অহেতুক গ্যাপ কমানোর জন্য */
            .c-table th, .c-table td {
                line-height: 1.1 !important;
            }

            .dotted-table th, .dotted-table td {
                padding: 4px !important; /* প্রিন্টে প্যাডিং কিছুটা কমিয়ে দেওয়া হলো */
            }

            hr { border-color: #000 !important; }

            /* প্রিন্টে টেবিলের লাইন গ্যাপ কমানোর জন্য */
            .c-table tr td {
                padding-top: 2px !important;
                padding-bottom: 1px !important;
                line-height: 1.3 !important; /* লাইন হাইট একদম কমিয়ে দেওয়া হলো */
            }

            /* সাপ্লায়ার এবং কাস্টমার ডিটেইলস টেবিলের মার্জিন রিসেট */
            .c-table {
                margin-bottom: 2px !important;
            }

            /* হিসাবের টেবিলের লাইন গ্যাপ কমানোর জন্য */
            .c-outstanding-table td {
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                line-height: 1.6 !important; /* আরও কমাতে চাইলে 1.0 দিতে পারেন */
            }
        }

    </style>

    @php
        // আগের ধাপের Signed Route পদ্ধতি ব্যবহার করলে কিউআর লিংক এমন হবে
        $qrUrl = URL::signedRoute('qrinvoice.preview', ['invoice_no' => $purchase->invoice_no ?? 'N/A']);
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qrUrl);

        // Barcode API URL (এই লাইনটি মিসিং থাকার কারণেই এররটি আসছিল)
        $barcodeApiUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . ($purchase->invoice_no ?? 'N/A') . "&code=Code128&dpi=96";

        $grandTotal = $purchase->grand_total ?? 0;

        // Custom helper function to convert numbers to words
        if (!function_exists('amountToWords')) {
            function amountToWords($number) {
                $number = floor($number);
                if ($number == 0) return 'Zero';

                $words = [
                    0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                    10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
                    20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
                ];

                if ($number < 20) {
                    return $words[$number];
                } elseif ($number < 100) {
                    return $words[10 * floor($number / 10)] . ($number % 10 ? ' ' . $words[$number % 10] : '');
                } elseif ($number < 1000) {
                    return $words[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . amountToWords($number % 100) : '');
                } elseif ($number < 100000) {
                    return amountToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . amountToWords($number % 1000) : '');
                } elseif ($number < 10000000) {
                    return amountToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . amountToWords($number % 100000) : '');
                } else {
                    return amountToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . amountToWords($number % 10000000) : '');
                }
            }
        }

        try {
            $inWords = amountToWords($purchase->total_amount) . ' Taka Only.';
        } catch (\Exception $e) {
            $inWords = number_format($purchase->total_amount, 2) . ' Taka Only.';
        }
    @endphp

    <template id="invoiceTemplate">
        <div class="classic-invoice">

            <div class="watermark-bg">
                @if(isset($company) && $company->invoice_watermark_logo)
                    @if(filter_var($company->invoice_watermark_logo, FILTER_VALIDATE_URL))
                        <img src="{{ $company->invoice_watermark_logo }}" alt="Watermark Logo">
                    @else
                        <img src="{{ asset('storage/' . $company->invoice_watermark_logo) }}" alt="Watermark Logo">
                    @endif
                @elseif(isset($company) && $company->invoice_watermark_text)
                    <span>{{ $company->invoice_watermark_text }}</span>
                @else
                    <span>DUPLICATE</span>
                @endif
            </div>

            <div class="classic-content">
                <div class="c-header" style="text-align: center; margin-bottom: 5px;">
                    <h1 style="margin: 0; font-size: 22px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                        {{ $company->invoice_title ?? 'SHOE ERP' }}
                    </h1>
                </div>

                <div class="c-contact" style="text-align: center; font-size: 13px; margin-bottom: 10px;">
                    @if(isset($company) && $company->address)
                        <div style="margin-bottom: 3px;">
                            <strong>Address:</strong> {{ $company->address }}
                        </div>
                    @endif
                    <div style="font-size: 12px; color: #222;">
                        @if(isset($company) && $company->phone)
                            <strong>Phone:</strong> {{ $company->phone }}
                        @endif

                        @if(isset($company) && $company->email)
                            <span style="margin: 0 5px; color: #888;">|</span>
                            <strong>Email:</strong> {{ $company->email }}
                        @endif

                        @if(isset($company) && $company->website)
                            <span style="margin: 0 5px; color: #888;">|</span>
                            <strong>Web:</strong> {{ $company->website }}
                        @endif
                    </div>
                </div>

                <table class="c-table" style="margin-bottom: 10px;">
                    <tbody>
                    <tr>
                        <td colspan="3" style="background: #b4b2b2; text-align: center; padding: 6px 0; font-weight: bold; -webkit-print-color-adjust: exact;">
                            Purchase Invoice/Bill
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding: 5px 5px 0 0;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="width: 110px; font-weight:bold; padding: 1px 0;">Invoice No:</td><td style="padding: 1px 0;">{{ $purchase->invoice_no ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="width: 100px; font-weight:bold; padding: 1px 0;">Supplier ID:</td><td style="padding: 1px 0;">{{ $purchase->supplier_id ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Supplier Name:</td><td style="padding: 1px 0;">{{ optional($purchase->supplier)->company_name ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Phone:</td><td style="padding: 1px 0;">{{ optional($purchase->supplier)->phone ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Address:</td><td style="padding: 1px 0;">{{ optional($purchase->supplier)->address ?? 'N/A' }}</td></tr>
                            </table>
                        </td>

                        <td style="width: 20%; text-align: center; vertical-align: top; padding: 10px 0 0 0;">
                            <img src="{{ $barcodeApiUrl }}" alt="Barcode" style="max-width: 100%; padding-top: 2px; max-height: 35px; margin: 0 auto; display: block;">
                        </td>

                        <td style="width: 40%; vertical-align: top; padding: 5px 0 0 5px;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Date:</td><td style="padding: 1px 0;">{{ isset($purchase->purchase_date) ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') : 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Ref. No:</td><td style="padding: 1px 0;">{{ $purchase->reference_no ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Invoice Type:</td><td style="padding: 1px 0;">{{ $purchase->invoice_type ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Order Status:</td><td style="padding: 1px 0; font-weight:bold;">{{ $purchase->status ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Payment Method:</td><td style="padding: 1px 0;">{{ $purchase->payment_method ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Bill By:</td><td style="padding: 1px 0;">{{ optional($purchase->creator)->name ?? 'System' }}</td></tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="c-table dotted-table" width="100%" cellspacing="0" cellpadding="0" style="font-size: 12px;">
                    <thead>
                    <tr>
                        <th style="width: 40px; padding: 2px 4px;">No.</th>
                        <th style="text-align: left; padding: 2px 4px;">Product Description</th>
                        <th style="width: 80px; padding: 2px 4px;">Unit</th>
                        <th style="width: 80px; padding: 2px 4px;">Quantity</th>
                        <th class="nowrap" style="width: 90px; text-align: right; padding: 2px 4px;">Unit Price</th>
                        <th style="width: 100px; text-align: right; padding: 2px 4px;">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($purchase->items) && $purchase->items->count() > 0)
                        @foreach($purchase->items as $index => $item)
                            <tr>
                                <td style="text-align: center; padding: 2px 4px;">{{ $index + 1 }}</td>
                                <td style="padding: 2px 4px;">{{ optional($item->product)->name ?? 'Unknown Product' }}</td>
                                <td style="text-align: center; padding: 2px 4px;">{{ optional($item->product->unit)->name ?? 'N/A' }}</td>
                                <td style="text-align: center; padding: 2px 4px;">{{ number_format($item->quantity ?? 0, 2) }}</td>
                                <td style="text-align: right; padding: 2px 4px;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                <td style="text-align: right; padding: 2px 4px;">{{ number_format($item->total_price ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2px 4px;">No items found</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" style="text-align: left; font-weight: bold; font-style: italic; padding: 2px 4px;">
                            In Words: {{ $inWords }}
                        </td>

                        <td class="nowrap" style="text-align: right; font-weight: bold; padding: 2px 4px;">Total Amount</td>
                        <td style="text-align: right; font-weight: bold; padding: 2px 4px;">{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                    </tr>
                    </tbody>
                </table>

                @php
                    // এই ট্রানজেকশনের সময় মোট কত টাকা রিসিভ হয়েছে তা ডাটাবেস থেকে ডাইনামিক বের করা
                    // যেহেতু সব পেমেন্ট একই সাথে সেভ হয়েছে, তাই created_at সময়টা একই থাকবে
                    $total_cash_given = \App\Models\SupplierPayment::where('supplier_id', $purchase->supplier_id)
                        ->where('created_at', $purchase->created_at)
                        ->sum('amount');

                    // যদি কোনো কারণে created_at একদম সেম না হয়, তাহলে অন্তত current paid_amount দেখাবে
                    if($total_cash_given == 0) {
                        $total_cash_given = $purchase->paid_amount;
                    }

                    // নোট থেকে পেমেন্ট ডিস্ট্রিবিউশন আলাদা করা (যাতে আলাদা বক্সে সুন্দর করে দেখানো যায়)
                    $general_note = $purchase->note;
                    $distribution_note = '';

                    if (strpos($general_note, '--- Payment Distribution ---') !== false) {
                        $parts = explode('--- Payment Distribution ---', $general_note);
                        $general_note = trim($parts[0]);
                        $distribution_note = trim($parts[1]);
                    }
                @endphp

                <div style="width: 100%; display: table; padding: 0; font-size: 14px;">
                    <div style="display: table-cell; width: 45%; vertical-align: top; padding-right: 15px; padding-top: 10px">
                        <table style="border: 1px solid #000; width: 100%; border-collapse: collapse;" class="c-outstanding-table">
                            <tbody>
                            @if(isset($purchase->discount) && $purchase->discount > 0)
                                <tr>
                                    <td class="nowrap" style="width: 60%; padding: 1px 4px;">Discount</td>
                                    <td class="nowrap" style="width: 40%; text-align: right; padding: 1px 4px;">- {{ number_format($purchase->discount, 2) }}</td>
                                </tr>
                            @endif

                            @php
                                $tax = $purchase->tax_amount ?? 0;
                                $shipping = $purchase->shipping_cost ?? 0;
                                $other = $purchase->other_charges ?? 0;
                                $total_extra = $tax + $shipping + $other;
                            @endphp

                            @if($total_extra > 0)
                                <tr>
                                    <td class="nowrap" style="padding: 1px 4px;">Tax/Shipping/Other</td>
                                    <td class="nowrap" style="text-align: right; padding: 1px 4px;">+ {{ number_format($total_extra, 2) }}</td>
                                </tr>
                            @endif

                            @if(isset($purchase->round_adjustment) && $purchase->round_adjustment != 0)
                                <tr>
                                    <td class="nowrap" style="padding: 1px 4px;">Adjustment</td>
                                    <td class="nowrap" style="text-align: right; padding: 1px 4px;">{{ number_format($purchase->round_adjustment, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="nowrap" style="width: 60%; padding: 1px 4px;">Purchase Amount</td>
                                <td class="nowrap" style="width: 40%; text-align: right; padding: 1px 4px;">{{ number_format($purchase->grand_total ?? 0, 2) }}</td>
                            </tr>

                            <tr>
                                <td class="nowrap" style="padding: 1px 4px; font-weight: bold;">Total Cash Given</td>
                                <td class="nowrap" style="text-align: right; padding: 1px 4px; font-weight: bold;">{{ number_format($total_cash_given, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="nowrap" style="padding: 1px 4px; color: #555;">Applied to this Invoice</td>
                                <td class="nowrap" style="text-align: right; padding: 1px 4px; color: #555;">{{ number_format($purchase->paid_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="nowrap" style="border-top: 2px solid #ccc !important; font-weight:bold; padding: 1px 4px;">Current Invoice Due</td>
                                <td class="nowrap" style="border-top: 2px solid #ccc !important; text-align: right; font-weight:bold; padding: 1px 4px;">{{ number_format($purchase->due_amount ?? 0, 2) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- Payment Distribution Details Box (যদি এক্সট্রা পেমেন্ট থাকে তবেই এটি শো করবে) --}}
                        @if(!empty($distribution_note))
                            <div style="margin-top: 10px; border: 1px dashed #28a745; padding: 8px; background-color: #f8fff9; border-radius: 4px;">
                                <strong style="color: #28a745; font-size: 12px; text-transform: uppercase;">Payment Distribution:</strong>
                                <p style="margin: 5px 0 0 0; font-size: 13px; line-height: 1.5; color: #333;">
                                    {!! nl2br(e($distribution_note)) !!}
                                </p>
                            </div>
                        @endif

                        {{-- General Note Box (ইউজার যদি বিল করার সময় আলাদা কোনো নোট দিয়ে থাকে) --}}
                        @if(!empty($general_note))
                            <div style="margin-top: 10px; font-size: 12px; color: #555;">
                                <strong>Note:</strong> {{ $general_note }}
                            </div>
                        @endif

                    </div>

                    <div style="display: table-cell; width: 10%;"></div>

                    <div style="display: table-cell; width: 45%; vertical-align: bottom;">
                        <table class="c-table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                            <thead>
                            <tr><td colspan="2" style="padding: 0;"><hr style="margin: 0; border: 1px solid #000;"></td></tr>
                            <tr>
                                <th class="nowrap" style="text-align: right; padding: 4px 15px 0 0; font-size: 14px;">Grand Total :</th>
                                <th class="nowrap" style="text-align: right; padding: 4px 0 0 0; font-size: 16px; width: 40%;">৳ {{ number_format($purchase->grand_total ?? 0, 2) }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 25px; display: table; width: 100%;">
                    <div style="display: table-cell; width: 33%; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Receiver's Signature
                        </strong>
                    </div>

                    <div style="display: table-cell; width: 34%; text-align: center; vertical-align: bottom;">
                        <img src="{{ $qrApiUrl }}" alt="QR Code" style="width: 70px; height: 70px; margin: 0 auto; margin-bottom: 2px;">
                        <div style="font-size: 10px; color: #555;">Scan to verify</div>
                    </div>

                    <div style="display: table-cell; width: 33%; text-align: right; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Manager Signature
                        </strong>
                    </div>
                </div>

                <div style="border-top: 1px solid #000; width: 100%; margin-top: 10px;"></div>

                <table class="c-table" style="margin-bottom: 0; margin-top: 2px; width: 100%; border-collapse: collapse;">
                    <tbody>
                    <tr>
                        <td style="font-size: 10px; text-align: left; padding: 2px 0;">Printing: {{ date('d-M-Y h:i A') }}</td>
                        <td style="font-size: 10px; text-align: right; padding: 2px 0;">
                            <i>Software Developed By: moniruddin.com 01644-871 968</i>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </template>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const template = document.getElementById('invoiceTemplate').innerHTML;
            document.getElementById('webPreview').innerHTML = template;
            document.getElementById('printPreview').innerHTML = template;
        });
    </script>
</x-app-layout>
