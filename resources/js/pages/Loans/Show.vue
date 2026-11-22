<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import { conditions, date, status, overdue, type Loan } from './types';
import './loans.css';
type Entry = {
    id: number;
    returned_at: string | null;
    condition_out: string;
    notes_out: string | null;
    condition_in: string | null;
    notes_in: string | null;
    returner: { name: string } | null;
    item_unit: {
        code: string;
        serial_number: string | null;
        item: { name: string };
    };
};
const props = defineProps<{
    loan: Loan & {
        institution: { name: string; address: string | null };
        recorder: { name: string };
        items: Entry[];
    };
    success?: string;
}>();
const summary = computed(() => ({
    ...props.loan,
    items_count: props.loan.items.length,
    returned_count: props.loan.items.filter((i) => i.returned_at).length,
}));
const form = useForm({
    items: [] as { id: number; condition_in: string; notes_in: string }[],
});
function toggle(entry: Entry, checked: boolean) {
    if (checked)
        form.items.push({
            id: entry.id,
            condition_in: entry.condition_out,
            notes_in: '',
        });
    else form.items = form.items.filter((i) => i.id !== entry.id);
}
function print() {
    window.print();
}
</script>
<template>
    <Head :title="loan.code" /><AdminLayout
        active="peminjaman"
        title="Detail peminjaman"
        ><div class="loan-page">
            <div class="loan-toolbar no-print">
                <Link href="/loans">← Daftar peminjaman</Link
                ><button @click="print">Cetak bukti</button>
            </div>
            <p v-if="success" role="status" class="success no-print">
                {{ success }}
            </p>
            <section class="loan-panel">
                <h1>Bukti peminjaman aset</h1>
                <h2>{{ loan.institution.name }}</h2>
                <p>{{ loan.institution.address }}</p>
                <h3>
                    {{ loan.code }} · {{ status(summary) }}
                    <span v-if="overdue(summary)" class="error"
                        >· Terlambat</span
                    >
                </h3>
                <dl class="loan-fields">
                    <div>
                        <dt>Nama peminjam</dt>
                        <dd>{{ loan.borrower_name }}</dd>
                    </div>
                    <div>
                        <dt>Identitas / kelas / kontak</dt>
                        <dd>
                            {{ loan.borrower_identifier || '—' }} ·
                            {{ loan.borrower_department || '—' }} ·
                            {{ loan.borrower_contact || '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt>Bidang pemilik</dt>
                        <dd>{{ loan.department.name }}</dd>
                    </div>
                    <div>
                        <dt>Petugas pencatat</dt>
                        <dd>{{ loan.recorder.name }}</dd>
                    </div>
                    <div>
                        <dt>Tanggal pinjam</dt>
                        <dd>{{ date(loan.borrowed_at) }}</dd>
                    </div>
                    <div>
                        <dt>Batas kembali</dt>
                        <dd>{{ date(loan.due_at) }}</dd>
                    </div>
                </dl>
                <p class="loan-notes">Keperluan / catatan: {{ loan.notes }}</p>
                <div class="loan-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Unit / barang</th>
                                <th>Kondisi & kelengkapan keluar</th>
                                <th>Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in loan.items" :key="entry.id">
                                <td>
                                    <strong>{{ entry.item_unit.code }}</strong
                                    ><small
                                        >{{ entry.item_unit.item.name }} ·
                                        {{
                                            entry.item_unit.serial_number ||
                                            'Tanpa nomor seri'
                                        }}</small
                                    >
                                </td>
                                <td>
                                    {{ conditions[entry.condition_out]
                                    }}<small class="loan-notes">{{
                                        entry.notes_out || '—'
                                    }}</small>
                                </td>
                                <td v-if="entry.returned_at">
                                    {{ date(entry.returned_at)
                                    }}<small
                                        >{{
                                            conditions[entry.condition_in ?? '']
                                        }}
                                        · {{ entry.returner?.name }}</small
                                    ><small class="loan-notes">{{
                                        entry.notes_in || '—'
                                    }}</small>
                                </td>
                                <td v-else>Belum kembali</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="loan-signatures">
                    <div>Petugas<br /><br /><br />{{ loan.recorder.name }}</div>
                    <div>
                        Peminjam<br /><br /><br />{{ loan.borrower_name }}
                    </div>
                </div>
            </section>
            <section
                v-if="summary.returned_count < summary.items_count"
                class="loan-panel no-print"
            >
                <h2>Catat pengembalian</h2>
                <p>
                    Pilih unit yang diterima. Waktu kembali dicatat otomatis
                    saat disimpan.
                </p>
                <form
                    @submit.prevent="
                        form.post(`/loans/${loan.id}/returns`, {
                            preserveScroll: true,
                            onSuccess: () => form.reset(),
                        })
                    "
                >
                    <template v-for="entry in loan.items" :key="entry.id"
                        ><div v-if="!entry.returned_at" class="loan-panel">
                            <label class="loan-option"
                                ><input
                                    type="checkbox"
                                    :checked="
                                        form.items.some(
                                            (i) => i.id === entry.id,
                                        )
                                    "
                                    @change="
                                        toggle(
                                            entry,
                                            ($event.target as HTMLInputElement)
                                                .checked,
                                        )
                                    "
                                />{{ entry.item_unit.code }} ·
                                {{ entry.item_unit.item.name }}</label
                            >
                            <div
                                v-for="row in form.items.filter(
                                    (i) => i.id === entry.id,
                                )"
                                :key="row.id"
                                class="loan-fields"
                            >
                                <label
                                    >Kondisi kembali<select
                                        v-model="row.condition_in"
                                    >
                                        <option
                                            v-for="(label, key) in conditions"
                                            :key="key"
                                            :value="key"
                                        >
                                            {{ label }}
                                        </option>
                                    </select></label
                                ><label
                                    >Kelengkapan / catatan kembali<textarea
                                        v-model="row.notes_in"
                                        maxlength="5000"
                                        placeholder="Periksa charger, tas, dan kondisi unit"
                                    />
                                </label>
                            </div></div
                    ></template>
                    <p
                        v-for="(error, key) in form.errors"
                        :key="key"
                        class="error"
                        role="alert"
                    >
                        {{ error }}
                    </p>
                    <button
                        class="primary"
                        :disabled="form.processing || !form.items.length"
                    >
                        Simpan pengembalian ({{ form.items.length }} unit)
                    </button>
                </form>
            </section>
        </div></AdminLayout
    >
</template>
