<template>
  <div class="app-layout">
    <Sidebar />
    <main class="main-content p-4">
      <div class="container-fluid">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <Link href="/dashboard" class="text-decoration-none">Dashboard</Link>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Projetos</li>
          </ol>
        </nav>
      </div>
    </main>
  </div>
  <div>
    <div v-if="localFlashSuccess" class="alert alert-success text-center" role="alert">
      {{ localFlashSuccess }}
    </div>

    <div v-if="localFlashError" class="alert alert-danger text-center" role="alert">
      {{ localFlashError }}
    </div>

    <div class="container py-5" v-if="projects && projects.data">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">Projetos</h3>

        <div class="d-flex gap-2 flex-wrap">
          <Link href="/projects/create" class="btn btn-primary d-flex gap-2">
            Novo Projeto
          </Link>
        </div>
      </div>

      <form @submit.prevent="submitFilters" class="mb-4 p-3 border rounded bg-light">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Nome do Projeto</label>
            <input v-model="localFilters.name" type="text" class="form-control" />
          </div>

          <div class="col-md-3">
            <label class="form-label">Nome da Empresa</label>
            <input v-model="localFilters.company" type="text" class="form-control" />
          </div>

          <div class="col-md-3">
            <label class="form-label">Criado de</label>
            <input v-model="localFilters.created_from" type="date" class="form-control" />
          </div>

          <div class="col-md-3">
            <label class="form-label">Até</label>
            <input v-model="localFilters.created_to" type="date" class="form-control" />
          </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary" @click="clearFilters">
            Limpar
          </button>
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
      </form>

      <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover mb-0 responsive-table">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Empresa</th>
              <th>Status</th>
              <th>Criado em</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>

          <tbody>
            <template v-for="project in projects.data" :key="project.id">
              <tr @click="toggleExpand(project.id)" style="cursor: pointer">
                <td data-label="ID">{{ project.id }}</td>

                <td data-label="Nome">
                  <span v-if="project.tickets_count" class="badge bg-primary me-2">
                    {{ project.tickets_count }}
                  </span>
                  {{ project.name }}
                </td>

                <td data-label="Empresa">{{ project.company?.name || "—" }}</td>

                <td data-label="Status">
                  <span
                    :class="['modern-badge', statusBadgeClass(project.status)]"
                    v-if="project.status"
                    style="
                      display: inline-flex;
                      align-items: center;
                      justify-content: center;
                      width: 24px;
                      height: 24px;
                      padding: 0;
                    "
                    :title="
                      typeof project.status === 'object'
                        ? project.status.name
                        : project.status
                    "
                    aria-label="Status do projeto"
                  >
                    <template
                      v-if="
                        (typeof project.status === 'object'
                          ? project.status.name
                          : project.status
                        ).toLowerCase() === 'ativo'
                      "
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="#fff"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        focusable="false"
                      >
                        <path
                          d="M13.485 1.929a.75.75 0 1 1 1.06 1.06L6.53 11.005 3.455 7.93a.75.75 0 0 1 1.06-1.06l2.015 2.015 7.955-7.955z"
                        />
                      </svg>
                    </template>

                    <template
                      v-else-if="
                        (typeof project.status === 'object'
                          ? project.status.name
                          : project.status
                        ).toLowerCase() === 'pendente'
                      "
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="#fff"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        focusable="false"
                      >
                        <path
                          d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3 1.5a.5.5 0 0 1-.5.866l-3.5-1.75A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"
                        />
                        <path
                          d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16zM8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1z"
                        />
                      </svg>
                    </template>

                    <template
                      v-else-if="
                        (typeof project.status === 'object'
                          ? project.status.name
                          : project.status
                        ).toLowerCase() === 'concluído'
                      "
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="#fff"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        focusable="false"
                      >
                        <path
                          d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l1.94 1.94 3.646-4.293z"
                        />
                      </svg>
                    </template>

                    <template
                      v-else-if="
                        (typeof project.status === 'object'
                          ? project.status.name
                          : project.status
                        ).toLowerCase() === 'cancelado'
                      "
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="#fff"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        focusable="false"
                      >
                        <path
                          d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
                        />
                      </svg>
                    </template>

                    <template v-else>
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="#fff"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        focusable="false"
                      >
                        <circle cx="8" cy="8" r="7" />
                      </svg>
                    </template>
                  </span>
                  <span v-else>—</span>
                </td>

                <td data-label="Criado em">{{ formatDate(project.created_at) }}</td>

                <td data-label="Ações" class="text-center" @click.stop>
                  <div class="actions-cell d-flex justify-content-center gap-2">
                    <Link
                      :href="`/projects/${project.id}/edit`"
                      class="btn btn-sm btn-outline-secondary p-1"
                      style="
                        border-radius: 50%;
                        width: 32px;
                        height: 32px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                      "
                      aria-label="Editar projeto"
                      title="Editar Projeto"
                      @click.stop
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        width="16"
                        height="16"
                      >
                        <path d="M12 20h9" />
                        <path
                          d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"
                        />
                      </svg>
                    </Link>

                    <button
                      @click.stop="openCreateTicketModal(project)"
                      class="btn btn-sm btn-outline-primary p-1"
                      style="border-radius: 50%; width: 32px; height: 32px"
                      aria-label="Adicionar ticket"
                      title="Adicionar Ticket"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        width="16"
                        height="16"
                      >
                        <path stroke-linejoin="round" d="M12 4v16m8-8H4" />
                      </svg>
                    </button>

                    <button
                      @click.stop="openChangeProjectStatusModal(project)"
                      class="btn btn-sm btn-outline-info p-1"
                      style="border-radius: 50%; width: 32px; height: 32px"
                      aria-label="Alterar status do projeto"
                      title="Alterar status do projeto"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        width="16"
                        height="16"
                      >
                        <path d="M4 4v5h.582M20 20v-5h-.581M4.58 9A8 8 0 1119.42 15" />
                      </svg>
                    </button>

                    <button
                      @click.stop="openDeleteModal(project.id)"
                      class="btn btn-sm btn-outline-danger p-1"
                      style="border-radius: 50%; width: 32px; height: 32px"
                      aria-label="Excluir projeto"
                      title="Excluir Projeto"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        width="16"
                        height="16"
                      >
                        <path
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z"
                        />
                        <path stroke-linejoin="round" d="M10 11v6m4-6v6" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="expandedProjects.includes(project.id)">
                <td colspan="6" class="bg-light">
                  <div class="p-3">
                    <h6>Tickets do projeto</h6>

                    <ul v-if="project.tickets.length" class="list-group">
                      <li
                        v-for="ticket in project.tickets"
                        :key="ticket.id"
                        class="list-group-item d-flex justify-content-between align-items-center"
                      >
                        <div>
                          <span class="badge bg-secondary">#{{ ticket.id }}</span>
                          {{ ticket.title }}
                        </div>

                        <div class="d-flex align-items-center gap-3">
                          <span
                            :class="['badge', statusBadgeClass(ticket.status)]"
                            v-if="ticket.status"
                            style="min-width: 90px; text-align: center"
                          >
                            {{ ticket.status }}
                          </span>

                          <button
                            class="btn btn-sm btn-outline-primary"
                            @click.stop="openTicketModal(ticket)"
                            aria-label="Abrir ticket"
                            title="Abrir ticket"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="16"
                              height="16"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              viewBox="0 0 24 24"
                            >
                              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                              <circle cx="12" cy="12" r="3" />
                            </svg>
                          </button>

                          <button
                            class="btn btn-sm btn-outline-info"
                            @click.stop="openChangeTicketStatusModal(ticket)"
                            aria-label="Alterar status do ticket"
                            title="Alterar status do ticket"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              width="16"
                              height="16"
                            >
                              <path
                                d="M4 4v5h.582M20 20v-5h-.581M4.58 9A8 8 0 1119.42 15"
                              />
                            </svg>
                          </button>

                          <button
                            class="btn btn-sm btn-outline-danger"
                            @click.stop="openDeleteTicketModal(ticket)"
                            aria-label="Excluir ticket"
                            title="Excluir ticket"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="16"
                              height="16"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              viewBox="0 0 24 24"
                            >
                              <rect x="3" y="6" width="18" height="14" rx="2" ry="2" />
                              <line x1="9" y1="10" x2="9" y2="17" />
                              <line x1="15" y1="10" x2="15" y2="17" />
                              <line x1="4" y1="6" x2="20" y2="6" />
                            </svg>
                          </button>
                        </div>
                      </li>
                    </ul>

                    <div v-else class="alert alert-warning" role="alert">
                      Não existem tickets para este projeto.
                    </div>

                    <div v-if="project.tickets_count > 2" class="mt-3">
                      <button
                        @click="openAllTicketsModal(project)"
                        class="btn btn-sm btn-outline-primary fw-semibold"
                        style="
                          font-size: 0.95rem;
                          border-radius: 0.35rem;
                          box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
                          transition: background-color 0.2s ease, box-shadow 0.2s ease;
                          color: #0d6efd;
                        "
                        @mouseover="
                          (e) => {
                            e.currentTarget.style.backgroundColor = '#e7f1ff';
                            e.currentTarget.style.boxShadow =
                              '0 4px 8px rgba(0,0,0,0.15)';
                            e.currentTarget.style.color = '#0d6efd';
                          }
                        "
                        @mouseout="
                          (e) => {
                            e.currentTarget.style.backgroundColor = '';
                            e.currentTarget.style.boxShadow = '0 1px 4px rgba(0,0,0,0.1)';
                            e.currentTarget.style.color = '#0d6efd';
                          }
                        "
                      >
                        Ver todos os tickets ({{ project.tickets_count }})
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            </template>

            <tr v-if="projects.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4 bg-light">
                Nenhum projeto encontrado para os filtros aplicados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <nav v-if="projects.last_page > 1" aria-label="Paginação">
        <ul class="pagination justify-content-center my-4">
          <li
            class="page-item"
            :class="{ disabled: projects.current_page === 1 }"
            @click.prevent="changePage(projects.current_page - 1)"
          >
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>

          <li
            v-for="page in totalPages"
            :key="page"
            class="page-item"
            :class="{ active: page === projects.current_page }"
            @click.prevent="changePage(page)"
          >
            <a class="page-link" href="#">{{ page }}</a>
          </li>

          <li
            class="page-item"
            :class="{ disabled: projects.current_page === projects.last_page }"
            @click.prevent="changePage(projects.current_page + 1)"
          >
            <a class="page-link" href="#">Próximo</a>
          </li>
        </ul>
      </nav>

      <!-- Modais -->
      <div class="modal fade" ref="createTicketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content p-3">
            <button
              type="button"
              class="btn-close float-end"
              aria-label="Close"
              @click="closeCreateTicketModal"
            ></button>
            <CreateTicket
              v-if="projectForNewTicket"
              :project="projectForNewTicket"
              @close="closeCreateTicketModal"
              @created="onTicketCreated"
            />
          </div>
        </div>
      </div>

      <div class="modal fade" ref="ticketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5>Ticket #{{ selectedTicket?.id }}</h5>
              <button type="button" class="btn-close" @click="closeTicketModal"></button>
            </div>

            <div class="modal-body" v-if="selectedTicket">
              <p><strong>Título:</strong> {{ selectedTicket.title }}</p>
              <p><strong>Descrição:</strong></p>
              <p class="text-muted">
                {{ selectedTicket.description || "Sem descrição." }}
              </p>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeTicketModal">
                Fechar
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" ref="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5>Confirmar exclusão</h5>
              <button type="button" class="btn-close" @click="closeDeleteModal"></button>
            </div>
            <div class="modal-body">Tem certeza que deseja excluir este projeto?</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeDeleteModal">
                Cancelar
              </button>
              <button type="button" class="btn btn-danger" @click="deleteProject">
                Excluir
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" ref="deleteTicketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5>Confirmar exclusão</h5>
              <button
                type="button"
                class="btn-close"
                @click="closeDeleteTicketModal"
              ></button>
            </div>
            <div class="modal-body">
              Tem certeza que deseja excluir o ticket #{{ ticketToDelete?.id }}?
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeDeleteTicketModal"
              >
                Cancelar
              </button>
              <button type="button" class="btn btn-danger" @click="deleteTicket">
                Excluir
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" ref="allTicketsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content p-3">
            <button
              type="button"
              class="btn-close float-end"
              aria-label="Close"
              @click="closeAllTicketsModal"
            ></button>

            <h5>Todos os tickets do projeto: {{ allTicketsProject?.name }}</h5>

            <input
              type="text"
              class="form-control my-3"
              placeholder="Pesquisar tickets pelo título..."
              v-model="allTicketsSearch"
            />

            <ul
              v-if="filteredAllTickets.length"
              class="list-group"
              style="max-height: 400px; overflow-y: auto"
            >
              <li
                v-for="ticket in filteredAllTickets"
                :key="ticket.id"
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div>
                  <span class="badge bg-secondary">#{{ ticket.id }}</span>
                  {{ ticket.title }}
                </div>

                <div class="d-flex gap-2">
                  <button
                    class="btn btn-sm btn-outline-primary"
                    @click.stop="openTicketModal(ticket)"
                    title="Abrir ticket"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      viewBox="0 0 24 24"
                    >
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>

                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click.stop="openDeleteTicketModal(ticket)"
                    title="Excluir ticket"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      viewBox="0 0 24 24"
                    >
                      <rect x="3" y="6" width="18" height="14" rx="2" ry="2" />
                      <line x1="9" y1="10" x2="9" y2="17" />
                      <line x1="15" y1="10" x2="15" y2="17" />
                      <line x1="4" y1="6" x2="20" y2="6" />
                    </svg>
                  </button>
                </div>
              </li>
            </ul>

            <div v-else class="text-center text-muted py-4">
              Nenhum ticket encontrado.
            </div>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        ref="changeProjectStatusModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-3">
            <button
              type="button"
              class="btn-close float-end"
              aria-label="Close"
              @click="closeModal('changeProjectStatusModal')"
            ></button>
            <h5>Alterar status do projeto: {{ projectToChangeStatus?.name }}</h5>

            <form @submit.prevent="saveProjectStatus">
              <div class="mb-3">
                <label for="projectStatusSelect" class="form-label">Status</label>
                <select
                  id="projectStatusSelect"
                  v-model="newProjectStatus"
                  class="form-select"
                  required
                >
                  <option value="" disabled>Selecione o status</option>
                  <option v-for="status in statusOptions" :key="status" :value="status">
                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                  </option>
                </select>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="closeModal('changeProjectStatusModal')"
                >
                  Cancelar
                </button>
                <button type="submit" class="btn btn-primary">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        ref="changeTicketStatusModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-3">
            <button
              type="button"
              class="btn-close float-end"
              aria-label="Close"
              @click="closeModal('changeTicketStatusModal')"
            ></button>
            <h5>Alterar status do ticket: #{{ ticketToChangeStatus?.id }}</h5>

            <form @submit.prevent="saveTicketStatus">
              <div class="mb-3">
                <label for="ticketStatusSelect" class="form-label">Status</label>
                <select
                  id="ticketStatusSelect"
                  v-model="newTicketStatus"
                  class="form-select"
                  required
                >
                  <option value="" disabled>Selecione o status</option>
                  <option v-for="status in statusOptions" :key="status" :value="status">
                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                  </option>
                </select>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="closeModal('changeTicketStatusModal')"
                >
                  Cancelar
                </button>
                <button type="submit" class="btn btn-primary">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import componentOptions from "./Index.script.js";
import Sidebar from "@/Components/Sidebar.vue";
export default {
  components: { Sidebar },
  ...componentOptions,
};
</script>

<style src="./Index.style.css"></style>
