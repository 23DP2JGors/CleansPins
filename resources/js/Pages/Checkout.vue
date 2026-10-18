<script setup>
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import PakomatsPicker from '@/Components/PakomatsPicker.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    defaults: { type: Object, default: () => ({}) },
});

const form = useForm({
    vards: props.defaults.vards ?? '',
    uzvards: props.defaults.uzvards ?? '',
    talrunis: '',
    pakomata_id: null,
});

// viss izvēlētā pakomāta objekts — tikai attēlošanai, formā aiziet tikai ID
const pakomats = ref(null);

const price = (value) => `${Number(value).toFixed(2)} €`;
const lineTotal = (i) => Number(i.produkts.cena) * i.daudzums;
const total = computed(() => props.items.reduce((sum, i) => sum + lineTotal(i), 0));

// atstarpes numurā ("+371 2000 0000") netraucē validācijai
const submit = () =>
    form
        .transform((data) => ({ ...data, talrunis: data.talrunis.replace(/\s+/g, '') }))
        .post(route('pasutijums.store'), { preserveScroll: true });
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
                    <form @submit.prevent="submit">
                        <v-card variant="flat" border class="pa-6 mb-6">
                            <div class="text-title-large font-weight-bold mb-4">Kontaktinformācija</div>

                            <v-row dense>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="form.vards"
                                        label="Vārds"
                                        autocomplete="given-name"
                                        maxlength="30"
                                        :error-messages="form.errors.vards"
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="form.uzvards"
                                        label="Uzvārds"
                                        autocomplete="family-name"
                                        maxlength="30"
                                        :error-messages="form.errors.uzvards"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.talrunis"
                                        label="Tālrunis"
                                        type="tel"
                                        autocomplete="tel"
                                        placeholder="+371XXXXXXXX"
                                        prepend-inner-icon="mdi-phone-outline"
                                        hint="Formāts: +371 un 8 cipari, piemēram, +37120000000"
                                        persistent-hint
                                        :error-messages="form.errors.talrunis"
                                    />
                                </v-col>
                            </v-row>
                        </v-card>

                        <v-card variant="flat" border class="pa-6">
                            <div class="text-title-large font-weight-bold mb-1">Piegāde uz pakomātu</div>
                            <div class="text-body-medium text-medium-emphasis mb-4">
                                Izvēlies pārvadātāju un pakomātu, kurā saņemsi pasūtījumu.
                            </div>

                            <PakomatsPicker
                                v-model="form.pakomata_id"
                                :error="form.errors.pakomata_id"
                                @select="pakomats = $event"
                            />

                            <v-btn
                                type="submit"
                                color="primary"
                                size="large"
                                block
                                class="mt-6"
                                :loading="form.processing"
                                :disabled="form.processing || !form.pakomata_id"
                            >
                                Apstiprināt pasūtījumu
                            </v-btn>
                            <div class="text-caption text-medium-emphasis text-center mt-2">
                                <template v-if="!form.pakomata_id">Lai turpinātu, izvēlies pakomātu.</template>
                                <template v-else>Pēc apstiprināšanas pakomātu vairs nevarēs mainīt.</template>
                            </div>
                        </v-card>
                    </form>
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

                        <template v-if="pakomats">
                            <v-divider class="my-3" />
                            <div class="text-body-medium text-medium-emphasis">Piegāde</div>
                            <div class="text-body-large">{{ pakomats.nosaukums }}</div>
                            <div class="text-body-medium text-medium-emphasis">
                                {{ pakomats.adrese }}, {{ pakomats.pilseta }}
                            </div>
                        </template>
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
