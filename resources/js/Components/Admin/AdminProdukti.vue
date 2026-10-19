<script setup>
import { computed, onMounted, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const produkti = ref([]);
const kategorijas = ref([]);
const zonas = ref([]);
const problemas = ref([]);
const loading = ref(false);
const loadError = ref('');
const search = ref('');

// saraksts ielādējas tikai tad, kad cilne pirmo reizi atvērta
const load = async () => {
    loading.value = true;
    loadError.value = '';
    try {
        const { data } = await axios.get(route('admin.produkti.index'));
        produkti.value = data.produkti;
        kategorijas.value = data.kategorijas;
        zonas.value = data.zonas;
        problemas.value = data.problemas;
    } catch {
        loadError.value = 'Neizdevās ielādēt produktus.';
    } finally {
        loading.value = false;
    }
};
onMounted(load);

const byNumber = (a, b) => Number(a) - Number(b);
const headers = [
    { title: 'Nosaukums', key: 'nosaukums' },
    { title: 'Kategorija', key: 'kategorija.nosaukums' },
    { title: 'Cena', key: 'cena', align: 'end', sort: byNumber },
    { title: 'Krājums', key: 'krajums', align: 'end' },
    { title: '', key: 'actions', align: 'end', sortable: false },
];

const price = (value) => `${Number(value).toFixed(2)} €`;

// --- Forma ---

const blank = () => ({
    kategorijas_id: null,
    nosaukums: '',
    apraksts: '',
    cena: '',
    krajums: 0,
    attels: '',
    zonas: [],
    problemas: [],
});

const dialog = ref(false);
const editId = ref(null);
const form = useForm(blank());

const openForm = (p = null) => {
    editId.value = p?.produkta_id ?? null;
    form.defaults(
        p
            ? {
                  kategorijas_id: p.kategorijas_id,
                  nosaukums: p.nosaukums,
                  apraksts: p.apraksts ?? '',
                  cena: p.cena,
                  krajums: p.krajums,
                  attels: p.attels ?? '',
                  zonas: p.zonas.map((z) => z.zonas_id),
                  problemas: p.problemas.map((pr) => pr.problemas_id),
              }
            : blank(),
    );
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

const save = () =>
    editId.value
        ? form.put(route('admin.produkti.update', editId.value), submitOptions)
        : form.post(route('admin.produkti.store'), submitOptions);

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

const askDelete = (p) => {
    deleteError.value = '';
    toDelete.value = p;
};

const confirmDelete = () =>
    router.delete(route('admin.produkti.destroy', toDelete.value.produkta_id), {
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
            <v-text-field
                v-model="search"
                label="Meklēt produktu"
                prepend-inner-icon="mdi-magnify"
                density="compact"
                hide-details
                clearable
                style="min-width: 220px; max-width: 360px"
            />
            <v-spacer />
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openForm()">Pievienot produktu</v-btn>
        </div>

        <v-alert v-if="loadError" type="error" variant="tonal" density="compact" class="mb-4">{{ loadError }}</v-alert>

        <v-data-table
            :headers="headers"
            :items="produkti"
            :search="search"
            :loading="loading"
            item-value="produkta_id"
            density="comfortable"
            items-per-page="10"
            no-data-text="Produktu nav."
            loading-text="Ielādē..."
            items-per-page-text="Rindas lapā:"
        >
            <template #item.cena="{ item }">
                <span class="text-no-wrap">{{ price(item.cena) }}</span>
            </template>
            <template #item.krajums="{ item }">
                <v-chip :color="item.krajums > 0 ? 'success' : 'error'" variant="tonal" size="small">
                    {{ item.krajums }}
                </v-chip>
            </template>
            <template #item.actions="{ item }">
                <div class="d-flex justify-end ga-1">
                    <v-btn icon="mdi-pencil-outline" variant="text" size="small" aria-label="Rediģēt" @click="openForm(item)" />
                    <v-btn icon="mdi-delete-outline" variant="text" size="small" color="error" aria-label="Dzēst" @click="askDelete(item)" />
                </div>
            </template>
        </v-data-table>

        <!-- DIALOGS: produkta forma -->
        <v-dialog v-model="dialog" max-width="720" :fullscreen="$vuetify.display.xs" scrollable>
            <v-card>
                <v-card-title class="pa-6 pb-2 text-title-large font-weight-bold">
                    {{ editId ? 'Rediģēt produktu' : 'Jauns produkts' }}
                </v-card-title>
                <v-card-text class="pa-6 pt-2">
                    <form id="produkts-form" @submit.prevent="save">
                        <v-row dense>
                            <v-col cols="12" sm="8">
                                <v-text-field v-model="form.nosaukums" label="Nosaukums" maxlength="255" :error-messages="form.errors.nosaukums" />
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-select
                                    v-model="form.kategorijas_id"
                                    :items="kategorijas"
                                    item-title="nosaukums"
                                    item-value="kategorijas_id"
                                    label="Kategorija"
                                    :error-messages="form.errors.kategorijas_id"
                                />
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="form.cena"
                                    label="Cena"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    suffix="€"
                                    :error-messages="form.errors.cena"
                                />
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="form.krajums"
                                    label="Krājums"
                                    type="number"
                                    min="0"
                                    step="1"
                                    :error-messages="form.errors.krajums"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="form.attels"
                                    label="Attēls (ceļš vai URL)"
                                    maxlength="255"
                                    :error-messages="form.errors.attels"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                    v-model="form.apraksts"
                                    label="Apraksts"
                                    variant="outlined"
                                    rounded="lg"
                                    rows="3"
                                    auto-grow
                                    :error-messages="form.errors.apraksts"
                                />
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select
                                    v-model="form.zonas"
                                    :items="zonas"
                                    item-title="nosaukums"
                                    item-value="zonas_id"
                                    label="Zonas"
                                    multiple
                                    chips
                                    closable-chips
                                    :error-messages="form.errors.zonas"
                                />
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select
                                    v-model="form.problemas"
                                    :items="problemas"
                                    item-title="nosaukums"
                                    item-value="problemas_id"
                                    label="Problēmas"
                                    multiple
                                    chips
                                    closable-chips
                                    :error-messages="form.errors.problemas"
                                />
                            </v-col>
                        </v-row>
                    </form>
                </v-card-text>
                <v-card-actions class="pa-6 pt-0">
                    <v-spacer />
                    <v-btn variant="text" @click="dialog = false">Atcelt</v-btn>
                    <v-btn type="submit" form="produkts-form" color="primary" :loading="form.processing">Saglabāt</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <ConfirmDialog
            v-model="deleteDialog"
            title="Dzēst produktu?"
            :text="toDelete ? `“${toDelete.nosaukums}” tiks neatgriezeniski dzēsts.` : ''"
            :loading="deleting"
            :error="deleteError"
            @confirm="confirmDelete"
        />
    </div>
</template>
