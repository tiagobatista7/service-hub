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

      <h2 class="mb-4">Novo Projeto</h2>

      <form
        @submit.prevent="submit"
        class="needs-validation"
        :class="{ 'was-validated': validated }"
        novalidate
      >
        <div class="mb-3">
          <label for="category" class="form-label">Categoria do Projeto *</label>
          <input
            id="category"
            type="text"
            v-model="form.category"
            class="form-control"
            placeholder="Digite a categoria do projeto"
            required
            aria-describedby="categoryHelp"
          />
          <div class="invalid-feedback">Categoria do projeto é obrigatória.</div>
        </div>

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
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
          {{ form.processing ? "Salvando..." : "Criar Projeto" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  setup() {
    const form = useForm({
      category: "",
      name: "",
    });

    const validated = ref(false);

    function submit() {
      validated.value = true;

      if (!form.category || !form.name) {
        return;
      }

      form.post("/projects", {
        onSuccess: () => {
          form.reset();
          validated.value = false;
        },
      });
    }

    return { form, submit, validated };
  },
};
</script>
