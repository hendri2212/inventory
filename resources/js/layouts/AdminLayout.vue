<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppIcon from '../components/AppIcon.vue';
import type { Auth } from '../types/auth';

defineProps<{ active: string; title: string }>();
const emit = defineEmits<{ navigate: [key: string] }>();
const page = usePage<{ auth: Auth }>();
const mobileOpen = ref(false);
const loggingOut = ref(false);
const initials = computed(() =>
    page.props.auth.user.name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase(),
);
const groups = computed(() => [
    {
        label: 'WORKSPACE',
        items: [
            { key: 'dashboard', label: 'Dashboard', icon: 'grid' },
            { key: 'barang', label: 'Katalog barang', icon: 'box' },
            { key: 'unit', label: 'Unit aset', icon: 'box' },
            ...(['superadmin', 'admin'].includes(page.props.auth.user.role)
                ? [{ key: 'kategori', label: 'Kategori', icon: 'layers' }]
                : []),
            { key: 'lokasi', label: 'Lokasi & ruangan', icon: 'pin' },
        ],
    },
    {
        label: 'MANAJEMEN',
        items: [
            { key: 'peminjaman', label: 'Peminjaman', icon: 'arrows' },
            ...(page.props.auth.user.role === 'superadmin'
                ? [{ key: 'instansi', label: 'Instansi', icon: 'layers' }]
                : []),
            ...(['superadmin', 'admin'].includes(page.props.auth.user.role)
                ? [
                      { key: 'bidang', label: 'Bidang', icon: 'layers' },
                      { key: 'pengguna', label: 'Pengguna', icon: 'users' },
                  ]
                : []),
            { key: 'laporan', label: 'Laporan', icon: 'chart' },
        ],
    },
    {
        label: 'PREFERENSI',
        items: [{ key: 'pengaturan', label: 'Pengaturan', icon: 'settings' }],
    },
]);
function navigate(key: string) {
    if (key === 'barang') {
        router.visit('/items');
    } else if (key === 'unit') {
        router.visit('/item-units');
    } else if (key === 'lokasi') {
        router.visit('/rooms');
    } else if (key === 'kategori') {
        router.visit('/categories');
    } else if (key === 'instansi') {
        router.visit('/institutions');
    } else if (key === 'bidang') {
        router.visit('/departments');
    } else if (key === 'pengguna') {
        router.visit('/users');
    } else if (page.component !== 'Welcome') {
        router.visit('/?menu=' + encodeURIComponent(key));
    } else {
        emit('navigate', key);
    }
    mobileOpen.value = false;
}
function logout() {
    loggingOut.value = true;
    router.post(
        '/logout',
        {},
        {
            onFinish: () => {
                loggingOut.value = false;
            },
        },
    );
}
</script>

<template>
    <div class="admin-shell" @keydown.esc="mobileOpen = false">
        <button
            v-if="mobileOpen"
            class="sidebar-backdrop"
            aria-label="Tutup menu navigasi"
            @click="mobileOpen = false"
        ></button>
        <aside
            id="admin-sidebar"
            class="admin-sidebar"
            :class="{ 'is-open': mobileOpen }"
            aria-label="Navigasi admin"
        >
            <div class="sidebar-brand">
                <span class="brand-icon"><AppIcon name="box" :size="23" /></span
                ><span>inventory<span class="brand-dot">.</span></span
                ><button
                    class="mobile-close icon-button"
                    aria-label="Tutup menu"
                    @click="mobileOpen = false"
                >
                    <AppIcon name="close" />
                </button>
            </div>
            <div class="workspace-switch">
                <span class="workspace-symbol">I</span>
                <div>
                    <strong>Inventory Workspace</strong
                    ><small>Panel inventaris</small>
                </div>
                <span class="workspace-tag">PRO</span>
            </div>
            <nav class="sidebar-navigation">
                <div
                    v-for="group in groups"
                    :key="group.label"
                    class="nav-group"
                >
                    <div class="nav-group-label">{{ group.label }}</div>
                    <button
                        v-for="item in group.items"
                        :key="item.key"
                        class="sidebar-link"
                        :class="{ active: active === item.key }"
                        :aria-current="active === item.key ? 'page' : undefined"
                        @click="navigate(item.key)"
                    >
                        <AppIcon :name="item.icon" /><span>{{
                            item.label
                        }}</span
                        ><span
                            v-if="active === item.key"
                            class="active-dot"
                        ></span>
                    </button>
                </div>
            </nav>
            <div class="sidebar-bottom">
                <div class="workspace-note">
                    <span class="note-dot"></span
                    ><span
                        >Satu tempat.<br /><strong
                            >Semua aset tertata.</strong
                        ></span
                    ><AppIcon name="box" :size="30" />
                </div>
                <button
                    class="sidebar-link logout-button"
                    :disabled="loggingOut"
                    @click="logout"
                >
                    <AppIcon name="logout" /><span>{{
                        loggingOut ? 'Keluar…' : 'Keluar dari akun'
                    }}</span>
                </button>
            </div>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="breadcrumb-row">
                    <button
                        class="mobile-toggle icon-button"
                        aria-label="Buka menu navigasi"
                        :aria-expanded="mobileOpen"
                        aria-controls="admin-sidebar"
                        @click="mobileOpen = true"
                    >
                        <AppIcon name="menu" /></button
                    ><span class="breadcrumb-root">Workspace</span
                    ><AppIcon name="chevron" :size="13" /><span
                        class="breadcrumb-current"
                        >{{ title }}</span
                    >
                </div>
                <div class="topbar-account">
                    <span class="account-avatar">{{ initials }}</span>
                    <div>
                        <strong>{{ page.props.auth.user.name }}</strong
                        ><small>{{ page.props.auth.user.role }}</small>
                    </div>
                    <span class="account-status" title="Sesi aktif"></span>
                </div>
            </header>
            <main id="main-content" class="admin-content"><slot /></main>
            <footer class="admin-footer">
                <span>© {{ new Date().getFullYear() }} Inventory</span
                ><span>Dibuat untuk ruang kerja yang lebih teratur.</span>
            </footer>
        </div>
    </div>
</template>

<style scoped>
.admin-shell {
    --ink: #203e33;
    --muted: #88958c;
    min-height: 100svh;
    background: #f6f8f5;
    color: var(--ink);
    color-scheme: light;
    font-family: 'Instrument Sans', sans-serif;
}
.admin-sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 40;
    width: 258px;
    display: flex;
    flex-direction: column;
    padding: 32px 20px 20px;
    background: #123d33;
    color: #e6eee8;
    overflow-y: auto;
}
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 12px;
    font-size: 27px;
    font-weight: 600;
    letter-spacing: -1px;
}
.brand-icon {
    color: #d5e9b5;
    display: flex;
}
.brand-dot {
    color: #d5e9b5;
}
.workspace-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 31px 0 28px;
    padding: 13px 10px;
    border: 1px solid #ffffff18;
    border-radius: 10px;
    background: #ffffff04;
}
.workspace-symbol {
    width: 31px;
    height: 34px;
    display: grid;
    place-items: center;
    border: 1px solid #ffffff20;
    background: #ffffff0b;
    border-radius: 7px;
    font-size: 15px;
    color: #d5e9b5;
}
.workspace-switch strong {
    display: block;
    font-size: 11px;
    font-weight: 500;
}
.workspace-switch small {
    display: block;
    color: #9db7ab;
    font-size: 10px;
    margin-top: 3px;
}
.workspace-tag {
    margin-left: auto;
    font-size: 8px;
    letter-spacing: 0.6px;
    border: 1px solid #cfe6ae40;
    padding: 3px 5px;
    color: #d5e9b5;
    border-radius: 4px;
}
.sidebar-navigation {
    flex: 1;
}
.nav-group {
    margin-bottom: 26px;
}
.nav-group-label {
    padding: 0 13px;
    font-size: 9px;
    letter-spacing: 1.8px;
    color: #89a99a;
    margin-bottom: 11px;
}
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    background: transparent;
    border: 0;
    border-radius: 8px;
    padding: 12px 13px;
    color: #b7cdc1;
    text-align: left;
    font-size: 12px;
    cursor: pointer;
    margin: 3px 0;
    transition:
        background 0.18s,
        color 0.18s;
}
.sidebar-link:hover {
    color: #fff;
    background: #ffffff0a;
}
.sidebar-link:focus-visible,
.icon-button:focus-visible {
    outline: 2px solid #a8c695;
    outline-offset: 3px;
}
.sidebar-link.active {
    color: #173c2f;
    background: #d6e8b9;
    font-weight: 600;
    box-shadow: 0 4px 14px #062d2220;
}
.active-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #416449;
    margin-left: auto;
}
.sidebar-bottom {
    margin-top: 20px;
}
.workspace-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 13px;
    background: #ffffff05;
    border: 1px solid #ffffff12;
    border-radius: 10px;
    color: #9db9a9;
    font-size: 10px;
    line-height: 1.8;
    margin-bottom: 15px;
}
.workspace-note strong {
    font-weight: 500;
    color: #d3dfd6;
}
.workspace-note svg {
    margin-left: auto;
    opacity: 0.45;
}
.note-dot,
.account-status {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #c4df9e;
    flex-shrink: 0;
}
.logout-button {
    border-top: 1px solid #ffffff13;
    border-radius: 0;
    padding-top: 20px;
}
.logout-button:disabled {
    opacity: 0.5;
}
.admin-main {
    margin-left: 258px;
    min-height: 100svh;
    display: flex;
    flex-direction: column;
}
.admin-topbar {
    min-height: 84px;
    padding: 18px 38px;
    background: #ffffffba;
    border-bottom: 1px solid #e7ece4;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.breadcrumb-row {
    display: flex;
    gap: 14px;
    align-items: center;
    font-size: 12px;
}
.breadcrumb-root,
.breadcrumb-row > svg {
    color: #99a399;
}
.breadcrumb-current {
    font-weight: 500;
}
.topbar-account {
    display: flex;
    align-items: center;
    gap: 10px;
}
.account-avatar {
    width: 35px;
    height: 35px;
    display: grid;
    place-items: center;
    background: #e9eee2;
    border: 3px solid white;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 600;
}
.topbar-account strong {
    font-size: 11px;
    display: block;
    font-weight: 600;
}
.topbar-account small {
    font-size: 9px;
    color: #8b968c;
    display: block;
    margin-top: 3px;
}
.account-status {
    background: #80a974;
    margin-left: 8px;
}
.admin-content {
    padding: 36px 38px;
    flex: 1;
}
.admin-footer {
    padding: 20px 38px;
    font-size: 10px;
    color: #97a194;
    display: flex;
    justify-content: space-between;
    gap: 15px;
}
.icon-button {
    display: grid;
    place-items: center;
    background: transparent;
    border: 0;
    color: inherit;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
}
.mobile-toggle,
.mobile-close,
.sidebar-backdrop {
    display: none;
}
@media (max-width: 1100px) {
    .admin-content {
        padding: 28px 24px;
    }
    .admin-topbar {
        padding: 18px 24px;
    }
    .admin-footer {
        padding: 20px 24px;
    }
    .admin-sidebar {
        width: 235px;
        padding-right: 16px;
        padding-left: 16px;
    }
    .admin-main {
        margin-left: 235px;
    }
}
@media (max-width: 800px) {
    .admin-sidebar {
        transform: translateX(-100%);
        transition: transform 0.2s;
        width: 258px;
        visibility: hidden;
    }
    .admin-sidebar.is-open {
        transform: translateX(0);
        visibility: visible;
    }
    .admin-main {
        margin-left: 0;
    }
    .mobile-toggle,
    .mobile-close {
        display: grid;
    }
    .mobile-close {
        margin-left: auto;
    }
    .sidebar-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: #09251b70;
        border: 0;
        z-index: 35;
    }
    .admin-topbar {
        min-height: 75px;
        padding: 16px 20px;
    }
    .admin-content {
        padding: 28px 20px;
    }
    .breadcrumb-root,
    .breadcrumb-row > svg {
        display: none;
    }
    .breadcrumb-row {
        gap: 9px;
    }
    .admin-footer {
        padding: 20px;
    }
}
@media (max-width: 440px) {
    .topbar-account > div,
    .account-status {
        display: none;
    }
    .admin-footer span:last-child {
        display: none;
    }
}
@media (prefers-reduced-motion: reduce) {
    .admin-sidebar,
    .sidebar-link {
        transition: none;
    }
}
</style>
