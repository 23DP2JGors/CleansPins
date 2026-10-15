<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
</script>

<template>
    <Head title="Sākums" />
    <MainLayout>
        <v-container class="py-16" style="max-width: 1280px">
            <h1 class="text-h3 font-weight-bold mb-4">Auto Care test</h1>
            <v-btn color="primary" size="large">Skatīt katalogu</v-btn>
        </v-container>
    </MainLayout>
</template>