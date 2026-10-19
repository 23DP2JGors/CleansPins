<script setup>
import { computed, onMounted, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const page = usePage();
const myId = computed(() => page.props.auth.user.lietotaja_id);

const lietotaji = ref([]);
const loading = ref(false);
const loadError = ref('');
const search = ref('');

const load = async () => {
    loading.value = true;
    loadError.value = '';
    try {
        const { data } = await axios.get(route('admin.lietotaji.index'));
        lietotaji.value = data;
    } catch {
        loadError.value = 'Neizdevās ielādēt lietotājus.';
    } finally {
        loading.value = false;
    }
};
onMounted(load);

const lomas = [
    { title: 'Pircējs', value: 0 },
    { title: 'Administrators', value: 1 },
];

const headers = [
    { title: 'Vārds', key: 'vards' },
    { title: 'Uzvārds', key: 'uzvards' },
    { title: 'E-pasts', key: 'e_pasts' },
    { title: 'Loma', key: 'loma' },
    { title: 'Pasūtījumi', key: 'pasutijumi_count', align: 'end' },
    { title: '', key: 'actions', align: 'end', sortable: false },
];

// --- Forma ---

const dialog = ref(false);
const editId = ref(null);
const isSelf = computed(() => editId.value === myId.value);
const form = useForm({ vards: '', uzvards: '', e_pasts: '', adrese: '', loma: 0, parole: '' });

const openForm = (u = null) => {
    editId.value = u?.lietotaja_id ?? null;
    form.defaults({
        vards: u?.vards ?? '',
        uzvards: u?.uzvards ?? '',
        e_pasts: u?.e_pasts ?? '',
        adrese: u?.adrese ?? '',
        loma: u?.loma ?? 0,
        parole: '',
    });
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
    onFinish: () => form.reset('parole'),
};

const save = () => {
    if (editId.value) {
        // paroli šeit nemaina
        form.transform(({ parole, ...data }) => data).put(route('admin.lietotaji.update', editId.value), submitOptions);
    } else {
        form.transform((data) => data).post(route('admin.lietotaji.store'), submitOptions);
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

const askDelete = (u) => {
    deleteError.value = '';
    toDelete.value = u;
};

const confirmDelete = () =>
    router.delete(route('admin.lietotaji.destroy', toDelete.value.lietotaja_id), {
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
                label="Meklēt lietotāju"
                prepend-inner-icon="mdi-magnify"
                density="compact"
                hide-details
                clearable
                style="min-width: 220px; max-width: 360px"
            />
            <v-spacer />
            <v-btn color="primary" prepend-icon="mdi-account-plus-outline" @click="openForm()">Pievienot lietotāju</v-btn>
        </div>

        <v-alert v-if="loadError" type="error" variant="tonal" density="compact" class="mb-4">{{ loadError }}</v-alert>

        <v-data-table
            :headers="headers"
            :items="lietotaji"
            :search="search"
            :loading="loading"
            item-value="lietotaja_id"
            density="comfortable"
            items-per-page="10"
            no-data-text="Lietotāju nav."
            loading-text="Ielādē..."
            items-per-page-text="Rindas lapā:"
        >
            <template #item.loma="{ item }">
                <v-chip :color="item.loma === 1 ? 'secondary' : undefined" variant="flat" size="small">
                    {{ item.loma === 1 ? 'Administrators' : 'Pircējs' }}
                </v-chip>
                <span v-if="item.lietotaja_id === myId" class="text-body-small text-medium-emphasis ml-1">(jūs)</span>
            </template>
            <template #item.actions="{ item }">
                <div class="d-flex justify-end ga-1">
                    <v-btn icon="mdi-pencil-outline" variant="text" size="small" aria-label="Rediģēt" @click="openForm(item)" />
                    <v-btn
                        icon="mdi-delete-outline"
                        variant="text"
                        size="small"
                        color="error"
                        aria-label="Dzēst"
                        :disabled="item.lietotaja_id === myId"
                        @click="askDelete(item)"
                    />
                </div>
            </template>
        </v-data-table>

        <!-- DIALOGS: lietotāja forma -->
        <v-dialog v-model="dialog" max-width="560" :fullscreen="$vuetify.display.xs">
            <v-card class="pa-6">
                <div class="text-title-large font-weight-bold mb-4">
                    {{ editId ? 'Rediģēt lietotāju' : 'Jauns lietotājs' }}
                </div>
                <form @submit.prevent="save">
                    <v-row dense>
                        <v-col cols="12" sm="6">
                            <v-text-field v-model="form.vards" label="Vārds" maxlength="30" :error-messages="form.errors.vards" />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field v-model="form.uzvards" label="Uzvārds" maxlength="30" :error-messages="form.errors.uzvards" />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.e_pasts"
                                label="E-pasts"
                                type="email"
                                maxlength="30"
                                :error-messages="form.errors.e_pasts"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="form.adrese" label="Adrese" maxlength="255" :error-messages="form.errors.adrese" />
                        </v-col>
                        <v-col cols="12">
                            <v-select
                                v-model="form.loma"
                                :items="lomas"
                                label="Loma"
                                :disabled="isSelf"
                                :hint="isSelf ? 'Savu lomu mainīt nevar.' : ''"
                                persistent-hint
                                :error-messages="form.errors.loma"
                            />
                        </v-col>
                        <v-col v-if="!editId" cols="12">
                            <v-text-field
                                v-model="form.parole"
                                label="Sākotnējā parole"
                                type="password"
                                autocomplete="new-password"
                                :error-messages="form.errors.parole"
                            />
                        </v-col>
                    </v-row>
                    <div class="d-flex justify-end ga-2 mt-2">
                        <v-btn variant="text" @click="dialog = false">Atcelt</v-btn>
                        <v-btn type="submit" color="primary" :loading="form.processing">Saglabāt</v-btn>
                    </div>
                </form>
            </v-card>
        </v-dialog>

        <ConfirmDialog
            v-model="deleteDialog"
            title="Dzēst lietotāju?"
            :text="toDelete ? `${toDelete.vards} ${toDelete.uzvards} (${toDelete.e_pasts}) tiks neatgriezeniski dzēsts.` : ''"
            :loading="deleting"
            :error="deleteError"
            @confirm="confirmDelete"
        />
    </div>
</template>
