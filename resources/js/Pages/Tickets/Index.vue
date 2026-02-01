<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  tickets: Array,
  project: Object,
});
</script>

<template>
  <div class="container py-5">
    <h2 class="mb-4 text-center">Lista de Tickets</h2>
    <ul>
      <li v-for="ticket in props.tickets" :key="ticket.id">
        {{ ticket.title }} - {{ ticket.status }}
      </li>
    </ul>
  </div>
</template>

<style scoped></style>
