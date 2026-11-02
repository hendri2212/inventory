<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';

type Institution = {
    id: number;
    code: string;
    name: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    is_active: boolean;
};
const props = defineProps<{
    institutions: {
        data: Institution[];
        total: number;
        from: number | null;
        to: number | null;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search: string };
    success?: string;
    errors?: { institution?: string };
}>();
const search = ref(props.filters.search);
const editing = ref<number | null>(null);
const open = ref(false);
const deleting = ref<Institution | null>(null);
const deleteForm = useForm({ institution: '' });
const form = useForm({
    code: '',
    name: '',
    address: '',
    phone: '',
    email: '',
    is_active: true,
});
function show(institution?: Institution) {
    form.reset();
    form.clearErrors();
    editing.value = institution?.id ?? null;
    if (institution)
        Object.assign(form, {
            code: institution.code,
            name: institution.name,
            address: institution.address ?? '',
            phone: institution.phone ?? '',
            email: institution.email ?? '',
            is_active: institution.is_active,
        });
    open.value = true;
}
function save() {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
    };
    if (editing.value) form.put(`/institutions/${editing.value}`, options);
    else form.post('/institutions', options);
}
function remove() {
    if (!deleting.value) return;
    deleteForm.delete(`/institutions/${deleting.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleting.value = null;
        },
    });
}
function filter() {
    router.get('/institutions', search.value ? { search: search.value } : {}, {
        preserveState: true,
        replace: true,
    });
}
</script>

<template>
    <Head title="Manajemen instansi" />
    <AdminLayout active="instansi" title="Instansi">
        <div class="heading">
            <div>
                <small>MANAJEMEN</small>
                <h1>Instansi</h1>
                <p>Kelola identitas dan akses instansi dalam workspace.</p>
            </div>
            <button class="primary" @click="show()">+ Tambah instansi</button>
        </div>
        <p v-if="success" class="notice" role="status">{{ success }}</p>
        <p v-if="errors?.institution && !deleting" class="error" role="alert">
            {{ errors.institution }}
        </p>
        <section class="panel">
            <form class="toolbar" @submit.prevent="filter">
                <label for="search">Cari instansi</label
                ><input
                    id="search"
                    v-model="search"
                    placeholder="Kode atau nama instansi"
                /><button type="submit">Cari</button
                ><span>{{ institutions.total }} instansi</span>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode / nama</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="institution in institutions.data"
                            :key="institution.id"
                        >
                            <td>
                                <strong>{{ institution.name }}</strong
                                ><small>{{ institution.code }}</small>
                            </td>
                            <td>
                                {{ institution.email || '—'
                                }}<small>{{ institution.phone || '—' }}</small>
                            </td>
                            <td class="address">
                                {{ institution.address || '—' }}
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="{
                                        inactive: !institution.is_active,
                                    }"
                                    >{{
                                        institution.is_active
                                            ? 'Aktif'
                                            : 'Nonaktif'
                                    }}</span
                                >
                            </td>
                            <td>
                                <div class="actions">
                                    <button
                                        :aria-label="`Edit ${institution.name}`"
                                        @click="show(institution)"
                                    >
                                        Edit</button
                                    ><button
                                        class="danger"
                                        :aria-label="`Hapus ${institution.name}`"
                                        @click="
                                            deleteForm.clearErrors();
                                            deleting = institution;
                                        "
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!institutions.data.length">
                            <td colspan="5" class="empty">
                                {{
                                    filters.search
                                        ? 'Instansi tidak ditemukan.'
                                        : 'Belum ada instansi. Tambahkan instansi pertama.'
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <span
                    >{{ institutions.from ?? 0 }}–{{
                        institutions.to ?? 0
                    }}
                    dari {{ institutions.total }}</span
                >
                <nav aria-label="Pagination instansi">
                    <template
                        v-for="(link, index) in institutions.links"
                        :key="index"
                        ><Link
                            v-if="link.url"
                            :href="link.url"
                            :class="{ selected: link.active }"
                            :aria-current="link.active ? 'page' : undefined"
                            >{{
                                index === 0
                                    ? 'Sebelumnya'
                                    : index === institutions.links.length - 1
                                      ? 'Berikutnya'
                                      : link.label
                            }}</Link
                        ><span v-else class="disabled">{{
                            index === 0
                                ? 'Sebelumnya'
                                : index === institutions.links.length - 1
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
                role="dialog"
                aria-modal="true"
                aria-labelledby="form-title"
                class="modal-card"
            >
                <h2 id="form-title">
                    {{ editing ? 'Edit instansi' : 'Tambah instansi' }}
                </h2>
                <form @submit.prevent="save">
                    <div class="fields">
                        <label
                            >Kode instansi *<input
                                v-model="form.code"
                                required
                                maxlength="50"
                                autofocus
                            /><span class="error">{{
                                form.errors.code
                            }}</span></label
                        ><label
                            >Nama instansi *<input
                                v-model="form.name"
                                required
                                maxlength="150"
                            /><span class="error">{{
                                form.errors.name
                            }}</span></label
                        ><label
                            >Email<input
                                v-model="form.email"
                                type="email"
                                maxlength="255"
                            /><span class="error">{{
                                form.errors.email
                            }}</span></label
                        ><label
                            >Telepon<input
                                v-model="form.phone"
                                maxlength="30"
                            /><span class="error">{{
                                form.errors.phone
                            }}</span></label
                        ><label class="wide"
                            >Alamat<textarea
                                v-model="form.address"
                                rows="3"
                                maxlength="5000"
                            ></textarea
                            ><span class="error">{{
                                form.errors.address
                            }}</span></label
                        >
                    </div>
                    <label class="checkbox"
                        ><input v-model="form.is_active" type="checkbox" />
                        Instansi aktif</label
                    >
                    <p class="hint">
                        Instansi nonaktif membatasi akses akun di dalamnya.
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
                <h2 id="delete-title">Hapus instansi?</h2>
                <p>Instansi {{ deleting.name }} akan dihapus permanen.</p>
                <p
                    v-if="deleteForm.errors.institution"
                    class="error"
                    role="alert"
                >
                    {{ deleteForm.errors.institution }}
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
                                : 'Hapus instansi'
                        }}
                    </button>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
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
