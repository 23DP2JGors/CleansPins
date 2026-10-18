<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    // izvēlētā pakomāta ID (v-model)
    modelValue: { type: Number, default: null },
    // kļūdas teksts no servera validācijas (form.errors.pakomata_id)
    error: { type: String, default: '' },
});

// update:modelValue — tikai ID (formai), select — viss pakomāta objekts (attēlošanai)
const emit = defineEmits(['update:modelValue', 'select']);

const carriers = [
    { value: 'omniva', title: 'Omniva' },
    { value: 'dpd', title: 'DPD' },
    { value: 'latvijas_pasts', title: 'Latvijas Pasts' },
];
const carrierTitle = (value) => carriers.find((c) => c.value === value)?.title ?? value;

const carrier = ref('omniva');
const search = ref('');
const results = ref([]);
const loading = ref(false);
const failed = ref(false);
const selected = ref(null);
const editing = ref(props.modelValue === null);

let debounceTimer = null;
let controller = null;

const load = async () => {
    clearTimeout(debounceTimer);
    controller?.abort(); // atceļ iepriekšējo pieprasījumu, lai veca atbilde nepārrakstītu jaunāko
    controller = new AbortController();

    loading.value = true;
    failed.value = false;

    try {
        const params = { pakalpojums: carrier.value };
        if (search.value?.trim()) params.q = search.value.trim();

        const { data } = await window.axios.get(route('pakomati.index'), {
            params,
            signal: controller.signal,
        });
        results.value = data;
        loading.value = false;
    } catch (e) {
        if (window.axios.isCancel(e)) return; // jaunāks pieprasījums jau ceļā
        results.value = [];
        failed.value = true;
        loading.value = false;
    }
};

// pārvadātāja maiņa — uzreiz, meklēšanas teksts — ar 350 ms aizturi
watch(carrier, load);
watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(load, 350);
});

// ja vecāks notīra vērtību (piem. formas reset), atveram sarakstu
watch(
    () => props.modelValue,
    (id) => {
        if (id === null) {
            selected.value = null;
            editing.value = true;
        }
    },
);

onMounted(load);
onBeforeUnmount(() => {
    clearTimeout(debounceTimer);
    controller?.abort();
});

const choose = (pakomats) => {
    selected.value = pakomats;
    editing.value = false;
    emit('update:modelValue', pakomats.pakomata_id);
    emit('select', pakomats);
};

const change = () => {
    editing.value = true;
};

const cancelChange = () => {
    editing.value = false;
};
</script>

<template>
    <div>
        <!-- izvēlētais pakomāts -->
        <v-card v-if="!editing && selected" variant="flat" border class="pa-4">
            <div class="d-flex align-start ga-3 flex-wrap">
                <v-icon icon="mdi-package-variant-closed" color="secondary" class="mt-1" />
                <div class="flex-grow-1" style="min-width: 0">
                    <div class="text-caption text-medium-emphasis">
                        {{ carrierTitle(selected.pakalpojums) }} pakomāts
                    </div>
                    <div class="text-body-large font-weight-bold">{{ selected.nosaukums }}</div>
                    <div class="text-body-medium text-medium-emphasis">
                        {{ selected.adrese }}, {{ selected.pilseta }}
                    </div>
                    <div v-if="selected.darba_laiks" class="text-caption text-medium-emphasis mt-1">
                        {{ selected.darba_laiks }}
                    </div>
                </div>
                <v-btn variant="text" color="secondary" prepend-icon="mdi-pencil-outline" @click="change">
                    Mainīt
                </v-btn>
            </div>
        </v-card>

        <!-- meklēšana un saraksts -->
        <template v-else>
            <div class="text-body-medium text-medium-emphasis mb-2" id="pakomats-carrier-label">
                Pārvadātājs
            </div>
            <v-chip-group
                v-model="carrier"
                mandatory
                column
                selected-class="text-secondary"
                class="mb-3"
                aria-labelledby="pakomats-carrier-label"
            >
                <v-chip
                    v-for="c in carriers"
                    :key="c.value"
                    :value="c.value"
                    variant="outlined"
                    filter
                >
                    {{ c.title }}
                </v-chip>
            </v-chip-group>

            <v-text-field
                v-model="search"
                label="Pilsēta vai pakomāta nosaukums"
                prepend-inner-icon="mdi-magnify"
                clearable
                hide-details
                :loading="loading"
                class="mb-3"
                @click:clear="search = ''"
            />

            <v-alert
                v-if="failed"
                type="error"
                variant="tonal"
                density="compact"
                class="mb-3"
            >
                Neizdevās ielādēt pakomātus.
                <template #append>
                    <v-btn variant="text" size="small" @click="load">Mēģināt vēlreiz</v-btn>
                </template>
            </v-alert>

            <v-sheet border rounded="lg" class="picker-list">
                <v-skeleton-loader
                    v-if="loading && results.length === 0"
                    type="list-item-two-line@4"
                />

                <div
                    v-else-if="!failed && results.length === 0"
                    class="pa-6 text-center text-body-medium text-medium-emphasis"
                >
                    Nav atrasts neviens pakomāts šai pilsētai/nosaukumam.
                </div>

                <v-list
                    v-else-if="results.length"
                    lines="three"
                    density="compact"
                    :class="{ 'is-loading': loading }"
                    aria-label="Pakomātu saraksts"
                >
                    <v-list-item
                        v-for="p in results"
                        :key="p.pakomata_id"
                        :title="p.nosaukums"
                        :subtitle="`${p.adrese}, ${p.pilseta}`"
                        :active="p.pakomata_id === modelValue"
                        color="secondary"
                        prepend-icon="mdi-package-variant-closed"
                        @click="choose(p)"
                    >
                        <div v-if="p.darba_laiks" class="text-caption text-medium-emphasis">
                            {{ p.darba_laiks }}
                        </div>
                    </v-list-item>
                </v-list>
            </v-sheet>

            <div v-if="selected" class="text-right mt-2">
                <v-btn variant="text" size="small" @click="cancelChange">Atcelt</v-btn>
            </div>
        </template>

        <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            density="compact"
            class="mt-3"
        >
            {{ error }}
        </v-alert>
    </div>
</template>

<style scoped>
.picker-list {
    max-height: 320px;
    overflow-y: auto;
}

.is-loading {
    opacity: 0.6;
    transition: opacity 0.15s;
}
</style>
