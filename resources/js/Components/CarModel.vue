<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    zones: { type: Array, default: () => [] },
});

const hovered = ref(null);

const labels = [
    { name: 'Virsbūve', icon: 'mdi-car-side' },
    { name: 'Stikli', icon: 'mdi-car-windshield-outline' },
    { name: 'Salons', icon: 'mdi-car-seat' },
    { name: 'Diski', icon: 'mdi-car-turbocharger' },
    { name: 'Riepas', icon: 'mdi-tire' },
];

const wheels = [150, 450];
const spokes = [0, 72, 144, 216, 288];

const zoneId = (name) => props.zones.find((z) => z.nosaukums === name)?.zonas_id ?? null;
const isEnabled = (name) => zoneId(name) !== null;

const go = (name) => {
    const id = zoneId(name);
    if (!id) return;
    router.get(route('katalogs'), { zona: id });
};

const enter = (name) => {
    if (isEnabled(name)) hovered.value = name;
};
const leave = (name) => {
    if (hovered.value === name) hovered.value = null;
};

const cls = (name) => ({
    zone: true,
    active: hovered.value === name,
    disabled: !isEnabled(name),
});
</script>

<template>
    <div>
        <svg
            viewBox="0 0 600 270"
            class="car"
            role="group"
            aria-label="Interaktīvs auto modelis"
        >
            <!-- Shadow -->
            <ellipse cx="300" cy="240" rx="250" ry="8" fill="rgba(18,35,35,0.12)" />

            <!-- RIEPAS -->
            <g
                :class="cls('Riepas')"
                :tabindex="isEnabled('Riepas') ? 0 : -1"
                role="link"
                aria-label="Riepas"
                @mouseenter="enter('Riepas')"
                @mouseleave="leave('Riepas')"
                @focus="enter('Riepas')"
                @blur="leave('Riepas')"
                @click="go('Riepas')"
                @keydown.enter="go('Riepas')"
            >
                <circle
                    v-for="w in wheels"
                    :key="w"
                    :cx="w"
                    cy="195"
                    r="42"
                    class="tire-shape"
                />
            </g>

            <!-- VIRSBŪVE -->
            <g
                :class="cls('Virsbūve')"
                :tabindex="isEnabled('Virsbūve') ? 0 : -1"
                role="link"
                aria-label="Virsbūve"
                @mouseenter="enter('Virsbūve')"
                @mouseleave="leave('Virsbūve')"
                @focus="enter('Virsbūve')"
                @blur="leave('Virsbūve')"
                @click="go('Virsbūve')"
                @keydown.enter="go('Virsbūve')"
            >
                <path
                    class="body-shape"
                    d="M50 185 L50 150 Q50 135 70 132 L130 125 L175 88 Q185 80 200 80 L370 80 Q385 80 395 88 L440 122 L520 130 Q555 135 555 160 L555 185 Q555 195 545 195 L505 195 A55 55 0 0 0 395 195 L205 195 A55 55 0 0 0 95 195 L60 195 Q50 195 50 185 Z"
                />
                <g class="deco">
                    <line x1="290" y1="122" x2="290" y2="192" />
                    <rect x="255" y="134" width="24" height="5" rx="2.5" class="handle" />
                    <rect x="300" y="134" width="24" height="5" rx="2.5" class="handle" />
                    <rect x="538" y="145" width="14" height="10" rx="3" fill="#fff7c2" stroke="none" />
                    <rect x="52" y="145" width="10" height="10" rx="3" fill="#ff6b6b" stroke="none" />
                </g>
            </g>

            <!-- STIKLI -->
            <g
                :class="cls('Stikli')"
                :tabindex="isEnabled('Stikli') ? 0 : -1"
                role="link"
                aria-label="Stikli"
                @mouseenter="enter('Stikli')"
                @mouseleave="leave('Stikli')"
                @focus="enter('Stikli')"
                @blur="leave('Stikli')"
                @click="go('Stikli')"
                @keydown.enter="go('Stikli')"
            >
                <path
                    class="glass-shape"
                    d="M150 122 L184 95 Q188 91 196 91 L282 91 L282 122 Z"
                />
                <path
                    class="glass-shape"
                    d="M298 91 L368 91 Q376 91 382 97 L412 122 L298 122 Z"
                />
            </g>

            <!-- SALONS (seats seen through the windows) -->
            <g
                :class="cls('Salons')"
                :tabindex="isEnabled('Salons') ? 0 : -1"
                role="link"
                aria-label="Salons"
                @mouseenter="enter('Salons')"
                @mouseleave="leave('Salons')"
                @focus="enter('Salons')"
                @blur="leave('Salons')"
                @click="go('Salons')"
                @keydown.enter="go('Salons')"
            >
                <path
                    class="seat-shape"
                    d="M208 122 L212 104 Q214 97 223 97 L232 97 Q241 97 243 104 L248 122 Z"
                />
                <path
                    class="seat-shape"
                    d="M318 122 L322 104 Q324 97 333 97 L342 97 Q351 97 353 104 L358 122 Z"
                />
            </g>

            <!-- DISKI -->
            <g
                :class="cls('Diski')"
                :tabindex="isEnabled('Diski') ? 0 : -1"
                role="link"
                aria-label="Diski"
                @mouseenter="enter('Diski')"
                @mouseleave="leave('Diski')"
                @focus="enter('Diski')"
                @blur="leave('Diski')"
                @click="go('Diski')"
                @keydown.enter="go('Diski')"
            >
                <g v-for="w in wheels" :key="w" :transform="`translate(${w} 195)`">
                    <circle r="26" class="rim-shape" />
                    <line
                        v-for="a in spokes"
                        :key="a"
                        x1="0"
                        y1="0"
                        x2="0"
                        y2="-22"
                        class="spoke"
                        :transform="`rotate(${a})`"
                    />
                    <circle r="6" class="hub" />
                </g>
            </g>
        </svg>

        <div
            class="text-title-medium font-weight-bold text-center mt-2"
            style="min-height: 28px"
        >
            {{ hovered ?? 'Uzspied uz auto daļas' }}
        </div>

        <!-- same zones as chips: bigger click targets, works on touch screens -->
        <div class="d-flex flex-wrap justify-center ga-2 mt-3">
            <v-chip
                v-for="l in labels"
                :key="l.name"
                :prepend-icon="l.icon"
                :variant="hovered === l.name ? 'flat' : 'outlined'"
                :color="hovered === l.name ? 'primary' : undefined"
                :disabled="!isEnabled(l.name)"
                @mouseenter="enter(l.name)"
                @mouseleave="leave(l.name)"
                @click="go(l.name)"
            >
                {{ l.name }}
            </v-chip>
        </div>
    </div>
</template>

<style scoped>
.car {
    width: 100%;
    height: auto;
    display: block;
}

.zone {
    cursor: pointer;
    outline: none;
}
.zone.disabled {
    cursor: not-allowed;
    opacity: 0.45;
}

.body-shape {
    fill: #1b3a3a;
    stroke: #122323;
    stroke-width: 2;
    transition: fill 0.2s ease;
}
.glass-shape {
    fill: #cfe3ea;
    stroke: #122323;
    stroke-width: 2;
    transition: fill 0.2s ease;
}
.seat-shape {
    fill: #122323;
    stroke: #122323;
    stroke-width: 1;
    transition: fill 0.2s ease;
}
.tire-shape {
    fill: #122323;
    stroke: #0a1414;
    stroke-width: 2;
    transition: fill 0.2s ease;
}
.rim-shape {
    fill: #9fb8b0;
    stroke: #122323;
    stroke-width: 2;
    transition: fill 0.2s ease;
}
.spoke {
    stroke: #122323;
    stroke-width: 4;
    stroke-linecap: round;
    pointer-events: none;
}
.hub {
    fill: #122323;
    pointer-events: none;
}
.deco {
    pointer-events: none;
}
.deco line {
    stroke: #122323;
    stroke-width: 2;
}
.handle {
    fill: #9fb8b0;
    stroke: none;
}

/* hover / focus highlight */
.zone.active .body-shape,
.zone.active .glass-shape,
.zone.active .seat-shape,
.zone.active .tire-shape,
.zone.active .rim-shape {
    fill: #b4f8c8;
}

@media (prefers-reduced-motion: reduce) {
    .body-shape,
    .glass-shape,
    .seat-shape,
    .tire-shape,
    .rim-shape {
        transition: none;
    }
}
</style>