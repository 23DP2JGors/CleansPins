<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import AdminProdukti from '@/Components/Admin/AdminProdukti.vue';
import AdminVardnica from '@/Components/Admin/AdminVardnica.vue';
import AdminPasutijumi from '@/Components/Admin/AdminPasutijumi.vue';
import AdminLietotaji from '@/Components/Admin/AdminLietotaji.vue';

const props = defineProps({
    pasutijumi: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// aktīvā cilne: 'profils' | 'pasutijumi' | 'admin'
const tab = ref('profils');

// "Administrēšana" redz tikai loma = 1 (pašus datus sargā 'admin' middleware)
const isAdmin = computed(() => user.value.loma === 1);

const navItems = computed(() => [
    { value: 'profils', title: 'Profils', icon: 'mdi-account-outline' },
    { value: 'pasutijumi', title: 'Pasūtījumi', icon: 'mdi-receipt-text-outline' },
    ...(isAdmin.value
        ? [{ value: 'admin', title: 'Administrēšana', icon: 'mdi-shield-crown-outline' }]
        : []),
]);

const logout = () => router.post(route('logout'));

// --- Profils ---

const profileRows = computed(() => [
    { label: 'Vārds', value: user.value.vards },
    { label: 'Uzvārds', value: user.value.uzvards },
    { label: 'E-pasts', value: user.value.e_pasts },
    { label: 'Adrese', value: user.value.adrese || '—' },
]);

const editDialog = ref(false);
const profileForm = useForm({ vards: '', uzvards: '', e_pasts: '', adrese: '' });

const openEdit = () => {
    // katru reizi aizpilda ar aktuālajiem datiem
    profileForm.defaults({
        vards: user.value.vards,
        uzvards: user.value.uzvards,
        e_pasts: user.value.e_pasts,
        adrese: user.value.adrese ?? '',
    });
    profileForm.reset();
    profileForm.clearErrors();
    editDialog.value = true;
};

const saveProfile = () =>
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => (editDialog.value = false),
    });

const passwordDialog = ref(false);
const passwordForm = useForm({ current_password: '', password: '', password_confirmation: '' });

const openPassword = () => {
    passwordForm.reset();
    passwordForm.clearErrors();
    passwordDialog.value = true;
};

const savePassword = () =>
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            passwordDialog.value = false; // paziņojumu "Parole nomainīta." parāda MainLayout snackbar
        },
        onError: (errors) => {
            if (errors.password) passwordForm.reset('password', 'password_confirmation');
            if (errors.current_password) passwordForm.reset('current_password');
        },
    });

// konta dzēšana (dzēš arī paša pasūtījumus); pēc veiksmes serveris izlogo un pāradresē uz /
const deleteAccountDialog = ref(false);
const deleteAccountForm = useForm({ password: '' });

const openDeleteAccount = () => {
    deleteAccountForm.reset();
    deleteAccountForm.clearErrors();
    deleteAccountDialog.value = true;
};

const deleteAccount = () =>
    deleteAccountForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onError: () => deleteAccountForm.reset('password'),
    });

// --- Pasūtījumi ---

const statusColors = {
    jauns: 'info',
    'apstrādē': 'warning',
    'nosūtīts': 'primary',
    'piegādāts': 'success',
    atcelts: 'error',
};
const statusColor = (s) => statusColors[s] ?? 'secondary';

const price = (value) => `${Number(value).toFixed(2)} €`;
const lineTotal = (r) => Number(r.cena_bridi) * r.daudzums;
const date = (d) =>
    new Date(d).toLocaleString('lv-LV', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const selectedOrder = ref(null);
const orderDialog = computed({
    get: () => selectedOrder.value !== null,
    set: (open) => {
        if (!open) selectedOrder.value = null;
    },
});

// "Aktuālie" — vēl ceļā; "Vēsture" — piegādāti vai atcelti
const closedStatuses = ['piegādāts', 'atcelts'];
const ordersTab = ref('aktualie');
const activeOrders = computed(() => props.pasutijumi.filter((o) => !closedStatuses.includes(o.statuss)));
const historyOrders = computed(() => props.pasutijumi.filter((o) => closedStatuses.includes(o.statuss)));
const shownOrders = computed(() => (ordersTab.value === 'aktualie' ? activeOrders.value : historyOrders.value));

// --- Pasūtījuma atcelšana (tikai "jauns" / "apstrādē"; serveris pārbauda vēlreiz) ---

const canCancel = (o) => ['jauns', 'apstrādē'].includes(o.statuss);

const toCancel = ref(null);
const cancelling = ref(false);
const cancelDialog = computed({
    get: () => toCancel.value !== null,
    set: (open) => {
        if (!open) toCancel.value = null;
    },
});

const cancelOrder = () =>
    router.post(
        route('pasutijums.atcelt', toCancel.value.pasutijuma_id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (cancelling.value = true),
            onFinish: () => {
                cancelling.value = false;
                toCancel.value = null;
                selectedOrder.value = null; // saraksts ielādēts no jauna — vecais objekts vairs nav aktuāls
            },
        },
    );

// --- Administrēšana ---

const adminTab = ref('produkti');
const adminTabs = [
    { value: 'produkti', title: 'Produkti', icon: 'mdi-package-variant-closed' },
    { value: 'vardnicas', title: 'Kategorijas / Zonas / Problēmas', icon: 'mdi-shape-outline' },
    { value: 'pasutijumi', title: 'Pasūtījumi', icon: 'mdi-receipt-text-outline' },
    { value: 'lietotaji', title: 'Lietotāji', icon: 'mdi-account-group-outline' },
];

const vardnica = ref('kategorijas');
const vardnicas = {
    kategorijas: { idKey: 'kategorijas_id', nosaukums: 'Kategorija', arAprakstu: true, maxlength: 255 },
    zonas: { idKey: 'zonas_id', nosaukums: 'Zona', arAprakstu: false, maxlength: 100 },
    problemas: { idKey: 'problemas_id', nosaukums: 'Problēma', arAprakstu: false, maxlength: 100 },
};
</script>

<template>
    <Head title="Mans konts" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-headline-large font-weight-bold text-white">
                    Mans <span class="text-primary">konts</span>
                </h1>
                <p class="text-body-large text-white mt-2" style="opacity: 0.7">
                    Sveiks, {{ user.vards }}!
                </p>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-10">
            <v-row>
                <!-- NAVIGĀCIJA: mobilajā — horizontālas cilnes -->
                <v-col cols="12" class="d-sm-none">
                    <v-card variant="flat" border>
                        <v-tabs v-model="tab" color="secondary" grow show-arrows>
                            <v-tab v-for="item in navItems" :key="item.value" :value="item.value" :prepend-icon="item.icon">
                                {{ item.title }}
                            </v-tab>
                        </v-tabs>
                    </v-card>
                </v-col>

                <!-- NAVIGĀCIJA: no 600px — kreisā kolonna -->
                <v-col cols="12" sm="4" md="3" class="d-none d-sm-block">
                    <v-card variant="flat" border>
                        <v-list nav aria-label="Konta navigācija">
                            <v-list-item
                                v-for="item in navItems"
                                :key="item.value"
                                :title="item.title"
                                :prepend-icon="item.icon"
                                :active="tab === item.value"
                                color="secondary"
                                @click="tab = item.value"
                            />
                            <v-divider class="my-2" />
                            <v-list-item title="Iziet" prepend-icon="mdi-logout" @click="logout" />
                        </v-list>
                    </v-card>
                </v-col>

                <!-- SATURS -->
                <v-col cols="12" sm="8" md="9">
                    <v-window v-model="tab">
                        <!-- PROFILS -->
                        <v-window-item value="profils">
                            <v-card variant="flat" border class="pa-6">
                                <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-4">
                                    <h2 class="text-title-large font-weight-bold">Lietotāja dati</h2>
                                    <div class="d-flex flex-wrap ga-2">
                                        <v-btn color="primary" prepend-icon="mdi-pencil-outline" @click="openEdit">
                                            Rediģēt datus
                                        </v-btn>
                                        <v-btn variant="outlined" color="secondary" prepend-icon="mdi-lock-outline" @click="openPassword">
                                            Mainīt paroli
                                        </v-btn>
                                        <v-btn
                                            variant="outlined"
                                            color="error"
                                            prepend-icon="mdi-account-remove-outline"
                                            @click="openDeleteAccount"
                                        >
                                            Dzēst kontu
                                        </v-btn>
                                    </div>
                                </div>

                                <v-list lines="two" class="pa-0">
                                    <template v-for="(row, i) in profileRows" :key="row.label">
                                        <v-divider v-if="i > 0" />
                                        <v-list-item class="px-0">
                                            <v-list-item-subtitle>{{ row.label }}</v-list-item-subtitle>
                                            <v-list-item-title class="text-body-large text-wrap">
                                                {{ row.value }}
                                            </v-list-item-title>
                                        </v-list-item>
                                    </template>
                                </v-list>
                            </v-card>
                        </v-window-item>

                        <!-- PASŪTĪJUMI -->
                        <v-window-item value="pasutijumi">
                            <v-card variant="flat" border class="pa-6">
                                <h2 class="text-title-large font-weight-bold mb-4">Jūsu pasūtījumi</h2>

                                <div v-if="pasutijumi.length === 0" class="text-center py-10">
                                    <v-icon icon="mdi-receipt-text-outline" size="56" color="secondary" class="mb-3" />
                                    <p class="text-body-large text-medium-emphasis mb-4">
                                        Jums vēl nav neviena pasūtījuma.
                                    </p>
                                    <v-btn color="primary" :href="route('katalogs')" prepend-icon="mdi-shopping-outline">
                                        Uz katalogu
                                    </v-btn>
                                </div>

                                <template v-else>
                                    <v-tabs v-model="ordersTab" color="secondary" density="compact" class="mb-2">
                                        <v-tab value="aktualie">Aktuālie ({{ activeOrders.length }})</v-tab>
                                        <v-tab value="vesture">Vēsture ({{ historyOrders.length }})</v-tab>
                                    </v-tabs>

                                    <p v-if="shownOrders.length === 0" class="text-body-large text-medium-emphasis text-center py-8">
                                        {{ ordersTab === 'aktualie' ? 'Aktuālu pasūtījumu nav.' : 'Vēsturē vēl nav neviena pasūtījuma.' }}
                                    </p>

                                    <v-list v-else lines="two" class="pa-0" aria-label="Pasūtījumu saraksts">
                                        <template v-for="(o, i) in shownOrders" :key="o.pasutijuma_id">
                                            <v-divider v-if="i > 0" />
                                            <v-list-item class="px-2" @click="selectedOrder = o">
                                                <v-list-item-title class="font-weight-bold">
                                                    Pasūtījums #{{ o.pasutijuma_id }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>{{ date(o.datums) }}</v-list-item-subtitle>

                                                <template #append>
                                                    <div class="d-flex align-center flex-wrap justify-end ga-3">
                                                        <v-chip :color="statusColor(o.statuss)" variant="flat" size="small">
                                                            {{ o.statuss }}
                                                        </v-chip>
                                                        <span class="text-body-large font-weight-medium">
                                                            {{ price(o.kopsumma) }}
                                                        </span>
                                                        <v-btn
                                                            v-if="canCancel(o)"
                                                            icon="mdi-close-circle-outline"
                                                            variant="text"
                                                            size="small"
                                                            color="error"
                                                            aria-label="Atcelt pasūtījumu"
                                                            @click.stop="toCancel = o"
                                                        />
                                                        <v-icon icon="mdi-chevron-right" class="d-none d-md-inline-flex" />
                                                    </div>
                                                </template>
                                            </v-list-item>
                                        </template>
                                    </v-list>
                                </template>
                            </v-card>
                        </v-window-item>

                        <!-- ADMINISTRĒŠANA (tikai loma = 1; katra apakšcilne ielādē datus, kad pirmo reizi atvērta) -->
                        <v-window-item v-if="isAdmin" value="admin">
                            <v-card variant="flat" border class="pa-6">
                                <h2 class="text-title-large font-weight-bold mb-4">Administrēšana</h2>

                                <v-tabs v-model="adminTab" color="secondary" density="compact" show-arrows class="mb-6">
                                    <v-tab v-for="t in adminTabs" :key="t.value" :value="t.value" :prepend-icon="t.icon">
                                        {{ t.title }}
                                    </v-tab>
                                </v-tabs>

                                <v-window v-model="adminTab">
                                    <v-window-item value="produkti">
                                        <AdminProdukti />
                                    </v-window-item>

                                    <v-window-item value="vardnicas">
                                        <v-btn-toggle
                                            v-model="vardnica"
                                            mandatory
                                            color="secondary"
                                            variant="outlined"
                                            density="comfortable"
                                            class="mb-4"
                                        >
                                            <v-btn value="kategorijas">Kategorijas</v-btn>
                                            <v-btn value="zonas">Zonas</v-btn>
                                            <v-btn value="problemas">Problēmas</v-btn>
                                        </v-btn-toggle>
                                        <AdminVardnica :key="vardnica" :resurss="vardnica" v-bind="vardnicas[vardnica]" />
                                    </v-window-item>

                                    <v-window-item value="pasutijumi">
                                        <AdminPasutijumi :status-color="statusColor" :price="price" :date="date" />
                                    </v-window-item>

                                    <v-window-item value="lietotaji">
                                        <AdminLietotaji />
                                    </v-window-item>
                                </v-window>
                            </v-card>
                        </v-window-item>
                    </v-window>

                    <!-- mobilajā "Iziet" zem satura -->
                    <v-btn
                        class="d-sm-none mt-4"
                        block
                        variant="outlined"
                        color="secondary"
                        prepend-icon="mdi-logout"
                        @click="logout"
                    >
                        Iziet
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <!-- DIALOGS: datu rediģēšana -->
        <v-dialog v-model="editDialog" max-width="560" :fullscreen="$vuetify.display.xs">
            <v-card class="pa-6">
                <div class="text-title-large font-weight-bold mb-4">Rediģēt datus</div>
                <form @submit.prevent="saveProfile">
                    <v-row dense>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="profileForm.vards"
                                label="Vārds"
                                maxlength="30"
                                autocomplete="given-name"
                                :error-messages="profileForm.errors.vards"
                            />
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="profileForm.uzvards"
                                label="Uzvārds"
                                maxlength="30"
                                autocomplete="family-name"
                                :error-messages="profileForm.errors.uzvards"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="profileForm.e_pasts"
                                label="E-pasts"
                                type="email"
                                maxlength="30"
                                autocomplete="email"
                                :error-messages="profileForm.errors.e_pasts"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="profileForm.adrese"
                                label="Adrese"
                                maxlength="255"
                                autocomplete="street-address"
                                :error-messages="profileForm.errors.adrese"
                            />
                        </v-col>
                    </v-row>
                    <div class="d-flex justify-end ga-2 mt-2">
                        <v-btn variant="text" @click="editDialog = false">Atcelt</v-btn>
                        <v-btn type="submit" color="primary" :loading="profileForm.processing">Saglabāt</v-btn>
                    </div>
                </form>
            </v-card>
        </v-dialog>

        <!-- DIALOGS: paroles maiņa -->
        <v-dialog v-model="passwordDialog" max-width="480" :fullscreen="$vuetify.display.xs">
            <v-card class="pa-6">
                <div class="text-title-large font-weight-bold mb-4">Mainīt paroli</div>
                <form @submit.prevent="savePassword">
                    <v-text-field
                        v-model="passwordForm.current_password"
                        label="Pašreizējā parole"
                        type="password"
                        autocomplete="current-password"
                        :error-messages="passwordForm.errors.current_password"
                    />
                    <v-text-field
                        v-model="passwordForm.password"
                        label="Jaunā parole"
                        type="password"
                        autocomplete="new-password"
                        :error-messages="passwordForm.errors.password"
                    />
                    <v-text-field
                        v-model="passwordForm.password_confirmation"
                        label="Atkārtot jauno paroli"
                        type="password"
                        autocomplete="new-password"
                        :error-messages="passwordForm.errors.password_confirmation"
                    />
                    <div class="d-flex justify-end ga-2 mt-2">
                        <v-btn variant="text" @click="passwordDialog = false">Atcelt</v-btn>
                        <v-btn type="submit" color="primary" :loading="passwordForm.processing">Mainīt paroli</v-btn>
                    </div>
                </form>
            </v-card>
        </v-dialog>

        <!-- DIALOGS: pasūtījuma detaļas -->
        <v-dialog v-model="orderDialog" max-width="720" :fullscreen="$vuetify.display.xs" scrollable>
            <v-card v-if="selectedOrder">
                <v-card-title class="d-flex align-center justify-space-between flex-wrap ga-2 pa-6 pb-2">
                    <span class="text-title-large font-weight-bold">Pasūtījums #{{ selectedOrder.pasutijuma_id }}</span>
                    <v-chip :color="statusColor(selectedOrder.statuss)" variant="flat" size="small">
                        {{ selectedOrder.statuss }}
                    </v-chip>
                </v-card-title>

                <v-card-text class="pa-6 pt-2">
                    <div class="text-body-medium text-medium-emphasis mb-4">{{ date(selectedOrder.datums) }}</div>

                    <div class="text-body-medium text-medium-emphasis">Piegādes adrese</div>
                    <div class="text-body-large mb-4">{{ selectedOrder.piegades_adrese }}</div>

                    <v-table density="comfortable">
                        <thead>
                            <tr>
                                <th>Produkts</th>
                                <th class="text-right">Daudzums</th>
                                <th class="text-right">Cena</th>
                                <th class="text-right">Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="r in selectedOrder.rindas" :key="r.rindas_id ?? `${r.pasutijuma_id}-${r.produkta_id}`">
                                <td>{{ r.produkts?.nosaukums ?? 'Produkts vairs nav pieejams' }}</td>
                                <td class="text-right">{{ r.daudzums }}</td>
                                <td class="text-right text-no-wrap">{{ price(r.cena_bridi) }}</td>
                                <td class="text-right text-no-wrap">{{ price(lineTotal(r)) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Kopā</td>
                                <td class="text-right font-weight-bold text-no-wrap">{{ price(selectedOrder.kopsumma) }}</td>
                            </tr>
                        </tfoot>
                    </v-table>
                </v-card-text>

                <v-card-actions class="pa-6 pt-0">
                    <v-btn
                        v-if="canCancel(selectedOrder)"
                        color="error"
                        variant="outlined"
                        prepend-icon="mdi-close-circle-outline"
                        @click="toCancel = selectedOrder"
                    >
                        Atcelt pasūtījumu
                    </v-btn>
                    <v-spacer />
                    <v-btn variant="text" @click="orderDialog = false">Aizvērt</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- DIALOGS: pasūtījuma atcelšanas apstiprinājums -->
        <ConfirmDialog
            v-model="cancelDialog"
            :title="`Atcelt pasūtījumu #${toCancel?.pasutijuma_id ?? ''}?`"
            text="Pasūtījums tiks atcelts, un preces atgriezīsies noliktavā. Šo darbību nevar atsaukt."
            confirm-text="Atcelt pasūtījumu"
            :loading="cancelling"
            @confirm="cancelOrder"
        />

        <!-- DIALOGS: konta dzēšanas apstiprinājums -->
        <ConfirmDialog
            v-model="deleteAccountDialog"
            title="Dzēst kontu?"
            text="Konts un visa tava pasūtījumu vēsture tiks neatgriezeniski dzēsta. Lai apstiprinātu, ievadi savu paroli."
            confirm-text="Dzēst kontu"
            :loading="deleteAccountForm.processing"
            @confirm="deleteAccount"
        >
            <v-text-field
                v-model="deleteAccountForm.password"
                label="Parole"
                type="password"
                autocomplete="current-password"
                class="mt-4"
                :error-messages="deleteAccountForm.errors.password"
                @keyup.enter="deleteAccount"
            />
        </ConfirmDialog>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
</style>
