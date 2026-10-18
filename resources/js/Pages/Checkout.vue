<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
});

const form = useForm({ piegades_adrese: '' });

const price = (value) => `${Number(value).toFixed(2)} €`;
const lineTotal = (i) => Number(i.produkts.cena) * i.daudzums;
const total = computed(() => props.items.reduce((sum, i) => sum + lineTotal(i), 0));

const submit = () => form.post(route('pasutijums.store'));
</script>

<template>
    <Head title="Noformēt pasūtījumu" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-headline-large font-weight-bold text-white">
                    Noformēt <span class="text-primary">pasūtījumu</span>
                </h1>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-10">
            <v-row>
                <v-col cols="12" md="7">
                    <v-card variant="flat" border class="pa-6">
                        <div class="text-title-large font-weight-bold mb-4">Piegādes adrese</div>

                        <form @submit.prevent="submit">
                            <v-textarea
                                v-model="form.piegades_adrese"
                                label="Adrese (iela, pilsēta, pasta indekss)"
                                rows="3"
                                auto-grow
                                prepend-inner-icon="mdi-map-marker-outline"
                                :error-messages="form.errors.piegades_adrese"
                                autofocus
                            />

                            <v-btn
                                type="submit"
                                color="primary"
                                size="large"
                                block
                                class="mt-2"
                                :loading="form.processing"
                            >
                                Apstiprināt pasūtījumu
                            </v-btn>
                        </form>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <v-card variant="flat" border class="pa-6">
                        <div class="text-title-large font-weight-bold mb-4">Tavs pasūtījums</div>

                        <div
                            v-for="item in items"
                            :key="item.rindas_id"
                            class="d-flex justify-space-between ga-4 mb-3 text-body-large"
                        >
                            <span>{{ item.produkts.nosaukums }} × {{ item.daudzums }}</span>
                            <span class="font-weight-medium">{{ price(lineTotal(item)) }}</span>
                        </div>

                        <v-divider class="my-3" />
                        <div class="d-flex justify-space-between text-title-large font-weight-bold">
                            <span>Kopā</span>
                            <span>{{ price(total) }}</span>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
</style>