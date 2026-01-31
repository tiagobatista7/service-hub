<template>
  <div class="default-layout d-flex">
    <aside :class="['sidebar bg-light', { open: sidebarOpen }]">
      <button class="toggle-btn" @click="toggleSidebar" aria-label="Toggle Sidebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <nav>
        <ul>
          <li>
            <Link
              :href="route('dashboard')"
              @click="closeSidebarOnMobile"
              class="menu-link"
              :class="{ active: isActiveRoute('dashboard') }"
            >
              <span class="icon">🏠</span>
              <span class="text">Dashboard</span>
            </Link>
          </li>

          <li>
            <Link
              :href="route('projects.index')"
              @click="closeSidebarOnMobile"
              class="menu-link"
              :class="{ active: isActiveRoute('projects.index') }"
            >
              <span class="icon">📁</span>
              <span class="text">Projetos</span>
            </Link>
          </li>
        </ul>
      </nav>
    </aside>

    <main class="main-content flex-grow-1 p-3">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const page = usePage();

// Estado da sidebar com persistência
const sidebarOpen = ref(false);

onMounted(() => {
  const saved = localStorage.getItem("sidebarOpen");
  sidebarOpen.value = saved === "true";
});

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
  localStorage.setItem("sidebarOpen", sidebarOpen.value);
}

function closeSidebarOnMobile() {
  if (window.innerWidth <= 768) {
    sidebarOpen.value = false;
    localStorage.setItem("sidebarOpen", "false");
  }
}

/**
 * Verifica se a rota atual corresponde à rota do menu
 */
function isActiveRoute(routeName) {
  try {
    const currentPath = page.url;
    const targetPath = new URL(route(routeName)).pathname;

    return currentPath.startsWith(targetPath);
  } catch (e) {
    return false;
  }
}
</script>

<style scoped>
.default-layout {
  min-height: 100vh;
  display: flex;
  background: #f8f9fa;
}

.sidebar {
  width: 64px;
  min-height: 100vh;
  border-right: 1px solid #ddd;
  padding: 1rem 0.5rem;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: width 0.3s ease;
  overflow: hidden;
  background: white;
  box-shadow: 2px 0 5px rgb(0 0 0 / 0.05);
}

.sidebar.open {
  width: 220px;
  align-items: flex-start;
  padding-left: 1rem;
}

.toggle-btn {
  background: #e2e8f0;
  border: none;
  border-radius: 8px;
  padding: 6px;
  margin-bottom: 1.5rem;
  cursor: pointer;
  align-self: center;
  transition: background-color 0.2s ease;
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

nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
}

nav li {
  margin-bottom: 1rem;
}

.menu-link {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #334155;
  text-decoration: none;
  font-weight: 500;
  white-space: nowrap;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.menu-link:hover {
  background-color: #e7f1ff;
  color: #0d6efd;
}

.menu-link.active {
  background-color: #0d6efd;
  color: white;
}

.icon {
  flex-shrink: 0;
  width: 24px;
  text-align: center;
  font-size: 18px;
  user-select: none;
}

.sidebar:not(.open) .text {
  display: none;
}

.main-content {
  overflow-x: auto;
  transition: margin-left 0.3s ease;
  background: #fff;
  min-height: 100vh;
  padding: 1.5rem;
  box-sizing: border-box;
}

@media (max-width: 768px) {
  .default-layout {
    flex-direction: column;
  }

  .sidebar {
    width: 100% !important;
    min-height: auto;
    border-right: none;
    border-bottom: 1px solid #ddd;
    padding-left: 1rem;
    align-items: flex-start;
    position: relative;
  }

  .sidebar.open {
    width: 100% !important;
  }

  nav ul {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .sidebar.open nav ul {
    max-height: 500px;
  }
}
</style>
