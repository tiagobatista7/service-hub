<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

const emit = defineEmits(["close"]);

const props = defineProps({
  project: Object,
});

const page = usePage();
const currentUrl = page.url;

const form = useForm({
  title: "",
  description: "",
  attachment: null,
});

const validated = ref(false);

function handleFileUpload(event) {
  form.attachment = event.target.files[0];
}

function submit() {
  validated.value = true;
  if (!form.title) return;

  form.post(`/projects/${props.project.id}/tickets`, {
    onSuccess: () => {
      form.reset();
      validated.value = false;
      emit("close");
    },
  });
}
</script>

<template>
  <div class="container py-5">
    <div class="mx-auto position-relative" style="max-width: 600px">
      <button
        v-if="currentUrl.endsWith('/tickets/create')"
        @click="$inertia.get(`/dashboard/`)"
        type="button"
        class="back-btn mb-5"
        aria-label="Voltar para o projeto"
      >
        <svg viewBox="0 0 24 24">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        <span>Voltar para o Projeto</span>
      </button>

      <h2 class="mb-4 text-center">
        <small class="text-muted d-block mb-1">Novo Ticket para o Projeto</small>
        <span class="fs-4 fw-semibold text-primary">{{ props.project.name }}</span>
      </h2>

      <form
        @submit.prevent="submit"
        enctype="multipart/form-data"
        class="needs-validation"
        :class="{ 'was-validated': validated }"
        novalidate
      >
        <div class="mb-3">
          <label for="title" class="form-label">Título *</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="form-control"
            placeholder="Digite o título do ticket"
            required
          />
          <div class="invalid-feedback">Título é obrigatório.</div>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="form-control"
            rows="5"
            placeholder="Detalhe mais informações sobre o ticket (opcional)"
          ></textarea>
        </div>

        <div class="mb-4">
          <label for="attachment" class="form-label">Anexo (JSON ou TXT)</label>
          <input
            id="attachment"
            type="file"
            @change="handleFileUpload"
            accept=".json,.txt"
            class="form-control"
          />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
          {{ form.processing ? "Enviando..." : "Criar Ticket" }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 999px;
  background: #334155;
  color: #f1f5f9;
  border: 1px solid #475569;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
}

.back-btn svg {
  width: 18px;
  height: 18px;
  stroke: #f1f5f9;
  fill: none;
  stroke-width: 2;
}

.back-btn:hover {
  background: #1e293b;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
  transform: translateY(-1px);
}
</style>
