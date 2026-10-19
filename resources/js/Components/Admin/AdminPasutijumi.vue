<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

// formatēšanas palīgi nāk no Dashboard, lai statusu krāsas būtu vienā vietā
const props = defineProps({
    statusColor: { type: Function, required: true },
    price: { type: Function, required: true },
    date: { type: Function, required: true },
});

const items = ref([]);
const total = ref(0);
const statusi = ref([]);
const page = ref(1);
const filter = ref(null);
const loading = ref(false);
const message = ref('');

// serveris lapo pa 20 (paginate(20))
const load = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('admin.pasutijumi.index'), {
            params: { page: page.value, statuss: filter.value || undefined },
        });
        items.value = data.pasutijumi.data;
        total.value = data.pasutijumi.total;
        statusi.value = data.statusi;
    } catch {
        message.value = 'Neizdevās ielādēt pasūtījumus.';
    } finally {
        loading.value = false;
    }
};

// v-data-table-server izsauc šo arī pirmajā renderēšanā
const onOptions = (options) => {
    page.value = options.page;
    load();
};

watch(filter, () => {
    if (page.value !== 1) page.value = 1; // lapas maiņa pati izsauks onOptions
    else load();
});

const headers = [
    { title: '#', key: 'pasutijuma_id', sortable: false },
    { title: 'Datums', key: 'datums', sortable: false },
    { title: 'Pircējs', key: 'lietotajs', sortable: false },
    { title: 'Summa', key: 'kopsumma', align: 'end', sortable: false },
    { title: 'Statuss', key: 'statuss', sortable: false, width: 190 },
    { title: '', key: 'data-table-expand', align: 'end' },
];

const lineTotal = (r) => Number(r.cena_bridi) * r.daudzums;

// --- Statusa maiņa ---

const sending = ref(null); // pasutijuma_id, kamēr sūta
const toCancel = ref(null);
const cancelDialog = computed({
    get: () => toCancel.value !== null,
    set: (open) => {
        if (!open) toCancel.value = null;
    },
});

const sendStatus = (order, statuss) =>
    router.patch(
        route('admin.pasutijumi.statuss', order.pasutijuma_id),
        { statuss },
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (sending.value = order.pasutijuma_id),
            onFinish: () => {
                sending.value = null;
                toCancel.value = null;
                load();
            },
            onError: (errors) => (message.value = Object.values(errors)[0]),
        },
    );

const changeStatus = (order, statuss) => {
    if (statuss === order.statuss) return;
    message.value = '';
    // atcelšana atgriež preces krājumā un ir neatgriezeniska — prasa apstiprinājumu
    if (statuss === 'atcelts') toCancel.value = order;
    else sendStatus(order, statuss);
};
</script>

<template>
    <div>
        <div class="d-flex align-center flex-wrap ga-3 mb-4">
            <v-select
                v-model="filter"
                :items="statusi"
                label="Filtrēt pēc statusa"
                density="compact"
                hide-details
                clearable
                style="min-width: 220px; max-width: 280px"
            />
            <v-spacer />
            <span class="text-body-medium text-medium-emphasis">Kopā: {{ total }}</span>
        </div>

        <v-alert
            v-if="message"
            type="error"
            variant="tonal"
            density="compact"
            closable
            class="mb-4"
            @click:close="message = ''"
        >
            {{ message }}
        </v-alert>

        <v-data-table-server
            v-model:page="page"
            :headers="headers"
            :items="items"
            :items-length="total"
            :items-per-page="20"
            :items-per-page-options="[{ value: 20, title: '20' }]"
            :loading="loading"
            item-value="pasutijuma_id"
            show-expand
            density="comfortable"
            no-data-text="Pasūtījumu nav."
            loading-text="Ielādē..."
            items-per-page-text="Rindas lapā:"
            @update:options="onOptions"
        >
            <template #item.pasutijuma_id="{ item }">
                <span class="font-weight-bold">#{{ item.pasutijuma_id }}</span>
            </template>
            <template #item.datums="{ item }">
                <span class="text-no-wrap">{{ props.date(item.datums) }}</span>
            </template>
            <template #item.lietotajs="{ item }">
                {{ item.lietotajs ? `${item.lietotajs.vards} ${item.lietotajs.uzvards}` : '—' }}
            </template>
            <template #item.kopsumma="{ item }">
                <span class="text-no-wrap">{{ props.price(item.kopsumma) }}</span>
            </template>
            <template #item.statuss="{ item }">
                <v-select
                    :model-value="item.statuss"
                    :items="statusi"
                    :disabled="item.statuss === 'atcelts'"
                    :loading="sending === item.pasutijuma_id"
                    density="compact"
                    hide-details
                    aria-label="Pasūtījuma statuss"
                    @update:model-value="(s) => changeStatus(item, s)"
                >
                    <template #selection="{ item: s }">
                        <v-chip :color="props.statusColor(s.value)" variant="flat" size="small">{{ s.title }}</v-chip>
                    </template>
                </v-select>
            </template>

            <template #expanded-row="{ columns, item }">
                <tr>
                    <td :colspan="columns.length" class="py-4">
                        <div class="text-body-medium text-medium-emphasis">Kontaktpersona</div>
                        <div class="text-body-large mb-2">
                            {{ [item.vards, item.uzvards].filter(Boolean).join(' ') || '—' }}
                            <span v-if="item.talrunis" class="text-medium-emphasis">· {{ item.talrunis }}</span>
                        </div>
                        <div class="text-body-medium text-medium-emphasis">Piegādes adrese</div>
                        <div class="text-body-large mb-3">{{ item.piegades_adrese }}</div>

                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th>Produkts</th>
                                    <th class="text-right">Daudzums</th>
                                    <th class="text-right">Cena</th>
                                    <th class="text-right">Summa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="r in item.rindas" :key="r.rindas_id">
                                    <td>{{ r.produkts?.nosaukums ?? 'Produkts vairs nav pieejams' }}</td>
                                    <td class="text-right">{{ r.daudzums }}</td>
                                    <td class="text-right text-no-wrap">{{ props.price(r.cena_bridi) }}</td>
                                    <td class="text-right text-no-wrap">{{ props.price(lineTotal(r)) }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </td>
                </tr>
            </template>
        </v-data-table-server>

        <ConfirmDialog
            v-model="cancelDialog"
            :title="`Atcelt pasūtījumu #${toCancel?.pasutijuma_id ?? ''}?`"
            text="Preces tiks atgrieztas krājumā. Atceltu pasūtījumu vairs nevarēs atjaunot."
            confirm-text="Atcelt pasūtījumu"
            :loading="sending !== null"
            @confirm="sendStatus(toCancel, 'atcelts')"
        />
    </div>
</template>
