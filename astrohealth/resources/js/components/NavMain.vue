<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

/**
 * Un grupo del menú lateral. Las opciones sin pantalla todavía se ven
 * deshabilitadas con "Pronto"; las que tienen contador muestran su número.
 */
withDefaults(defineProps<{ items: NavItem[]; label?: string }>(), {
    label: 'Misión',
});

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ label }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    v-if="item.soon"
                    :tooltip="`${item.title} (pronto)`"
                    aria-disabled="true"
                    class="cursor-default opacity-55 hover:bg-transparent"
                >
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                </SidebarMenuButton>
                <SidebarMenuButton
                    v-else
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>

                <SidebarMenuBadge
                    v-if="item.badge"
                    class="rounded-full bg-[var(--status-red)] px-1.5 text-white peer-data-[active=true]/menu-button:text-white"
                >
                    {{ item.badge }}
                </SidebarMenuBadge>
                <SidebarMenuBadge
                    v-else-if="item.soon"
                    class="rounded-full border px-1.5 text-[10px] font-medium text-muted-foreground"
                >
                    Pronto
                </SidebarMenuBadge>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
