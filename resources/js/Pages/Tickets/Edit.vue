<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  ticket: Object,
});

const form = useForm({
  title: props.ticket.title,
  description: props.ticket.description,
});

function submit() {
  form.put(`/tickets/${props.ticket.id}`);
}
</script>

<template>
  <div class="container py-5">
    <h2 class="mb-4 text-center">Editar Ticket</h2>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Título</label>
        <input v-model="form.title" type="text" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea v-model="form.description" class="form-control"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>
</template>

<style scoped></style>
