<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>

        @page{
            margin:20px;
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

        .title{
            text-align:center;
            font-size:22px;
            font-weight:bold;
        }

        .subtitle{
            text-align:center;
            margin-bottom:20px;
        }

        .border{
            border:1px solid #000;
        }

        .border td,
        .border th{
            border:1px solid #000;
            padding:7px;
        }

        .label{
            width:160px;
            font-weight:bold;
            vertical-align:top;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .bold{
            font-weight:bold;
        }

    </style>

</head>

<body>

<div class="title">

    FAKTUR PEMBAYARAN

</div>

<div class="subtitle">

    No Faktur :

    FKT/{{ date('y') }}/{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}

</div>

<table>

    <tr>

        <td class="label">

            Tanggal

        </td>

        <td>

            {{ date('d F Y') }}

        </td>

    </tr>

    <tr>

        <td class="label">

            No. SPK

        </td>

        <td>

            NFM/{{ date('y') }}-{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}

        </td>

    </tr>

</table>

<br>

<table>

    <tr>

        <td class="label">

            Sudah diterima dari

        </td>

        <td>

            {{ $po->nama_pemesan }}

        </td>

    </tr>

    <tr>

        <td></td>

        <td>

            {{ $po->alamat_pemesan }}

        </td>

    </tr>

    <tr>

        <td class="label">

            Untuk Pembayaran

        </td>

        <td>

            {{ $po->judul_cetak }}

        </td>

    </tr>

</table>

<br>

<table class="border">

    <thead>

        <tr>

            <th width="70%">

                Keterangan

            </th>

            <th>

                Nominal

            </th>

        </tr>

    </thead>

    <tbody>

        <tr>

            <td>

                Pembayaran pekerjaan

                <b>{{ $po->judul_cetak }}</b>

                ukuran

                {{ $po->ukuran }}

            </td>

            <td class="text-right">

                Rp {{ number_format($po->total_order,0,',','.') }}

            </td>

        </tr>

    </tbody>

</table>

<br>

<table>

    <tr>

        <td width="55%">

            <b>Terbilang</b>

            <br><br>

            {{ $po->terbilang }}

        </td>

        <td width="45%">

            <table>

                <tr>

                    <td class="bold">

                        Total Pembayaran

                    </td>

                    <td class="text-right bold">

                        Rp {{ number_format($po->total_order,0,',','.') }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

<br>

<table>

    <tr>

        <td>

            Catatan :

            <br><br>

            {{ $po->keterangan }}

        </td>

    </tr>

</table>

<br><br><br>

<table>

    <tr>

        <td width="60%"></td>

        <td class="text-center">

            Diterima Oleh

            <br><br><br><br><br>

            ______________________

        </td>

    </tr>

</table>

</body>

</html>