<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

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

const logout = () => router.post(route('logout'));

const year = new Date().getFullYear();
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
                    <span class="text-title-large font-weight-bold text-white" style="letter-spacing: 2px">
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
                            {{ user.vards }}
                        </v-btn>
                        <v-btn color="primary" variant="flat" class="ml-2" @click="logout">
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
                    title="Iziet"
                    prepend-icon="mdi-logout"
                    base-color="primary"
                    @click="logout"
                />
            </v-list>
        </v-navigation-drawer>

        <!-- PAGE CONTENT -->
        <v-main>
            <slot />
        </v-main>

        <!-- FOOTER -->
        <v-footer color="secondary" class="pa-0">
            <v-container style="max-width: 1280px" class="py-10">
                <v-row>
                    <v-col cols="12" md="5">
                        <div class="d-flex align-center ga-2 mb-3">
                            <v-icon icon="mdi-car-wash" color="primary" size="28" />
                            <span class="text-title-large font-weight-bold text-white" style="letter-spacing: 2px">
                                AUTO <span class="text-primary">CARE</span>
                            </span>
                        </div>
                        <p class="text-body-medium text-white" style="opacity: 0.7; max-width: 360px">
                            Profesionāla auto kopšanas ķīmija. Atrodi īsto produktu savam auto pēc zonas vai problēmas.
                        </p>
                    </v-col>

                    <v-col cols="6" md="3">
                        <div class="text-title-small font-weight-bold text-primary mb-3">Navigācija</div>
                        <div class="d-flex flex-column ga-2">
                            <Link
                                v-for="item in links"
                                :key="item.href"
                                :href="item.href"
                                class="text-body-medium text-white text-decoration-none"
                                style="opacity: 0.8"
                            >
                                {{ item.title }}
                            </Link>
                        </div>
                    </v-col>

                    <v-col cols="6" md="4">
                        <div class="text-title-small font-weight-bold text-primary mb-3">Kontakti</div>
                        <div class="d-flex flex-column ga-2 text-body-medium text-white" style="opacity: 0.8">
                            <span><v-icon icon="mdi-email-outline" size="16" class="mr-2" />info@autocare.lv</span>
                            <span><v-icon icon="mdi-phone-outline" size="16" class="mr-2" />+371 20000000</span>
                            <span><v-icon icon="mdi-map-marker-outline" size="16" class="mr-2" />Rīga, Latvija</span>
                        </div>
                    </v-col>
                </v-row>

                <v-divider class="my-6" style="opacity: 0.2" color="white" />

                <div class="text-body-small text-white text-center" style="opacity: 0.6">
                    © {{ year }} Auto Care. Visas tiesības aizsargātas.
                </div>
            </v-container>
        </v-footer>
    </v-app>
</template>