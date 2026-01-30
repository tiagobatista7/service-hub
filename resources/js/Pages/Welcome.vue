<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted } from "vue";

onMounted(() => {
  router.visit(route("login"));
});
</script>
