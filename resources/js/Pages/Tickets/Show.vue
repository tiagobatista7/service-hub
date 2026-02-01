<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  ticket: Object,
});
</script>

<template>
  <div class="container py-5">
    <h2 class="mb-4 text-center">Detalhes do Ticket</h2>
    <p><strong>Título:</strong> {{ props.ticket.title }}</p>
    <p><strong>Descrição:</strong> {{ props.ticket.description || "Sem descrição" }}</p>
    <p><strong>Status:</strong> {{ props.ticket.status }}</p>
    <p v-if="props.ticket.attachment">
      <strong>Anexo:</strong> {{ props.ticket.attachment }}
    </p>
  </div>
</template>

<style scoped></style>
