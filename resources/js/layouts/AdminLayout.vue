<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppIcon from '../components/AppIcon.vue';
import type { Auth } from '../types/auth';

defineProps<{ active: string; title: string }>();
const emit = defineEmits<{ navigate: [key: string] }>();
const page = usePage<{ auth: Auth }>();
const mobileOpen = ref(false);
const accountOpen = ref(false);
const accountTrigger = ref<HTMLButtonElement | null>(null);
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
            { key: 'barang', label: 'Manajemen barang', icon: 'box' },
            { key: 'unit', label: 'Unit aset', icon: 'box' },
            { key: 'label', label: 'Cetak label', icon: 'box' },
            ...(['superadmin', 'admin'].includes(page.props.auth.user.role)
                ? [{ key: 'kategori', label: 'Kategori', icon: 'layers' }]
                : []),
            { key: 'lokasi', label: 'Manajemen ruangan', icon: 'pin' },
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
    } else if (key === 'label') {
        router.visit('/asset-labels');
    } else if (key === 'lokasi') {
        router.visit('/rooms');
    } else if (key === 'laporan') {
        router.visit('/reports');
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
function closeAccountOnFocusOut(event: FocusEvent) {
    const account = event.currentTarget as HTMLElement;
    if (!account.contains(event.relatedTarget as Node | null)) {
        accountOpen.value = false;
    }
}
function closeAccountOnEscape() {
    if (accountOpen.value) {
        accountOpen.value = false;
        accountTrigger.value?.focus();
    }
}
</script>

<template>
    <div
        class="admin-shell"
        @click="accountOpen = false"
        @keydown.esc="mobileOpen = false"
    >
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
                ><span>Skawan Inventory<span class="brand-dot">.</span></span
                ><button
                    class="mobile-close icon-button"
                    aria-label="Tutup menu"
                    @click="mobileOpen = false"
                >
                    <AppIcon name="close" />
                </button>
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
                <div
                    class="topbar-account"
                    @click.stop
                    @focusout="closeAccountOnFocusOut"
                    @keydown.esc="closeAccountOnEscape"
                >
                    <button
                        ref="accountTrigger"
                        type="button"
                        class="account-trigger"
                        :aria-expanded="accountOpen"
                        aria-controls="account-dropdown"
                        @click="accountOpen = !accountOpen"
                    >
                        <span class="account-avatar" aria-hidden="true">{{
                            initials
                        }}</span>
                        <span class="account-details">
                            <strong>{{ page.props.auth.user.name }}</strong>
                            <small>{{ page.props.auth.user.role }}</small>
                        </span>
                        <AppIcon
                            class="account-chevron"
                            name="chevron"
                            :size="14"
                        />
                    </button>
                    <div
                        v-show="accountOpen"
                        id="account-dropdown"
                        class="account-dropdown"
                    >
                        <button
                            type="button"
                            class="logout-button"
                            :disabled="loggingOut"
                            @click="logout"
                        >
                            <AppIcon name="logout" :size="16" />
                            {{ loggingOut ? 'Keluar…' : 'Keluar dari akun' }}
                        </button>
                    </div>
                </div>
            </header>
            <main id="main-content" class="admin-content"><slot /></main>
            <footer class="admin-footer">
                <span>© {{ new Date().getFullYear() }} Skawan Inventory</span
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
.sidebar-navigation {
    margin-top: 28px;
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
.icon-button:focus-visible,
.account-trigger:focus-visible,
.logout-button:focus-visible {
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
    position: relative;
    min-width: 0;
}
.account-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 100%;
    padding: 4px 8px;
    border-radius: 8px;
    text-align: left;
    cursor: pointer;
}
.account-trigger:hover,
.account-trigger[aria-expanded='true'],
.logout-button:hover:not(:disabled) {
    background: #edf2e9;
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
    flex-shrink: 0;
}
.account-details {
    min-width: 0;
}
.account-details strong {
    font-size: 11px;
    display: block;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.account-details small {
    font-size: 9px;
    color: #8b968c;
    display: block;
    margin-top: 3px;
}
.account-chevron {
    flex-shrink: 0;
    transform: rotate(90deg);
}
.account-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    z-index: 30;
    min-width: 168px;
    padding: 5px;
    border: 1px solid #e7ece4;
    border-radius: 10px;
    background: white;
    box-shadow: 0 8px 24px #123d331a;
}
.logout-button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 10px;
    border-radius: 6px;
    font-size: 12px;
    text-align: left;
    cursor: pointer;
}
.logout-button:disabled {
    opacity: 0.5;
    cursor: wait;
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
