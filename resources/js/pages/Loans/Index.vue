<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import { date, status, overdue, localNow, type Loan } from './types';
import './loans.css';
type Unit = {
    id: number;
    department_id: number;
    code: string;
    serial_number: string | null;
    condition: string | null;
    item: { name: string };
};
const props = defineProps<{
    loans: {
        data: Loan[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    departments: { id: number; name: string; institution: { name: string } }[];
    units: Unit[];
    filters: { search?: string; status?: string };
    success?: string;
}>();
const search = ref(props.filters.search ?? '');
const filterStatus = ref(props.filters.status ?? '');
const open = ref(false);
const unitSearch = ref('');
const form = useForm({
    department_id: null as number | null,
    borrower_name: '',
    borrower_identifier: '',
    borrower_department: '',
    borrower_contact: '',
    borrowed_at: localNow(),
    due_at: '',
    notes: '',
    items: [] as {
        item_unit_id: number;
        condition_out: string;
        notes_out: string;
    }[],
});
watch(
    () => form.department_id,
    () => {
        form.items = [];
    },
);
const available = computed(() =>
    props.units.filter(
        (u) =>
            u.department_id === form.department_id &&
            `${u.code} ${u.item.name} ${u.serial_number ?? ''}`
                .toLowerCase()
                .includes(unitSearch.value.toLowerCase()),
    ),
);
function toggle(u: Unit, checked: boolean) {
    if (checked)
        form.items.push({
            item_unit_id: u.id,
            condition_out:
                u.condition === 'minor_damage' ? 'minor_damage' : 'good',
            notes_out: '',
        });
    else form.items = form.items.filter((i) => i.item_unit_id !== u.id);
}
function start() {
    form.reset();
    form.clearErrors();
    form.borrowed_at = localNow();
    if (props.departments.length === 1)
        form.department_id = props.departments[0].id;
    unitSearch.value = '';
    open.value = true;
}
function filter() {
    router.get(
        '/loans',
        { search: search.value, status: filterStatus.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Peminjaman" /><AdminLayout
        active="peminjaman"
        title="Peminjaman"
        ><div class="loan-page">
            <div class="loan-heading">
                <div>
                    <h1>Peminjaman</h1>
                    <p>Catat penyerahan dan pengembalian aset per unit.</p>
                </div>
                <button class="primary" @click="start">
                    + Tambah peminjaman
                </button>
            </div>
            <p v-if="success" role="status" class="success">{{ success }}</p>
            <form class="loan-toolbar" @submit.prevent="filter">
                <input
                    v-model="search"
                    placeholder="Cari nomor atau peminjam"
                    aria-label="Cari peminjaman"
                /><select v-model="filterStatus" aria-label="Status">
                    <option value="">Semua status</option>
                    <option value="active">Dipinjam</option>
                    <option value="partial">Sebagian kembali</option>
                    <option value="completed">Selesai</option>
                    <option value="overdue">Terlambat</option></select
                ><button>Cari</button>
            </form>
            <div class="loan-panel loan-table">
                <table>
                    <thead>
                        <tr>
                            <th>Transaksi / bidang</th>
                            <th>Peminjam</th>
                            <th>Tanggal pinjam / batas kembali</th>
                            <th>Unit kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="loan in loans.data" :key="loan.id">
                            <td>
                                <strong>{{ loan.code }}</strong
                                ><small>{{ loan.department.name }}</small>
                            </td>
                            <td>
                                {{ loan.borrower_name
                                }}<small>{{ loan.borrower_department }}</small>
                            </td>
                            <td>
                                {{ date(loan.borrowed_at)
                                }}<small>Batas: {{ date(loan.due_at) }}</small>
                            </td>
                            <td>
                                {{ loan.returned_count }} /
                                {{ loan.items_count }}
                            </td>
                            <td>
                                {{ status(loan)
                                }}<small v-if="overdue(loan)" class="error"
                                    >Terlambat</small
                                >
                            </td>
                            <td>
                                <Link :href="`/loans/${loan.id}`"
                                    >Detail / pengembalian</Link
                                >
                            </td>
                        </tr>
                        <tr v-if="!loans.data.length">
                            <td colspan="6">Belum ada peminjaman.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav class="loan-toolbar" aria-label="Halaman">
                <template v-for="(link, i) in loans.links" :key="i"
                    ><Link
                        v-if="link.url"
                        :href="link.url"
                        :aria-current="link.active ? 'page' : undefined"
                        v-html="link.label" /><span v-else v-html="link.label"
                /></template>
            </nav>
            <div
                v-if="open"
                class="loan-overlay"
                @click.self="open = false"
                @keydown.esc="open = false"
            >
                <section
                    class="loan-modal"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="loan-title"
                >
                    <div class="loan-heading">
                        <h2 id="loan-title">Tambah peminjaman</h2>
                        <button
                            type="button"
                            aria-label="Tutup"
                            @click="open = false"
                        >
                            ×
                        </button>
                    </div>
                    <form @submit.prevent="form.post('/loans')">
                        <div class="loan-fields">
                            <label
                                >Bidang pemilik<select
                                    v-model="form.department_id"
                                    required
                                >
                                    <option :value="null">Pilih bidang</option>
                                    <option
                                        v-for="d in departments"
                                        :key="d.id"
                                        :value="d.id"
                                    >
                                        {{ d.name }} · {{ d.institution.name }}
                                    </option>
                                </select></label
                            >
                            <label
                                >Nama peminjam<input
                                    v-model="form.borrower_name"
                                    required
                                    maxlength="150" /></label
                            ><label
                                >NIS / NIP / identitas<input
                                    v-model="form.borrower_identifier"
                                    maxlength="100" /></label
                            ><label
                                >Kelas / asal peminjam<input
                                    v-model="form.borrower_department"
                                    maxlength="150" /></label
                            ><label
                                >Kontak<input
                                    v-model="form.borrower_contact"
                                    maxlength="100" /></label
                            ><label
                                >Tanggal pinjam (WITA)<input
                                    v-model="form.borrowed_at"
                                    type="datetime-local"
                                    required /></label
                            ><label
                                >Batas kembali (WITA)<input
                                    v-model="form.due_at"
                                    type="datetime-local"
                                    :min="form.borrowed_at" /></label
                            ><label
                                >Keperluan / catatan<textarea
                                    v-model="form.notes"
                                    required
                                    maxlength="5000"
                                />
                            </label>
                        </div>
                        <h3>Pilih unit tersedia</h3>
                        <input
                            v-model="unitSearch"
                            placeholder="Cari kode, barang, atau nomor seri"
                            aria-label="Cari unit"
                        />
                        <div class="loan-options">
                            <label
                                v-for="u in available"
                                :key="u.id"
                                class="loan-option"
                                ><input
                                    type="checkbox"
                                    :checked="
                                        form.items.some(
                                            (i) => i.item_unit_id === u.id,
                                        )
                                    "
                                    @change="
                                        toggle(
                                            u,
                                            ($event.target as HTMLInputElement)
                                                .checked,
                                        )
                                    "
                                />{{ u.code }} · {{ u.item.name }} ·
                                {{
                                    u.serial_number || 'Tanpa nomor seri'
                                }}</label
                            >
                            <p v-if="!available.length">
                                Tidak ada unit tersedia. Pilih bidang atau ubah
                                pencarian.
                            </p>
                        </div>
                        <div
                            v-for="entry in form.items"
                            :key="entry.item_unit_id"
                            class="loan-panel"
                        >
                            <strong>{{
                                units.find((u) => u.id === entry.item_unit_id)
                                    ?.code
                            }}</strong>
                            <div class="loan-fields">
                                <label
                                    >Kondisi saat diserahkan<select
                                        v-model="entry.condition_out"
                                    >
                                        <option value="good">Baik</option>
                                        <option value="minor_damage">
                                            Rusak ringan
                                        </option>
                                    </select></label
                                ><label
                                    >Kelengkapan / catatan<input
                                        v-model="entry.notes_out"
                                        placeholder="Contoh: charger, tas; goresan pada casing"
                                        maxlength="5000"
                                /></label>
                            </div>
                        </div>
                        <p
                            v-for="(error, key) in form.errors"
                            :key="key"
                            class="error"
                            role="alert"
                        >
                            {{ error }}
                        </p>
                        <footer class="loan-toolbar">
                            <button type="button" @click="open = false">
                                Batal</button
                            ><button
                                class="primary"
                                :disabled="
                                    form.processing || !form.items.length
                                "
                            >
                                Simpan peminjaman ({{ form.items.length }} unit)
                            </button>
                        </footer>
                    </form>
                </section>
            </div>
        </div></AdminLayout
    >
</template>
