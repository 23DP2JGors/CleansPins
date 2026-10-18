<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    zones: { type: Array, default: () => [] },
});

// katalogs filtrē pēc zonas_id, tāpēc ID meklējam pēc nosaukuma (kā CarModel.vue)
const catalogLink = (zoneName) => {
    const id = props.zones.find((z) => z.nosaukums === zoneName)?.zonas_id;
    return id ? route('katalogs', { zona: id }) : route('katalogs');
};

const sections = [
    {
        zone: 'Virsbūve',
        title: 'Virsbūves kopšana',
        subtitle: 'Mazgāšana, žāvēšana un aizsardzība, lai krāsojums ilgāk paliktu spožs un bez skrāpējumiem.',
        icon: 'mdi-car-side',
        tips: [
            {
                title: 'Sāc ar diskiem un riepām',
                text: 'Diski ir visnetīrākā auto daļa — bremžu putekļi un ceļa netīrumi. Mazgā tos pirmos ar atsevišķu suku un spaini, lai, mazgājot virsbūvi, netīrumi neaizšļakstītu jau notīrītās vietas un tie nenonāktu uz krāsojuma tīrīšanas sūkļa.',
            },
            {
                title: 'Mazgā ar divu spaiņu metodi',
                text: 'Vienā spainī ir ūdens ar šampūnu, otrā — tīrs skalošanas ūdens. Pēc katras apstrādātās detaļas izskalo cimdu tīrajā spainī un tikai tad iemērc šampūnā. Tā smiltis un netīrumi paliek skalošanas spainī, nevis tiek berzti atpakaļ pa lakas virsmu, radot sīkus apļveida skrāpējumus.',
            },
            {
                title: 'Nemazgā tiešā saulē',
                text: 'Uz sakarsušas virsbūves šampūns un ūdens nožūst pārāk ātri un atstāj kaļķa traipus un svītras. Mazgā ēnā, agri no rīta vai vakarā, kad virsma ir vēsa pieskārienam, un strādā pa vienai detaļai, uzreiz to noskalojot.',
            },
            {
                title: 'Mazgā no augšas uz leju',
                text: 'Sāc ar jumtu un logiem, tad pārsegu un durvis, bet sliekšņus un bamperu apakšu atstāj beigām — tur ir visvairāk smilšu. Tā netīrākās vietas netiek mazgātas ar to pašu cimda pusi, ar kuru pēc tam pieskaries tīrākajām.',
            },
            {
                title: 'Pirms vaska — māla stienis',
                text: 'Pēc mazgāšanas pārvelc ar pirkstiem pa lakas virsmu: ja tā šķiet raupja, tajā ir iesēdušās daļiņas (rūsas putekļi, sveķi, bitumens). Apstrādā virsmu ar māla stieni un slīdošo šķidrumu, līdz tā kļūst gluda — tikai tad vasks vai hermētiķis pielīp vienmērīgi un turas ilgāk.',
            },
            {
                title: 'Žāvē tikai ar mikrošķiedru',
                text: 'Izmanto lielu, biezu mikrošķiedras žāvēšanas dvieli un uzklāj to uz virsmas, viegli velkot vai nospiežot, nevis berzējot apļos. Zamšādas lupatas un parastie dvieļi var saskrāpēt laku. Dvieļus mazgā atsevišķi, bez veļas mīkstinātāja, citādi tie zaudē uzsūkšanas spēju.',
            },
        ],
    },
    {
        zone: 'Salons',
        title: 'Salona kopšana',
        subtitle: 'Pareiza secība un piemēroti līdzekļi, lai salons būtu tīrs, bez traipiem un nepatīkamām smakām.',
        icon: 'mdi-car-seat',
        tips: [
            {
                title: 'Vispirms putekļsūcējs',
                text: 'Pirms jebkura šķidra tīrīšanas līdzekļa rūpīgi izsūc sēdekļus, paklājus un spraugas. Ja smiltis un drupačas sajaucas ar tīrītāju, tās pārvēršas dubļos, kas iesūcas audumā un berž virsmas. Spraugām izmanto šauru uzgali un mīkstu otiņu, lai izkustinātu netīrumus.',
            },
            {
                title: 'Pārbaudi tīrītāju neredzamā vietā',
                text: 'Ādas un auduma tīrītāju vispirms pamēģini nelielā, neuzkrītošā vietā, piemēram, sēdekļa sānos pie sliedēm. Pagaidi, līdz nožūst, un pārbaudi, vai nav mainījusies krāsa vai virsmas faktūra. Tīrītāju uzklāj uz mikrošķiedras vai otiņas, nevis tieši uz sēdekļa, lai to nepārmitrinātu.',
            },
            {
                title: 'Aizsargā paneli no UV stariem',
                text: 'Saule pakāpeniski izbalina un sausina plastmasas un ādas virsmas, līdz parādās plaisas. Pēc tīrīšanas apstrādā paneli ar līdzekli ar UV aizsardzību, vēlams matētu, jo spīdīgs pārklājums rada atspīdumu vējstiklā. Vasarā stāvvietā lieto saulessargu.',
            },
            {
                title: 'Tīri stiklus bez svītrām',
                text: 'Iekšējo stiklu tīri ar divām mikrošķiedras drānām: ar vienu, nedaudz samitrinātu ar stiklu tīrītāju, notīri, ar otru, sausu, uzreiz nopulē. Tīrītāju smidzini uz drānas, nevis uz stikla, lai tas nenokļūtu uz paneļa. Vienu pusi slauki horizontāli, otru vertikāli — tā uzreiz redzams, kurā pusē palikušas svītras.',
            },
            {
                title: 'Likvidē smakas avotu, nevis maskē',
                text: 'Gaisa atsvaidzinātājs tikai uz laiku pārklāj smaku. Atrodi avotu: izlijis dzēriens paklājā, pārtikas paliekas zem sēdekļiem vai mitrs paklājs. Notīri to ar fermentatīvu tīrītāju, ļauj pilnībā izžūt un nomaini salona gaisa filtru — novecojis filtrs pats var būt pelējuma smakas cēlonis.',
            },
            {
                title: 'Kop ādu regulāri',
                text: 'Ādas sēdekļus notīri ar pH neitrālu ādas tīrītāju ik pēc 1–2 mēnešiem un pēc tam apstrādā ar kondicionieri, lai āda nesaplaisātu. Neizmanto universālos tīrītājus un spirtu saturošas salvetes — tās izžāvē ādu un noņem tās aizsargslāni.',
            },
        ],
    },
];
</script>

<template>
    <Head title="Padomi" />
    <MainLayout>
        <section class="page-header">
            <v-container style="max-width: 1280px" class="py-12">
                <h1 class="text-headline-large text-md-display-medium font-weight-bold text-white">
                    Padomi tavam <span class="text-primary">auto</span>
                </h1>
                <p class="text-white mt-3" style="opacity: 0.8; max-width: 520px">
                    Praktiski ieteikumi virsbūves un salona kopšanai, ko vari izmantot jau nākamajā mazgāšanas reizē.
                </p>
            </v-container>
        </section>

        <v-container style="max-width: 1280px" class="py-12">
            <v-row>
                <v-col v-for="section in sections" :key="section.zone" cols="12" md="6">
                    <v-card variant="flat" border class="pa-6 h-100 d-flex flex-column">
                        <div class="d-flex align-center ga-4 mb-2">
                            <v-avatar color="primary" size="52">
                                <v-icon :icon="section.icon" color="on-primary" size="28" />
                            </v-avatar>
                            <h2 class="text-title-large font-weight-bold">{{ section.title }}</h2>
                        </div>
                        <p class="text-body-medium text-medium-emphasis mb-6">{{ section.subtitle }}</p>

                        <v-expansion-panels variant="accordion" class="mb-6">
                            <v-expansion-panel v-for="tip in section.tips" :key="tip.title" elevation="0">
                                <v-expansion-panel-title class="text-title-small font-weight-bold">
                                    {{ tip.title }}
                                </v-expansion-panel-title>
                                <v-expansion-panel-text class="text-body-medium text-medium-emphasis">
                                    {{ tip.text }}
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>

                        <div class="mt-auto">
                            <v-btn color="primary" size="large" :href="catalogLink(section.zone)" append-icon="mdi-arrow-right">
                                Skatīt produktus
                            </v-btn>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </MainLayout>
</template>

<style scoped>
.page-header {
    background: linear-gradient(135deg, #122323 0%, #1b3a3a 100%);
}
</style>
