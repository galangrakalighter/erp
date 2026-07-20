@extends('layouts.app')

@section('title', 'Request Plat')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                SPK
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Daftar SPK.
            </p>
        </div>

        <button
            onclick="loadSpk()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

            Refresh

        </button>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white rounded-xl shadow border p-5">

            <p class="text-sm text-gray-500">
                Total SPK
            </p>

            <h2
                id="total-request"
                class="text-3xl font-bold mt-2">

                0

            </h2>

        </div>

        <div class="bg-white rounded-xl shadow border p-5">

            <p class="text-sm text-gray-500">
                Draft
            </p>

            <h2
                id="waiting-request"
                class="text-3xl font-bold text-yellow-500 mt-2">

                0

            </h2>

        </div>

        <div class="bg-white rounded-xl shadow border p-5">

            <p class="text-sm text-gray-500">
                Selesai
            </p>

            <h2
                id="approved-request"
                class="text-3xl font-bold text-green-600 mt-2">

                0

            </h2>

        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow border overflow-hidden">

        <div class="px-6 py-4 border-b">

            <h3 class="font-semibold">

                Daftar SPK

            </h3>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr class="text-left text-sm text-gray-600">

                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">SPK</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Action</th>

                    </tr>

                </thead>

                <tbody
                    id="request-table">

                    <tr>

                        <td colspan="7"
                            class="text-center py-10 text-gray-400">

                            Memuat data...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
const role = "{{ Auth::user()->role }}";
async function loadSpk(){

    const tbody=document.getElementById('request-table');

    tbody.innerHTML=`
        <tr>
            <td colspan="7" class="text-center py-8">
                Memuat...
            </td>
        </tr>
    `;

    const response=await fetch('/api/gudang/spk-all');

    const data=await response.json();

    console.log(data);

    document.getElementById('total-request').innerText=data.length;

    document.getElementById('waiting-request').innerText=
        data.filter(x=>x.status==0).length;

    document.getElementById('approved-request').innerText=
        data.filter(x=>x.status==1).length;

    if(data.length==0){

        tbody.innerHTML=`
            <tr>
                <td colspan="7"
                    class="text-center py-10 text-gray-400">
                    Tidak ada request.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML='';

    data.forEach((item,index)=>{
        console.log(item);

        const quotation = role === 'Gudang'
            ? item.quotation
            : item.warehouse?.quotation;

        let status='';

        if(item.status==0){

            status=`
                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs">
                    Draft
                </span>
            `;

        }else if(item.status==1){

            status=`
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                    Disiapkan
                </span>
            `;

        }

        tbody.innerHTML+=`

        <tr class="border-b hover:bg-gray-50">

            <td class="px-5 py-4">

                ${index+1}

            </td>

            <td class="px-5 py-4 font-medium">

                ${item.spk_number}

            </td>

            <td class="px-5 py-4">

                ${item.warehouse.quotation.nama_customer ?? '-'}

            </td>

            <td class="px-5 py-4">
                ${new Date(item.created_at).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                })}
            </td>

            <td class="px-5 py-4">

                ${status}

            </td>

            <td class="px-5 py-4">

                <div class="flex justify-center gap-2">

                    ${
                        item.status == 0
                        ?
                        `
                            <button
                                onclick="openSpkDetail(${item.id})"
                                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">

                                Detail SPK

                            </button>

                            <button
                                onclick="acceptSpk(${item.id})"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

                                Terima SPK

                            </button>
                        `
                        :
                        item.status == 1
                        ?
                        `
                            <button
                                onclick="openSpkDetail(${item.id})"
                                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">

                                Detail SPK

                            </button>

                            <button
                                onclick="cancelSpk(${item.id})"
                                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">

                                Batalkan

                            </button>
                        `
                        :
                        `
                            <button
                                onclick="openSpkDetail(${item.id})"
                                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">

                                Detail SPK

                            </button>
                        `
                    }

                </div>

            </td>

        </tr>

        `;

    });

}

function openSpkDetail(id) {

    if(role === 'gudang'){
        window.open(`/gudang/spk/${id}/pdf`, '_blank');
    }else{
        window.open(`/production/spk/${id}/pdf`, '_blank');
    }

}

async function acceptSpk(id){

    if(!confirm('Terima SPK ini?')){
        return;
    }

    try{
        const url = role === 'Gudang'
            ? `/api/gudang/spk/${id}/accept`
            : `/api/production/spk/${id}/accept`;

        const response = await fetch(url,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
            }
        });

        const result = await response.json();

        if(!response.ok){
            throw new Error(result.message);
        }

        alert(result.message);

        window.location.reload();

    }catch(err){

        alert(err.message);

    }

}

async function cancelSpk(id){

    if(!confirm('Batalkan pengerjaan SPK ini?')){
        return;
    }

    try{

        const url = role === 'gudang'
            ? `/api/gudang/spk/${id}/cancel`
            : `/api/production/spk/${id}/cancel`;

        const response = await fetch(url,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
            }
        });

        const result = await response.json();

        alert(result.message);

        window.location.reload();

    }catch(e){

        alert('Gagal membatalkan pengerjaan.');

    }

}

loadSpk();

</script>

@endsection