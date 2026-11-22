<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import { date, localNow } from '../Loans/types';
import type { Transfer } from './types';
import '../Loans/loans.css';
type Unit = {
    id: number;
    institution_id: number;
    department_id: number;
    room_id: number | null;
    code: string;
    serial_number: string | null;
    item: { name: string } | null;
};
const props = defineProps<{
    transfers: {
        data: Transfer[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    can_create: boolean;
    institutions: { id: number; name: string }[];
    departments: { id: number; institution_id: number; name: string }[];
    rooms: {
        id: number;
        institution_id: number;
        name: string;
        is_active: boolean;
    }[];
    units: Unit[];
    filters: { search?: string; type?: string };
    success?: string;
}>();
const open = ref(false);
const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');
const unitSearch = ref('');
const form = useForm({
    institution_id: null as number | null,
    type: 'room' as 'room' | 'department',
    from_department_id: null as number | null,
    to_department_id: null as number | null,
    from_room_id: null as number | null,
    to_room_id: null as number | null,
    transferred_at: localNow(),
    notes: '',
    unit_ids: [] as number[],
});
const departments = computed(() =>
    props.departments.filter((d) => d.institution_id === form.institution_id),
);
const rooms = computed(() =>
    props.rooms.filter((r) => r.institution_id === form.institution_id),
);
const available = computed(() =>
    props.units.filter(
        (u) =>
            u.institution_id === form.institution_id &&
            u.department_id === form.from_department_id &&
            u.room_id === form.from_room_id &&
            `${u.code} ${u.item?.name ?? ''} ${u.serial_number ?? ''}`
                .toLowerCase()
                .includes(unitSearch.value.toLowerCase()),
    ),
);
watch(
    () => form.institution_id,
    () => {
        form.from_department_id = null;
        form.to_department_id = null;
        form.from_room_id = null;
        form.to_room_id = null;
        form.unit_ids = [];
    },
);
watch(
    () => form.from_department_id,
    () => {
        form.unit_ids = [];
        if (form.type === 'room')
            form.to_department_id = form.from_department_id;
    },
);
watch(
    () => form.from_room_id,
    () => {
        form.unit_ids = [];
    },
);
watch(
    () => form.type,
    () => {
        form.to_department_id =
            form.type === 'room' ? form.from_department_id : null;
        form.to_room_id = null;
    },
);
function start() {
    form.reset();
    form.clearErrors();
    form.transferred_at = localNow();
    if (props.institutions.length === 1)
        form.institution_id = props.institutions[0].id;
    unitSearch.value = '';
    open.value = true;
}
function filter() {
    router.get(
        '/item-transfers',
        { search: search.value, type: type.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Mutasi unit barang" /><AdminLayout
        active="mutasi"
        title="Mutasi unit barang"
        ><div class="loan-page">
            <div class="loan-heading">
                <div>
                    <h1>Mutasi unit barang</h1>
                    <p>
                        Pindahkan penempatan unit dan simpan riwayat
                        perpindahannya.
                    </p>
                </div>
                <button v-if="can_create" class="primary" @click="start">
                    + Tambah mutasi
                </button>
            </div>
            <p v-if="success" role="status" class="success">{{ success }}</p>
            <form class="loan-toolbar" @submit.prevent="filter">
                <input
                    v-model="search"
                    aria-label="Cari mutasi"
                    placeholder="Cari nomor atau catatan"
                /><select v-model="type" aria-label="Jenis mutasi">
                    <option value="">Semua jenis</option>
                    <option value="room">Antarruang</option>
                    <option value="department">Antarbidang</option></select
                ><button>Cari</button>
            </form>
            <div class="loan-panel loan-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nomor / instansi</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Asal</th>
                            <th>Tujuan</th>
                            <th>Unit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in transfers.data" :key="t.id">
                            <td>
                                <strong>{{ t.code }}</strong
                                ><small>{{ t.institution.name }}</small>
                            </td>
                            <td>{{ date(t.transferred_at) }}</td>
                            <td>
                                {{
                                    t.type === 'room'
                                        ? 'Antarruang'
                                        : 'Antarbidang'
                                }}
                            </td>
                            <td>
                                {{ t.from_department_name || '—'
                                }}<small>{{
                                    t.from_room?.name || 'Belum ditempatkan'
                                }}</small>
                            </td>
                            <td>
                                {{ t.to_department_name || '—'
                                }}<small>{{
                                    t.to_room?.name || 'Belum ditempatkan'
                                }}</small>
                            </td>
                            <td>{{ t.items_count }}</td>
                            <td>
                                <Link :href="`/item-transfers/${t.id}`"
                                    >Detail / cetak</Link
                                >
                            </td>
                        </tr>
                        <tr v-if="!transfers.data.length">
                            <td colspan="7">Belum ada riwayat mutasi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav class="loan-toolbar" aria-label="Halaman">
                <template v-for="(link, i) in transfers.links" :key="i"
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
                    aria-labelledby="transfer-title"
                >
                    <div class="loan-heading">
                        <h2 id="transfer-title">Tambah mutasi</h2>
                        <button
                            type="button"
                            aria-label="Tutup"
                            @click="open = false"
                        >
                            ×
                        </button>
                    </div>
                    <form @submit.prevent="form.post('/item-transfers')">
                        <div class="loan-fields">
                            <label
                                >Instansi<select
                                    v-model="form.institution_id"
                                    required
                                >
                                    <option :value="null">
                                        Pilih instansi
                                    </option>
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
                                >Jenis mutasi<select v-model="form.type">
                                    <option value="room">
                                        Antarruang (bidang tetap)
                                    </option>
                                    <option value="department">
                                        Antarbidang
                                    </option>
                                </select></label
                            >
                            <label
                                >Bidang asal<select
                                    v-model="form.from_department_id"
                                    required
                                >
                                    <option :value="null">
                                        Pilih bidang asal
                                    </option>
                                    <option
                                        v-for="d in departments"
                                        :key="d.id"
                                        :value="d.id"
                                    >
                                        {{ d.name }}
                                    </option>
                                </select></label
                            >
                            <label
                                >Ruang asal<select v-model="form.from_room_id">
                                    <option :value="null">
                                        Belum ditempatkan
                                    </option>
                                    <option
                                        v-for="r in rooms"
                                        :key="r.id"
                                        :value="r.id"
                                    >
                                        {{ r.name
                                        }}{{ r.is_active ? '' : ' (nonaktif)' }}
                                    </option>
                                </select></label
                            >
                            <label v-if="form.type === 'department'"
                                >Bidang tujuan<select
                                    v-model="form.to_department_id"
                                    required
                                >
                                    <option :value="null">
                                        Pilih bidang tujuan
                                    </option>
                                    <option
                                        v-for="d in departments.filter(
                                            (d) =>
                                                d.id !==
                                                form.from_department_id,
                                        )"
                                        :key="d.id"
                                        :value="d.id"
                                    >
                                        {{ d.name }}
                                    </option>
                                </select></label
                            >
                            <label
                                >Ruang tujuan<select v-model="form.to_room_id">
                                    <option :value="null">
                                        Belum ditempatkan
                                    </option>
                                    <option
                                        v-for="r in rooms.filter(
                                            (r) => r.is_active,
                                        )"
                                        :key="r.id"
                                        :value="r.id"
                                    >
                                        {{ r.name }}
                                    </option>
                                </select></label
                            >
                            <label
                                >Tanggal mutasi (WITA)<input
                                    v-model="form.transferred_at"
                                    type="datetime-local"
                                    required /></label
                            ><label
                                >Alasan / catatan<textarea
                                    v-model="form.notes"
                                    required
                                    maxlength="5000"
                                />
                            </label>
                        </div>
                        <h3>Pilih unit dari penempatan asal</h3>
                        <p>
                            Hanya unit yang tidak sedang dipinjam ditampilkan.
                            Kode unit tetap setelah mutasi.
                        </p>
                        <input
                            v-model="unitSearch"
                            aria-label="Cari unit"
                            placeholder="Cari kode, barang, atau nomor seri"
                        />
                        <div class="loan-options">
                            <label
                                v-for="u in available"
                                :key="u.id"
                                class="loan-option"
                                ><input
                                    v-model="form.unit_ids"
                                    type="checkbox"
                                    :value="u.id"
                                />{{ u.code }} · {{ u.item?.name || '—' }} ·
                                {{
                                    u.serial_number || 'Tanpa nomor seri'
                                }}</label
                            >
                            <p v-if="!available.length">
                                Tidak ada unit tersedia. Periksa bidang dan
                                ruang asal.
                            </p>
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
                                    form.processing || !form.unit_ids.length
                                "
                            >
                                Simpan mutasi ({{ form.unit_ids.length }} unit)
                            </button>
                        </footer>
                    </form>
                </section>
            </div>
        </div></AdminLayout
    >
</template>
