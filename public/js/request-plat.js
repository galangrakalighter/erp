const API_BASE_URL = 'http://127.0.0.1:8000';
const role = document.getElementById('role').value;
async function requestPlat(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/plat/request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer YOUR_TOKEN' // Sesuaikan dengan sistem auth Anda
            },
            body: JSON.stringify({ 
                plat_id: id,
                timestamp: new Date().toISOString() 
            })
        });

        if (!response.ok) {
            throw new Error(`Gagal meminta plat: ${response.statusText}`);
        }

        const data = await response.json();
       showNotification("Berhasil Meminta Plat " + data.data.quotation_number);
        
        // Refresh tabel saja
        await loadQuotations();
    } catch (error) {
        console.error('Error saat requestPlat:', error);
        throw error;
    }
}

function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-area'); // Pastikan ID ini ada di HTML
    const div = document.createElement('div');
    
    // Styling mengikuti tema Laravel (menggunakan Tailwind)
    const colorClass = type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
    
    div.className = `${colorClass} p-4 rounded-xl border mb-4 animate-in fade-in slide-in-from-top-2 duration-300`;
    div.innerText = message;
    
    container.appendChild(div);
    
    // Hapus pesan otomatis setelah 3 detik
    setTimeout(() => {
        div.remove();
    }, 1500);
}

async function cancelPlat(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/plat/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer YOUR_TOKEN'
            },
            body: JSON.stringify({ 
                plat_id: id 
            })
        });

        if (!response.ok) {
            throw new Error(`Gagal membatalkan plat: ${response.statusText}`);
        }

        const data = await response.json();
        showNotification(`Permintaan Plat ${data.data.quotation_number} Berhasil Di Batalkan`);
        
        // Refresh tabel saja
        await loadQuotations();
    } catch (error) {
        console.error('Error saat cancelPlat:', error);
        throw error;
    }
}

let quotations = [];

async function loadQuotations() {
    let cabang = document.getElementById('cabang').value;
    const response = await fetch('/api/quotations/' + cabang);

    quotations = await response.json();

    renderQuotationTable();
}

function renderQuotationTable() {

    const tbody = document.getElementById('quotation-table');

    tbody.innerHTML = '';

    quotations.forEach((q, index) => {

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">

                <td class="px-6 py-4">${index + 1}</td>

                <td class="px-6 py-4">
                    ${q.quotation_number}
                </td>

                <td class="px-6 py-4">
                    ${q.nama_customer}
                </td>

                <td class="px-6 py-4">
                    Rp ${Number(q.total_amount).toLocaleString('id-ID')}
                </td>

                <td class="px-6 py-4 text-center">
                    ${renderAction(q, index)}
                </td>

            </tr>
        `;
    });

}

function renderAction(q, index) {

    if (role === 'sales') {
        if(q.status === 0){
            return `
                <button
                    onclick="openEditModal(quotations[${index}])"
                    class="text-blue-600 hover:text-blue-800">
                    Edit
                </button>
    
                <button
                    onclick="openDeleteModal('/quotations/${q.id}')"
                    class="text-red-600 hover:text-red-800">
                    Delete
                </button>
            `;
        }else{
            return `
                <span class="text-gray-400 text-sm italic">
                    Sudah Di Approve
                </span>
            `;
        }
    }

    if (role === 'pricing'){
        if(q.status === 0){
            return `
                <button
                    onclick="openApproveModal(quotations[${index}])"
                    class="text-blue-600 hover:text-blue-800">
                    Detail
                </button>
            `;
        }else{
            return `
                <span class="text-gray-400 text-sm italic">
                    Sudah Di Approve
                </span>
            `;
        }
    }

    if (role === 'penjualan') {

        if (q.status == 0) {
            return `
                <span class="text-gray-400 text-sm italic">
                    Belum Di-approve
                </span>
            `;
        }

        if (q.status == 1) {
            return `
                <button
                    onclick="requestPlat(${q.id})"
                    class="px-4 py-2 border-2 border-blue-600 text-blue-600 text-sm font-bold rounded-full hover:bg-blue-50 transition-all duration-200">
                    Request Plat
                </button>
            `;
        }

        if (q.status == 2) {
            return `
                <button
                    onclick="cancelPlat(${q.id})"
                    class="px-4 py-2 border border-red-200 text-red-500 text-sm font-medium rounded-lg hover:bg-red-50 hover:border-red-300 transition-all duration-200">
                    Cancel Request
                </button>
            `;
        }
    }

    return `
        <span class="text-gray-400 text-sm italic">
            Tidak Ada Akses
        </span>
    `;
}

let inventories = [];

async function loadInventories(){
    let cabang = document.getElementById('cabang').value;
    const response = await fetch('/api/inventories/' + cabang);

    inventories = await response.json();
}

function inventoryOptions(selected = null){
    return inventories.map(inv => {
        return `
            <option
                value="${inv.id}"
                ${selected == inv.id ? 'selected' : ''}>
                ${inv.barang}
            </option>
        `;

    }).join('');

}

loadQuotations();
loadInventories()