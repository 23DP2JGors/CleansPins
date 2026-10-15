<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    e_pasts: '',
    parole: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('parole'),
    });
};
</script>

<template>
    <Head title="Pieslēgties" />
    <MainLayout>
        <v-container class="d-flex justify-center py-16">
            <v-card class="pa-8 w-100" max-width="440">
                <h1 class="text-h5 font-weight-bold mb-1">Pieslēgties</h1>
                <p class="text-body-2 text-medium-emphasis mb-6">
                    Ievadi savu e-pastu un paroli.
                </p>

                <v-alert v-if="status" type="success" variant="tonal" class="mb-4">
                    {{ status }}
                </v-alert>

                <form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.e_pasts"
                        label="E-pasts"
                        type="email"
                        autocomplete="username"
                        prepend-inner-icon="mdi-email-outline"
                        :error-messages="form.errors.e_pasts"
                        class="mb-2"
                        autofocus
                    />

                    <v-text-field
                        v-model="form.parole"
                        label="Parole"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        prepend-inner-icon="mdi-lock-outline"
                        :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showPassword = !showPassword"
                        :error-messages="form.errors.parole"
                    />

                    <div class="d-flex align-center justify-space-between mb-4">
                        <v-checkbox
                            v-model="form.remember"
                            label="Atcerēties mani"
                            density="compact"
                            hide-details
                        />
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-body-2"
                        >
                            Aizmirsi paroli?
                        </Link>
                    </div>

                    <v-btn
                        type="submit"
                        color="primary"
                        size="large"
                        block
                        :loading="form.processing"
                    >
                        Pieslēgties
                    </v-btn>
                </form>

                <div class="text-center text-body-2 mt-6">
                    Nav konta?
                    <Link :href="route('register')" class="font-weight-bold">Reģistrēties</Link>
                </div>
            </v-card>
        </v-container>
    </MainLayout>
</template>