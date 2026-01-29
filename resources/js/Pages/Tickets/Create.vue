<template>
  <div class="container py-5">
    <div class="mx-auto position-relative" style="max-width: 600px">
      <!-- Botão Voltar moderno e clean -->
      <button
        @click="$inertia.get(route('projects.index'))"
        type="button"
        class="btn btn-outline-secondary d-flex align-items-center gap-2 mb-4"
        style="
          border-radius: 50px;
          box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
          font-weight: 500;
        "
        aria-label="Voltar para lista de projetos"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="feather feather-arrow-left"
          viewBox="0 0 24 24"
          aria-hidden="true"
          focusable="false"
        >
          <line x1="19" y1="12" x2="5" y2="12" />
          <polyline points="12 19 5 12 12 5" />
        </svg>
        Voltar
      </button>

      <h2 class="mb-4 text-center">
        <small class="text-muted d-block mb-1">Novo Ticket para o Projeto</small>
        <span class="fs-3 fw-semibold text-primary">{{ project.name }}</span>
      </h2>

      <form
        @submit.prevent="submit"
        enctype="multipart/form-data"
        class="needs-validation"
        :class="{ 'was-validated': validated }"
        novalidate
      >
        <!-- formulário igual ao anterior -->
        <div class="mb-3">
          <label for="title" class="form-label">Título *</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="form-control"
            placeholder="Digite o título do ticket"
            required
            aria-describedby="titleHelp"
          />
          <div class="invalid-feedback">Título é obrigatório.</div>
          <div id="titleHelp" class="form-text">
            Um título claro ajuda a identificar o ticket.
          </div>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="form-control"
            rows="5"
            placeholder="Detalhe mais informações sobre o ticket (opcional)"
            aria-label="Descrição do ticket"
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
            aria-label="Anexo do ticket"
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary w-100"
          :disabled="form.processing"
          aria-live="polite"
        >
          {{ form.processing ? "Enviando..." : "Criar Ticket" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  props: {
    project: Object,
  },
  setup(props) {
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
      if (!form.title) {
        return;
      }

      form.post(`/projects/${props.project.id}/tickets`, {
        onSuccess: () => {
          form.reset();
          validated.value = false;
        },
      });
    }

    return { form, submit, handleFileUpload, validated };
  },
};
</script>
