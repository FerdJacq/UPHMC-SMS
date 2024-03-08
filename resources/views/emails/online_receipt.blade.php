<div class="container">
    <div class="card-container">
        <!-- <div class="w-100 text-center">
            <img src="https://ltodriverseducation.com/images/edgecomm.png" class="edgecomm-logo" alt="Logo">
        </div> -->
        <div class="card">
            <table class="w-100" style="margin-bottom:10px;">
                <tr>
                    <!-- <td class="img-container">
                        <img src='http://taxengine.psmed.org{{$data["ServiceProvider"]["logo"]}}' class="img-logo"/>
                    </td> -->
                    <td class="w-100">
                        <div class="font-2r w-100">{{ ucwords($data["ServiceProvider"]["company_name"]) }}</div>
                        <div class="w-100">{{ ucwords($data["ServiceProvider"]["address"]) }}</div>
                        <div class="w-100">Registered TIN: {{ ucwords($data["ServiceProvider"]["tin"]) }}</div>
                    </td>
                </tr>
            </table>
            <table >
                <tr>
                    <td class="w-75 or-title">SAMPLE TRANSACTION RECEIPT</td>
                    <td></td>
                </tr>
            </table>
            <div class="row">
                <h1>Customer Information</h1>
            </div>
            <table class="text-nowrap">
                <tr>
                    <td></td>
                    <td class="font-bold">Date</td>
                    <td class="text-right">{{  date("M d, Y", strtotime($data["completed_date"])) }}</td>
                </tr>
                <tr>
                    <td class="w-60"><h3>{{ ucwords($data["customer"]["full_name"]) }}</h3></td>
                    <td class="font-bold">Trx No</td>
                    <td class="text-right"><a href="{!! env('APP_URL') !!}/verify/?or_number={!! $data['or_number'] !!}">#{{ $data["or_number"] }}</a></td>
                </tr>
            </table>
            <div class="table-item-container">
                <table class="table table-item">
                    <thead>
                        <th class="w-10">#</th>
                        <th class="w-50">Item Decsription</th>
                        <th class=text-nowrap>Unit Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach ($data["details"] as $item)
                        <tr>
                            <td class="text-center">{{ $loop->index+1}}</td>
                            <td class="text-left">{{ $item["item"] }}</td>
                            <td class="text-right">{{ number_format($item["unit_price"],2) }}</td>
                            <td class="text-center">{{ number_format($item["qty"]) }}</td>
                            <td class="text-right">{{ number_format($item["total_price"],2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <table>
                <tr>
                    <td>

                    </td>
                    <td class="w-50">
                        <table class="table-sm text-nowrap">
                            <tr>
                                <td class="text-bold w-50">Subtotal</td>
                                <td class="text-right">{{number_format($data["subtotal_amount"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Shipping Fee</td>
                                <td class="text-right">{{number_format($data["shipping_fee"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Voucher</td>
                                <td class="text-right">{{number_format($data["voucher"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Coins</td>
                                <td class="text-right">{{number_format($data["coins"],2)}}</td>
                            </tr>
                            <tr class="font-header">
                                <td class="text-left">Total</td>
                                <td class="text-right">{{number_format($data["total_amount"],2)}}</td>
                            </tr>
                        </table>

                        <table class="table-sm text-nowrap">
                            <!-- <tr>
                                <td class="text-bold">Transaction Fee</td>
                                <td class="text-right">19,203</td>
                            </tr>
                            <tr>
                                <td class="text-bold w-50">Service Fee</td>
                                <td class="text-right">19,203</td>
                            </tr>
                            <tr>
                                <td class="text-bold w-50">Commission Fee</td>
                                <td class="text-right">19,203</td>
                            </tr> -->
                            <tr>
                                <td class="text-bold">Online Platform VAT</td>
                                <td class="text-right">{{number_format($data["online_platform_vat"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Shipping VAT</td>
                                <td class="text-right">{{number_format($data["shipping_vat"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Item VAT</td>
                                <td class="text-right">{{number_format($data["item_vat"],2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Withholding Tax</td>
                                <td class="text-right">{{number_format($data["withholding_tax"],2)}}</td>
                            </tr>
                            <tr class="bg-template font-header">
                                <td class="text-left">Total Taxes Due</td>
                                <td class="text-right">{{number_format($data["tax"],2)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="footer">©2023 {{ config('app.name') }}. All rights reserved.</div>
    </div>
</div>

<style>
    .img-container{
        height: 100px; /* Set the initial height of the table cell */
        /* max-height:100px; */
        position: relative;
        width:100px;
    }
    .p-relative{
        position: relative;
    }
    .pl-1{
        padding-left:1.5rem;
    }
    .img-logo{
        /* height: 100%; 
        width: 100%; */
        height:100px;
        width:100px;
        display: block;
        object-fit: contain;
        background: #aaa3a3;
        border-radius: 50%;
        border: 1px solid #aaa3a3;
        /* border:1px solid black; */
    }
    .text-nowrap{
        white-space: nowrap;
    }
    .font-2r{
        font-size:2rem;
        font-weight:600;
    }
    h3{
        font-size:1.5rem;
    }
    h1{
        font-size:1.75rem;
    }
    .footer{
        margin-top:10px !important;
    }
    .font-header{
        font-size:1.25rem;
        font-weight: 700;
    }
    .table-item-container{
        min-height: 400px;
        border:2px solid black;
        margin-top:20px;
    }
    .table-item {
        width: 100%;
        border-collapse: collapse;
    }
    .table-item thead th {
        background-color:#0b060c;
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
        color: white;
    }
    .table-item tbody td {
      border: 1px solid #dee2e6;
      padding: 8px;
    }
    .bg-template{
        background:#0b060c;
        color:white;
    }
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
        border-collapse: collapse;
    }
    thead{
        display: table-header-group;
        vertical-align: middle;
        border-color: inherit;
    }
    tbody td{
        padding:5px;
    }
    th{
        padding:10px;
    }
    .w-10{
        width:10%;
    }
    .w-90{
        width:90%;
    }
    .table-sm{
        border-spacing: 0;
        border-collapse: separate;
    }
    .table-sm tbody td{
        padding:2px 5px 2px 5px;
    }
    .table-sm tr{
        border-bottom: 1px solid #dee2e6;
    }
    h3{
        margin:0;
    }
    .w-50{
        width:50%;
    }
    .text-right{
        text-align:right;
    }
    .text-center{
        text-align: center;
    }
    .text-left{
        text-align: left;
    }
    .font-bold{
        font-weight: 600;
    }
    .row{
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }
    .w-60{
        width:60%;
    }
    .col-12{
        width:100%;
    }

    .w-100{
        width:100%;
    }
    .w-75{
        width:75%;
    }

    table{
        width:100%;
    }

    .container{
        /* max-width:900px; */
        min-height:800px;
        background:#edf2f7;
        padding:20px;
        width:100%;
        font-family:Karla,Google Sans,sans-serif;
        font-size:1rem;
        color:#292b30;
    }

    .card-container{
        max-width:900px;
        /* min-width:900px; */
        min-width:450px;
        padding:10px;
        width:100%;
        margin-left:auto;
        margin-right:auto;
    }

    .card{
        background:white;
        width:100%;
        /* max-height:800px; */
        padding:20px;
    }

    .or-title{
        background:#0b060c;
        color:white;
        padding:10px;
        font-weight:600;
        font-size:1.5rem;
        text-align:center;
    }
    .edgecomm-logo{
        max-width: 100%;
        /* width:100%; */
        /* height: 75px;
        max-height: 75px; */
        margin-bottom:10px;
    }
</style>