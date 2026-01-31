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
          <h5 class="modal-title">Alterar Status do Projeto</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">         
          <p>Modal de alteração de status do projeto</p>
          <button class="btn btn-primary" @click="$emit('statusChanged')">Salvar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(["close", "statusChanged"]);
</script>

<style scoped>
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
