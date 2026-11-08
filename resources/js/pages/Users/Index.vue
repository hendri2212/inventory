<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type UserRow = {
    id: number;
    name: string;
    email: string;
    role: string;
    institution_id: number | null;
    department_id: number | null;
    institution: { name: string } | null;
    department: { name: string } | null;
    can_update: boolean;
    can_delete: boolean;
};
const props = defineProps<{
    users: {
        data: UserRow[];
        total: number;
        from: number | null;
        to: number | null;
        links: { url: string | null; label: string; active: boolean }[];
    };
    institutions: { id: number; name: string }[];
    departments: { id: number; name: string; institution_id: number }[];
    roles: string[];
    filters: { search: string; role: string };
    success?: string;
}>();
const page = usePage<{ auth: Auth }>();
const search = ref(props.filters.search);
const roleFilter = ref(props.filters.role);
const editing = ref<number | null>(null);
const open = ref(false);
const deleting = ref<UserRow | null>(null);
const deleteForm = useForm({});
const form = useForm({
    name: '',
    email: '',
    role: 'operator',
    institution_id: null as number | null,
    department_id: null as number | null,
    password: '',
    password_confirmation: '',
});
const needsDepartment = computed(() => ['operator'].includes(form.role));
const departments = computed(() =>
    props.departments.filter(
        (item) => item.institution_id === form.institution_id,
    ),
);
watch(
    () => form.role,
    (role) => {
        if (role === 'superadmin') form.institution_id = null;
        if (!['operator'].includes(role)) form.department_id = null;
    },
);
watch(
    () => form.institution_id,
    () => {
        if (!departments.value.some((item) => item.id === form.department_id))
            form.department_id = null;
    },
);
function show(user?: UserRow) {
    form.reset();
    form.clearErrors();
    editing.value = user?.id ?? null;
    Object.assign(
        form,
        user
            ? {
                  name: user.name,
                  email: user.email,
                  role: user.role,
                  institution_id: user.institution_id,
                  department_id: user.department_id,
              }
            : {
                  role: 'operator',
                  institution_id:
                      page.props.auth.user.role === 'admin'
                          ? page.props.auth.user.institution_id
                          : null,
              },
    );
    open.value = true;
}
function save() {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
    };
    if (editing.value) form.put(`/users/${editing.value}`, options);
    else form.post('/users', options);
}
function remove() {
    if (deleting.value)
        deleteForm.delete(`/users/${deleting.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleting.value = null;
            },
        });
}
function filter() {
    router.get(
        '/users',
        { search: search.value, role: roleFilter.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Manajemen pengguna" />
    <AdminLayout active="pengguna" title="Pengguna">
        <div class="heading">
            <div>
                <small>MANAJEMEN</small>
                <h1>Pengguna</h1>
                <p>Kelola akun, role, dan penempatan pengguna.</p>
            </div>
            <button class="primary" @click="show()">+ Tambah pengguna</button>
        </div>
        <p v-if="success" class="notice" role="status">{{ success }}</p>
        <section class="panel">
            <form class="toolbar" @submit.prevent="filter">
                <label for="search">Cari pengguna</label
                ><input
                    id="search"
                    v-model="search"
                    placeholder="Nama atau email"
                /><select v-model="roleFilter" aria-label="Filter role">
                    <option value="">Semua role</option>
                    <option
                        v-for="role in [
                            'superadmin',
                            'admin',
                            'operator',
                        ].filter(
                            (role) =>
                                page.props.auth.user.role === 'superadmin' ||
                                role !== 'superadmin',
                        )"
                        :key="role"
                        :value="role"
                    >
                        {{ role }}
                    </option></select
                ><button>Cari</button><span>{{ users.total }} pengguna</span>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Instansi / bidang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td>
                                <strong
                                    >{{ user.name }}
                                    {{
                                        user.id === page.props.auth.user.id
                                            ? '(Anda)'
                                            : ''
                                    }}</strong
                                ><small>{{ user.email }}</small>
                            </td>
                            <td>
                                <span class="badge">{{ user.role }}</span>
                            </td>
                            <td>
                                {{ user.institution?.name ?? 'Semua instansi'
                                }}<small>{{
                                    user.department?.name ?? '—'
                                }}</small>
                            </td>
                            <td>
                                <div class="actions">
                                    <button
                                        v-if="user.can_update"
                                        :aria-label="`Edit ${user.name}`"
                                        @click="show(user)"
                                    >
                                        Edit</button
                                    ><button
                                        v-if="user.can_delete"
                                        class="danger"
                                        :aria-label="`Hapus ${user.name}`"
                                        @click="deleting = user"
                                    >
                                        Hapus</button
                                    ><span
                                        v-if="
                                            !user.can_update && !user.can_delete
                                        "
                                        >—</span
                                    >
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td colspan="4" class="empty">
                                Pengguna tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <span
                    >{{ users.from ?? 0 }}–{{ users.to ?? 0 }} dari
                    {{ users.total }}</span
                >
                <nav aria-label="Pagination pengguna">
                    <template v-for="(link, index) in users.links" :key="index"
                        ><Link
                            v-if="link.url"
                            :href="link.url"
                            :class="{ selected: link.active }"
                            :aria-current="link.active ? 'page' : undefined"
                            >{{
                                index === 0
                                    ? 'Sebelumnya'
                                    : index === users.links.length - 1
                                      ? 'Berikutnya'
                                      : link.label
                            }}</Link
                        ><span v-else class="disabled">{{
                            index === 0
                                ? 'Sebelumnya'
                                : index === users.links.length - 1
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
                aria-labelledby="user-title"
            >
                <h2 id="user-title">
                    {{ editing ? 'Edit pengguna' : 'Tambah pengguna' }}
                </h2>
                <form @submit.prevent="save">
                    <div class="fields">
                        <label
                            >Nama *<input
                                v-model="form.name"
                                required
                                maxlength="255"
                                autofocus
                            /><span class="error">{{
                                form.errors.name
                            }}</span></label
                        >
                        <label
                            >Email *<input
                                v-model="form.email"
                                type="email"
                                required
                                maxlength="255"
                                autocomplete="off"
                            /><span class="error">{{
                                form.errors.email
                            }}</span></label
                        >
                        <label
                            >Role *<select v-model="form.role">
                                <option
                                    v-for="role in roles"
                                    :key="role"
                                    :value="role"
                                >
                                    {{ role }}
                                </option></select
                            ><span class="error">{{
                                form.errors.role
                            }}</span></label
                        >
                        <label
                            v-if="
                                page.props.auth.user.role === 'superadmin' &&
                                form.role !== 'superadmin'
                            "
                            >Instansi *<select
                                v-model="form.institution_id"
                                required
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
                            }}</span></label
                        >
                        <label v-if="needsDepartment" class="wide"
                            >Bidang *<select
                                v-model="form.department_id"
                                required
                            >
                                <option :value="null" disabled>
                                    Pilih bidang
                                </option>
                                <option
                                    v-for="department in departments"
                                    :key="department.id"
                                    :value="department.id"
                                >
                                    {{ department.name }}
                                </option></select
                            ><span class="error">{{
                                form.errors.department_id
                            }}</span
                            ><small
                                v-if="
                                    form.institution_id && !departments.length
                                "
                                >Belum ada bidang aktif pada instansi
                                ini.</small
                            ></label
                        >
                        <label
                            >Password {{ editing ? '(opsional)' : '*'
                            }}<input
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                :required="!editing"
                                minlength="8"
                                maxlength="255"
                            /><span class="error">{{
                                form.errors.password
                            }}</span></label
                        >
                        <label
                            >Konfirmasi password<input
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                :required="!!form.password"
                        /></label>
                    </div>
                    <p v-if="editing" class="hint">
                        Kosongkan password untuk mempertahankan password saat
                        ini.
                    </p>
                    <div class="modal-actions">
                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="
                                open = false;
                                form.reset();
                            "
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
                <h2 id="delete-title">Hapus pengguna?</h2>
                <p>Akun {{ deleting.name }} akan dihapus permanen.</p>
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
                                : 'Hapus pengguna'
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
select,
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
