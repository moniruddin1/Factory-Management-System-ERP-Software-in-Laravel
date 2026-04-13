<x-app-layout>
    <div class="py-8 bg-gray-100 dark:bg-slate-900 min-h-screen font-sans flex justify-center no-print">
        <div class="w-full max-w-[850px]">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('purchase-returns.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-sm font-medium">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-md font-medium">
                    <i class="fa-solid fa-print mr-1"></i> Print Return Voucher
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
            font-size: 100px;
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

            @page {
                margin: 10mm;
                size: A4 portrait;
            }

            body, html {
                margin: 0 !important;
                padding: 0 !important;
                background-color: #fff !important;
                width: 100% !important;
                min-width: auto !important;
            }

            ::-webkit-scrollbar { display: none; }

            .classic-invoice {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid;
            }

            .c-table th, .c-table td { line-height: 1.1 !important; }
            .dotted-table th, .dotted-table td { padding: 4px !important; }
            hr { border-color: #000 !important; }

            .c-table tr td {
                padding-top: 2px !important;
                padding-bottom: 1px !important;
                line-height: 1.3 !important;
            }

            .c-table { margin-bottom: 2px !important; }

            .c-outstanding-table td {
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                line-height: 1.6 !important;
            }
        }
    </style>

    @php
        // Barcode API URL for Return No
        $barcodeApiUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . ($return->return_no ?? 'N/A') . "&code=Code128&dpi=96";

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
            $inWords = amountToWords($return->total_return_amount) . ' Taka Only.';
        } catch (\Exception $e) {
            $inWords = number_format($return->total_return_amount, 2) . ' Taka Only.';
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
                    <span>RETURNED</span>
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
                        <td colspan="3" style="background: #b4b2b2; text-align: center; padding: 6px 0; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; -webkit-print-color-adjust: exact;">
                            Purchase Return / Debit Note
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding: 5px 5px 0 0;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="width: 110px; font-weight:bold; padding: 1px 0;">Return No:</td><td style="padding: 1px 0; font-weight:bold;">{{ $return->return_no ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="width: 100px; font-weight:bold; padding: 1px 0;">Supplier ID:</td><td style="padding: 1px 0;">{{ $return->supplier_id ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Supplier Name:</td><td style="padding: 1px 0;">{{ optional($return->supplier)->company_name ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Phone:</td><td style="padding: 1px 0;">{{ optional($return->supplier)->phone ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Address:</td><td style="padding: 1px 0;">{{ optional($return->supplier)->address ?? 'N/A' }}</td></tr>
                            </table>
                        </td>

                        <td style="width: 20%; text-align: center; vertical-align: top; padding: 10px 0 0 0;">
                            <img src="{{ $barcodeApiUrl }}" alt="Barcode" style="max-width: 100%; padding-top: 2px; max-height: 35px; margin: 0 auto; display: block;">
                        </td>

                        <td style="width: 40%; vertical-align: top; padding: 5px 0 0 5px;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Date:</td><td style="padding: 1px 0;">{{ isset($return->return_date) ? \Carbon\Carbon::parse($return->return_date)->format('d-M-Y') : 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Orig. Invoice:</td><td style="padding: 1px 0;">{{ optional($return->purchase)->invoice_no ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Reason:</td><td style="padding: 1px 0;">{{ $return->reason ?? 'Not Provided' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Status:</td><td style="padding: 1px 0; font-weight:bold;">Returned</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Created By:</td><td style="padding: 1px 0;">{{ optional($return->creator)->name ?? 'System' }}</td></tr>
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
                        <th class="nowrap" style="width: 90px; text-align: right; padding: 2px 4px;">Unit Price</th>
                        <th style="width: 80px; padding: 2px 4px;">Return Qty</th>
                        <th style="width: 100px; text-align: right; padding: 2px 4px;">Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($return->items) && $return->items->count() > 0)
                        @foreach($return->items as $index => $item)
                            <tr>
                                <td style="text-align: center; padding: 2px 4px;">{{ $index + 1 }}</td>
                                <td style="padding: 2px 4px;">{{ $item->product_name ?? 'Unknown Product' }}</td>
                                <td style="text-align: right; padding: 2px 4px;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                <td style="text-align: center; font-weight: bold; padding: 2px 4px;">{{ number_format($item->return_qty ?? 0, 2) }}</td>
                                <td style="text-align: right; padding: 2px 4px;">{{ number_format($item->total_price ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2px 4px;">No items found</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" style="text-align: left; font-weight: bold; font-style: italic; padding: 2px 4px;">
                            In Words: {{ $inWords }}
                        </td>

                        <td class="nowrap" style="text-align: right; font-weight: bold; padding: 2px 4px;">Grand Total</td>
                        <td style="text-align: right; font-weight: bold; padding: 2px 4px;">{{ number_format($return->total_return_amount ?? 0, 2) }}</td>
                    </tr>
                    </tbody>
                </table>

                <div style="width: 100%; display: table; padding: 0; font-size: 14px;">
                    <div style="display: table-cell; width: 45%; vertical-align: top; padding-right: 15px; padding-top: 10px">
                        <table style="border: 1px solid #000; width: 100%; border-collapse: collapse;" class="c-outstanding-table">
                            <tbody>
                            <tr>
                                <td class="nowrap" style="width: 60%; padding: 1px 4px;">Total Returned Amount</td>
                                <td class="nowrap" style="width: 40%; text-align: right; padding: 1px 4px;">{{ number_format($return->total_return_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="nowrap" style="border-top: 2px solid #ccc !important; font-weight:bold; padding: 1px 4px;">Deducted from Ledger</td>
                                <td class="nowrap" style="border-top: 2px solid #ccc !important; text-align: right; font-weight:bold; padding: 1px 4px;">{{ number_format($return->total_return_amount ?? 0, 2) }}</td>
                            </tr>
                            </tbody>
                        </table>

                        @if($return->note)
                            <div style="margin-top: 8px; font-size: 12px; font-style: italic;">
                                <strong>Note:</strong> {{ $return->note }}
                            </div>
                        @endif
                    </div>

                    <div style="display: table-cell; width: 10%;"></div>

                    <div style="display: table-cell; width: 45%; vertical-align: bottom;">
                        <table class="c-table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                            <thead>
                            <tr><td colspan="2" style="padding: 0;"><hr style="margin: 0; border: 1px solid #000;"></td></tr>
                            <tr>
                                <th class="nowrap" style="text-align: right; padding: 4px 15px 0 0; font-size: 14px;">Total Return Value :</th>
                                <th class="nowrap" style="text-align: right; padding: 4px 0 0 0; font-size: 16px; width: 40%;">৳ {{ number_format($return->total_return_amount ?? 0, 2) }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 40px; display: table; width: 100%;">
                    <div style="display: table-cell; width: 33%; text-align: left; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Supplier's Signature
                        </strong>
                    </div>

                    <div style="display: table-cell; width: 34%; text-align: center; vertical-align: bottom;">
                    </div>

                    <div style="display: table-cell; width: 33%; text-align: right; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Authorized Signature
                        </strong>
                    </div>
                </div>

                <div style="border-top: 1px solid #000; width: 100%; margin-top: 15px;"></div>

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
