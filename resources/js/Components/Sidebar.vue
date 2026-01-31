<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const sidebarOpen = ref(false);
</script>

<template>
  <div class="app-layout">
    <aside :class="['sidebar', { open: sidebarOpen }]">
      <button
        class="toggle-btn"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="Toggle Sidebar"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <nav class="menu" aria-label="Main Navigation">
        <Link
          :href="route('projects.index')"
          class="menu-item"
          @click="sidebarOpen = false"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
          </svg>
          <span>Projetos</span>
        </Link>
      </nav>
    </aside>

    <main :class="{ shifted: sidebarOpen }" class="main-content">
      <h1>Conteúdo Principal</h1>
      <p>Este conteúdo empurra para a direita quando a sidebar está aberta.</p>
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 64px;
  background: #f8fafc;
  color: #334155;
  min-height: 100vh;
  transition: width 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 1rem;
  border-right: 1px solid #e2e8f0;
  flex-shrink: 0;
  position: relative;
}

.sidebar.open {
  width: 220px;
  align-items: flex-start;
  padding-left: 1rem;
}

.toggle-btn {
  background: #e2e8f0;
  border: none;
  border-radius: 10px;
  padding: 6px;
  margin-bottom: 2rem;
  transition: background-color 0.2s ease;
  cursor: pointer;
  align-self: center;
}

.toggle-btn:hover {
  background: #cbd5f5;
}

.toggle-btn svg {
  width: 22px;
  height: 22px;
  stroke: #334155;
  fill: none;
  stroke-width: 2;
}

.menu {
  width: 100%;
  flex-grow: 1;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  margin: 4px 8px;
  border-radius: 10px;
  color: #334155;
  text-decoration: none;
  transition: background-color 0.2s ease, color 0.2s ease;
  font-weight: 500;
  white-space: nowrap;
  cursor: pointer;
}

.menu-item:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.menu-item svg {
  width: 22px;
  height: 22px;
  stroke: #475569;
  fill: none;
  stroke-width: 1.8;
  flex-shrink: 0;
}

.menu-item span {
  display: none;
  user-select: none;
}

.sidebar.open .menu-item span {
  display: inline;
}

.main-content {
  flex-grow: 1;
  padding: 1.5rem;
  transition: margin-left 0.3s ease;
  margin-left: 64px;
}

.main-content.shifted {
  margin-left: 220px;
}

@media (max-width: 768px) {
  .app-layout {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    height: auto;
    min-height: auto;
    border-right: none;
    border-bottom: 1px solid #e2e8f0;
    padding-left: 1rem;
    align-items: flex-start;
  }

  .sidebar.open {
    width: 100%;
  }

  .main-content {
    margin-left: 0 !important;
  }
}
</style>
