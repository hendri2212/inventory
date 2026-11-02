<script setup lang="ts">
withDefaults(defineProps<{ name: string; size?: number }>(), { size: 20 });
const paths: Record<string, string> = {
    grid: 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z',
    box: 'm12 3 9 5-9 5-9-5 9-5Z M3 8v9l9 5 9-5V8 M12 13v9 M7.5 5.5l9 5',
    layers: 'm12 3 10 5-10 5L2 8l10-5Z M2 12l10 5 10-5 M2 16l10 5 10-5',
    arrows: 'M4 7h15m-4-4 4 4-4 4 M20 17H5m4-4-4 4 4 4',
    users: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
    pin: 'M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0',
    chart: 'M4 3v18h17 M8 16v-4 M13 16V8 M18 16V5',
    settings:
        'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8 M9 3h6l1 3 3 1 2 5-2 5-3 1-1 3H9l-1-3-3-1-2-5 2-5 3-1 1-3Z',
    logout: 'M9 21H4V3h5 M9 12h12m-5-5 5 5-5 5',
    menu: 'M4 6h16 M4 12h16 M4 18h16',
    close: 'm6 6 12 12 M6 18 18 6',
    arrow: 'M5 12h14m-5-5 5 5-5 5',
    clock: 'M12 8v4l3 2 M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0',
    check: 'm5 12 4 4L19 6',
    plus: 'M12 5v14 M5 12h14',
    chevron: 'm9 5 7 7-7 7',
};
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.65"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <path :d="paths[name] ?? paths.box" />
    </svg>
</template>
