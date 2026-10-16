<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
});

const search = ref('');

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.products;
    return props.products.filter(
        (p) =>
            p.nosaukums.toLowerCase().includes(q) ||
            (p.apraksts ?? '').toLowerCase().includes(q),
    );
});

const price = (value) => `${Number(value).toFixed(2)} €`;
</script>

<template>
    <Head title="Katalogs" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-h4 text-md-h3 font-weight-bold text-white">
                    <span class="text-primary">Katalogs</span>
                </h1>
                <p class="text-white mt-3" style="opacity: 0.8">
                    {{ products.length }} produkti auto kopšanai
                </p>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-10">
            <v-text-field
                v-model="search"
                placeholder="Meklēt produktu..."
                prepend-inner-icon="mdi-magnify"
                clearable
                hide-details
                class="mb-8"
                style="max-width: 480px"
            />

            <v-row v-if="filtered.length">
                <v-col v-for="p in filtered" :key="p.produkta_id" cols="12" sm="6" md="4" lg="3">
                    <v-card class="h-100 d-flex flex-column" variant="flat" border>
                        <div class="product-img d-flex align-center justify-center">
                            <v-img v-if="p.attels" :src="p.attels" height="160" cover />
                            <v-icon v-else icon="mdi-bottle-tonic-outline" size="64" color="secondary" />
                        </div>

                        <div class="pa-4 d-flex flex-column flex-grow-1">
                            <v-chip size="x-small" color="primary" variant="flat" class="mb-2 align-self-start">
                                {{ p.kategorija?.nosaukums }}
                            </v-chip>
                            <div class="text-subtitle-1 font-weight-bold mb-1">{{ p.nosaukums }}</div>
                            <div class="text-body-2 text-medium-emphasis mb-4">{{ p.apraksts }}</div>

                            <div class="d-flex align-center justify-space-between mt-auto">
                                <span class="text-h6 font-weight-bold">{{ price(p.cena) }}</span>
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
                <div>Nekas netika atrasts</div>
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
}
</style>