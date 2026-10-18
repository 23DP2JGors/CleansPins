<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

// lauku nosaukumi atbilst tabulai "lietotaji"
const form = useForm({
    vards: user.vards,
    uzvards: user.uzvards,
    e_pasts: user.e_pasts,
    adrese: user.adrese ?? '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Lietotāja dati
            </h2>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="vards" value="Vārds" />
                <TextInput id="vards" type="text" class="mt-1 block w-full" v-model="form.vards" required autocomplete="given-name" />
                <InputError class="mt-2" :message="form.errors.vards" />
            </div>

            <div>
                <InputLabel for="uzvards" value="Uzvārds" />
                <TextInput id="uzvards" type="text" class="mt-1 block w-full" v-model="form.uzvards" required autocomplete="family-name" />
                <InputError class="mt-2" :message="form.errors.uzvards" />
            </div>

            <div>
                <InputLabel for="e_pasts" value="E-pasts" />
                <TextInput id="e_pasts" type="email" class="mt-1 block w-full" v-model="form.e_pasts" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.e_pasts" />
            </div>

            <div>
                <InputLabel for="adrese" value="Adrese" />
                <TextInput id="adrese" type="text" class="mt-1 block w-full" v-model="form.adrese" autocomplete="street-address" />
                <InputError class="mt-2" :message="form.errors.adrese" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Saglabāt</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                        Saglabāts.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
