<template>
  <div class="container py-5">
    <div class="mx-auto position-relative" style="max-width: 600px">
      <button
        @click="$inertia.get('/projects')"
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

      <h2 class="mb-4">Editar Projeto</h2>

      <form
        @submit.prevent="submit"
        class="needs-validation"
        :class="{ 'was-validated': validated }"
        novalidate
        enctype="multipart/form-data"
      >
        <!-- Empresa removida -->

        <div class="mb-3">
          <label for="name" class="form-label">Nome do Projeto *</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            class="form-control"
            placeholder="Digite o nome do projeto"
            required
            aria-describedby="nameHelp"
          />
          <div class="invalid-feedback">Nome do projeto é obrigatório.</div>
          <div id="nameHelp" class="form-text">Escolha um nome claro e objetivo.</div>
        </div>

        <div class="mb-3">
          <label for="attachment" class="form-label">Anexo</label>
          <input
            id="attachment"
            type="file"
            @change="onFileChange"
            class="form-control"
            accept="*/*"
            aria-describedby="attachmentHelp"
          />
          <div id="attachmentHelp" class="form-text">
            Selecione um arquivo para anexar ao projeto. (Opcional)
          </div>

          <div v-if="project.attachment" class="mt-2">
            <strong>Anexo atual:</strong> {{ project.attachment }}
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
          {{ form.processing ? "Salvando..." : "Salvar Alterações" }}
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
    companies: Array, // pode remover se não usar
  },
  setup(props) {
    const form = useForm({
      name: props.project.name || "",
      attachment: null,
    });

    const validated = ref(false);

    function onFileChange(event) {
      const file = event.target.files[0];
      form.attachment = file || null;
    }

    function submit() {
      validated.value = true;
      if (!form.name) {
        return;
      }

      form.put(`/projects/${props.project.id}`, {
        preserveState: true,
        onSuccess: () => {
          validated.value = false;
        },
      });
    }

    return { form, submit, validated, onFileChange };
  },
};
</script>
