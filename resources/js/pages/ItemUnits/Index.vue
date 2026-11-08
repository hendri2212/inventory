<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppIcon from '../../components/AppIcon.vue';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type Option = { id: number; name: string; institution_id?: number };
type UnitPhoto = { id: number; url: string };
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
    photos: UnitPhoto[];
    item: Option;
    institution: Option;
    department: Option;
    room: Option | null;
};
const props = defineProps<{
    units: {
        data: Unit[];
        total: number;
        from: number | null;
        to: number | null;
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
const existingPhotos = ref<UnitPhoto[]>([]);
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
    purchase_price: '',
    funding_source: '',
    condition: '',
    condition_notes: '',
    disposed_at: '',
    disposal_notes: '',
    photos: [] as File[],
    remove_photo_ids: [] as number[],
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
const currentInactiveRoom = computed(() => {
    const room = props.units.data.find(
        (unit) => unit.id === editing.value,
    )?.room;
    return room && !rooms.value.some((option) => option.id === room.id)
        ? room
        : null;
});
watch(
    () => form.institution_id,
    () => {
        if (!items.value.some((i) => i.id === form.item_id))
            form.item_id = null;
        if (!departments.value.some((i) => i.id === form.department_id))
            form.department_id = null;
        if (!editing.value && !rooms.value.some((i) => i.id === form.room_id))
            form.room_id = null;
    },
);
const conditions: Record<string, string> = {
    good: 'Baik',
    minor_damage: 'Rusak ringan',
    major_damage: 'Rusak parah',
};
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
});

function formatPrice(price: string | null) {
    return price === null ? '—' : rupiah.format(Number(price));
}

const purchasePriceInput = computed(() => {
    const [whole, fraction] = form.purchase_price.split('.');
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return fraction === undefined ? grouped : `${grouped},${fraction}`;
});

function updatePurchasePrice(event: Event) {
    const input = event.target as HTMLInputElement;
    const text = input.value;
    const pastedDecimal =
        event instanceof InputEvent &&
        event.inputType === 'insertFromPaste' &&
        /^\d+\.\d{1,2}$/.test(text);
    const raw = pastedDecimal
        ? text.replace('.', ',')
        : text.replaceAll('.', '');

    if (/^\d*(?:,\d{0,2})?$/.test(raw)) {
        const [whole, fraction] = raw.split(',');
        const digits = whole.replace(/^0+(?=\d)/, '');
        form.purchase_price =
            fraction === undefined ? digits : `${digits || '0'}.${fraction}`;
    }

    const formatted = purchasePriceInput.value;
    if (formatted === text) return;

    let remaining = text
        .slice(input.selectionStart ?? text.length)
        .replaceAll('.', '').length;
    let caret = formatted.length;
    while (caret > 0 && remaining > 0) {
        caret--;
        if (formatted[caret] !== '.') remaining--;
    }
    input.value = formatted;
    input.setSelectionRange(caret, caret);
}

function show(unit?: Unit, duplicate = false) {
    form.reset();
    form.clearErrors();
    existingPhotos.value = duplicate ? [] : (unit?.photos ?? []);
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
            purchase_price: unit.purchase_price?.replace(/\.00$/, '') ?? '',
            funding_source: unit.funding_source ?? '',
            condition: unit.condition ?? '',
            condition_notes: unit.condition_notes ?? '',
            disposed_at: duplicate ? '' : (unit.disposed_at ?? ''),
            disposal_notes: duplicate ? '' : (unit.disposal_notes ?? ''),
        });
    else
        Object.assign(form, {
            institution_id: page.props.auth.user.institution_id,
            department_id: page.props.auth.user.department_id,
        });
    open.value = true;
}
function selectPhotos(event: Event) {
    form.photos = Array.from((event.target as HTMLInputElement).files ?? []);
}
function save() {
    form.transform((data) => ({
        ...data,
        purchase_price: data.purchase_price.replace(/\.$/, ''),
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
                            <th>Pembelian</th>
                            <th>Kondisi</th>
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
                            <td class="purchase-cell">
                                <strong>{{
                                    formatPrice(unit.purchase_price)
                                }}</strong>
                                <small
                                    >Tahun
                                    {{ unit.purchase_year ?? '—' }}</small
                                >
                            </td>
                            <td>
                                {{
                                    conditions[unit.condition ?? ''] ??
                                    'Belum diperiksa'
                                }}
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="unit-actions-trigger"
                                    :aria-label="`Aksi unit ${unit.code}`"
                                    :popovertarget="`unit-actions-${unit.id}`"
                                >
                                    <AppIcon name="more-vertical" :size="18" />
                                </button>
                                <div
                                    :id="`unit-actions-${unit.id}`"
                                    class="unit-actions"
                                    popover
                                    role="group"
                                    :aria-label="`Aksi unit ${unit.code}`"
                                >
                                    <button
                                        type="button"
                                        :popovertarget="`unit-actions-${unit.id}`"
                                        popovertargetaction="hide"
                                        :aria-label="`Detail ${unit.code}`"
                                        @click="detail = unit"
                                    >
                                        <AppIcon name="eye" :size="15" />
                                        Detail</button
                                    ><button
                                        type="button"
                                        :popovertarget="`unit-actions-${unit.id}`"
                                        popovertargetaction="hide"
                                        :aria-label="`Edit ${unit.code}`"
                                        @click="show(unit)"
                                    >
                                        <AppIcon name="pencil" :size="15" />
                                        Edit</button
                                    ><button
                                        type="button"
                                        :popovertarget="`unit-actions-${unit.id}`"
                                        popovertargetaction="hide"
                                        :aria-label="`Duplikat ${unit.code}`"
                                        @click="show(unit, true)"
                                    >
                                        <AppIcon name="copy" :size="15" />
                                        Duplikat</button
                                    ><button
                                        type="button"
                                        :popovertarget="`unit-actions-${unit.id}`"
                                        popovertargetaction="hide"
                                        class="danger"
                                        :aria-label="`Hapus ${unit.code}`"
                                        @click="
                                            deleting = unit;
                                            deletion.clearErrors();
                                        "
                                    >
                                        <AppIcon name="trash" :size="15" />
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
            <div class="pagination">
                <span
                    >{{ units.from ?? 0 }}–{{ units.to ?? 0 }} dari
                    {{ units.total }}</span
                >
                <nav aria-label="Pagination unit aset">
                    <template v-for="(link, index) in units.links" :key="index"
                        ><Link
                            v-if="link.url"
                            :href="link.url"
                            :class="{ selected: link.active }"
                            :aria-current="link.active ? 'page' : undefined"
                            >{{
                                index === 0
                                    ? 'Sebelumnya'
                                    : index === units.links.length - 1
                                      ? 'Berikutnya'
                                      : link.label
                            }}</Link
                        ><span v-else class="disabled">{{
                            index === 0
                                ? 'Sebelumnya'
                                : index === units.links.length - 1
                                  ? 'Berikutnya'
                                  : link.label
                        }}</span></template
                    >
                </nav>
            </div>
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
                            v-if="
                                page.props.auth.user.role === 'superadmin' &&
                                !editing
                            "
                            >Instansi<select
                                v-model="form.institution_id"
                                required
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
                            >Barang<select v-model="form.item_id" required>
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
                        <label v-if="page.props.auth.user.role !== 'operator'"
                            >Bidang pemilik<select
                                v-model="form.department_id"
                                required
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
                            >Ruang penempatan<select v-model="form.room_id">
                                <option :value="null">Belum ditempatkan</option>
                                <option
                                    v-if="currentInactiveRoom"
                                    :value="currentInactiveRoom.id"
                                >
                                    {{ currentInactiveRoom.name }} (nonaktif)
                                </option>
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
                                :value="purchasePriceInput"
                                type="text"
                                inputmode="decimal"
                                placeholder="2.000.000"
                                @input="updatePurchasePrice"
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
                        <label>
                            Tambah foto
                            <input
                                type="file"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                @change="selectPhotos"
                            />
                            <small
                                >Maks. 10 foto per unggahan, 5 MB per
                                foto.</small
                            >
                        </label>
                        <div v-if="existingPhotos.length" class="photo-gallery">
                            <label
                                v-for="(photo, index) in existingPhotos"
                                :key="photo.id"
                            >
                                <img
                                    :src="photo.url"
                                    :alt="'Foto ' + (index + 1)"
                                    class="thumbnail"
                                />
                                <span>
                                    <input
                                        v-model="form.remove_photo_ids"
                                        type="checkbox"
                                        :value="photo.id"
                                    />
                                    Hapus foto {{ index + 1 }}
                                </span>
                            </label>
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
                <div v-if="detail.photos.length" class="photo-gallery">
                    <img
                        v-for="(photo, index) in detail.photos"
                        :key="photo.id"
                        :src="photo.url"
                        :alt="'Foto unit aset ' + (index + 1)"
                        class="photo-preview"
                    />
                </div>
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
                        {{ detail.purchase_year ?? '—' }} ·
                        {{ formatPrice(detail.purchase_price) }} ·
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
.photo-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
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
.modal-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
.heading {
    justify-content: space-between;
    margin-bottom: 26px;
}
.unit-actions-trigger {
    display: inline-grid;
    place-items: center;
    width: 38px;
    height: 38px;
    padding: 0;
}
.unit-actions-trigger:hover {
    background: #eef4e9;
}
.unit-actions {
    position-area: block-end span-inline-start;
    position-try-fallbacks: flip-block, flip-inline;
    justify-self: end;
    margin: 4px 0;
    min-width: 148px;
    padding: 4px;
    border: 1px solid #dce4d6;
    border-radius: 8px;
    background: white;
    box-shadow: 0 8px 24px #203e3326;
}
.unit-actions > button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 10px;
    border: 0;
    text-align: start;
}
.unit-actions > button:hover {
    background: #eef4e9;
}
.unit-actions > button.danger:hover {
    background: #fdf0f0;
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
.purchase-cell {
    white-space: nowrap;
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
