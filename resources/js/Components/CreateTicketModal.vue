<template>
  <div
    class="modal fade show"
    style="display: block"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Novo Ticket para Projeto: {{ project?.name }}</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submit">
            <div class="mb-3">
              <label class="form-label">Título do Ticket</label>
              <input v-model="title" type="text" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea v-model="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Criar Ticket</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  project: Object,
});

const emit = defineEmits(["close", "ticketCreated"]);

const title = ref("");
const description = ref("");

watch(
  () => props.project,
  () => {
    title.value = "";
    description.value = "";
  }
);

function submit() {
  alert(`Ticket "${title.value}" criado para o projeto "${props.project?.name}"!`);
  emit("ticketCreated");
  emit("close");
}
</script>

<style scoped>
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
