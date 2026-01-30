<template>
  <div>
    <FlashMessage :flash="$page.props.flash" />

    <header class="mb-4 p-3 bg-light border-bottom">
      <h2>{{ $page.props.auth?.user?.name || "ServiceHub" }}</h2>
    </header>

    <main class="container">
      <slot />
    </main>

    <footer class="mt-5 p-3 text-center text-muted border-top">
      &copy; 2026 ServiceHub
    </footer>
  </div>
</template>

<script>
import FlashMessage from "@/Components/FlashMessage.vue";

export default {
  components: {
    FlashMessage,
  },
};
</script>

<style scoped>
header {
  background-color: #f8f9fa;
}

footer {
  font-size: 0.9rem;
}
</style>
