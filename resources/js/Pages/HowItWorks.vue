<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const steps = [
    { icon: 'mdi-magnify', title: 'Izvēlies zonu vai problēmu', text: 'Diski, stikli, salons vai krāsojums. Vai atlasi pēc konkrētas problēmas.' },
    { icon: 'mdi-bottle-tonic-outline', title: 'Atrodi īsto produktu', text: 'Mēs parādām tikai tos produktus, kas der tavai situācijai.' },
    { icon: 'mdi-cart-outline', title: 'Pievieno grozam', text: 'Pasūti dažos klikšķos, bez liekas sarežģītības.' },
    { icon: 'mdi-truck-fast-outline', title: 'Saņem piegādi', text: 'Produkti tiek nosūtīti ātri, un tu vari sākt kopšanu.' },
];
</script>

<template>
    <Head title="Kā tas darbojas" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-h4 text-md-h3 font-weight-bold text-white">
                    Kā tas <span class="text-primary">darbojas</span>
                </h1>
                <p class="text-white mt-3" style="opacity: 0.8; max-width: 520px">
                    Četri vienkārši soļi no problēmas līdz tīram un spožam auto.
                </p>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-12">
            <v-row>
                <v-col v-for="(s, i) in steps" :key="s.title" cols="12" sm="6" md="3">
                    <v-card class="pa-6 h-100" variant="flat" border>
                        <div class="d-flex align-center justify-space-between mb-4">
                            <v-avatar color="primary" size="52">
                                <v-icon :icon="s.icon" color="on-primary" size="28" />
                            </v-avatar>
                            <span class="text-h4 font-weight-bold text-grey-lighten-1">0{{ i + 1 }}</span>
                        </div>
                        <div class="text-subtitle-1 font-weight-bold mb-2">{{ s.title }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ s.text }}</div>
                    </v-card>
                </v-col>
            </v-row>

            <div class="text-center mt-10">
                <v-btn color="primary" size="x-large" href="/katalogs" class="px-10">
                    Skatīt katalogu
                </v-btn>
            </div>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
</style>