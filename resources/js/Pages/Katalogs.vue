<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
    total: { type: Number, default: 0 },
    categories: { type: Array, default: () => [] },
    zones: { type: Array, default: () => [] },
    problems: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const toNum = (v) => (v ? Number(v) : null);

const search = ref(props.filters.q ?? '');
const kategorija = ref(toNum(props.filters.kategorija));
const zona = ref(toNum(props.filters.zona));
const problema = ref(toNum(props.filters.problema));

const hasFilters = computed(
    () => !!(search.value || kategorija.value || zona.value || problema.value),
);

const apply = () => {
    const params = {};
    if (search.value) params.q = search.value;
    if (kategorija.value) params.kategorija = kategorija.value;
    if (zona.value) params.zona = zona.value;
    if (problema.value) params.problema = problema.value;

    router.get(route('katalogs'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// search waits 300 ms after typing stops
let timer;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 300);
});

// selects apply immediately
watch([kategorija, zona, problema], apply);

const clearAll = () => {
    search.value = '';
    kategorija.value = null;
    zona.value = null;
    problema.value = null;
};

const price = (value) => `${Number(value).toFixed(2)} €`;
</script>

<template>
    <Head title="Katalogs" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-headline-large font-weight-bold text-white">
                    <span class="text-primary">Katalogs</span>
                </h1>
                <p class="text-body-large text-white mt-3" style="opacity: 0.8">
                    {{ products.length }} no {{ total }} produktiem
                </p>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-10">
            <!-- FILTERS -->
            <v-card variant="flat" border class="pa-4 mb-8">
                <v-row dense align="center">
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="search"
                            placeholder="Meklēt produktu..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            hide-details
                        />
                    </v-col>
                    <v-col cols="12" sm="4" md="3">
                        <v-select
                            v-model="kategorija"
                            :items="categories"
                            item-title="nosaukums"
                            item-value="kategorijas_id"
                            label="Kategorija"
                            clearable
                            hide-details
                        />
                    </v-col>
                    <v-col cols="12" sm="4" md="2">
                        <v-select
                            v-model="zona"
                            :items="zones"
                            item-title="nosaukums"
                            item-value="zonas_id"
                            label="Zona"
                            clearable
                            hide-details
                        />
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                        <v-select
                            v-model="problema"
                            :items="problems"
                            item-title="nosaukums"
                            item-value="problemas_id"
                            label="Problēma"
                            clearable
                            hide-details
                        />
                    </v-col>
                </v-row>

                <div v-if="hasFilters" class="d-flex justify-end mt-3">
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-filter-remove-outline"
                        @click="clearAll"
                    >
                        Notīrīt filtrus
                    </v-btn>
                </div>
            </v-card>

            <!-- PRODUCTS -->
            <v-row v-if="products.length">
                <v-col
                    v-for="(p, i) in products"
                    :key="p.produkta_id"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                    <v-card
                        class="product-card h-100 d-flex flex-column"
                        variant="flat"
                        border
                        :style="{ animationDelay: `${Math.min(i, 12) * 40}ms` }"
                    >
                        <div class="product-img d-flex align-center justify-center">
                            <v-img
                                v-if="p.attels"
                                :src="p.attels"
                                height="160"
                                cover
                                class="product-img-inner"
                            />
                            <v-icon
                                v-else
                                icon="mdi-bottle-tonic-outline"
                                size="64"
                                color="secondary"
                                class="product-img-inner"
                            />
                        </div>

                        <div class="pa-4 d-flex flex-column flex-grow-1">
                            <v-chip
                                size="x-small"
                                color="primary"
                                variant="flat"
                                class="mb-2 align-self-start"
                            >
                                {{ p.kategorija?.nosaukums }}
                            </v-chip>
                            <div class="text-title-medium font-weight-bold mb-1">{{ p.nosaukums }}</div>
                            <div class="text-body-medium text-medium-emphasis mb-4">{{ p.apraksts }}</div>

                            <div class="d-flex align-center justify-space-between mt-auto">
                                <span class="text-title-large font-weight-bold">{{ price(p.cena) }}</span>
                                <v-chip
                                    size="small"
                                    :color="p.krajums > 0 ? 'success' : 'error'"
                                    variant="tonal"
                                >
                                    {{ p.krajums > 0 ? 'Ir noliktavā' : 'Nav pieejams' }}
                                </v-chip>
                            </div>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <div v-else class="text-center py-16 text-medium-emphasis">
                <v-icon icon="mdi-magnify-close" size="48" class="mb-2" />
                <div class="text-body-large mb-4">Nekas netika atrasts</div>
                <v-btn v-if="hasFilters" variant="outlined" @click="clearAll">
                    Notīrīt filtrus
                </v-btn>
            </div>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}

.product-img {
    height: 160px;
    background: #eef6f0;
    overflow: hidden;
}

.product-card {
    opacity: 0;
    animation: card-in 0.5s ease forwards;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.product-card:hover {
    transform: translateY(-6px);
    border-color: #b4f8c8;
    box-shadow: 0 12px 28px rgba(18, 35, 35, 0.12) !important;
}

.product-img-inner {
    transition: transform 0.35s ease;
}
.product-card:hover .product-img-inner {
    transform: scale(1.12);
}

@keyframes card-in {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .product-card {
        animation: none;
        opacity: 1;
    }
    .product-card:hover,
    .product-card:hover .product-img-inner {
        transform: none;
    }
}
</style>