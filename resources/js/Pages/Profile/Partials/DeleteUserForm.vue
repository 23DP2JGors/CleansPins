<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingDeletion = ref(false);
const deletionTarget = ref("user");
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmDeletion = (target) => {
    deletionTarget.value = target;
    confirmingDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const submitDeletion = () => {
    const routeName =
        deletionTarget.value === "orders"
            ? "profile.orders.destroy"
            : "profile.destroy";

    form.delete(route(routeName), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
    deletionTarget.value = "user";

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <div class="flex flex-wrap gap-3">
            <DangerButton @click="confirmDeletion('orders')">
                Delete My Orders
            </DangerButton>

            <DangerButton @click="confirmDeletion('user')">
                Delete Account
            </DangerButton>
        </div>

        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{
                        deletionTarget === "orders"
                            ? "Are you sure you want to delete your orders?"
                            : "Are you sure you want to delete your account?"
                    }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{
                        deletionTarget === "orders"
                            ? "This will permanently remove all of your orders and order details. Please enter your password to confirm."
                            : "Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account."
                    }}
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="submitDeletion"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="submitDeletion"
                    >
                        {{
                            deletionTarget === "orders"
                                ? "Delete My Orders"
                                : "Delete Account"
                        }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
