<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type Option = { id: number; name: string; institution_id?: number };
type Unit = {
    id: number;
    institution_id: number;
    item_id: number;
    department_id: number;
    room_id: number | null;
    code: string;
    serial_number: string | null;
    description: string | null;
    purchase_year: number | null;
    purchase_price: string | null;
    funding_source: string | null;
    condition: string | null;
    condition_notes: string | null;
    disposed_at: string | null;
    disposal_notes: string | null;
    is_active: boolean;
    photo_url: string | null;
    item: Option;
    institution: Option;
    department: Option;
    room: Option | null;
};
const props = defineProps<{
    units: {
        data: Unit[];
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    items: Option[];
    institutions: Option[];
    departments: Option[];
    rooms: Option[];
    filters: { search?: string; item_id?: string };
    success?: string;
}>();
const page = usePage<{ auth: Auth }>();
const search = ref(props.filters.search ?? '');
const itemFilter = ref(props.filters.item_id ?? '');
const editing = ref<number | null>(null);
const duplicateSource = ref<string | null>(null);
const open = ref(false);
const detail = ref<Unit | null>(null);
const deleting = ref<Unit | null>(null);
const deletion = useForm({ unit: '' });
const form = useForm({
    institution_id: null as number | null,
    item_id: null as number | null,
    department_id: null as number | null,
    room_id: null as number | null,
    code: '',
    serial_number: '',
    description: '',
    purchase_year: null as number | null,
    purchase_price: '' as string | number,
    funding_source: '',
    condition: '',
    condition_notes: '',
    disposed_at: '',
    disposal_notes: '',
    is_active: true,
    photo: null as File | null,
    remove_photo: false,
});
const items = computed(() =>
    props.items.filter((i) => i.institution_id === form.institution_id),
);
const departments = computed(() =>
    props.departments.filter((i) => i.institution_id === form.institution_id),
);
const rooms = computed(() =>
    props.rooms.filter((i) => i.institution_id === form.institution_id),
);
watch(
    () => form.institution_id,
    () => {
        if (!items.value.some((i) => i.id === form.item_id))
            form.item_id = null;
        if (!departments.value.some((i) => i.id === form.department_id))
            form.department_id = null;
        if (!rooms.value.some((i) => i.id === form.room_id))
            form.room_id = null;
    },
);
const conditions: Record<string, string> = {
    good: 'Baik',
    minor_damage: 'Rusak ringan',
    major_damage: 'Rusak parah',
};
function show(unit?: Unit, duplicate = false) {
    form.reset();
    form.clearErrors();
    editing.value = duplicate ? null : (unit?.id ?? null);
    duplicateSource.value = duplicate && unit ? unit.code : null;
    if (unit)
        Object.assign(form, {
            institution_id: unit.institution_id,
            item_id: unit.item_id,
            department_id: unit.department_id,
            room_id: unit.room_id,
            code: duplicate ? '' : unit.code,
            serial_number: duplicate ? '' : (unit.serial_number ?? ''),
            description: unit.description ?? '',
            purchase_year: unit.purchase_year,
            purchase_price: unit.purchase_price ?? '',
            funding_source: unit.funding_source ?? '',
            condition: unit.condition ?? '',
            condition_notes: unit.condition_notes ?? '',
            disposed_at: duplicate ? '' : (unit.disposed_at ?? ''),
            disposal_notes: duplicate ? '' : (unit.disposal_notes ?? ''),
            is_active: duplicate ? true : unit.is_active,
        });
    else
        Object.assign(form, {
            institution_id: page.props.auth.user.institution_id,
            department_id: page.props.auth.user.department_id,
        });
    open.value = true;
}
function photo(event: Event) {
    form.photo = (event.target as HTMLInputElement).files?.[0] ?? null;
}
function save() {
    form.transform((data) => ({
        ...data,
        ...(editing.value ? { _method: 'put' } : {}),
    })).post(editing.value ? `/item-units/${editing.value}` : '/item-units', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
    });
}
function remove() {
    if (deleting.value)
        deletion.delete(`/item-units/${deleting.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleting.value = null;
            },
        });
}
function filter() {
    router.get(
        '/item-units',
        { search: search.value, item_id: itemFilter.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Unit aset" />
    <AdminLayout active="unit" title="Unit aset">
        <div class="heading">
            <div>
                <small>INVENTARIS</small>
                <h1>Unit aset</h1>
                <p>Satu kode inventaris untuk setiap aset fisik.</p>
            </div>
            <button class="primary" @click="show()">+ Tambah unit</button>
        </div>
        <p v-if="success" class="notice" role="status">{{ success }}</p>
        <section class="panel">
            <form class="toolbar" @submit.prevent="filter">
                <label for="search">Cari unit</label
                ><input
                    id="search"
                    v-model="search"
                    placeholder="Kode, nomor seri, atau barang"
                /><select v-model="itemFilter" aria-label="Filter barang">
                    <option value="">Semua barang</option>
                    <option v-for="i in props.items" :key="i.id" :value="i.id">
                        {{ i.name }}
                    </option></select
                ><button>Cari</button><span>{{ units.total }} unit</span>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Unit / barang</th>
                            <th>Bidang / ruang</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="unit in units.data" :key="unit.id">
                            <td>
                                <strong>{{ unit.code }}</strong
                                ><small
                                    >{{ unit.item.name }} ·
                                    {{
                                        unit.serial_number || 'Tanpa nomor seri'
                                    }}</small
                                ><small>{{ unit.institution.name }}</small>
                            </td>
                            <td>
                                {{ unit.department.name
                                }}<small>{{
                                    unit.room?.name || 'Belum ditempatkan'
                                }}</small>
                            </td>
                            <td>
                                {{
                                    conditions[unit.condition ?? ''] ??
                                    'Belum diperiksa'
                                }}
                            </td>
                            <td>
                                {{
                                    unit.disposed_at
                                        ? 'Dimusnahkan'
                                        : unit.is_active
                                          ? 'Aktif'
                                          : 'Nonaktif'
                                }}
                            </td>
                            <td>
                                <div class="actions">
                                    <button @click="detail = unit">
                                        Detail</button
                                    ><button @click="show(unit)">Edit</button
                                    ><button
                                        :aria-label="`Duplikat ${unit.code}`"
                                        @click="show(unit, true)"
                                    >
                                        Duplikat</button
                                    ><button
                                        @click="
                                            deleting = unit;
                                            deletion.clearErrors();
                                        "
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!units.data.length">
                            <td colspan="5">Belum ada unit aset.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav class="pagination" aria-label="Halaman unit">
                <template v-for="(link, index) in units.links" :key="index"
                    ><Link
                        v-if="link.url"
                        :href="link.url"
                        :class="{ active: link.active }"
                        ><span v-html="link.label" /></Link
                    ><span v-else v-html="link.label"
                /></template>
            </nav>
        </section>
        <div v-if="open" class="overlay" @click.self="open = false">
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="unit-title"
                @keydown.esc="open = false"
            >
                <header>
                    <h2 id="unit-title">
                        {{ editing ? 'Edit unit aset' : 'Tambah unit aset' }}
                    </h2>
                    <button aria-label="Tutup" @click="open = false">×</button>
                </header>
                <form @submit.prevent="save">
                    <p v-if="duplicateSource" class="hint" role="status">
                        Data disalin dari {{ duplicateSource }}. Isi kode
                        inventaris dan nomor seri unit baru, lalu sesuaikan data
                        lainnya. Foto dan data pemusnahan tidak disalin.
                    </p>
                    <div class="fields">
                        <label
                            >Instansi<select
                                v-model="form.institution_id"
                                required
                                :disabled="
                                    !!editing ||
                                    page.props.auth.user.role !== 'superadmin'
                                "
                            >
                                <option :value="null">Pilih instansi</option>
                                <option
                                    v-for="i in institutions"
                                    :key="i.id"
                                    :value="i.id"
                                >
                                    {{ i.name }}
                                </option>
                            </select></label
                        >
                        <label
                            >Barang<select
                                v-model="form.item_id"
                                required
                                :disabled="!!editing"
                            >
                                <option :value="null">Pilih barang</option>
                                <option
                                    v-for="i in items"
                                    :key="i.id"
                                    :value="i.id"
                                >
                                    {{ i.name }}
                                </option>
                            </select></label
                        >
                        <label
                            >Bidang pemilik<select
                                v-model="form.department_id"
                                required
                                :disabled="
                                    !!editing ||
                                    page.props.auth.user.role === 'operator'
                                "
                            >
                                <option :value="null">Pilih bidang</option>
                                <option
                                    v-for="i in departments"
                                    :key="i.id"
                                    :value="i.id"
                                >
                                    {{ i.name }}
                                </option>
                            </select></label
                        >
                        <label
                            >Ruang penempatan<select
                                v-model="form.room_id"
                                :disabled="!!editing"
                            >
                                <option :value="null">Belum ditempatkan</option>
                                <option
                                    v-for="i in rooms"
                                    :key="i.id"
                                    :value="i.id"
                                >
                                    {{ i.name }}
                                </option>
                            </select></label
                        >
                        <label
                            >Kode inventaris<input
                                v-model="form.code"
                                required
                                maxlength="50" /></label
                        ><label
                            >Nomor seri<input
                                v-model="form.serial_number"
                                maxlength="150"
                        /></label>
                        <label class="wide">
                            Spesifikasi / keterangan unit
                            <textarea
                                v-model="form.description"
                                rows="3"
                                maxlength="5000"
                                placeholder="Contoh: Lenovo ThinkPad T14, Core i5, RAM 8 GB, SSD 512 GB"
                            />
                        </label>
                        <label
                            >Tahun pembelian<input
                                v-model="form.purchase_year"
                                type="number"
                                min="1900"
                                :max="new Date().getFullYear()" /></label
                        ><label
                            >Harga pembelian (Rp)<input
                                v-model="form.purchase_price"
                                type="number"
                                min="0"
                                step="0.01"
                        /></label>
                        <label
                            >Sumber dana<select v-model="form.funding_source">
                                <option value="">Belum diketahui</option>
                                <option>BOSP</option>
                                <option>BOSDA</option>
                                <option>Bantuan</option>
                            </select></label
                        >
                        <label
                            >Kondisi<select v-model="form.condition">
                                <option value="">Belum diperiksa</option>
                                <option
                                    v-for="(name, key) in conditions"
                                    :key="key"
                                    :value="key"
                                >
                                    {{ name }}
                                </option>
                            </select></label
                        >
                        <label
                            >Catatan kondisi<textarea
                                v-model="form.condition_notes"
                            /></label
                        ><label
                            >Tanggal pemusnahan<input
                                v-model="form.disposed_at"
                                type="date" /></label
                        ><label
                            >Catatan pemusnahan<textarea
                                v-model="form.disposal_notes"
                            />
                        </label>
                        <label
                            >Foto<input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="photo" /></label
                        ><label
                            ><input
                                v-model="form.remove_photo"
                                type="checkbox"
                            />
                            Hapus foto lama</label
                        ><label
                            ><input v-model="form.is_active" type="checkbox" />
                            Aktif</label
                        >
                    </div>
                    <p v-if="editing">
                        Perpindahan bidang atau ruang dicatat melalui transaksi
                        mutasi.
                    </p>
                    <p
                        v-for="(error, key) in form.errors"
                        :key="key"
                        class="error"
                        role="alert"
                    >
                        {{ error }}
                    </p>
                    <footer class="modal-actions">
                        <button type="button" @click="open = false">
                            Batal</button
                        ><button class="primary" :disabled="form.processing">
                            Simpan
                        </button>
                    </footer>
                </form>
            </section>
        </div>
        <div v-if="detail" class="overlay" @click.self="detail = null">
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="detail-title"
            >
                <header>
                    <h2 id="detail-title">
                        {{ detail.code }} · {{ detail.item.name }}
                    </h2>
                    <button aria-label="Tutup" @click="detail = null">×</button>
                </header>
                <img
                    v-if="detail.photo_url"
                    :src="detail.photo_url"
                    alt="Foto unit aset"
                    style="max-width: 100%; max-height: 300px"
                />
                <dl>
                    <dt>Nomor seri</dt>
                    <dd>{{ detail.serial_number || '—' }}</dd>
                    <dt>Spesifikasi / keterangan unit</dt>
                    <dd class="unit-description">
                        {{ detail.description || '—' }}
                    </dd>
                    <dt>Penempatan</dt>
                    <dd>
                        {{ detail.department.name }} /
                        {{ detail.room?.name || '—' }}
                    </dd>
                    <dt>Pembelian</dt>
                    <dd>
                        {{ detail.purchase_year || '—' }} · Rp
                        {{ detail.purchase_price ?? '—' }} ·
                        {{ detail.funding_source || '—' }}
                    </dd>
                    <dt>Kondisi</dt>
                    <dd>
                        {{
                            conditions[detail.condition ?? ''] ??
                            'Belum diperiksa'
                        }}
                        · {{ detail.condition_notes || '—' }}
                    </dd>
                    <dt>Pemusnahan</dt>
                    <dd>
                        {{ detail.disposed_at || '—' }} ·
                        {{ detail.disposal_notes || '—' }}
                    </dd>
                </dl>
                <footer class="modal-actions">
                    <button @click="detail = null">Tutup</button>
                </footer>
            </section>
        </div>
        <div v-if="deleting" class="overlay" @click.self="deleting = null">
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-title"
            >
                <h2 id="delete-title">Hapus {{ deleting.code }}?</h2>
                <p>Unit yang memiliki riwayat transaksi tidak dapat dihapus.</p>
                <p v-if="deletion.errors.unit" class="error" role="alert">
                    {{ deletion.errors.unit }}
                </p>
                <footer class="modal-actions">
                    <button @click="deleting = null">Batal</button
                    ><button :disabled="deletion.processing" @click="remove">
                        Hapus
                    </button>
                </footer>
            </section>
        </div>
    </AdminLayout>
</template>
<style scoped>
.unit-description {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}
.item-name {
    display: flex;
    align-items: center;
    gap: 10px;
}
.thumbnail {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 6px;
}
.photo-preview {
    max-width: 100%;
    max-height: 220px;
    object-fit: contain;
    border-radius: 8px;
}
dt {
    font-size: 12px;
    color: #7a8972;
    margin-top: 14px;
}
dd {
    font-size: 13px;
    margin: 4px 0;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}
select {
    font: inherit;
    font-size: 13px;
    padding: 10px 13px;
    border: 1px solid #dce4d6;
    border-radius: 7px;
    background: white;
    min-width: 0;
}
.heading,
.toolbar,
.pagination,
.actions,
.modal-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
.heading {
    justify-content: space-between;
    margin-bottom: 26px;
}
h1 {
    font-size: 30px;
    margin: 8px 0;
}
h2 {
    font-size: 21px;
    margin-bottom: 22px;
}
p {
    font-size: 13px;
    color: #71816f;
}
.heading small {
    letter-spacing: 2px;
    color: #80917c;
    font-size: 10px;
}
button,
input,
textarea {
    font: inherit;
    font-size: 13px;
    border: 1px solid #dce4d6;
    border-radius: 7px;
    padding: 10px 13px;
}
button {
    cursor: pointer;
    background: white;
    color: #294638;
}
button:disabled {
    opacity: 0.5;
    cursor: wait;
}
.primary {
    background: #254a37;
    color: white;
    border-color: #254a37;
}
.danger,
.error {
    color: #a53232;
}
.error {
    font-size: 12px;
}
.notice {
    background: #e6f0df;
    padding: 14px;
    border-radius: 8px;
}
.panel {
    background: white;
    border: 1px solid #e3e9dd;
    border-radius: 12px;
    overflow: hidden;
}
.toolbar {
    padding: 20px;
    flex-wrap: wrap;
}
.toolbar label {
    font-size: 12px;
}
.toolbar span {
    margin-left: auto;
    font-size: 12px;
    color: #7d8b76;
}
.table-wrap {
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
th,
td {
    padding: 16px 20px;
    text-align: left;
    border-bottom: 1px solid #edf0e8;
}
th {
    background: #f8faf5;
    font-size: 11px;
    color: #7a8972;
}
td strong,
td small {
    display: block;
}
td small {
    color: #829078;
    margin-top: 5px;
}
.address {
    max-width: 240px;
    overflow-wrap: anywhere;
}
.badge {
    background: #e8f1e1;
    color: #4c7040;
    padding: 5px 9px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
}
.inactive {
    background: #f1eee6;
    color: #8b7850;
}
.empty {
    text-align: center;
    padding: 45px;
    color: #819177;
}
.pagination {
    justify-content: space-between;
    padding: 18px 20px;
    font-size: 12px;
    flex-wrap: wrap;
}
nav {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
nav a,
nav span {
    padding: 7px;
    color: #45623b;
    text-decoration: none;
}
.selected {
    background: #e8efdf;
    border-radius: 5px;
}
.disabled {
    opacity: 0.45;
}
.overlay {
    position: fixed;
    inset: 0;
    background: #09251b80;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-card {
    width: 100%;
    max-width: 600px;
    background: white;
    padding: 28px;
    border-radius: 14px;
    max-height: 90svh;
    overflow: auto;
}
.fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.modal-card > header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.fields label {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 7px;
    font-size: 12px;
}
.wide {
    grid-column: 1/-1;
}
.checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    margin-top: 18px;
}
.hint {
    font-size: 12px;
}
.modal-actions {
    justify-content: flex-end;
    margin-top: 24px;
}
input,
textarea {
    min-width: 0;
}
button:focus-visible,
input:focus-visible,
textarea:focus-visible,
a:focus-visible {
    outline: 2px solid #73935c;
    outline-offset: 3px;
}
@media (max-width: 600px) {
    .heading {
        align-items: flex-start;
        flex-direction: column;
    }
    .fields {
        grid-template-columns: 1fr;
    }
    .toolbar input {
        width: 100%;
    }
    .modal-card {
        padding: 20px;
    }
}
</style>
