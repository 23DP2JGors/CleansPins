<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const form = useForm({
    vards: '',
    uzvards: '',
    e_pasts: '',
    parole: '',
    parole_confirmation: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('parole', 'parole_confirmation'),
    });
};
</script>

<template>
    <Head title="Reģistrēties" />
    <MainLayout>
        <v-container class="d-flex justify-center py-16">
            <v-card class="pa-8 w-100" max-width="480">
                <h1 class="text-headline-small font-weight-bold mb-1">Izveidot kontu</h1>
                <p class="text-body-medium text-medium-emphasis mb-6">
                    Aizpildi datus, lai sāktu iepirkties.
                </p>

                <form @submit.prevent="submit">
                    <v-row dense>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.vards"
                                label="Vārds"
                                autocomplete="given-name"
                                :error-messages="form.errors.vards"
                                autofocus
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.uzvards"
                                label="Uzvārds"
                                autocomplete="family-name"
                                :error-messages="form.errors.uzvards"
                            />
                        </v-col>
                    </v-row>

                    <v-text-field
                        v-model="form.e_pasts"
                        label="E-pasts"
                        type="email"
                        autocomplete="username"
                        prepend-inner-icon="mdi-email-outline"
                        :error-messages="form.errors.e_pasts"
                    />

                    <v-text-field
                        v-model="form.parole"
                        label="Parole"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        prepend-inner-icon="mdi-lock-outline"
                        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showPassword = !showPassword"
                        :error-messages="form.errors.parole"
                    />

                    <v-text-field
                        v-model="form.parole_confirmation"
                        label="Atkārtot paroli"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        prepend-inner-icon="mdi-lock-check-outline"
                        :error-messages="form.errors.parole_confirmation"
                        class="mb-2"
                    />

                    <v-btn
                        type="submit"
                        color="primary"
                        size="large"
                        block
                        :loading="form.processing"
                    >
                        Reģistrēties
                    </v-btn>
                </form>

                <div class="text-center text-body-medium mt-6">
                    Jau ir konts?
                    <Link :href="route('login')" class="font-weight-bold">Pieslēgties</Link>
                </div>
            </v-card>
        </v-container>
    </MainLayout>
</template>