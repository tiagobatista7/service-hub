<script setup>
import { onMounted } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DefaultLayout from "@/Layouts/DefaultLayout.vue";

const { props } = usePage();

const kpis = props.kpis;
const ticketsLast7Days = props.tickets_last_7_days;
const projectsByCategory = props.projects_by_category;
const latestTickets = props.latest_tickets;
const criticalSlas = props.critical_slas;

const statusBadge = (status) => {
  if (status === "pendente" || status === "open") return "bg-danger";
  if (status === "em_andamento" || status === "in_progress")
    return "bg-warning text-dark";
  if (status === "resolvido" || status === "resolved") return "bg-success";
  if (status === "closed") return "bg-secondary";
  return "bg-secondary";
};

onMounted(() => {
  const script = document.createElement("script");
  script.src = "https://www.gstatic.com/charts/loader.js";
  script.onload = () => {
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawCharts);
  };
  document.head.appendChild(script);
});

function drawCharts() {
  drawTicketsChart();
  drawProjectsChart();
}

function drawTicketsChart() {
  const data = new google.visualization.DataTable();
  data.addColumn("string", "Data");
  data.addColumn("number", "Tickets");

  const last7Days = [];
  const today = new Date();

  for (let i = 6; i >= 0; i--) {
    const day = new Date(today);
    day.setDate(today.getDate() - i);
    const dayStr = `${day.getDate().toString().padStart(2, "0")}/${(day.getMonth() + 1)
      .toString()
      .padStart(2, "0")}`;
    last7Days.push({ date: dayStr, total: 0 });
  }

  ticketsLast7Days.forEach((row) => {
    const dateObj = new Date(row.date);
    const formattedDate = `${dateObj.getDate().toString().padStart(2, "0")}/${(
      dateObj.getMonth() + 1
    )
      .toString()
      .padStart(2, "0")}`;

    const dayEntry = last7Days.find((d) => d.date === formattedDate);
    if (dayEntry) {
      dayEntry.total = row.total;
    }
  });

  last7Days.forEach((day) => {
    data.addRow([day.date, day.total]);
  });

  const options = {
    legend: { position: "bottom", textStyle: { fontSize: 12 } },
    curveType: "function",
    height: 280,
    chartArea: { width: "85%", height: "70%" },
    colors: ["#0d6efd"],
    backgroundColor: "transparent",
    fontName:
      'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
  };

  const chart = new google.visualization.LineChart(
    document.getElementById("ticketsChart")
  );
  chart.draw(data, options);
}

function drawProjectsChart() {
  const data = new google.visualization.DataTable();
  data.addColumn("string", "Categoria");
  data.addColumn("number", "Projetos");

  projectsByCategory.forEach((row) => {
    data.addRow([row.category, row.total]);
  });

  const options = {
    legend: { position: "none" },
    height: 280,
    chartArea: { width: "85%", height: "70%" },
    colors: ["#198754"],
    backgroundColor: "transparent",
    fontName:
      'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
  };

  const chart = new google.visualization.ColumnChart(
    document.getElementById("projectsChart")
  );
  chart.draw(data, options);
}
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h3 class="fw-semibold m-2">Service Hub — Dashboard</h3>
    </template>

    <DefaultLayout>
      <main class="main-content container py-4">
        <div class="row g-3 mb-4">
          <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-primary text-white">
              <div
                class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between"
              >
                <div class="mb-2 mb-md-0">
                  <small class="kpi-label">Tickets Abertos</small>
                  <h3 class="kpi-value">{{ kpis.open_tickets }}</h3>
                </div>
                <svg
                  class="kpi-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  aria-hidden="true"
                >
                  <path d="M12 9v4M12 17h.01" />
                  <path d="M5 21h14L12 3 5 21z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-success text-white">
              <div
                class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between"
              >
                <div class="mb-2 mb-md-0">
                  <small class="kpi-label">Resolvidos Hoje</small>
                  <h3 class="kpi-value">{{ kpis.resolved_today }}</h3>
                </div>
                <svg
                  class="kpi-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  aria-hidden="true"
                >
                  <path d="M20 6L9 17l-5-5" />
                </svg>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-warning text-dark">
              <div
                class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between"
              >
                <div class="mb-2 mb-md-0">
                  <small class="kpi-label">Em Risco (SLA)</small>
                  <h3 class="kpi-value">{{ kpis.sla_risk }}</h3>
                </div>
                <svg
                  class="kpi-icon-dark"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#000"
                  stroke-width="1.8"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  aria-hidden="true"
                >
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 6v6l4 2" />
                </svg>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-dark text-white">
              <div
                class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between"
              >
                <div class="mb-2 mb-md-0">
                  <small class="kpi-label">Total Tickets</small>
                  <h3 class="kpi-value">{{ kpis.total ?? 0 }}</h3>
                </div>
                <svg
                  class="kpi-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  aria-hidden="true"
                >
                  <path d="M3 7h18v4a2 2 0 010 4v4H3v-4a2 2 0 010-4V7z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4 mb-4 charts-row">
          <div class="col-lg-8 col-12">
            <div class="card shadow-sm rounded">
              <div class="card-header bg-white fw-semibold fs-6">
                Tickets - Últimos 7 dias
              </div>
              <div class="card-body px-3 py-2">
                <div id="ticketsChart" style="width: 100%; height: 280px"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-12">
            <div class="card shadow-sm rounded">
              <div class="card-header bg-white fw-semibold fs-6">
                Tickets em Risco (SLA)
              </div>
              <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                  <li
                    v-for="sla in criticalSlas"
                    :key="sla.id"
                    class="list-group-item d-flex justify-content-between align-items-center"
                  >
                    <span class="sla-title text-truncate">{{ sla.title }}</span>
                    <span class="badge bg-danger rounded-pill fs-7 px-2 py-1">
                      {{ new Date(sla.sla_due_at).toLocaleDateString() }}
                    </span>
                  </li>

                  <li
                    v-if="!criticalSlas.length"
                    class="list-group-item text-muted text-center py-3"
                  >
                    Nenhum ticket em risco
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-lg-6">
            <div class="card shadow-sm rounded">
              <div class="card-header bg-white fw-semibold fs-6">
                Projetos por Categoria
              </div>
              <div class="card-body px-3 py-2">
                <div id="projectsChart" style="width: 100%; height: 280px"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card shadow-sm rounded">
              <div class="card-header bg-white fw-semibold fs-6">Últimos Tickets</div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                      <tr>
                        <th class="text-nowrap">ID</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Categoria</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="ticket in latestTickets" :key="ticket.id">
                        <td class="text-nowrap">#{{ ticket.id }}</td>
                        <td class="text-truncate" style="max-width: 180px">
                          {{ ticket.title }}
                        </td>
                        <td>
                          <span
                            class="badge px-2 py-1"
                            :class="statusBadge(ticket.status)"
                          >
                            {{ ticket.status }}
                          </span>
                        </td>
                        <td>
                          <span class="badge bg-secondary px-2 py-1 rounded-pill fs-7">
                            {{ ticket.category || "—" }}
                          </span>
                        </td>
                      </tr>

                      <tr v-if="!latestTickets.length">
                        <td colspan="4" class="text-center text-muted py-4">
                          Nenhum ticket registrado
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </DefaultLayout>
  </AuthenticatedLayout>
</template>

<style scoped>
.dashboard-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  margin-left: 0;
  transition: margin 0.25s ease-in-out;
}

.kpi-card {
  border-radius: 1rem;
  padding: 0.75rem 1rem;
  box-shadow: 0 2px 10px rgb(0 0 0 / 0.06);
  cursor: default;
  transition: box-shadow 0.3s ease-in-out, background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #2c3e50;
  background-color: #f5f7fa;
  font-family: "Inter", sans-serif;
  min-height: 80px;
}

.kpi-card:hover {
  box-shadow: 0 8px 25px rgb(0 0 0 / 0.12);
  background-color: #e9eff5;
}

.kpi-card.bg-primary {
  background-color: #c8dafc;
  color: #1a3e72;
}

.kpi-card.bg-success {
  background-color: #cdebd9;
  color: #1b4d28;
}

.kpi-card.bg-warning {
  background-color: #fff7d9;
  color: #7a5a00;
}

.kpi-card.bg-dark {
  background-color: #dde3eb;
  color: #3e4a5a;
}

.kpi-label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  opacity: 0.75;
  margin-bottom: 0.1rem;
}

.kpi-value {
  font-size: 1.4rem;
  font-weight: 700;
  line-height: 1.2;
  margin: 0;
}

.kpi-icon,
.kpi-icon-dark {
  width: 24px;
  height: 24px;
  stroke-width: 1.4;
  opacity: 0.7;
  flex-shrink: 0;
  stroke: currentColor;
}

.kpi-icon-dark {
  stroke: #333;
}

.card {
  border-radius: 0.75rem;
  box-shadow: 0 1px 8px rgb(0 0 0 / 0.04);
  transition: box-shadow 0.3s ease;
  background-color: #fff;
  color: #444;
}

.card:hover {
  box-shadow: 0 10px 30px rgb(0 0 0 / 0.1);
}

.card-header {
  border-bottom: none;
  font-weight: 600;
  font-size: 1rem;
  padding: 0.75rem 1rem;
  background-color: #fafafa;
  border-radius: 0.75rem 0.75rem 0 0;
  color: #222;
}

.list-group-item {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  border: none;
  color: #555;
}

.list-group-item:hover {
  background-color: #f5f7fa;
  cursor: default;
}

.badge {
  font-weight: 600;
  font-size: 0.85rem;
  border-radius: 12px;
}

.bg-danger {
  background-color: #fca5a5 !important;
  color: #7f1d1d !important;
}

.bg-success {
  background-color: #86efac !important;
  color: #166534 !important;
}

.bg-warning {
  background-color: #fde68a !important;
  color: #713f12 !important;
}

.bg-secondary {
  background-color: #cbd5e1 !important;
  color: #475569 !important;
}

.badge.bg-danger,
.badge.bg-success,
.badge.bg-warning,
.badge.bg-secondary {
  padding: 0.25rem 0.6rem;
  font-size: 0.75rem;
  font-weight: 700;
  border-radius: 0.75rem;
}

.table-hover tbody tr:hover {
  background-color: #f5f7fa;
  cursor: pointer;
}

.table thead th {
  font-weight: 600;
  font-size: 0.875rem;
  color: #666;
}

.table tbody td {
  font-size: 0.875rem;
  color: #444;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sla-title {
  max-width: 75%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: 991.98px) {
  .kpi-value {
    font-size: 1.2rem;
  }
  .kpi-icon,
  .kpi-icon-dark {
    width: 20px;
    height: 20px;
  }
}

@media (max-width: 575.98px) {
  .kpi-value {
    font-size: 1rem;
  }
  .kpi-icon,
  .kpi-icon-dark {
    width: 18px;
    height: 18px;
  }
}

@media (max-width: 767.98px) {
  .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    -ms-overflow-style: -ms-autohiding-scrollbar;
  }

  .table-responsive table {
    min-width: 500px;
  }

  .charts-row {
    flex-direction: column !important;
  }

  .charts-row > .col-lg-8,
  .charts-row > .col-lg-4 {
    max-width: 100% !important;
    flex: 0 0 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-bottom: 1rem;
  }

  .charts-row > .col-lg-4:last-child {
    margin-bottom: 0;
  }
}
</style>
