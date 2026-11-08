<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type Option = { id: number; name: string; institution_id?: number };
type Filters = {
    search?: string;
    institution_id?: string;
    category_id?: string;
    department_id?: string;
    room_id?: string;
    condition?: string;
    status?: string;
};
type Unit = {
    id: number;
    code: string;
    serial_number: string | null;
    description: string | null;
    department: string | null;
    room: string | null;
    purchase_year: number | null;
    purchase_price: string | null;
    funding_source: string | null;
    condition: string | null;
    condition_notes: string | null;
    disposed_at: string | null;
    disposal_notes: string | null;
    is_active: boolean;
};
type Item = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    institution: string | null;
    category: string | null;
    unit: string;
    units_count: number;
    units: Unit[];
};
const props = defineProps<{
    items: Item[];
    pagination: {
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    } | null;
    institutions: Option[];
    categories: Option[];
    departments: Option[];
    rooms: Option[];
    filters: Filters;
    printing: boolean;
    generated_at: string;
}>();
const page = usePage<{ auth: Auth }>();
const filters = ref({
    search: props.filters.search ?? '',
    institution_id: String(props.filters.institution_id ?? ''),
    category_id: String(props.filters.category_id ?? ''),
    department_id: String(props.filters.department_id ?? ''),
    room_id: String(props.filters.room_id ?? ''),
    condition: props.filters.condition ?? '',
    status: props.filters.status ?? '',
});
const categories = computed(() => options(props.categories));
const departments = computed(() => options(props.departments));
const rooms = computed(() => options(props.rooms));
function options(rows: Option[]) {
    return rows.filter(
        (row) =>
            !filters.value.institution_id ||
            String(row.institution_id) === filters.value.institution_id,
    );
}
watch(
    () => filters.value.institution_id,
    () => {
        filters.value.category_id = '';
        filters.value.department_id = '';
        filters.value.room_id = '';
    },
);
function filter() {
    router.get('/reports', filters.value, {
        preserveState: true,
        replace: true,
    });
}
function reset() {
    router.get('/reports');
}
const printUrl = computed(
    () =>
        '/reports?' +
        new URLSearchParams({
            ...Object.fromEntries(
                Object.entries(props.filters)
                    .filter(([, value]) => value != null && value !== '')
                    .map(([key, value]) => [key, String(value)]),
            ),
            print: '1',
        }).toString(),
);
const printRequest = ref<{ id: number; url: string } | null>(null);
const conditions: Record<string, string> = {
    good: 'Baik',
    minor_damage: 'Rusak ringan',
    major_damage: 'Rusak parah',
    unknown: 'Belum diperiksa',
};
const selectedOptions = computed(() =>
    [
        props.filters.search ? `Pencarian: ${props.filters.search}` : null,
        props.institutions.find(
            (i) => String(i.id) === String(props.filters.institution_id),
        )?.name,
        props.categories.find(
            (i) => String(i.id) === String(props.filters.category_id),
        )?.name,
        props.departments.find(
            (i) => String(i.id) === String(props.filters.department_id),
        )?.name,
        props.rooms.find((i) => String(i.id) === String(props.filters.room_id))
            ?.name,
        props.filters.condition ? conditions[props.filters.condition] : null,
        (
            {
                active: 'Aktif',
                inactive: 'Nonaktif',
                disposed: 'Dimusnahkan',
            } as Record<string, string>
        )[props.filters.status ?? ''],
    ]
        .filter(Boolean)
        .join(' · '),
);
const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,
});
function money(value: string | null) {
    return value === null ? '—' : currency.format(Number(value));
}
function printReport() {
    if (props.printing) {
        window.print();
        return;
    }

    printRequest.value = {
        id: (printRequest.value?.id ?? 0) + 1,
        url: printUrl.value,
    };
}

onMounted(async () => {
    if (
        !props.printing ||
        window.frameElement?.id !== 'inventory-print-frame'
    ) {
        return;
    }

    await document.fonts.ready;
    window.print();
});
</script>
<template>
    <Head title="Laporan barang dan unit aset" />
    <AdminLayout active="laporan" title="Laporan">
        <div class="report">
            <header class="heading">
                <div>
                    <small>LAPORAN INVENTARIS</small>
                    <h1>Barang dan detail unit aset</h1>
                    <p>
                        Barang dikelompokkan beserta identitas, penempatan,
                        pembelian, dan kondisi setiap unit.
                    </p>
                </div>
                <div class="actions no-print">
                    <Link v-if="printing" href="/reports">
                        Kembali ke laporan
                    </Link>
                    <button type="button" class="primary" @click="printReport">
                        Cetak / Simpan PDF
                    </button>
                </div>
            </header>
            <iframe
                v-if="printRequest"
                id="inventory-print-frame"
                :key="printRequest.id"
                :src="printRequest.url"
                class="print-frame no-print"
                title="Laporan untuk dicetak"
                aria-hidden="true"
                tabindex="-1"
            />
            <form
                v-if="!printing"
                class="filters no-print"
                @submit.prevent="filter"
            >
                <label
                    >Cari barang<input
                        v-model="filters.search"
                        placeholder="Kode atau nama katalog"
                        maxlength="150"
                /></label>
                <label v-if="page.props.auth.user.role === 'superadmin'"
                    >Instansi<select v-model="filters.institution_id">
                        <option value="">Semua instansi</option>
                        <option
                            v-for="i in institutions"
                            :key="i.id"
                            :value="String(i.id)"
                        >
                            {{ i.name }}
                        </option>
                    </select></label
                >
                <label
                    >Kategori<select v-model="filters.category_id">
                        <option value="">Semua kategori</option>
                        <option
                            v-for="i in categories"
                            :key="i.id"
                            :value="String(i.id)"
                        >
                            {{ i.name }}
                        </option>
                    </select></label
                >
                <label
                    >Bidang<select v-model="filters.department_id">
                        <option value="">Semua bidang</option>
                        <option
                            v-for="i in departments"
                            :key="i.id"
                            :value="String(i.id)"
                        >
                            {{ i.name }}
                        </option>
                    </select></label
                >
                <label
                    >Ruangan<select v-model="filters.room_id">
                        <option value="">Semua ruangan</option>
                        <option
                            v-for="i in rooms"
                            :key="i.id"
                            :value="String(i.id)"
                        >
                            {{ i.name }}
                        </option>
                    </select></label
                >
                <label
                    >Kondisi<select v-model="filters.condition">
                        <option value="">Semua kondisi</option>
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
                    >Status administratif<select v-model="filters.status">
                        <option value="">Semua status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="disposed">Dimusnahkan</option>
                    </select></label
                >
                <div class="actions">
                    <button class="primary">Tampilkan</button
                    ><button type="button" @click="reset">Reset</button>
                </div>
            </form>
            <div class="report-meta">
                <p>
                    {{ pagination?.total ?? items.length }} jenis barang ·
                    Dibuat {{ generated_at }}
                </p>
                <p v-if="selectedOptions">Filter: {{ selectedOptions }}</p>
                <p>
                    Menampilkan barang dan unit sesuai filter. Status
                    menunjukkan keadaan administratif unit.
                </p>
                <p v-if="pagination && pagination.total > items.length">
                    Halaman ini menampilkan {{ items.length }} jenis barang.
                    Versi cetak memuat seluruh hasil filter.
                </p>
            </div>
            <p v-if="!items.length" class="empty">
                Tidak ada barang atau unit yang sesuai filter.
            </p>
            <section v-for="item in items" :key="item.id" class="item-group">
                <header class="item-heading">
                    <div>
                        <h2>{{ item.name }}</h2>
                        <p>
                            {{ item.code }} · {{ item.institution }} ·
                            {{ item.category }}
                        </p>
                        <p v-if="item.description">{{ item.description }}</p>
                    </div>
                    <span class="badge"
                        >{{ item.units_count }} {{ item.unit }}</span
                    >
                </header>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode / nomor seri</th>
                                <th>Spesifikasi / keterangan</th>
                                <th>Bidang / ruangan</th>
                                <th>Pembelian</th>
                                <th>Kondisi</th>
                                <th>Status / pemusnahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="unit in item.units" :key="unit.id">
                                <td>
                                    <strong>{{ unit.code }}</strong
                                    ><span
                                        >Seri:
                                        {{ unit.serial_number || '—' }}</span
                                    >
                                </td>
                                <td class="unit-description">
                                    {{ unit.description || '—' }}
                                </td>
                                <td>
                                    <strong>{{ unit.department || '—' }}</strong
                                    ><span>{{
                                        unit.room || 'Belum ditempatkan'
                                    }}</span>
                                </td>
                                <td>
                                    <strong>{{
                                        money(unit.purchase_price)
                                    }}</strong
                                    ><span
                                        >Tahun:
                                        {{ unit.purchase_year ?? '—' }}</span
                                    ><span
                                        >Dana:
                                        {{ unit.funding_source || '—' }}</span
                                    >
                                </td>
                                <td>
                                    <strong>{{
                                        conditions[unit.condition ?? 'unknown']
                                    }}</strong
                                    ><span>{{
                                        unit.condition_notes || '—'
                                    }}</span>
                                </td>
                                <td>
                                    <strong>{{
                                        unit.disposed_at
                                            ? 'Dimusnahkan'
                                            : unit.is_active
                                              ? 'Aktif'
                                              : 'Nonaktif'
                                    }}</strong
                                    ><span v-if="unit.disposed_at">{{
                                        unit.disposed_at
                                    }}</span
                                    ><span v-if="unit.disposal_notes">{{
                                        unit.disposal_notes
                                    }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <nav
                v-if="pagination"
                class="pagination no-print"
                aria-label="Halaman laporan"
            >
                <template
                    v-for="(link, index) in pagination.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :class="{ selected: link.active }"
                        ><span v-html="link.label" /></Link
                    ><span v-else class="disabled" v-html="link.label" />
                </template>
            </nav>
        </div>
    </AdminLayout>
</template>
<style scoped>
.print-frame {
    position: fixed;
    inset: 0 auto auto 0;
    width: 100%;
    height: 1px;
    border: 0;
    opacity: 0;
    pointer-events: none;
}

.unit-description {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.report {
    color: #203e33;
}

.heading,
.actions,
.item-heading {
    display: flex;
    gap: 16px;
    align-items: center;
    justify-content: space-between;
}

.heading {
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.heading small {
    color: #7a8c7a;
    font-size: 10px;
    letter-spacing: 1.8px;
}

h1 {
    font-size: 28px;
    margin: 8px 0;
}

p {
    color: #6a7c6b;
    font-size: 12px;
    line-height: 1.6;
    margin: 5px 0;
}

.actions {
    justify-content: flex-start;
    flex-wrap: wrap;
    gap: 8px;
}

button,
.actions a,
input,
select {
    font: inherit;
    font-size: 12px;
    padding: 11px 13px;
    border: 1px solid #dce4d6;
    border-radius: 7px;
    background: white;
    color: #294638;
}

button,
.actions a {
    cursor: pointer;
    text-decoration: none;
}

.primary {
    background: #254a37 !important;
    color: white !important;
    border-color: #254a37 !important;
}

.filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    background: white;
    padding: 22px;
    border: 1px solid #e3e9dd;
    border-radius: 12px;
}

.filters label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 12px;
    min-width: 0;
}

.filters .actions {
    align-self: end;
}

.report-meta {
    margin: 20px 0;
}

.item-group {
    margin-bottom: 24px;
    background: white;
    border: 1px solid #e3e9dd;
    border-radius: 12px;
    overflow: hidden;
}

.item-heading {
    padding: 20px;
    background: #f0f4eb;
}

h2 {
    font-size: 17px;
    margin: 0 0 5px;
}

.badge {
    padding: 7px 12px;
    background: #dce9cf;
    border-radius: 20px;
    white-space: nowrap;
    font-size: 12px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 12px;
}

th {
    color: #6f806e;
    background: #f8faf5;
    font-weight: 500;
}

th,
td {
    padding: 15px 20px;
    border-bottom: 1px solid #edf0e9;
    vertical-align: top;
}

td {
    min-width: 145px;
    overflow-wrap: anywhere;
}

td strong,
td span {
    display: block;
}

td span {
    margin-top: 5px;
    color: #74816e;
    line-height: 1.6;
}

.pagination {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.pagination a,
.pagination > span {
    padding: 8px 12px;
    border: 1px solid #dce4d6;
    border-radius: 6px;
    font-size: 12px;
    text-decoration: none;
    color: #294638;
}

.pagination .selected {
    background: #dce9cf;
}

.disabled {
    opacity: 0.5;
}

.empty {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border: 1px solid #e3e9dd;
    border-radius: 12px;
}

@media (max-width: 1000px) {
    .filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 500px) {
    .filters {
        grid-template-columns: 1fr;
    }

    h1 {
        font-size: 23px;
    }

    .item-heading {
        align-items: flex-start;
    }
}

@media print {
    @page {
        size: A4 landscape;
        margin: 12mm;
    }

    :global(.admin-sidebar),
    :global(.admin-topbar),
    :global(.admin-footer),
    .no-print {
        display: none !important;
    }

    :global(.admin-main) {
        margin: 0 !important;
    }

    :global(.admin-content) {
        padding: 0 !important;
    }

    :global(.admin-shell) {
        background: white !important;
    }

    .heading {
        margin-bottom: 12px;
    }

    .item-group {
        overflow: visible;
        border-radius: 0;
    }

    .item-heading {
        break-after: avoid;
    }

    .table-wrap {
        overflow: visible;
    }

    thead {
        display: table-header-group;
    }

    tr {
        break-inside: avoid;
    }

    th,
    td {
        padding: 8px;
        font-size: 10px;
    }

    td {
        min-width: 0;
    }
}
</style>
