<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '../layouts/AdminLayout.vue';
import AppIcon from '../components/AppIcon.vue';
import type { Auth } from '../types/auth';

type Loan = {
    id: number;
    code: string;
    borrower_name: string;
    borrower_department: string | null;
    borrowed_at: string;
    unit_names: string[];
    unit_count: number;
    returned_count: number;
};

const props = defineProps<{
    scope: { institution: string | null; department: string | null };
    summary: {
        total_units: number;
        total_purchase_value: `${number}`;
        loaned_units: number;
        available_units: number;
        total_users: number | null;
        item_types: number | null;
    };
    loans: Loan[];
}>();
const scopeLabel = computed(
    () =>
        [props.scope.institution, props.scope.department]
            .filter(Boolean)
            .join(' · ') || 'Semua instansi',
);

const page = usePage<{ auth: Auth }>();
const requestedMenu = new URLSearchParams(page.url.split('?')[1] ?? '').get(
    'menu',
);
const active = ref(requestedMenu ?? 'dashboard');
const titles: Record<string, string> = {
    dashboard: 'Dashboard',
    barang: 'Manajemen barang',
    kategori: 'Kategori',
    lokasi: 'Manajemen ruangan',
    peminjaman: 'Peminjaman',
    pengguna: 'Pengguna',
    laporan: 'Laporan',
    pengaturan: 'Pengaturan',
};
const title = computed(() => titles[active.value] ?? 'Dashboard');
const date = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'Asia/Makassar',
}).format(new Date());
const number = new Intl.NumberFormat('id-ID');
const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
});
const loanDate = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    timeZone: 'Asia/Makassar',
});
const metrics = computed(() => [
    {
        label: 'Total unit aset',
        value: props.summary.total_units,
        unit: 'unit tercatat',
        icon: 'box',
        tone: 'green',
    },
    {
        label: 'Sedang dipinjam',
        value: props.summary.loaned_units,
        unit: 'unit belum kembali',
        icon: 'arrows',
        tone: 'amber',
    },
    {
        label: 'Unit tersedia',
        value: props.summary.available_units,
        unit: 'unit aktif dan siap dipinjam',
        icon: 'check',
        tone: 'sage',
    },
    props.summary.total_users === null
        ? {
              label: 'Jenis barang',
              value: props.summary.item_types ?? 0,
              unit: 'jenis dengan unit tercatat',
              icon: 'layers',
              tone: 'blue',
          }
        : {
              label: 'Total pengguna',
              value: props.summary.total_users,
              unit: 'akun dalam cakupan Anda',
              icon: 'users',
              tone: 'blue',
          },
]);

function loanStatus(loan: Loan) {
    if (loan.returned_count === loan.unit_count) return 'Dikembalikan';
    if (loan.returned_count > 0) return 'Sebagian kembali';
    return 'Dipinjam';
}

function initials(name: string) {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase();
}

const shortcuts = [
    {
        key: 'barang',
        icon: 'box',
        label: 'Kelola barang',
        text: 'Aset tersusun, kerja lebih mudah.',
    },
    {
        key: 'lokasi',
        icon: 'pin',
        label: 'Kelola ruangan',
        text: 'Atur ruang penempatan setiap aset.',
    },
    {
        key: 'unit',
        icon: 'box',
        label: 'Lihat unit aset',
        text: 'Periksa kondisi dan penempatan unit.',
    },
    {
        key: 'laporan',
        icon: 'chart',
        label: 'Lihat laporan',
        text: 'Ringkasan untuk keputusan Anda.',
    },
];
</script>

<template>
    <Head :title="title" />
    <AdminLayout :active="active" :title="title" @navigate="active = $event">
        <div class="page-heading">
            <div>
                <div class="section-eyebrow">OVERVIEW WORKSPACE</div>
                <h1>
                    {{
                        active === 'dashboard'
                            ? 'Semua tertata, kerja lebih tenang.'
                            : title
                    }}
                </h1>
                <p>
                    {{
                        active === 'dashboard'
                            ? `Selamat datang kembali, ${page.props.auth.user.name}. Ini ringkasan inventaris Anda.`
                            : 'Ruang untuk mengelola inventaris dengan lebih mudah.'
                    }}
                </p>
            </div>
            <span class="date-chip"
                ><AppIcon name="clock" :size="15" />{{ date }}</span
            >
        </div>
        <template v-if="active === 'dashboard'">
            <section class="overview-banner">
                <div>
                    <span class="banner-eyebrow">{{ scopeLabel }}</span>
                    <h2>
                        Inventaris yang rapi.<br />Langkah kerja yang pasti.
                    </h2>
                    <p>Kelola aset, peminjaman, dan tim dalam satu ruang.</p>
                    <button
                        class="btn banner-button"
                        @click="router.visit('/item-units')"
                    >
                        Jelajahi inventaris <AppIcon name="arrow" :size="16" />
                    </button>
                </div>
                <div class="banner-art" aria-hidden="true">
                    <div class="art-orbit"></div>
                    <div class="art-card">
                        <span class="art-box"
                            ><AppIcon name="box" :size="44" /></span
                        ><span class="art-line"></span
                        ><span class="art-line short"></span
                        ><span class="art-status"
                            ><AppIcon name="check" :size="12" /> Tertata dengan
                            baik</span
                        >
                    </div>
                    <span class="art-floating"
                        ><AppIcon name="layers" :size="24"
                    /></span>
                </div>
            </section>
            <div class="section-caption">
                <span>Ringkasan inventaris</span>
            </div>
            <section class="metrics-grid" aria-label="Ringkasan inventaris">
                <article
                    v-for="metric in metrics"
                    :key="metric.label"
                    class="metric-card"
                >
                    <div class="metric-top">
                        <span>{{ metric.label }}</span
                        ><span class="metric-icon" :class="metric.tone"
                            ><AppIcon :name="metric.icon" :size="18"
                        /></span>
                    </div>
                    <strong>{{ number.format(metric.value) }}</strong
                    ><small>{{ metric.unit }}</small>
                </article>
                <article class="metric-card purchase-value">
                    <div class="metric-top">
                        <span>Total nilai barang</span>
                        <span class="metric-icon green">
                            <AppIcon name="chart" :size="18" />
                        </span>
                    </div>
                    <strong>{{
                        rupiah.format(props.summary.total_purchase_value)
                    }}</strong>
                    <small>Harga beli seluruh unit tercatat</small>
                </article>
            </section>
            <section class="loans-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Peminjaman terbaru</h2>
                        <p>Aktivitas barang dalam workspace Anda.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="loans-table mb-0 table align-middle">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Barang</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loans.length === 0">
                                <td colspan="4">
                                    Belum ada peminjaman tercatat.
                                </td>
                            </tr>
                            <tr v-for="loan in loans" :key="loan.id">
                                <td>
                                    <div class="borrower">
                                        <span class="borrower-avatar">{{
                                            initials(loan.borrower_name)
                                        }}</span>
                                        <div>
                                            <strong>{{
                                                loan.borrower_name
                                            }}</strong
                                            ><small
                                                v-if="loan.borrower_department"
                                                >{{
                                                    loan.borrower_department
                                                }}</small
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{
                                        loan.unit_names.join(', ')
                                    }}</strong>
                                    <small
                                        >{{ loan.code }} ·
                                        {{ number.format(loan.unit_count) }}
                                        unit</small
                                    >
                                </td>
                                <td class="loan-date">
                                    {{
                                        loanDate.format(
                                            new Date(
                                                loan.borrowed_at +
                                                    'T00:00:00+08:00',
                                            ),
                                        )
                                    }}
                                </td>
                                <td>
                                    <span
                                        class="status-badge"
                                        :class="
                                            loan.returned_count ===
                                            loan.unit_count
                                                ? 'green'
                                                : 'amber'
                                        "
                                        ><span></span
                                        >{{ loanStatus(loan) }}</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="shortcuts-grid" aria-label="Akses cepat">
                <button
                    v-for="shortcut in shortcuts"
                    :key="shortcut.key"
                    class="shortcut-card"
                    @click="
                        shortcut.key === 'barang'
                            ? router.visit('/items')
                            : shortcut.key === 'lokasi'
                              ? router.visit('/rooms')
                              : shortcut.key === 'unit'
                                ? router.visit('/item-units')
                                : router.visit('/reports')
                    "
                >
                    <span class="shortcut-icon"
                        ><AppIcon :name="shortcut.icon" :size="21" /></span
                    ><span
                        ><strong>{{ shortcut.label }}</strong
                        ><small>{{ shortcut.text }}</small></span
                    ><AppIcon name="arrow" :size="16" />
                </button>
            </section>
        </template>
        <section v-else class="placeholder-panel">
            <span class="placeholder-icon"
                ><AppIcon
                    :name="
                        shortcuts.find((item) => item.key === active)?.icon ??
                        'layers'
                    "
                    :size="34" /></span
            ><span class="demo-badge">Menu dummy</span>
            <h2>{{ title }}</h2>
            <p>
                Halaman {{ title.toLowerCase() }} sedang disiapkan.<br />Fitur
                pengelolaan akan ditambahkan pada tahap berikutnya.
            </p>
            <button class="btn placeholder-back" @click="active = 'dashboard'">
                <AppIcon name="grid" :size="16" /> Kembali ke dashboard
            </button>
        </section>
    </AdminLayout>
</template>

<style scoped>
.page-heading {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    align-items: center;
    margin-bottom: 27px;
}
.section-eyebrow {
    font-size: 9px;
    letter-spacing: 1.8px;
    color: #91a08f;
    font-weight: 600;
}
h1 {
    font-size: clamp(23px, 2.25vw, 32px);
    letter-spacing: -1.1px;
    font-weight: 500;
    color: #213f33;
    margin: 10px 0;
    line-height: 1.25;
}
.page-heading p {
    color: #8b968c;
    font-size: 12px;
    margin: 0;
    line-height: 1.7;
}
.date-chip {
    flex-shrink: 0;
    display: flex;
    gap: 8px;
    align-items: center;
    padding: 10px 13px;
    font-size: 10px;
    border-radius: 7px;
    background: white;
    color: #74816e;
    border: 1px solid #e5eae1;
}
.overview-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    background: #e8eedf;
    border: 1px solid #dde5d3;
    border-radius: 14px;
    padding: 30px 34px;
    min-height: 244px;
}
.banner-eyebrow {
    font-size: 8px;
    font-weight: 600;
    letter-spacing: 1.7px;
    color: #7e8c6e;
}
.overview-banner h2 {
    font-size: 29px;
    line-height: 1.2;
    font-weight: 500;
    letter-spacing: -0.9px;
    margin: 15px 0 11px;
    color: #2b4937;
}
.overview-banner p {
    font-size: 11px;
    color: #859078;
    margin: 0 0 22px;
}
.banner-button {
    display: inline-flex;
    align-items: center;
    gap: 17px;
    background: #254a37;
    color: #f6f8ef;
    border-radius: 6px;
    font-size: 10px;
    padding: 10px 13px;
}
.banner-button:hover {
    color: white;
    background: #163b2a;
}
.banner-art {
    position: relative;
    align-self: stretch;
    width: 260px;
    display: grid;
    place-items: center;
    margin-left: 15px;
}
.art-orbit {
    position: absolute;
    width: 280px;
    height: 280px;
    border: 1px solid #c4d0b64d;
    border-radius: 50%;
    box-shadow:
        0 0 0 35px #c4d0b615,
        0 0 0 70px #c4d0b60d;
}
.art-card {
    width: 145px;
    height: 177px;
    padding: 18px;
    background: #ffffffb5;
    border: 1px solid #fff;
    box-shadow: 10px 20px 35px #54704712;
    border-radius: 14px;
    transform: rotate(-10deg);
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.art-box {
    display: grid;
    place-items: center;
    width: 70px;
    height: 70px;
    background: #e6eddc;
    color: #547545;
    border-radius: 17px;
}
.art-line {
    width: 73px;
    height: 5px;
    background: #ccd6c3;
    border-radius: 3px;
    margin-top: 15px;
}
.art-line.short {
    width: 46px;
    height: 4px;
    background: #e3e8dc;
    margin-top: 7px;
}
.art-status {
    display: flex;
    gap: 4px;
    align-items: center;
    white-space: nowrap;
    background: #fcfdf9;
    border: 1px solid #e3e8da;
    border-radius: 6px;
    padding: 9px 11px;
    font-size: 8px;
    color: #627b4c;
    position: absolute;
    right: -42px;
    bottom: 2px;
    transform: rotate(10deg);
    box-shadow: 0 5px 15px #5470470b;
}
.art-floating {
    position: absolute;
    left: 16px;
    top: 36px;
    display: grid;
    place-items: center;
    width: 45px;
    height: 45px;
    border-radius: 11px;
    background: #f8faef;
    color: #879a62;
    transform: rotate(10deg);
    border: 1px solid white;
}
.section-caption {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 28px 0 13px;
    font-size: 12px;
    font-weight: 500;
}
.demo-badge {
    display: inline-block;
    color: #8c967e;
    background: #edf0e7;
    border: 1px solid #e2e8da;
    border-radius: 5px;
    padding: 4px 7px;
    font-size: 9px;
    font-weight: 400;
}
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
}
.metric-card {
    background: white;
    border: 1px solid #e7ece3;
    border-radius: 11px;
    padding: 18px;
    box-shadow: 0 3px 8px #1b332302;
}
.purchase-value {
    grid-column: 1 / -1;
    overflow-wrap: anywhere;
}
.metric-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    font-size: 10px;
    color: #8a9588;
}
.metric-icon {
    display: grid;
    place-items: center;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
}
.green {
    color: #5e805f;
    background: #edf4e9;
}
.amber {
    color: #ab8a4e;
    background: #f8f3e6;
}
.sage {
    color: #7d8b5c;
    background: #f0f3e8;
}
.blue {
    color: #6d8e9a;
    background: #ecf2f4;
}
.metric-card > strong {
    display: block;
    font-size: 33px;
    font-weight: 500;
    letter-spacing: -1px;
    color: #294638;
    margin: 9px 0 3px;
}
.metric-card > small {
    display: block;
    font-size: 9px;
    color: #98a091;
}
.loans-panel {
    background: white;
    border: 1px solid #e7ece3;
    border-radius: 12px;
    margin-top: 25px;
    overflow: hidden;
}
.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 23px 24px 19px;
}
.panel-heading h2 {
    font-size: 14px;
    font-weight: 500;
    margin: 0 0 7px;
}
.panel-heading p {
    font-size: 10px;
    color: #98a091;
    margin: 0;
}
.loans-table {
    width: 100%;
    border-collapse: collapse;
    --bs-table-bg: transparent;
    --bs-table-color: #52634f;
}
.loans-table th {
    background: #fafbf8;
    color: #9aa391;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 12px 24px;
    border-bottom: 1px solid #edf0e8;
    font-weight: 500;
    white-space: nowrap;
}
.loans-table td {
    padding: 16px 24px;
    border-bottom: 1px solid #f0f3ec;
    font-size: 10px;
    white-space: nowrap;
}
.loans-table tr:last-child td {
    border-bottom: 0;
}
.loans-table strong {
    display: block;
    font-weight: 500;
    font-size: 11px;
    color: #4c604b;
}
.loans-table small {
    display: block;
    font-size: 9px;
    color: #a2ab9b;
    margin-top: 5px;
}
.borrower {
    display: flex;
    align-items: center;
    gap: 11px;
}
.borrower-avatar {
    width: 32px;
    height: 32px;
    background: #f0f2e8;
    color: #879473;
    border-radius: 50%;
    font-size: 9px;
    display: grid;
    place-items: center;
}
.loan-date {
    color: #919d88;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 8px;
    font-size: 9px;
    border-radius: 5px;
}
.status-badge > span {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: currentColor;
}
.shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin-top: 22px;
}
.shortcut-card {
    display: flex;
    gap: 11px;
    align-items: center;
    padding: 18px 16px;
    border: 1px solid #e5eadf;
    border-radius: 10px;
    background: #fafbf8;
    text-align: left;
    color: #788b68;
    transition: background 0.18s;
    cursor: pointer;
}
.shortcut-card:hover {
    background: #edf1e7;
}
.shortcut-icon {
    flex-shrink: 0;
    display: grid;
    place-items: center;
}
.shortcut-card strong {
    display: block;
    font-size: 10px;
    font-weight: 500;
    color: #5d7052;
}
.shortcut-card small {
    display: block;
    font-size: 8px;
    color: #9aa48f;
    margin-top: 5px;
    line-height: 1.5;
}
.shortcut-card > svg {
    margin-left: auto;
    flex-shrink: 0;
}
.placeholder-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 440px;
    border: 1px solid #e4eadc;
    border-radius: 15px;
    background: white;
    padding: 35px 20px;
}
.placeholder-icon {
    display: grid;
    place-items: center;
    width: 80px;
    height: 80px;
    background: #eef3e6;
    color: #748b60;
    border-radius: 22px;
    margin-bottom: 24px;
}
.placeholder-panel h2 {
    font-size: 25px;
    letter-spacing: -0.7px;
    margin: 16px 0 12px;
    font-weight: 500;
}
.placeholder-panel p {
    font-size: 12px;
    color: #929d89;
    line-height: 1.9;
}
.placeholder-back {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    background: #234934;
    color: white;
    border: 0;
    border-radius: 7px;
    padding: 11px 16px;
    margin-top: 17px;
    font-size: 11px;
}
.placeholder-back:hover {
    background: #153724;
    color: white;
}
.member-home {
    padding: 60px 8%;
    min-height: 100svh;
    background: #f6f8f5;
    color: #203e33;
}
@media (min-width: 1550px) {
    .page-heading,
    .overview-banner,
    .section-caption,
    .metrics-grid,
    .loans-panel,
    .shortcuts-grid,
    .placeholder-panel {
        max-width: 1250px;
        margin-left: auto;
        margin-right: auto;
    }
}
@media (max-width: 1150px) {
    .metrics-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .date-chip {
        display: none;
    }
    .shortcuts-grid {
        grid-template-columns: 1fr;
    }
    .shortcut-card {
        padding: 16px;
    }
    .shortcut-card small {
        font-size: 10px;
    }
    .overview-banner {
        padding: 25px;
    }
    .banner-art {
        width: 180px;
    }
}
@media (max-width: 550px) {
    .page-heading {
        margin-bottom: 22px;
    }
    h1 {
        font-size: 26px;
    }
    .page-heading p {
        font-size: 11px;
    }
    .overview-banner {
        padding: 25px;
        min-height: 220px;
    }
    .overview-banner h2 {
        font-size: 27px;
    }
    .overview-banner p {
        max-width: 220px;
        line-height: 1.7;
    }
    .banner-art {
        display: none;
    }
    .metrics-grid {
        gap: 10px;
    }
    .metric-card {
        padding: 14px;
    }
    .metric-top {
        font-size: 9px;
    }
    .metric-card > strong {
        font-size: 29px;
    }
    .panel-heading {
        padding: 20px 17px;
    }
    .loans-table th,
    .loans-table td {
        padding-left: 17px;
        padding-right: 17px;
    }
}
</style>
