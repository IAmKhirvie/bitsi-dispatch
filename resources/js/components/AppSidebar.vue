<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart3, Bus, Clock, History, LayoutGrid, Route, Truck, UserCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const isAdmin = computed(() => page.props.auth.user.role === 'admin');

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
    { title: 'Dispatch Board', href: '/dispatch', icon: Bus },
    { title: 'Reports', href: '/reports', icon: BarChart3 },
    { title: 'History', href: '/history', icon: History },
];

const adminNavItems: NavItem[] = [
    { title: 'Users', href: '/admin/users', icon: Users },
    { title: 'Trip Codes', href: '/admin/trip-codes', icon: Route },
    { title: 'Vehicles', href: '/admin/vehicles', icon: Truck },
    { title: 'Drivers', href: '/admin/drivers', icon: UserCheck },
    { title: 'Attendance', href: '/admin/attendance', icon: Clock },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" label="Navigation" />
            <NavMain v-if="isAdmin" :items="adminNavItems" label="Administration" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
