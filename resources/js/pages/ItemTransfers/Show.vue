<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import { date } from '../Loans/types';
import type { Transfer } from './types';
import '../Loans/loans.css';
defineProps<{
    transfer: Transfer;
    units: {
        id: number;
        code: string;
        name: string;
        serial_number: string | null;
    }[];
    success?: string;
}>();
function print() {
    window.print();
}
</script>
<template>
    <Head :title="transfer.code" /><AdminLayout
        active="mutasi"
        title="Detail mutasi"
        ><div class="loan-page">
            <div class="loan-toolbar no-print">
                <Link href="/item-transfers">← Daftar mutasi</Link
                ><button @click="print">Cetak bukti</button>
            </div>
            <p v-if="success" role="status" class="success no-print">
                {{ success }}
            </p>
            <section class="loan-panel">
                <h1>Bukti mutasi unit barang</h1>
                <h2>{{ transfer.institution.name }}</h2>
                <p>{{ transfer.institution.address }}</p>
                <h3>
                    {{ transfer.code }} ·
                    {{
                        transfer.type === 'room' ? 'Antarruang' : 'Antarbidang'
                    }}
                </h3>
                <dl class="loan-fields">
                    <div>
                        <dt>Tanggal mutasi</dt>
                        <dd>{{ date(transfer.transferred_at) }}</dd>
                    </div>
                    <div>
                        <dt>Petugas pencatat</dt>
                        <dd>{{ transfer.recorder?.name || '—' }}</dd>
                    </div>
                    <div>
                        <dt>Penempatan asal</dt>
                        <dd>
                            {{ transfer.from_department_name || '—' }} /
                            {{
                                transfer.from_room?.name || 'Belum ditempatkan'
                            }}
                        </dd>
                    </div>
                    <div>
                        <dt>Penempatan tujuan</dt>
                        <dd>
                            {{ transfer.to_department_name || '—' }} /
                            {{ transfer.to_room?.name || 'Belum ditempatkan' }}
                        </dd>
                    </div>
                </dl>
                <p class="loan-notes">Alasan / catatan: {{ transfer.notes }}</p>
                <div class="loan-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode unit</th>
                                <th>Nama barang</th>
                                <th>Nomor seri</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="u in units" :key="u.id">
                                <td>{{ u.code }}</td>
                                <td>{{ u.name }}</td>
                                <td>{{ u.serial_number || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="loan-signatures">
                    <div>Penyerah<br /><br /><br />(________________)</div>
                    <div>Penerima<br /><br /><br />(________________)</div>
                </div>
                <p class="no-print">
                    Riwayat ini menyimpan asal dan tujuan saat mutasi.
                    Penempatan unit dapat berubah pada mutasi berikutnya.
                </p>
            </section>
        </div></AdminLayout
    >
</template>
