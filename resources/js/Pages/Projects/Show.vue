<template>
  <div>
    <h1>{{ project.name }}</h1>

    <h2>Tickets</h2>

    <ul>
      <li v-for="ticket in project.tickets" :key="ticket.id">
        {{ ticket.title }} - {{ ticket.status }}
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  project: Object
})
</script>
