<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    order: { type: Object, required: true },
});

const price = (value) => `${Number(value).toFixed(2)} €`;
const lineTotal = (r) => Number(r.cena_bridi) * r.daudzums;
const date = (d) => new Date(d).toLocaleString('lv-LV');
</script>

<template>
    <Head :title="`Pasūtījums #${order.pasutijuma_id}`" />
    <MainLayout>
        <v-container style="max-width: 800px" class="py-12">
            <div class="text-center mb-8">
                <v-avatar color="primary" size="72" class="mb-4">
                    <v-icon icon="mdi-check" size="40" color="on-primary" />
                </v-avatar>
                <h1 class="text-headline-large font-weight-bold mb-2">Paldies par pasūtījumu!</h1>
                <p class="text-body-large text-medium-emphasis">
                    Pasūtījums #{{ order.pasutijuma_id }} ir pieņemts.
                </p>
            </div>

            <v-card variant="flat" border class="pa-6">
                <v-row class="mb-4">
                    <v-col cols="12" sm="4">
                        <div class="text-body-small text-medium-emphasis">Datums</div>
                        <div class="text-body-large">{{ date(order.datums) }}</div>
                    </v-col>
                    <v-col cols="12" sm="4">
                        <div class="text-body-small text-medium-emphasis">Statuss</div>
                        <v-chip size="small" color="primary" variant="flat" class="mt-1">
                            {{ order.statuss }}
                        </v-chip>
                    </v-col>
                    <v-col cols="12" sm="4">
                        <div class="text-body-small text-medium-emphasis">Piegādes adrese</div>
                        <div class="text-body-large">{{ order.piegades_adrese }}</div>
                    </v-col>
                </v-row>

                <v-divider class="mb-4" />

                <div
                    v-for="r in order.rindas"
                    :key="r.rindas_id"
                    class="d-flex justify-space-between ga-4 mb-3 text-body-large"
                >
                    <span>{{ r.produkts?.nosaukums }} × {{ r.daudzums }}</span>
                    <span class="font-weight-medium">{{ price(lineTotal(r)) }}</span>
                </div>

                <v-divider class="my-3" />
                <div class="d-flex justify-space-between text-title-large font-weight-bold">
                    <span>Kopā</span>
                    <span>{{ price(order.kopsumma) }}</span>
                </div>
            </v-card>

            <div class="text-center mt-8">
                <v-btn color="primary" size="large" href="/katalogs">Turpināt iepirkties</v-btn>
            </div>
        </v-container>
    </MainLayout>
</template>