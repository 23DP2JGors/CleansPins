<script setup>
// vienkāršs CRUD saraksts ar "nosaukums" (+ "apraksts"): kategorijas, zonas, problēmas
import { computed, onMounted, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    resurss: { type: String, required: true }, // maršruta daļa: 'kategorijas' | 'zonas' | 'problemas'
    idKey: { type: String, required: true }, // 'kategorijas_id' | ...
    nosaukums: { type: String, required: true }, // vienskaitlī, piem., 'Kategorija'
    arAprakstu: { type: Boolean, default: false },
    maxlength: { type: Number, default: 100 },
});

const items = ref([]);
const loading = ref(false);
const loadError = ref('');

const load = async () => {
    loading.value = true;
    loadError.value = '';
    try {
        const { data } = await axios.get(route(`admin.${props.resurss}.index`));
        items.value = data;
    } catch {
        loadError.value = 'Neizdevās ielādēt sarakstu.';
    } finally {
        loading.value = false;
    }
};
onMounted(load);

const headers = computed(() => [
    { title: 'Nosaukums', key: 'nosaukums' },
    ...(props.arAprakstu ? [{ title: 'Apraksts', key: 'apraksts', sortable: false }] : []),
    { title: 'Produkti', key: 'produkti_count', align: 'end' },
    { title: '', key: 'actions', align: 'end', sortable: false },
]);

// --- Forma ---

const dialog = ref(false);
const editId = ref(null);
const form = useForm({ nosaukums: '', apraksts: '' });

const openForm = (row = null) => {
    editId.value = row?.[props.idKey] ?? null;
    form.defaults({ nosaukums: row?.nosaukums ?? '', apraksts: row?.apraksts ?? '' });
    form.reset();
    form.clearErrors();
    dialog.value = true;
};

const submitOptions = {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
        dialog.value = false;
        load();
    },
};

const save = () => {
    // zonām/problēmām apraksta nav
    form.transform((data) => (props.arAprakstu ? data : { nosaukums: data.nosaukums }));

    if (editId.value) {
        form.put(route(`admin.${props.resurss}.update`, editId.value), submitOptions);
    } else {
        form.post(route(`admin.${props.resurss}.store`), submitOptions);
    }
};

// --- Dzēšana ---

const toDelete = ref(null);
const deleteError = ref('');
const deleting = ref(false);
const deleteDialog = computed({
    get: () => toDelete.value !== null,
    set: (open) => {
        if (!open) toDelete.value = null;
    },
});

const askDelete = (row) => {
    deleteError.value = '';
    toDelete.value = row;
};

const confirmDelete = () =>
    router.delete(route(`admin.${props.resurss}.destroy`, toDelete.value[props.idKey]), {
        preserveScroll: true,
        preserveState: true,
        onStart: () => (deleting.value = true),
        onFinish: () => (deleting.value = false),
        onSuccess: () => {
            toDelete.value = null;
            load();
        },
        onError: (errors) => (deleteError.value = Object.values(errors)[0]),
    });
</script>

<template>
    <div>
        <div class="d-flex align-center flex-wrap ga-3 mb-4">
            <v-spacer />
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openForm()">Pievienot</v-btn>
        </div>

        <v-alert v-if="loadError" type="error" variant="tonal" density="compact" class="mb-4">{{ loadError }}</v-alert>

        <v-data-table
            :headers="headers"
            :items="items"
            :loading="loading"
            :item-value="idKey"
            density="comfortable"
            items-per-page="10"
            no-data-text="Ierakstu nav."
            loading-text="Ielādē..."
            items-per-page-text="Rindas lapā:"
        >
            <template #item.apraksts="{ item }">
                <span class="text-medium-emphasis">{{ item.apraksts || '—' }}</span>
            </template>
            <template #item.actions="{ item }">
                <div class="d-flex justify-end ga-1">
                    <v-btn icon="mdi-pencil-outline" variant="text" size="small" aria-label="Rediģēt" @click="openForm(item)" />
                    <v-btn icon="mdi-delete-outline" variant="text" size="small" color="error" aria-label="Dzēst" @click="askDelete(item)" />
                </div>
            </template>
        </v-data-table>

        <!-- DIALOGS: forma -->
        <v-dialog v-model="dialog" max-width="520" :fullscreen="$vuetify.display.xs">
            <v-card class="pa-6">
                <div class="text-title-large font-weight-bold mb-4">
                    {{ editId ? `Rediģēt: ${nosaukums.toLowerCase()}` : `Jauna ${nosaukums.toLowerCase()}` }}
                </div>
                <form @submit.prevent="save">
                    <v-text-field
                        v-model="form.nosaukums"
                        label="Nosaukums"
                        :maxlength="maxlength"
                        :error-messages="form.errors.nosaukums"
                    />
                    <v-textarea
                        v-if="arAprakstu"
                        v-model="form.apraksts"
                        label="Apraksts"
                        variant="outlined"
                        rounded="lg"
                        rows="3"
                        auto-grow
                        :error-messages="form.errors.apraksts"
                    />
                    <div class="d-flex justify-end ga-2 mt-2">
                        <v-btn variant="text" @click="dialog = false">Atcelt</v-btn>
                        <v-btn type="submit" color="primary" :loading="form.processing">Saglabāt</v-btn>
                    </div>
                </form>
            </v-card>
        </v-dialog>

        <ConfirmDialog
            v-model="deleteDialog"
            :title="`Dzēst: ${toDelete?.nosaukums ?? ''}?`"
            :text="toDelete?.produkti_count ? `Tai piesaistīti produkti: ${toDelete.produkti_count}.` : 'Darbību nevar atsaukt.'"
            :loading="deleting"
            :error="deleteError"
            @confirm="confirmDelete"
        />
    </div>
</template>
