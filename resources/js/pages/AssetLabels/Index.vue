<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../layouts/AdminLayout.vue';

type Unit = { id: number; code: string; item: string | null; institution: string | null; department: string | null; purchase_year: number | null; funding_source: string | null };
const props = defineProps<{ units: Unit[] }>();
const search = ref('');
const selected = reactive<Record<number, number>>({});
const settings = reactive({ paperWidth: 210, paperHeight: 297, width: 63.5, height: 38.1, columns: 3, rows: 7, left: 7.5, top: 15, gapX: 2, gapY: 0, start: 1 });
const filtered = computed(() => {
    const term = search.value.trim().toLocaleLowerCase();
    return props.units.filter((u) => [u.code, u.item, u.department, u.institution].some((v) => v?.toLocaleLowerCase().includes(term)));
});
const validLayout = computed(() => {
    const s = settings;
    return Object.values(s).every((v) => Number.isFinite(v)) && s.paperWidth > 0 && s.paperHeight > 0 && s.width >= 30 && s.height >= 20 && Number.isInteger(s.columns) && s.columns >= 1 && s.columns <= 20 && Number.isInteger(s.rows) && s.rows >= 1 && s.rows <= 50 && s.left >= 0 && s.top >= 0 && s.gapX >= 0 && s.gapY >= 0 && Number.isInteger(s.start) && s.start >= 1 && s.start <= s.columns * s.rows && s.left + s.columns * s.width + (s.columns - 1) * s.gapX <= s.paperWidth && s.top + s.rows * s.height + (s.rows - 1) * s.gapY <= s.paperHeight;
});
const validCounts = computed(() => Object.values(selected).every((n) => Number.isInteger(n) && n >= 1 && n <= 100));
const labelCount = computed(() => props.units.reduce((total, u) => total + (selected[u.id] || 0), 0));
const pages = computed(() => {
    if (!validLayout.value || !validCounts.value || labelCount.value > 1000) return [];
    const cells: (Unit | null)[] = Array(settings.start - 1).fill(null);
    for (const unit of props.units) for (let i = 0; i < (selected[unit.id] || 0); i++) cells.push(unit);
    const size = settings.rows * settings.columns;
    const result: (Unit | null)[][] = [];
    for (let i = 0; i < cells.length; i += size) result.push(cells.slice(i, i + size));
    return labelCount.value ? result : [];
});
const sheetStyle = computed(() => ({ width: `${settings.paperWidth}mm`, height: `${settings.paperHeight}mm`, paddingLeft: `${settings.left}mm`, paddingTop: `${settings.top}mm`, gridTemplateColumns: `repeat(${settings.columns}, ${settings.width}mm)`, gridTemplateRows: `repeat(${settings.rows}, ${settings.height}mm)`, columnGap: `${settings.gapX}mm`, rowGap: `${settings.gapY}mm` }));
function toggle(unit: Unit, checked: boolean) { if (checked) selected[unit.id] = 1; else delete selected[unit.id]; }
function selectVisible() { for (const unit of filtered.value) if (!(unit.id in selected)) selected[unit.id] = 1; }
function clear() { for (const id of Object.keys(selected)) delete selected[Number(id)]; }
function printLabels() {
    if (!pages.value.length) return;
    const style = document.createElement('style');
    style.textContent = `@page { size: ${settings.paperWidth}mm ${settings.paperHeight}mm; margin: 0; }`;
    document.head.appendChild(style);
    const cleanup = () => { style.remove(); window.removeEventListener('afterprint', cleanup); };
    window.addEventListener('afterprint', cleanup);
    window.print();
}
</script>

<template>
    <Head title="Cetak label aset" />
    <AdminLayout active="label" title="Cetak label aset">
        <section class="screen">
            <div class="heading"><div><h1>Cetak label aset</h1><p>Pilih unit dan tentukan jumlah label untuk setiap unit.</p></div><button class="primary" :disabled="!pages.length" @click="printLabels">Cetak {{ labelCount }} label</button></div>
            <div class="panel"><h2>Pilih unit aset</h2><div class="toolbar"><input v-model="search" placeholder="Cari kode, barang, bidang, atau instansi" aria-label="Cari unit aset" /><button @click="selectVisible">Pilih hasil pencarian</button><button @click="clear">Kosongkan pilihan</button></div>
                <div class="table-wrap"><table><thead><tr><th>Pilih</th><th>Kode unit</th><th>Barang</th><th>Bidang / instansi</th><th>Jumlah cetak</th></tr></thead><tbody><tr v-for="unit in filtered" :key="unit.id"><td><input type="checkbox" :checked="unit.id in selected" :aria-label="`Pilih ${unit.code}`" @change="toggle(unit, ($event.target as HTMLInputElement).checked)" /></td><td><strong>{{ unit.code }}</strong></td><td>{{ unit.item }}</td><td>{{ unit.department }}<br /><small>{{ unit.institution }}</small></td><td><input v-if="unit.id in selected" v-model.number="selected[unit.id]" type="number" min="1" max="100" step="1" :aria-label="`Jumlah cetak ${unit.code}`" class="count" /></td></tr><tr v-if="!filtered.length"><td colspan="5">Unit tidak ditemukan.</td></tr></tbody></table></div>
                <p v-if="!validCounts || labelCount > 1000" class="error">Jumlah per unit harus 1–100 dan total maksimal 1.000 label.</p>
            </div>
            <div class="panel"><h2>Pengaturan kertas dan label</h2><p>Contoh awal: A4, 3 kolom × 7 baris. Ukur stiker dan sesuaikan semua nilai sebelum mencetak.</p><div class="settings">
                <label>Lebar kertas (mm)<input v-model.number="settings.paperWidth" type="number" min="1" step="0.1" /></label><label>Tinggi kertas (mm)<input v-model.number="settings.paperHeight" type="number" min="1" step="0.1" /></label>
                <label>Lebar label (mm)<input v-model.number="settings.width" type="number" min="30" step="0.1" /></label><label>Tinggi label (mm)<input v-model.number="settings.height" type="number" min="20" step="0.1" /></label>
                <label>Kolom<input v-model.number="settings.columns" type="number" min="1" max="20" /></label><label>Baris<input v-model.number="settings.rows" type="number" min="1" max="50" /></label>
                <label>Margin kiri (mm)<input v-model.number="settings.left" type="number" min="0" step="0.1" /></label><label>Margin atas (mm)<input v-model.number="settings.top" type="number" min="0" step="0.1" /></label>
                <label>Jarak kolom (mm)<input v-model.number="settings.gapX" type="number" min="0" step="0.1" /></label><label>Jarak baris (mm)<input v-model.number="settings.gapY" type="number" min="0" step="0.1" /></label>
                <label>Mulai pada posisi<input v-model.number="settings.start" type="number" min="1" :max="settings.columns * settings.rows" /></label>
            </div><p>Posisi dihitung dari kiri ke kanan, lalu baris berikutnya. Pada dialog cetak pilih ukuran kertas sesuai pengaturan, skala 100% / ukuran sebenarnya, tanpa margin serta tanpa header dan footer. Cetak percobaan pada kertas biasa dan periksa agar teks tidak terpotong.</p><p v-if="!validLayout" class="error">Periksa ukuran: seluruh label harus muat pada kertas, dan posisi awal harus berada dalam jumlah kotak.</p></div>
            <div class="preview-heading"><h2>Pratinjau</h2><p>{{ labelCount }} label · {{ pages.length }} halaman</p></div><p v-if="!labelCount">Pilih unit untuk melihat pratinjau.</p>
        </section>
        <div class="sheets"><div v-for="(page, pageIndex) in pages" :key="pageIndex" class="sheet" :style="sheetStyle"><div v-for="(unit, index) in page" :key="index" class="cell"><div v-if="unit" class="label-content"><strong class="code">{{ unit.code }}</strong><strong class="name">{{ unit.item || '—' }}</strong><div>Tahun: {{ unit.purchase_year ?? '—' }}</div><div>Sumber dana: {{ unit.funding_source || '—' }}</div></div></div></div></div>
    </AdminLayout>
</template>

<style scoped>
.screen { max-width: 1200px; margin: auto; color: #172b3a; }.heading,.toolbar,.preview-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }.heading { margin-bottom:20px; }.heading h1 { font-size:1.65rem; font-weight:700; }.screen p { color:#64748b; margin:8px 0 14px; font-size:0.9rem; }.panel { background:white; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:20px; }.panel h2,.preview-heading h2 { font-weight:700; font-size:1.1rem; }.toolbar { justify-content:flex-start; margin:14px 0; }.toolbar input { min-width:260px; flex:1; }.screen input:not([type=checkbox]),.screen button { border:1px solid #cbd5e1; border-radius:7px; padding:8px 10px; background:white; }.screen button { cursor:pointer; font-weight:600; }.screen button.primary { color:white; background:#176b65; border-color:#176b65; }.screen button:disabled { opacity:.5; cursor:not-allowed; }.table-wrap { overflow:auto; max-height:400px; }table { width:100%; border-collapse:collapse; text-align:left; font-size:.9rem; }th,td { padding:10px; border-bottom:1px solid #e2e8f0; }th { background:#f8fafc; position:sticky; top:0; }small { color:#64748b; }.count { width:80px; }.settings { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin:16px 0; }.settings label { display:flex; flex-direction:column; gap:5px; font-size:.85rem; font-weight:600; }.error { color:#b91c1c !important; }.sheets { overflow:auto; padding:10px 0 30px; }.sheet { box-sizing:border-box; display:grid; align-content:start; justify-content:start; background:#fff; margin:0 auto 20px; box-shadow:0 2px 12px #0002; }.cell { box-sizing:border-box; border:1px dashed #cbd5e1; overflow:hidden; }.label-content { box-sizing:border-box; height:100%; padding:2mm; display:flex; flex-direction:column; justify-content:center; font-family:Arial,sans-serif; font-size:8pt; line-height:1.2; overflow-wrap:anywhere; }.code { font-size:11pt; }.name { font-size:9pt; margin:1mm 0; }
@media print { :global(html), :global(body) { margin:0 !important; padding:0 !important; background:white !important; } :global(.admin-sidebar), :global(.sidebar-backdrop), :global(.admin-topbar), :global(.admin-footer), :global(.mobile-menu-button), .screen { display:none !important; } :global(.admin-shell), :global(.admin-main), :global(.admin-content) { display:block !important; margin:0 !important; padding:0 !important; width:auto !important; max-width:none !important; } .sheets { overflow:visible; padding:0; }.sheet { margin:0; box-shadow:none; break-after:page; page-break-after:always; }.sheet:last-child { break-after:auto; page-break-after:auto; }.cell { border:0; } }
</style>
