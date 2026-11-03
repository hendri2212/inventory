<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type Department = {
    id: number;
    institution_id: number;
    institution: { name: string };
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
};
const props = defineProps<{
    departments: {
        data: Department[];
        total: number;
        from: number | null;
        to: number | null;
        links: { url: string | null; label: string; active: boolean }[];
    };
    institutions: { id: number; name: string }[];
    filters: { search: string; institution_id: string };
    success?: string;
}>();
const page = usePage<{ auth: Auth }>();
const search = ref(props.filters.search);
const institutionFilter = ref(props.filters.institution_id);
const editing = ref<number | null>(null);
const open = ref(false);
const deleting = ref<Department | null>(null);
const deleteForm = useForm({ department: '' });
const form = useForm({
    institution_id: null as number | null,
    code: '',
    name: '',
    description: '',
    is_active: true,
});
function show(department?: Department) {
    form.reset();
    form.clearErrors();
    editing.value = department?.id ?? null;
    Object.assign(
        form,
        department
            ? {
                  institution_id: department.institution_id,
                  code: department.code,
                  name: department.name,
                  description: department.description ?? '',
                  is_active: department.is_active,
              }
            : {
                  institution_id:
                      page.props.auth.user.role === 'admin'
                          ? page.props.auth.user.institution_id
                          : (props.institutions.find(
                                (item) =>
                                    String(item.id) === institutionFilter.value,
                            )?.id ?? null),
              },
    );
    open.value = true;
}
function save() {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
    };
    if (editing.value) form.put(`/departments/${editing.value}`, options);
    else form.post('/departments', options);
}
function remove() {
    if (deleting.value)
        deleteForm.delete(`/departments/${deleting.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleting.value = null;
            },
        });
}
function filter() {
    router.get(
        '/departments',
        { search: search.value, institution_id: institutionFilter.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Manajemen bidang" />
    <AdminLayout active="bidang" title="Bidang">
        <div class="heading">
            <div>
                <small>MANAJEMEN</small>
                <h1>Bidang</h1>
                <p>Kelola bidang dan unit kerja di setiap instansi.</p>
            </div>
            <button
                class="primary"
                :disabled="!institutions.length"
                @click="show()"
            >
                + Tambah bidang
            </button>
        </div>
        <p v-if="success" class="notice" role="status">{{ success }}</p>
        <p v-if="!institutions.length" class="notice">
            Tambahkan instansi terlebih dahulu sebelum membuat bidang.
        </p>
        <section class="panel">
            <form class="toolbar" @submit.prevent="filter">
                <label for="search">Cari bidang</label
                ><input
                    id="search"
                    v-model="search"
                    placeholder="Kode atau nama bidang"
                /><select
                    v-model="institutionFilter"
                    aria-label="Filter instansi"
                >
                    <option value="">Semua instansi</option>
                    <option
                        v-for="institution in institutions"
                        :key="institution.id"
                        :value="String(institution.id)"
                    >
                        {{ institution.name }}
                    </option></select
                ><button>Cari</button
                ><span>{{ departments.total }} bidang</span>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode / nama</th>
                            <th>Instansi</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="department in departments.data"
                            :key="department.id"
                        >
                            <td>
                                <strong>{{ department.name }}</strong
                                ><small>{{ department.code }}</small>
                            </td>
                            <td>{{ department.institution.name }}</td>
                            <td class="address">
                                {{ department.description || '—' }}
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="{ inactive: !department.is_active }"
                                    >{{
                                        department.is_active
                                            ? 'Aktif'
                                            : 'Nonaktif'
                                    }}</span
                                >
                            </td>
                            <td>
                                <div class="actions">
                                    <button
                                        :aria-label="`Edit ${department.name}`"
                                        @click="show(department)"
                                    >
                                        Edit</button
                                    ><button
                                        class="danger"
                                        :aria-label="`Hapus ${department.name}`"
                                        @click="
                                            deleteForm.clearErrors();
                                            deleting = department;
                                        "
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!departments.data.length">
                            <td colspan="5" class="empty">
                                {{
                                    filters.search || filters.institution_id
                                        ? 'Bidang tidak ditemukan.'
                                        : 'Belum ada bidang. Tambahkan bidang pertama.'
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <span
                    >{{ departments.from ?? 0 }}–{{ departments.to ?? 0 }} dari
                    {{ departments.total }}</span
                >
                <nav aria-label="Pagination bidang">
                    <template
                        v-for="(link, index) in departments.links"
                        :key="index"
                        ><Link
                            v-if="link.url"
                            :href="link.url"
                            :class="{ selected: link.active }"
                            :aria-current="link.active ? 'page' : undefined"
                            >{{
                                index === 0
                                    ? 'Sebelumnya'
                                    : index === departments.links.length - 1
                                      ? 'Berikutnya'
                                      : link.label
                            }}</Link
                        ><span v-else class="disabled">{{
                            index === 0
                                ? 'Sebelumnya'
                                : index === departments.links.length - 1
                                  ? 'Berikutnya'
                                  : link.label
                        }}</span></template
                    >
                </nav>
            </div>
        </section>
        <div
            v-if="open"
            class="overlay"
            @keydown.esc="!form.processing && (open = false)"
        >
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="form-title"
            >
                <h2 id="form-title">
                    {{ editing ? 'Edit bidang' : 'Tambah bidang' }}
                </h2>
                <form @submit.prevent="save">
                    <div class="fields">
                        <label class="wide"
                            >Instansi *<select
                                v-model="form.institution_id"
                                required
                                :disabled="
                                    !!editing ||
                                    page.props.auth.user.role === 'admin'
                                "
                            >
                                <option :value="null" disabled>
                                    Pilih instansi
                                </option>
                                <option
                                    v-for="institution in institutions"
                                    :key="institution.id"
                                    :value="institution.id"
                                >
                                    {{ institution.name }}
                                </option></select
                            ><span class="error">{{
                                form.errors.institution_id
                            }}</span
                            ><small v-if="editing"
                                >Instansi bidang tidak dapat dipindah.</small
                            ></label
                        >
                        <label
                            >Kode bidang *<input
                                v-model="form.code"
                                required
                                maxlength="50"
                            /><span class="error">{{
                                form.errors.code
                            }}</span></label
                        ><label
                            >Nama bidang *<input
                                v-model="form.name"
                                required
                                maxlength="150"
                            /><span class="error">{{
                                form.errors.name
                            }}</span></label
                        ><label class="wide"
                            >Deskripsi<textarea
                                v-model="form.description"
                                rows="3"
                                maxlength="5000"
                            ></textarea
                            ><span class="error">{{
                                form.errors.description
                            }}</span></label
                        >
                    </div>
                    <label class="checkbox"
                        ><input v-model="form.is_active" type="checkbox" />
                        Bidang aktif</label
                    >
                    <p class="hint">
                        Bidang nonaktif membatasi akses operator di dalamnya.
                    </p>
                    <span class="error">{{ form.errors.is_active }}</span>
                    <div class="modal-actions">
                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="open = false"
                        >
                            Batal</button
                        ><button class="primary" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan…' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
        <div v-if="deleting" class="overlay">
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-title"
            >
                <h2 id="delete-title">Hapus bidang?</h2>
                <p>Bidang {{ deleting.name }} akan dihapus permanen.</p>
                <p
                    v-if="deleteForm.errors.department"
                    class="error"
                    role="alert"
                >
                    {{ deleteForm.errors.department }}
                </p>
                <div class="modal-actions">
                    <button
                        :disabled="deleteForm.processing"
                        @click="deleting = null"
                    >
                        Batal</button
                    ><button
                        class="danger"
                        :disabled="deleteForm.processing"
                        @click="remove"
                    >
                        {{
                            deleteForm.processing
                                ? 'Menghapus…'
                                : 'Hapus bidang'
                        }}
                    </button>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
<style scoped>
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
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.fields label {
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
