<x-app-layout>
    <div class="py-8 bg-gray-100 dark:bg-slate-900 min-h-screen font-sans flex justify-center no-print">
        <div class="w-full max-w-[850px]">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('inventory.issue.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-sm font-medium">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm transition shadow-md font-medium">
                    <i class="fa-solid fa-print mr-1"></i> Print Voucher
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
            opacity: 0.05;
        }
        .watermark-bg img {
            width: 40%;
            max-height: 800px;
            object-fit: contain;
            filter: grayscale(100%);
        }
        .watermark-bg span {
            font-size: 80px;
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
            .c-table tr td { padding-top: 2px !important; padding-bottom: 1px !important; line-height: 1.3 !important; }
            .c-table { margin-bottom: 2px !important; }
        }
    </style>

    @php
        // QR Code & Barcode Generate
        $qrUrl = route('inventory.issue.show', $issue->id);
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qrUrl);
        $barcodeApiUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . ($issue->voucher_no ?? 'N/A') . "&code=Code128&dpi=96";

        $totalQuantity = $issue->items->sum('quantity');
    @endphp

    <template id="invoiceTemplate">
        <div class="classic-invoice">

            <div class="watermark-bg">
                @if(isset($company) && $company->invoice_watermark_logo)
                    <img src="{{ filter_var($company->invoice_watermark_logo, FILTER_VALIDATE_URL) ? $company->invoice_watermark_logo : asset('storage/' . $company->invoice_watermark_logo) }}" alt="Watermark Logo">
                @else
                    <span>INTERNAL ISSUE</span>
                @endif
            </div>

            <div class="classic-content">
                <div class="c-header" style="text-align: center; margin-bottom: 5px;">
                    <h1 style="margin: 0; font-size: 22px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                        {{ $company->invoice_title ?? config('app.name', 'SHOE ERP') }}
                    </h1>
                </div>

                <div class="c-contact" style="text-align: center; font-size: 13px; margin-bottom: 10px;">
                    @if(isset($company) && $company->address)
                        <div style="margin-bottom: 3px;"><strong>Address:</strong> {{ $company->address }}</div>
                    @endif
                    <div style="font-size: 12px; color: #222;">
                        @if(isset($company) && $company->phone)
                            <strong>Phone:</strong> {{ $company->phone }}
                        @endif
                        @if(isset($company) && $company->email)
                            <span style="margin: 0 5px; color: #888;">|</span> <strong>Email:</strong> {{ $company->email }}
                        @endif
                    </div>
                </div>

                <table class="c-table" style="margin-bottom: 10px;">
                    <tbody>
                    <tr>
                        <td colspan="3" style="background: #b4b2b2; text-align: center; padding: 6px 0; font-weight: bold; -webkit-print-color-adjust: exact; text-transform: uppercase; letter-spacing: 1px;">
                            Material Issue Voucher
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding: 5px 5px 0 0;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="width: 100px; font-weight:bold; padding: 1px 0;">Issued To:</td><td style="padding: 1px 0; font-weight:bold;">{{ $staff->name ?? 'Unknown' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Designation:</td><td style="padding: 1px 0;">{{ $staff->designation ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Phone:</td><td style="padding: 1px 0;">{{ $staff->phone ?? 'N/A' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Staff ID:</td><td style="padding: 1px 0;">{{ $staff->id ?? 'N/A' }}</td></tr>
                            </table>
                        </td>

                        <td style="width: 20%; text-align: center; vertical-align: top; padding: 10px 0 0 0;">
                            <img src="{{ $barcodeApiUrl }}" alt="Barcode" style="max-width: 100%; padding-top: 2px; max-height: 35px; margin: 0 auto; display: block;">
                        </td>

                        <td style="width: 40%; vertical-align: top; padding: 5px 0 0 5px;">
                            <table class="c-table" style="margin-bottom: 0;">
                                <tr><td class="nowrap" style="width: 90px; font-weight:bold; padding: 1px 0;">Voucher No:</td><td style="padding: 1px 0; font-weight:bold;">{{ $issue->voucher_no }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Issue Date:</td><td style="padding: 1px 0;">{{ \Carbon\Carbon::parse($issue->date)->format('d-M-Y') }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Prepared By:</td><td style="padding: 1px 0;">{{ optional($issue->creator)->name ?? 'System' }}</td></tr>
                                <tr><td class="nowrap" style="font-weight:bold; padding: 1px 0;">Remarks:</td><td style="padding: 1px 0;">{{ $issue->remarks ?? 'N/A' }}</td></tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="c-table dotted-table" width="100%" cellspacing="0" cellpadding="0" style="font-size: 13px;">
                    <thead>
                    <tr>
                        <th style="width: 40px; padding: 4px;">SL.</th>
                        <th style="text-align: left; padding: 4px;">Material Description (Code)</th>
                        <th style="text-align: left; padding: 4px;">Location / Batch</th>
                        <th style="width: 80px; padding: 4px;">Unit</th>
                        <th style="width: 100px; text-align: right; padding: 4px;">Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($issue->items) && $issue->items->count() > 0)
                        @foreach($issue->items as $index => $item)
                            <tr>
                                <td style="text-align: center; padding: 4px;">{{ $index + 1 }}</td>
                                <td style="padding: 4px;">
                                    {{ optional($item->product)->name ?? 'Unknown Product' }}
                                    @if(optional($item->product)->code)
                                        <span style="font-size: 11px; color: #555;">({{ $item->product->code }})</span>
                                    @endif
                                </td>
                                <td style="padding: 4px; font-size: 12px;">
                                    {{ optional(optional($item->stock)->location)->name ?? 'N/A' }}
                                    @if(optional($item->stock)->batch_no)
                                        <br><span style="font-size: 11px; color: #555;">Batch: {{ $item->stock->batch_no }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center; padding: 4px;">{{ optional(optional($item->product)->unit)->name ?? 'Unit' }}</td>
                                <td style="text-align: right; padding: 4px; font-weight: bold;">{{ number_format($item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4px;">No materials found</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; padding: 4px;">Total Quantity Issued :</td>
                        <td style="text-align: right; font-weight: bold; padding: 4px;">{{ number_format($totalQuantity, 2) }}</td>
                    </tr>
                    </tbody>
                </table>

                <div style="margin-top: 40px; display: table; width: 100%;">
                    <div style="display: table-cell; width: 33%; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Prepared By
                        </strong>
                    </div><br><br>



                    <div style="display: table-cell; width: 33%; text-align: right; vertical-align: bottom;">
                        <strong class="nowrap" style="border-top: 1px dashed #000; padding: 2px 10px; font-size: 13px;">
                            Receiver Signature ({{ $staff->name ?? 'Staff' }})
                        </strong>
                    </div>
                </div>

                <div style="border-top: 1px solid #000; width: 100%; margin-top: 10px;"></div>

                <table class="c-table" style="margin-bottom: 0; margin-top: 2px; width: 100%; border-collapse: collapse;">
                    <tbody>
                    <tr>
                        <td style="font-size: 10px; text-align: left; padding: 2px 0;">Printed On: {{ date('d-M-Y h:i A') }}</td>
                        <td style="font-size: 10px; text-align: right; padding: 2px 0;">
                            <i>Software Developed By: moniruddin.com | 01644-871 968</i>
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
