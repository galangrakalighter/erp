<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>

        @page{
            margin:20px;
        }

        body{
            font-family:DejaVu Sans,sans-serif;
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
            padding:6px;
        }

        .label{
            width:140px;
            font-weight:bold;
            vertical-align:top;
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

    SURAT JALAN

</div>

<div class="subtitle">

    No. Surat Jalan :

    SJ/{{ date('y') }}/{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}

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

            No. PO

        </td>

        <td>

            PO/{{ date('y') }}-{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}

        </td>

    </tr>

    <tr>

        <td class="label">

            Salesman

        </td>

        <td>

            {{ $po->salesman }}

        </td>

    </tr>

</table>

<br>

<table>

    <tr>

        <td class="label">

            Dikirim Kepada

        </td>

        <td>

            <b>{{ $po->nama_tempat }}</b>

        </td>

    </tr>

    <tr>

        <td></td>

        <td>

            {{ $po->alamat_tempat }}

        </td>

    </tr>

</table>

<br>

<table class="border">

    <thead>

        <tr>

            <th width="5%">

                No

            </th>

            <th>

                Nama Barang

            </th>

            <th width="15%">

                Qty

            </th>

            <th width="15%">

                Satuan

            </th>

        </tr>

    </thead>

    <tbody>

        @foreach($po->details as $detail)

            <tr>

                <td class="text-center">

                    {{ $loop->iteration }}

                </td>

                <td>

                    {{ $detail->barang->barang }}

                </td>

                <td class="text-center">

                    {{ number_format($detail->jumlah_beli) }}

                </td>

                <td class="text-center">

                    Roll

                </td>

            </tr>

        @endforeach

    </tbody>

</table>

<br>

<table>

    <tr>

        <td class="label">

            Judul Cetak

        </td>

        <td>

            {{ $po->judul_cetak }}

        </td>

    </tr>

    <tr>

        <td class="label">

            Ukuran

        </td>

        <td>

            {{ $po->ukuran }}

        </td>

    </tr>

    <tr>

        <td class="label">

            Jumlah Box

        </td>

        <td>

            {{ number_format($po->jumlah_box) }} Box

        </td>

    </tr>

    <tr>

        <td class="label">

            Keterangan

        </td>

        <td>

            {{ $po->keterangan }}

        </td>

    </tr>

</table>

<br><br><br>

<table>

    <tr>

        <td class="text-center">

            Pengirim

            <br><br><br><br><br>

            _______________________

        </td>

        <td class="text-center">

            Sopir

            <br><br><br><br><br>

            _______________________

        </td>

        <td class="text-center">

            Penerima

            <br><br><br><br><br>

            _______________________

        </td>

    </tr>

</table>

</body>

</html>