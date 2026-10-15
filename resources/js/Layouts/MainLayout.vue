<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const drawer = ref(false);

const user = computed(() => page.props.auth?.user ?? null);
const currentUrl = computed(() => page.url);

// Placeholder paths, to be replaced with route() calls once those routes exist
const links = [
    { title: 'Katalogs', href: '/katalogs' },
    { title: 'Kā tas darbojas', href: '/ka-tas-darbojas' },
    { title: 'Padomi', href: '/padomi' },
];

const isActive = (href) => currentUrl.value.startsWith(href);
</script>

<template>
    <v-app>
        <!-- HEADER -->
        <v-app-bar color="secondary" flat height="72">
            <v-container class="d-flex align-center fill-height" style="max-width: 1280px">
                <!-- Mobile menu button -->
                <v-app-bar-nav-icon
                    class="d-md-none"
                    @click="drawer = !drawer"
                />

                <!-- Logo -->
                <Link href="/" class="text-decoration-none d-flex align-center ga-2">
                    <v-icon icon="mdi-car-wash" color="primary" size="28" />
                    <span class="text-h6 font-weight-bold text-white" style="letter-spacing: 2px">
                        AUTO <span class="text-primary">CARE</span>
                    </span>
                </Link>

                <v-spacer />

                <!-- Desktop navigation -->
                <nav class="d-none d-md-flex align-center ga-1">
                    <v-btn
                        v-for="item in links"
                        :key="item.href"
                        :href="item.href"
                        :variant="isActive(item.href) ? 'flat' : 'text'"
                        :color="isActive(item.href) ? 'primary' : 'white'"
                        as="a"
                    >
                        {{ item.title }}
                    </v-btn>

                    <v-btn
                        v-if="!user"
                        :href="route('login')"
                        color="primary"
                        variant="flat"
                        class="ml-3 px-6"
                    >
                        Pieslēgties
                    </v-btn>

                    <template v-else>
                        <v-btn :href="route('dashboard')" variant="text" color="white">
                            <v-icon icon="mdi-account-circle-outline" start />
                            {{ user.name }}
                        </v-btn>
                        <v-btn
                            :href="route('logout')"
                            method="post"
                            as="button"
                            color="primary"
                            variant="flat"
                            class="ml-2"
                        >
                            Iziet
                        </v-btn>
                    </template>
                </nav>
            </v-container>
        </v-app-bar>

        <!-- MOBILE DRAWER -->
        <v-navigation-drawer v-model="drawer" color="secondary" temporary>
            <v-list nav>
                <v-list-item
                    v-for="item in links"
                    :key="item.href"
                    :href="item.href"
                    :title="item.title"
                    :active="isActive(item.href)"
                    active-color="primary"
                />
                <v-divider class="my-2" />
                <v-list-item
                    v-if="!user"
                    :href="route('login')"
                    title="Pieslēgties"
                    prepend-icon="mdi-login"
                    base-color="primary"
                />
                <v-list-item
                    v-else
                    :href="route('logout')"
                    title="Iziet"
                    prepend-icon="mdi-logout"
                    base-color="primary"
                />
            </v-list>
        </v-navigation-drawer>

        <!-- PAGE CONTENT -->
        <v-main>
            <slot />
        </v-main>
    </v-app>
</template>