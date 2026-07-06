<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>

        @page{
            margin:25px;
        }

        body{
            font-family: DejaVu Sans,sans-serif;
            font-size:12px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .border{
            border:1px solid #000;
        }

        .border td,
        .border th{
            border:1px solid #000;
            padding:7px;
        }

        .title{
            text-align:center;
            font-size:24px;
            font-weight:bold;
            margin-bottom:3px;
        }

        .subtitle{
            text-align:center;
            margin-bottom:20px;
        }

        .label{
            width:140px;
            font-weight:bold;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .bold{
            font-weight:bold;
        }

    </style>

</head>

<body>

<div class="title">
    INVOICE
</div>

<div class="subtitle">
    No. Invoice :
    INV/{{ date('y') }}/{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}
</div>

<table>

    <tr>
        <td class="label">No. SPK</td>
        <td>
            NFM/{{ date('y') }}-{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}
        </td>
    </tr>

    <tr>
        <td class="label">Tanggal</td>
        <td>{{ date('d F Y') }}</td>
    </tr>

</table>

<br>

<b>Kepada Yth.</b>

<br><br>

<b>{{ $po->nama_pemesan }}</b>

<br>

{{ $po->alamat_pemesan }}

<br><br>

<table class="border">

    <thead>

        <tr>

            <th width="5%">No</th>

            <th>Deskripsi</th>

            <th width="15%">Qty</th>

            <th width="20%">Harga</th>

            <th width="20%">Subtotal</th>

        </tr>

    </thead>

    <tbody>

        @php
            $grandTotal = 0;
        @endphp

        @foreach($po->details as $detail)

            @php

                $qty = $detail->jumlah_beli;

                $harga = $po->harga_per_box;

                $subtotal = $qty * $harga;

                $grandTotal += $subtotal;

            @endphp

            <tr>

                <td class="text-center">
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $detail->barang->barang }}
                </td>

                <td class="text-center">
                    {{ number_format($qty) }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($harga,0,',','.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($subtotal,0,',','.') }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>

<br>

<table>

    <tr>

        <td width="60%"></td>

        <td width="40%">

            <table>

                <tr>

                    <td>Total</td>

                    <td class="text-right">

                        Rp {{ number_format($po->total_order,0,',','.') }}

                    </td>

                </tr>

                <tr>

                    <td>DP</td>

                    <td class="text-right">

                        Rp {{ number_format($po->uang_muka,0,',','.') }}

                    </td>

                </tr>

                <tr>

                    <td class="bold">

                        Sisa Tagihan

                    </td>

                    <td class="text-right bold">

                        Rp {{ number_format($po->sisa_pembayaran,0,',','.') }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

<br>

<b>Terbilang :</b>

<br>

{{ $po->terbilang }}

<br><br>

<b>Pembayaran :</b>

<br>

Bank BCA

<br>

1234567890

<br>

a.n PT Nusa Fortuna Mandiri

<br><br>

<b>Catatan :</b>

<br>

{{ $po->keterangan }}

<br><br><br><br>

<table>

    <tr>

        <td width="50%"></td>

        <td class="text-center">

            Hormat Kami

            <br><br><br><br><br>

            ________________________

        </td>

    </tr>

</table>

</body>

</html>