<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CarModel from '@/Components/CarModel.vue';

defineProps({
    zones: { type: Array, default: () => [] },
});

const features = [
    { icon: 'mdi-flask-outline', text: 'Profesionālā ķīmija' },
    { icon: 'mdi-truck-fast-outline', text: 'Ātra piegāde' },
    { icon: 'mdi-shield-check-outline', text: 'Pārbaudīti produkti' },
];
</script>

<template>
    <Head title="Sākums" />
    <MainLayout>
        <!-- HERO -->
        <section class="hero">
            <v-container style="max-width: 1280px" class="py-16">
                <v-row align="center">
                    <v-col cols="12" md="7">
                        <v-chip color="primary" variant="flat" size="small" class="mb-6">
                            Auto kopšanas ķīmija
                        </v-chip>

                        <h1 class="text-display-small font-weight-bold text-white mb-4">
                            Tavs auto pelnījis
                            <span class="text-primary">spožumu</span>
                        </h1>

                        <p class="text-body-large text-white mb-8" style="opacity: 0.8; max-width: 520px">
                            Izvēlies produktus pēc zonas vai problēmas: diski, stikli, salons,
                            krāsojums. Mēs palīdzēsim atrast īsto risinājumu.
                        </p>

                        <div class="d-flex flex-wrap ga-3">
                            <v-btn color="primary" size="x-large" href="/katalogs" class="px-8">
                                Skatīt katalogu
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="white"
                                size="x-large"
                                href="/ka-tas-darbojas"
                                class="px-8"
                            >
                                Kā tas darbojas
                            </v-btn>
                        </div>
                    </v-col>

                    <v-col cols="12" md="5" class="d-none d-md-flex justify-center">
                        <div class="hero-visual d-flex align-center justify-center">
                            <v-icon icon="mdi-car-wash" size="160" color="primary" />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- FEATURE STRIP -->
        <v-container style="max-width: 1280px" class="py-10">
            <v-row>
                <v-col v-for="f in features" :key="f.text" cols="12" sm="4">
                    <v-card class="pa-5 d-flex align-center ga-4" variant="flat" border>
                        <v-avatar color="primary" size="48">
                            <v-icon :icon="f.icon" color="on-primary" />
                        </v-avatar>
                        <span class="text-title-medium font-weight-medium">{{ f.text }}</span>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- INTERACTIVE CAR -->
        <v-container style="max-width: 1280px" class="pb-16">
            <v-card variant="flat" border class="pa-6 pa-md-10">
                <v-row align="center">
                    <v-col cols="12" md="5">
                        <h2 class="text-headline-small font-weight-bold mb-3">
                            Izvēlies auto daļu
                        </h2>
                        <p class="text-body-large text-medium-emphasis mb-6">
                            Uzspied uz virsbūves, stikliem, salona, diskiem vai riepām, un mēs
                            parādīsim produktus tieši šai zonai.
                        </p>
                        <v-btn
                            variant="text"
                            href="/katalogs"
                            append-icon="mdi-arrow-right"
                            class="px-0"
                        >
                            Visi produkti
                        </v-btn>
                    </v-col>

                    <v-col cols="12" md="7">
                        <CarModel :zones="zones" />
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.hero {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
.hero-visual {
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: rgba(180, 248, 200, 0.08);
    border: 2px solid rgba(180, 248, 200, 0.25);
}
</style>