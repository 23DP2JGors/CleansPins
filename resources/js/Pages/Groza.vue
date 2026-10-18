<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
});

const price = (value) => `${Number(value).toFixed(2)} €`;
const lineTotal = (i) => Number(i.produkts.cena) * i.daudzums;
const total = computed(() => props.items.reduce((sum, i) => sum + lineTotal(i), 0));

const setQty = (item, qty) => {
    if (qty < 1) return;
    router.patch(route('groza.update', item.rindas_id), { daudzums: qty }, { preserveScroll: true });
};

const remove = (item) => {
    router.delete(route('groza.destroy', item.rindas_id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Grozs" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-headline-large font-weight-bold text-white">
                    <span class="text-primary">Grozs</span>
                </h1>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-10">
            <v-row v-if="items.length">
                <v-col cols="12" md="8">
                    <v-card
                        v-for="item in items"
                        :key="item.rindas_id"
                        variant="flat"
                        border
                        class="pa-4 mb-4"
                    >
                        <div class="d-flex align-center ga-4 flex-wrap">
                            <v-avatar color="surface-variant" rounded="lg" size="64">
                                <v-img v-if="item.produkts.attels" :src="item.produkts.attels" cover />
                                <v-icon v-else icon="mdi-bottle-tonic-outline" color="secondary" />
                            </v-avatar>

                            <div class="flex-grow-1">
                                <div class="text-title-medium font-weight-bold">
                                    {{ item.produkts.nosaukums }}
                                </div>
                                <div class="text-body-medium text-medium-emphasis">
                                    {{ price(item.produkts.cena) }}
                                </div>
                            </div>

                            <div class="d-flex align-center ga-1">
                                <v-btn
                                    icon="mdi-minus"
                                    size="small"
                                    variant="outlined"
                                    :disabled="item.daudzums <= 1"
                                    @click="setQty(item, item.daudzums - 1)"
                                />
                                <span class="text-title-medium px-3">{{ item.daudzums }}</span>
                                <v-btn
                                    icon="mdi-plus"
                                    size="small"
                                    variant="outlined"
                                    :disabled="item.daudzums >= item.produkts.krajums"
                                    @click="setQty(item, item.daudzums + 1)"
                                />
                            </div>

                            <div class="text-title-large font-weight-bold" style="min-width: 90px; text-align: right">
                                {{ price(lineTotal(item)) }}
                            </div>

                            <v-btn
                                icon="mdi-delete-outline"
                                variant="text"
                                color="error"
                                @click="remove(item)"
                            />
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card variant="flat" border class="pa-6">
                        <div class="text-title-large font-weight-bold mb-4">Kopsavilkums</div>
                        <div class="d-flex justify-space-between mb-2 text-body-large">
                            <span>Preces</span>
                            <span>{{ items.reduce((s, i) => s + i.daudzums, 0) }}</span>
                        </div>
                        <v-divider class="my-3" />
                        <div class="d-flex justify-space-between mb-6 text-title-large font-weight-bold">
                            <span>Kopā</span>
                            <span>{{ price(total) }}</span>
                        </div>
                        <v-btn block size="large" color="primary" :href="route('pasutijums.create')">
                            Noformēt pasūtījumu
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>

            <div v-else class="text-center py-16 text-medium-emphasis">
                <v-icon icon="mdi-cart-off" size="48" class="mb-2" />
                <div class="text-body-large mb-4">Tavs grozs ir tukšs</div>
                <v-btn color="primary" href="/katalogs">Uz katalogu</v-btn>
            </div>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
</style>