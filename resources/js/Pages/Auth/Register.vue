<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
  companies: Array,
});

const step = ref(1);

const rawPhone = ref("");

const form = useForm({
  name: "",
  email: "",
  phone: "",
  role: "",
  company_id: "",
  password: "",
  password_confirmation: "",
});

function maskPhone(value) {
  if (!value) return "";
  value = value.replace(/\D/g, "");
  if (value.length > 11) value = value.substring(0, 11);
  if (value.length <= 2) return `(${value}`;
  if (value.length === 3) return `(${value.substring(0, 2)}) ${value.substring(2)}`;
  if (value.length <= 6)
    return `(${value.substring(0, 2)}) ${value.substring(2, 3)}.${value.substring(3)}`;
  if (value.length <= 10)
    return `(${value.substring(0, 2)}) ${value.substring(2, 3)}.${value.substring(
      3,
      7
    )}-${value.substring(7)}`;
  return `(${value.substring(0, 2)}) ${value.substring(2, 3)}.${value.substring(
    3,
    7
  )}-${value.substring(7, 11)}`;
}

watch(
  () => form.phone,
  (val) => {
    const onlyNumbers = val.replace(/\D/g, "");
    if (onlyNumbers !== rawPhone.value) {
      rawPhone.value = onlyNumbers;
      form.phone = maskPhone(onlyNumbers);
    }
  }
);

watch(
  () => form.company_id,
  (val) => {
    if (val) step.value = 2;
  }
);

const submit = () => {
  form.phone = rawPhone.value;
  form.post(route("register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Registrar" />

    <form @submit.prevent="submit">
      <div v-if="step === 1" class="mt-4">
        <InputLabel for="company_id" value="Empresa" />

        <select
          id="company_id"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          v-model="form.company_id"
          required
        >
          <option value="" disabled>Selecione uma empresa</option>
          <option v-for="company in companies" :key="company.id" :value="company.id">
            {{ company.name }}
          </option>
        </select>

        <InputError class="mt-2" :message="form.errors.company_id" />
      </div>

      <div v-if="step === 2">
        <div>
          <InputLabel for="name" value="Nome" />
          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
          <InputLabel for="phone" value="Telefone" />
          <TextInput
            id="phone"
            type="text"
            class="mt-1 block w-full"
            v-model="form.phone"
            autocomplete="tel"
            maxlength="16"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <div class="mt-4">
          <InputLabel for="role" value="Cargo" />
          <TextInput
            id="role"
            type="text"
            class="mt-1 block w-full"
            v-model="form.role"
            autocomplete="organization-title"
          />
          <InputError class="mt-2" :message="form.errors.role" />
        </div>

        <div class="mt-4">
          <InputLabel for="password" value="Senha" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirmar Senha" />
          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 flex items-center justify-end">
          <Link
            :href="route('login')"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Já registrado?
          </Link>

          <PrimaryButton
            class="ms-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Registrar
          </PrimaryButton>
        </div>
      </div>
    </form>
  </GuestLayout>
</template>
