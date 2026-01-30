<script setup>
import { ref, watch } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  user: Object,
  profile: Object,
});

// Inicializa o rawPhone com números puros do telefone, ou vazio
const rawPhone = ref(props.profile?.phone ? props.profile.phone.replace(/\D/g, "") : "");

const form = useForm({
  name: props.user.name || "",
  email: props.user.email || "",
  phone: props.profile?.phone || "",
  role: props.profile?.role || "",
});

// Função para aplicar máscara no telefone
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

// Watcher que atualiza o telefone com máscara enquanto digita
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

const submit = () => {
  // No envio, enviar telefone sem máscara (apenas números)
  form.phone = rawPhone.value;
  form.patch(route("profile.update"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">Informações do Perfil</h2>

      <p class="mt-1 text-sm text-gray-600">
        Atualize as informações do seu perfil e o endereço de e-mail da sua conta.
      </p>
    </header>

    <form @submit.prevent="submit" class="mt-6 space-y-6">
      <!-- Nome -->
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

      <!-- Email -->
      <div>
        <InputLabel for="email" value="E-mail" />

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

      <!-- Telefone -->
      <div>
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

      <!-- Cargo -->
      <div>
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

      <!-- Verificação de e-mail -->
      <div v-if="mustVerifyEmail && user.email_verified_at === null">
        <p class="mt-2 text-sm text-gray-800">
          Seu endereço de e-mail não foi verificado.
          <Link
            :href="route('verification.send')"
            method="post"
            as="button"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Clique aqui para reenviar o e-mail de verificação.
          </Link>
        </p>

        <div
          v-show="status === 'verification-link-sent'"
          class="mt-2 text-sm font-medium text-green-600"
        >
          Um novo link de verificação foi enviado para seu endereço de e-mail.
        </div>
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>

        <transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Salvo.</p>
        </transition>
      </div>
    </form>
  </section>
</template>
