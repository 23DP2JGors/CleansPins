<script setup>
// apstiprinājuma dialogs (dzēšana, pasūtījuma atcelšana)
defineProps({
    title: { type: String, required: true },
    text: { type: String, default: '' },
    confirmText: { type: String, default: 'Dzēst' },
    color: { type: String, default: 'error' },
    loading: { type: Boolean, default: false },
    error: { type: String, default: '' },
});
defineEmits(['confirm']);

const open = defineModel({ type: Boolean, default: false });
</script>

<template>
    <v-dialog v-model="open" max-width="460">
        <v-card class="pa-6">
            <div class="text-title-large font-weight-bold mb-2">{{ title }}</div>
            <p v-if="text" class="text-body-large text-medium-emphasis">{{ text }}</p>
            <slot />
            <v-alert v-if="error" type="error" variant="tonal" density="compact" class="mt-4">
                {{ error }}
            </v-alert>
            <div class="d-flex justify-end flex-wrap ga-2 mt-6">
                <v-btn variant="text" @click="open = false">Atpakaļ</v-btn>
                <v-btn :color="color" :loading="loading" @click="$emit('confirm')">{{ confirmText }}</v-btn>
            </div>
        </v-card>
    </v-dialog>
</template>
