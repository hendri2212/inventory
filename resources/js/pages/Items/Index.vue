<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppIcon from '../../components/AppIcon.vue';
import AdminLayout from '../../layouts/AdminLayout.vue';
import type { Auth } from '../../types/auth';
type Option = { id: number; name: string; institution_id?: number };
type Item = {
    id: number;
    institution_id: number;
    category_id: number;
    code: string;
    name: string;
    description: string | null;
    unit: string;
    units_count: number;
    institution: Option;
    category: Option;
    can_update: boolean;
    can_delete: boolean;
};
const props = defineProps<{
    items: {
        data: Item[];
        total: number;
        from: number | null;
        to: number | null;
        links: { url: string | null; label: string; active: boolean }[];
    };
    institutions: Option[];
    categories: Option[];
    can_create: boolean;
    filters: { search?: string; institution_id?: string; category_id?: string };
    success?: string;
}>();
const page = usePage<{ auth: Auth }>();
const search = ref(props.filters.search ?? '');
const institutionFilter = ref(props.filters.institution_id ?? '');
const categoryFilter = ref(props.filters.category_id ?? '');
const filterCategories = computed(() =>
    props.categories.filter(
        (category) =>
            !institutionFilter.value ||
            String(category.institution_id) === institutionFilter.value,
    ),
);
watch(institutionFilter, () => {
    categoryFilter.value = '';
});
function filter() {
    router.get(
        '/items',
        {
            search: search.value,
            institution_id: institutionFilter.value,
            category_id: categoryFilter.value,
        },
        { preserveState: true, replace: true },
    );
}
const open = ref(false);
const editing = ref<number | null>(null);
const deleting = ref<Item | null>(null);
const deletion = useForm({ item: '' });
const form = useForm({
    institution_id: null as number | null,
    category_id: null as number | null,
    code: '',
    name: '',
    description: '',
    unit: 'unit',
});
const categories = computed(() =>
    props.categories.filter((c) => c.institution_id === form.institution_id),
);
watch(
    () => form.institution_id,
    () => {
        if (!categories.value.some((c) => c.id === form.category_id))
            form.category_id = null;
    },
);
function show(item?: Item) {
    form.reset();
    form.clearErrors();
    editing.value = item?.id ?? null;
    Object.assign(
        form,
        item
            ? {
                  institution_id: item.institution_id,
                  category_id: item.category_id,
                  code: item.code,
                  name: item.name,
                  description: item.description ?? '',
                  unit: item.unit,
              }
            : { institution_id: page.props.auth.user.institution_id },
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
    if (editing.value) form.put(`/items/${editing.value}`, options);
    else form.post('/items', options);
}
function remove() {
    if (deleting.value)
        deletion.delete(`/items/${deleting.value.id}`, {
            onSuccess: () => {
                deleting.value = null;
            },
        });
}
</script>
<template>
    <Head title="Manajemen barang" />
    <AdminLayout active="barang" title="Manajemen barang">
        <div class="heading">
            <div>
                <small>INVENTARIS</small>
                <h1>Manajemen barang</h1>
                <p>
                    Data umum barang. Identitas dan kondisi dicatat pada setiap
                    unit aset.
                </p>
            </div>
            <div class="actions">
                <Link class="primary" href="/item-units">Unit aset</Link
                ><button v-if="can_create" class="primary" @click="show()">
                    + Tambah barang
                </button>
            </div>
        </div>
        <p v-if="success" class="notice" role="status">{{ success }}</p>
        <p v-if="deletion.errors.item" class="notice" role="alert">
            {{ deletion.errors.item }}
        </p>
        <section class="panel">
            <form class="toolbar" @submit.prevent="filter">
                <label for="search">Cari barang</label
                ><input
                    id="search"
                    v-model="search"
                    placeholder="Kode atau nama barang"
                /><select
                    v-if="page.props.auth.user.role === 'superadmin'"
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
                ><select v-model="categoryFilter" aria-label="Filter kategori">
                    <option value="">Semua kategori</option>
                    <option
                        v-for="category in filterCategories"
                        :key="category.id"
                        :value="String(category.id)"
                    >
                        {{ category.name }}
                    </option></select
                ><button>Cari</button
                ><span>{{ items.total }} jenis barang</span>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Instansi / kategori</th>
                            <th>Unit tercatat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items.data" :key="item.id">
                            <td>
                                <strong>{{ item.name }}</strong
                                ><small
                                    >{{ item.code }} ·
                                    {{ item.description }}</small
                                >
                            </td>
                            <td>
                                {{ item.institution.name
                                }}<small>{{ item.category.name }}</small>
                            </td>
                            <td>
                                <Link :href="`/item-units?item_id=${item.id}`"
                                    >{{ item.units_count }}
                                    {{ item.unit }}</Link
                                >
                            </td>
                            <td>
                                <div
                                    v-if="item.can_update || item.can_delete"
                                    class="table-action-group"
                                    role="group"
                                    :aria-label="`Aksi ${item.name}`"
                                >
                                    <button
                                        type="button"
                                        v-if="item.can_update"
                                        :aria-label="`Edit ${item.name}`"
                                        @click="show(item)"
                                    >
                                        <AppIcon name="pencil" :size="15" />
                                        Edit</button
                                    ><button
                                        type="button"
                                        v-if="item.can_delete"
                                        class="danger"
                                        :aria-label="`Hapus ${item.name}`"
                                        @click="
                                            deleting = item;
                                            deletion.clearErrors();
                                        "
                                    >
                                        <AppIcon name="trash" :size="15" />
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!items.data.length">
                            <td colspan="4">Belum ada barang.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <span
                    >{{ items.from ?? 0 }}–{{ items.to ?? 0 }} dari
                    {{ items.total }}</span
                >
                <nav aria-label="Pagination barang">
                    <template v-for="(link, index) in items.links" :key="index"
                        ><Link
                            v-if="link.url"
                            :href="link.url"
                            :class="{ selected: link.active }"
                            :aria-current="link.active ? 'page' : undefined"
                            >{{
                                index === 0
                                    ? 'Sebelumnya'
                                    : index === items.links.length - 1
                                      ? 'Berikutnya'
                                      : link.label
                            }}</Link
                        ><span v-else class="disabled">{{
                            index === 0
                                ? 'Sebelumnya'
                                : index === items.links.length - 1
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
                aria-labelledby="catalog-title"
                @keydown.esc="open = false"
            >
                <header>
                    <h2 id="catalog-title">
                        {{ editing ? 'Edit barang' : 'Tambah barang' }}
                    </h2>
                    <button aria-label="Tutup" @click="open = false">×</button>
                </header>
                <form @submit.prevent="save">
                    <div class="fields">
                        <label v-if="page.props.auth.user.role === 'superadmin'"
                            >Instansi<select
                                v-model="form.institution_id"
                                required
                                :disabled="!!editing"
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
                        ><label
                            >Kategori<select
                                v-model="form.category_id"
                                required
                            >
                                <option :value="null">Pilih kategori</option>
                                <option
                                    v-for="c in categories"
                                    :key="c.id"
                                    :value="c.id"
                                >
                                    {{ c.name }}
                                </option>
                            </select></label
                        ><label
                            >Kode katalog<input
                                v-model="form.code"
                                required
                                maxlength="50" /></label
                        ><label
                            >Nama barang<input
                                v-model="form.name"
                                required
                                maxlength="150" /></label
                        ><label
                            >Satuan<input
                                v-model="form.unit"
                                required
                                maxlength="30" /></label
                        ><label
                            >Deskripsi umum barang<textarea
                                v-model="form.description"
                            />
                        </label>
                    </div>
                    <p
                        v-for="(error, field) in form.errors"
                        :key="field"
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
        <div v-if="deleting" class="overlay" @click.self="deleting = null">
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-title"
            >
                <h2 id="delete-title">Hapus {{ deleting.name }}?</h2>
                <p>Barang yang memiliki unit aset tidak dapat dihapus.</p>
                <p v-if="deletion.errors.item" class="error">
                    {{ deletion.errors.item }}
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
a.primary,
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
.actions a {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
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
