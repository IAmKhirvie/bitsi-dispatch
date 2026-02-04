<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bus, Clock, MapPin, Shield, Phone, ChevronRight, Armchair, Star, Route, Users } from 'lucide-vue-next';
import { ref } from 'vue';

// ─── HERO SECTION (edit text/tagline here) ─────────────────────────────────
const hero = {
    title: 'Bicol Isarog Transport System, Inc.',
    subtitle: 'BITSI Dispatch Management',
    tagline: 'Streamlining bus dispatch operations across the Bicol region with real-time tracking, smart scheduling, and reliable service.',
    cta: { label: 'Access Dispatch System', href: '/login' },
    secondaryCta: { label: 'Learn More', href: '#services' },
};

// ─── STATS (add/remove items freely) ────────────────────────────────────────
const stats = [
    { value: '30+', label: 'Years of Service', icon: Star },
    { value: '50+', label: 'Buses in Fleet', icon: Bus },
    { value: '20+', label: 'Routes Covered', icon: Route },
    { value: '1M+', label: 'Passengers Yearly', icon: Users },
];

// ─── SERVICES (add/remove items freely) ──────────────────────────────────────
const services = [
    {
        icon: Bus,
        title: 'Daily Dispatch Management',
        description: 'Comprehensive digital dispatch board replacing manual Excel sheets. Track every bus, driver, and trip in real-time.',
    },
    {
        icon: MapPin,
        title: 'Real-Time GPS Tracking',
        description: 'Monitor fleet positions live on an interactive map. Know exactly where every bus is at any moment.',
    },
    {
        icon: Clock,
        title: 'Smart Scheduling',
        description: 'Automated trip code assignment with departure scheduling. Auto-fill routes, terminals, and bus types instantly.',
    },
    {
        icon: Shield,
        title: 'Preventive Maintenance',
        description: 'PMS tracking by kilometers or trips with configurable thresholds. Never miss a maintenance schedule again.',
    },
    {
        icon: Phone,
        title: 'SMS Driver Notifications',
        description: 'Automatic SMS alerts to drivers on trip assignments and status changes via Semaphore integration.',
    },
    {
        icon: Armchair,
        title: 'Multiple Bus Types',
        description: 'Support for Regular, Deluxe, Super Deluxe, Elite, Sleeper, Single Seater, and SkyBus classifications.',
    },
];

// ─── ROUTES (add/remove items freely) ────────────────────────────────────────
const routes = [
    { origin: 'Cubao', destination: 'Naga', types: ['Regular', 'Deluxe', 'Sleeper'] },
    { origin: 'Cubao', destination: 'Legazpi', types: ['Regular', 'Super Deluxe'] },
    { origin: 'Cubao', destination: 'Sorsogon', types: ['Regular'] },
    { origin: 'Cubao', destination: 'Tabaco', types: ['Regular'] },
    { origin: 'Cubao', destination: 'Virac', types: ['Deluxe'] },
    { origin: 'Cubao', destination: 'Tacloban', types: ['Elite'] },
    { origin: 'Naga', destination: 'Masbate', types: ['Regular'] },
];

// ─── FAQ (add/remove items freely) ───────────────────────────────────────────
const faqs = [
    {
        question: 'What is the BITSI Dispatch System?',
        answer: 'It is a web-based fleet management platform that digitizes the daily bus status report, enabling real-time dispatch operations, GPS tracking, reporting, and driver notifications.',
    },
    {
        question: 'Who can access the system?',
        answer: 'The system supports three roles: Administrators (full access), Operations Managers (fleet and route management), and Dispatchers (daily dispatch operations).',
    },
    {
        question: 'How does the PMS tracking work?',
        answer: 'Each vehicle has a configurable maintenance threshold based on kilometers traveled or number of trips. The system alerts when a vehicle approaches or exceeds its PMS threshold.',
    },
    {
        question: 'What bus statuses are tracked?',
        answer: 'Vehicles are tracked as OK (available), UR (Under Repair), PMS (due for maintenance), In Transit (on a long trip), or Lutaw (idle/unused with day counting).',
    },
];

const openFaq = ref<number | null>(null);

function toggleFaq(index: number) {
    openFaq.value = openFaq.value === index ? null : index;
}

// ─── FOOTER LINKS (add/remove freely) ────────────────────────────────────────
const footerLinks = [
    { label: 'Dispatch Board', href: '/dispatch' },
    { label: 'Tracking Map', href: '/tracking' },
    { label: 'Reports', href: '/reports' },
    { label: 'History', href: '/history' },
];
</script>

<template>
    <Head title="BITSI - Bicol Isarog Transport System">
        <meta name="description" :content="hero.tagline" />
    </Head>

    <div class="min-h-screen bg-white dark:bg-zinc-950">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur-lg dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                        <Bus class="h-5 w-5" />
                    </div>
                    <div>
                        <span class="text-lg font-bold text-zinc-900 dark:text-white">BITSI</span>
                        <span class="ml-1 hidden text-sm text-zinc-500 dark:text-zinc-400 sm:inline">Dispatch</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    >
                        Dashboard
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                        >
                            Get Started
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSA2MCAwIEwgMCAwIDAgNjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-40"></div>
            <div class="relative mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:py-40">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm text-blue-100 backdrop-blur-sm">
                        <Bus class="h-4 w-4" />
                        Trusted Transport Since 1990s
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ hero.title }}
                    </h1>
                    <p class="mt-3 text-xl font-medium text-blue-200 sm:text-2xl">
                        {{ hero.subtitle }}
                    </p>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-blue-100">
                        {{ hero.tagline }}
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            :href="hero.cta.href"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition-all hover:bg-blue-50 hover:shadow-xl"
                        >
                            {{ hero.cta.label }}
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                        <a
                            :href="hero.secondaryCta.href"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-8 py-3.5 text-sm font-semibold text-white transition-all hover:bg-white/10"
                        >
                            {{ hero.secondaryCta.label }}
                        </a>
                    </div>
                </div>
            </div>
            <!-- Wave divider -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path d="M0 80L60 73.3C120 66.7 240 53.3 360 46.7C480 40 600 40 720 46.7C840 53.3 960 66.7 1080 70C1200 73.3 1320 66.7 1380 63.3L1440 60V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0Z" class="fill-white dark:fill-zinc-950"/>
                </svg>
            </div>
        </section>

        <!-- Stats -->
        <section class="relative z-10 -mt-4">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        class="rounded-2xl border border-zinc-200 bg-white p-6 text-center shadow-sm transition-shadow hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        <component :is="stat.icon" class="mx-auto mb-3 h-8 w-8 text-blue-600" />
                        <div class="text-3xl font-bold text-zinc-900 dark:text-white">{{ stat.value }}</div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services -->
        <section id="services" class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Dispatch System Features
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        A complete digital solution for managing daily bus operations
                    </p>
                </div>
                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="group rounded-2xl border border-zinc-200 bg-white p-8 transition-all hover:border-blue-200 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-800"
                    >
                        <div class="mb-5 inline-flex rounded-xl bg-blue-50 p-3 text-blue-600 transition-colors group-hover:bg-blue-100 dark:bg-blue-950 dark:group-hover:bg-blue-900">
                            <component :is="service.icon" class="h-6 w-6" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ service.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ service.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Routes -->
        <section class="border-y border-zinc-200 bg-zinc-50 py-24 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Routes We Serve
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Connecting Manila to the Bicol region and beyond
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-4xl">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            v-for="r in routes"
                            :key="`${r.origin}-${r.destination}`"
                            class="flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-5 transition-all hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <MapPin class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-zinc-900 dark:text-white">
                                    {{ r.origin }} <span class="mx-1 text-zinc-400">&rarr;</span> {{ r.destination }}
                                </div>
                                <div class="mt-1 flex flex-wrap gap-1.5">
                                    <span
                                        v-for="type in r.types"
                                        :key="type"
                                        class="inline-block rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ type }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
                        Frequently Asked Questions
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Common questions about the dispatch system
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-3xl space-y-4">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="rounded-xl border border-zinc-200 bg-white transition-all dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        <button
                            @click="toggleFaq(index)"
                            class="flex w-full items-center justify-between px-6 py-5 text-left"
                        >
                            <span class="pr-4 font-semibold text-zinc-900 dark:text-white">{{ faq.question }}</span>
                            <ChevronRight
                                class="h-5 w-5 shrink-0 text-zinc-400 transition-transform duration-200"
                                :class="{ 'rotate-90': openFaq === index }"
                            />
                        </button>
                        <div
                            v-show="openFaq === index"
                            class="border-t border-zinc-100 px-6 pb-5 pt-4 dark:border-zinc-800"
                        >
                            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ faq.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-6 py-24">
                <div class="rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-16 text-center sm:px-16">
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to modernize your dispatch?</h2>
                    <p class="mx-auto mt-4 max-w-xl text-lg text-blue-100">
                        Access the BITSI Dispatch System to manage daily operations, track your fleet, and generate reports.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition-all hover:bg-blue-50"
                        >
                            Sign In to Dashboard
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-8 py-3.5 text-sm font-semibold text-white transition-all hover:bg-white/10"
                        >
                            Create Account
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
                            <Bus class="h-4 w-4" />
                        </div>
                        <span class="font-semibold text-zinc-900 dark:text-white">BITSI Dispatch</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-6">
                        <Link
                            v-for="link in footerLinks"
                            :key="link.label"
                            :href="link.href"
                            class="text-sm text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                        >
                            {{ link.label }}
                        </Link>
                    </div>
                </div>
                <div class="mt-8 border-t border-zinc-200 pt-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-500">
                    &copy; {{ new Date().getFullYear() }} Bicol Isarog Transport System, Inc. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
