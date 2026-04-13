@php
    // API দিয়ে ডাইনামিক QR কোড জেনারেট (URL টির শেষে ইনভয়েস নম্বর যোগ হবে)
    $qrUrl = url('/qrinvoice/' . $purchase->invoice_no);
    $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qrUrl);

    // API দিয়ে ডাইনামিক Barcode জেনারেট
    $barcodeApiUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . $purchase->invoice_no . "&code=Code128&dpi=96";
@endphp

<div class="classic-invoice">
    <div class="watermark-bg">
        @if(isset($company) && $company->invoice_watermark_logo)
            <img src="{{ asset('storage/' . $company->invoice_watermark_logo) }}" alt="Watermark Logo">
        @elseif(isset($company) && $company->invoice_watermark_text)
            <span>{{ $company->invoice_watermark_text }}</span>
        @else
            <span>DUPLICATE</span>
        @endif
    </div>

    <div class="classic-content">
        <div class="c-header">
            <h1>{{ $company->name ?? 'Company Name' }}</h1>
        </div>

        <div class="c-contact" style="margin-bottom: 20px;">
            @if(isset($company) && $company->address)
                <div class="c-address"><strong>Address:</strong> {{ $company->address }}</div>
            @endif
            <div>
                @if(isset($company) && $company->phone)
                    &nbsp;Phone : {{ $company->phone }}
                @endif
                @if(isset($company) && $company->email)
                    &nbsp;&nbsp;Email : {{ $company->email }}
                @endif
                @if(isset($company) && $company->website)
                    &nbsp;&nbsp;Web : {{ $company->website }}
                @endif
            </div>
        </div>

        <table class="c-table">
            <tbody>
            <tr>
                <td colspan="4" style="background: #b4b2b2; text-align: center; padding: 7px 0; font-weight: bold; -webkit-print-color-adjust: exact;">
                    Purchase Invoice/Bill
                </td>
            </tr>
            <tr>
                <td style="width: 15%; font-weight:bold;">Invoice No:</td>
                <td style="width: 35%;">{{ $purchase->invoice_no }}</td>
                <td style="width: 15%; font-weight:bold;">Date:</td>
                <td style="width: 35%;">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Supplier ID:</td>
                <td>{{ $purchase->supplier_id }}</td>
                <td style="font-weight:bold;">Bill By:</td>
                <td>{{ optional($purchase->creator)->name ?? 'System' }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Supplier Name:</td>
                <td>{{ optional($purchase->supplier)->company_name ?? 'N/A' }}</td>
                <td colspan="2" rowspan="3" style="text-align: center; vertical-align: top; padding-top: 5px;">
                    <img src="{{ $barcodeApiUrl }}" alt="Barcode" style="max-height: 40px; margin: 0 auto;">
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Supplier Phone:</td>
                <td>{{ optional($purchase->supplier)->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Supplier Address:</td>
                <td>{{ optional($purchase->supplier)->address ?? 'N/A' }}</td>
            </tr>
            </tbody>
        </table>

        <table class="c-table dotted-table" width="100%" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th style="width: 50px; border-right: none; border-left: none;">SL. No.</th>
                <th style="border-right: none; border-left: none;">Product Description</th>
                <th style="width: 80px; border-right: none;">Quantity</th>
                <th style="width: 90px; border-right: none;">Unit Price</th>
                <th style="width: 100px;">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($purchase->items as $index => $item)
                <tr>
                    <td style="text-align: center; border-right: none; border-left: none;">{{ $index + 1 }}</td>
                    <td style="border-right: none; border-left: none;">
                        {{ optional($item->product)->name ?? 'Unknown Product' }}
                    </td>
                    <td style="text-align: center; border-right: none;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right; border-right: none;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; border-left: 1px dotted #000;">Total Amount</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($purchase->total_amount, 2) }}</td>
            </tr>
            </tbody>
        </table>

        <div style="width: 100%; display: table;">
            <div style="display: table-cell; width: 40%; vertical-align: top;">
                <table class="c-outstanding-table">
                    <tbody>
                    @if($purchase->discount > 0)
                        <tr>
                            <td style="width: 50%;">Discount</td>
                            <td style="width: 50%; text-align: right;">- {{ number_format($purchase->discount, 2) }}</td>
                        </tr>
                    @endif
                    @if($purchase->tax_amount > 0 || $purchase->shipping_cost > 0 || $purchase->other_charges > 0)
                        <tr>
                            <td>Tax/Shipping</td>
                            <td style="text-align: right;">+ {{ number_format($purchase->tax_amount + $purchase->shipping_cost + $purchase->other_charges, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="width: 50%;">Purchase Amount</td>
                        <td style="width: 50%; text-align: right;">{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Collections (Paid)</td>
                        <td style="text-align: right;">{{ number_format($purchase->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #ccc; font-weight:bold;">Net Outstanding (Due)</td>
                        <td style="border-top: 1px solid #ccc; text-align: right; font-weight:bold;">{{ number_format($purchase->due_amount, 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div style="display: table-cell; width: 20%;"></div>

            <div style="display: table-cell; width: 40%; vertical-align: bottom;">
                <table class="c-table">
                    <thead>
                    <tr><td colspan="2"><hr style="margin: 0; border: 1px solid #eee;"></td></tr>
                    <tr>
                        <th style="text-align: left; padding-bottom: 0px; font-size: 16px;">Grand Total</th>
                        <th style="text-align: right; padding-bottom: 0px; font-size: 18px;">৳ {{ number_format($purchase->grand_total, 2) }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div style="margin-top: 40px; display: table; width: 100%;">
            <div style="display: table-cell; width: 33%; vertical-align: bottom;">
                <strong style="border-top: 2px solid #ccc; padding: 5px 20px;">Receiver's Signature</strong>
            </div>

            <div style="display: table-cell; width: 34%; text-align: center; vertical-align: bottom;">
                <img src="{{ $qrApiUrl }}" alt="QR Code" style="width: 80px; height: 80px; margin: 0 auto; margin-bottom: 10px;">
                <div style="font-size: 11px; color: #555;">Scan to verify</div>
            </div>

            <div style="display: table-cell; width: 33%; text-align: right; vertical-align: bottom;">
                <strong style="border-top: 2px solid #ccc; padding: 5px 20px;">Manager Signature</strong>
            </div>
        </div>

        <div style="border-top: 1px solid #000; width: 100%; margin-top: 15px;"></div>

        <table class="c-table" style="margin-bottom: 0; margin-top: 5px;">
            <tbody>
            <tr>
                <td style="font-size: 11px;">SERVICE: 01XXXXXXXXX</td>
                <td style="text-align: center; font-size: 11px;">Printing: {{ date('Y-m-d h:i A') }}</td>
                <td style="text-align: right; font-size: 11px;"><i>Developed By: 01644871968</i></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
