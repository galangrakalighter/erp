<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid #000;
        }

        .border td,
        .border th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .label {
            width: 140px;
            font-weight: bold;
        }

        .section {
            margin-top: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 11px;
        }
        .label{
            width:140px;
            font-weight:bold;
            vertical-align:top;
        }

        .alamat{
            border-bottom:1px dashed #000;
            padding-bottom:4px;
        }
    </style>

</head>

<body>

    <div class="title">
        BUKTI PESANAN BARANG
    </div>

    <div class="subtitle">
        No. SPK :
        NFM/{{ date('y') }}-{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}
    </div>

    <table style="width:100%;">

        <tr>
            <td class="label">Nama Pemesan</td>
            <td>{{ $po->nama_pemesan }}</td>
        </tr>

        <tr>
            <td></td>
            <td>
                <div style="width:450px;border-bottom:1px dashed #000;padding-bottom:3px;">
                    {{ $po->alamat_pemesan }}
                </div>
            </td>
        </tr>

        <tr>
            <td class="label">Dikirim Kepada</td>
            <td>{{ $po->nama_tempat }}</td>
        </tr>

        <tr>
            <td></td>
            <td>{{ $po->alamat_tempat }}</td>
        </tr>

    </table>

    <div style="margin-top:15px;"></div>

    <table style="width:100%;">

        <tr>

            <!-- KIRI -->
            <td width="50%" valign="top">

                <table style="width:100%;">

                    <tr>
                        <td class="label">Judul Cetak</td>
                        <td>: {{ $po->judul_cetak }}</td>
                    </tr>

                    <tr>
                        <td class="label">Ukuran</td>
                        <td>: {{ $po->ukuran }}</td>
                    </tr>

                    <tr>
                        <td class="label" valign="top">Jenis Kertas</td>
                        <td valign="top">
                            :
                            <ul style="margin:0;padding-left:18px;">
                                @foreach($po->details as $detail)
                                    <li>{{ $detail->barang->barang }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>

                    

                    <tr>
                        <td class="label">Isi / Box</td>
                        <td>: {{ $po->isi_per_box }}</td>
                    </tr>

                    <tr>
                        <td class="label">Jumlah Box</td>
                        <td>: {{ number_format($po->jumlah_box) }}</td>
                    </tr>

                    <tr>
                        <td class="label">Harga / Box</td>
                        <td>: Rp {{ number_format($po->harga_per_box,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td class="label">Uang Muka</td>
                        <td>: Rp {{ number_format($po->uang_muka,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td class="label">Sisa Bayar</td>
                        <td>: Rp {{ number_format($po->sisa_pembayaran,0,',','.') }}</td>
                    </tr>

                    <tr>
                        <td class="label">Terbilang</td>
                        <td>: {{ $po->terbilang }}</td>
                    </tr>

                    <tr>
                        <td class="label">Keterangan</td>
                        <td>: {{ $po->keterangan }}</td>
                    </tr>

                </table>

            </td>

            <!-- KANAN -->
            <td width="50%" valign="top">

                <table style="width:100%;">

                    <tr>
                        <td class="label">Tanggal Pesan</td>
                        <td>: {{ $po->tanggal_pesan }}</td>
                    </tr>

                    <tr>
                        <td class="label">No. PO</td>
                        <td>
                            :
                            PO/{{ date('y') }}-{{ date('m') }}/{{ str_pad(rand(1,9999),4,'0',STR_PAD_LEFT) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Jumlah Ply</td>
                        <td>: {{ $po->jumlah_ply }}</td>
                    </tr>

                    <tr>
                        <td class="label">Perporasi</td>
                        <td>: {{ $po->perporasi }}</td>
                    </tr>

                    <tr>
                        <td class="label">Banyaknya</td>
                        <td>: {{ number_format($po->jumlah_box) }} Box</td>
                    </tr>

                    <tr>
                        <td class="label">Total Order</td>
                        <td>
                            : <strong>Rp {{ number_format($po->total_order,0,',','.') }}</strong>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">No. Film</td>
                        <td>: {{ $po->no_film }}</td>
                    </tr>

                    <tr>
                        <td class="label">Salesman</td>
                        <td>: {{ $po->salesman }}</td>
                    </tr>

                    <tr>
                        <td class="label">Cabang</td>
                        <td>: {{ $po->cabang }}</td>
                    </tr>

                    <tr>
                        <td class="label">Tipe Order</td>
                        <td>: {{ $po->tipe_pemesanan }}</td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <div style="margin-top:15px;"></div>

</body>

</html>