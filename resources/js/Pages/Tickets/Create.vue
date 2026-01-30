<template>
  <div class="container py-5">
    <div class="mx-auto position-relative" style="max-width: 600px">
      <h2 class="mb-4 text-center">
        <small class="text-muted d-block mb-1">Novo Ticket para o Projeto</small>
        <span class="fs-4 fw-semibold text-primary">{{ project.name }}</span>
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
            aria-describedby="titleHelp"
          />
          <div class="invalid-feedback">Título é obrigatório.</div>
          <div id="titleHelp" class="form-text"></div>
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
