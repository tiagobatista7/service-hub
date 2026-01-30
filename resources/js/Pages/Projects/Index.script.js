import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import bootstrap from "bootstrap/dist/js/bootstrap.bundle";
import CreateTicket from "@/Pages/Tickets/Create.vue";

export default {
    components: { Link, CreateTicket },
    props: {
        projects: Object,
        filters: Object,
        flash: Object,
    },
    data() {
        return {
            showAdvancedSearch: !!(
                this.filters?.name ||
                this.filters?.company ||
                this.filters?.created_from ||
                this.filters?.created_to
            ),
            expandedProjects: [],
            selectedTicket: null,
            ticketModalInstance: null,
            projectToDelete: null,
            deleteModalInstance: null,
            projectForNewTicket: null,
            createTicketModalInstance: null,
            localFilters: {
                name: this.filters?.name || "",
                company: this.filters?.company || "",
                created_from: this.filters?.created_from || "",
                created_to: this.filters?.created_to || "",
            },

            ticketToDelete: null,
            deleteTicketModalInstance: null,

            ticketsPage: {},

            allTicketsProject: null,
            allTicketsSearch: "",
            allTicketsModalInstance: null,

            openedModalsStack: [],

            localFlashSuccess: this.flash?.success || null,
            localFlashError: this.flash?.error || null,
        };
    },
    computed: {
        totalPages() {
            return Array.from({ length: this.projects.last_page }, (_, i) => i + 1);
        },

        filteredAllTickets() {
            if (!this.allTicketsProject) return [];

            const search = this.allTicketsSearch.trim().toLowerCase();
            if (!search) return this.allTicketsProject.tickets_all || [];

            return (this.allTicketsProject.tickets_all || []).filter((ticket) =>
                ticket.title.toLowerCase().includes(search)
            );
        },
    },
    mounted() {
        this.projects.data.forEach((project) => {
            if (project.tickets && project.tickets.current_page) {
                this.ticketsPage[project.id] = project.tickets.current_page;
            } else {
                this.ticketsPage[project.id] = 1;
            }
        });

        setTimeout(() => {
            this.localFlashSuccess = null;
            this.localFlashError = null;
        }, 4000);
    },
    methods: {
        toggleExpand(id) {
            if (this.expandedProjects.includes(id)) {
                this.expandedProjects = this.expandedProjects.filter((i) => i !== id);
            } else {
                this.expandedProjects.push(id);
                if (!this.ticketsPage[id]) {
                    this.ticketsPage[id] = 1;
                }
            }
        },

        formatDate(date) {
            if (!date) return "—";
            return new Date(date).toLocaleDateString("pt-BR");
        },

        submitFilters() {
            Inertia.get(
                "/projects",
                {
                    ...this.localFilters,
                    page: 1,
                },
                { preserveState: true, replace: true }
            );
        },

        clearFilters() {
            this.localFilters = {
                name: "",
                company: "",
                created_from: "",
                created_to: "",
            };
            this.submitFilters();
        },

        changePage(page) {
            Inertia.get(
                "/projects",
                {
                    ...this.localFilters,
                    page,
                },
                { preserveState: true, replace: true }
            );
        },

        openModal(modalRef) {
            if (!modalRef) return;

            const modalEl = this.$refs[modalRef];
            if (!modalEl) return;

            if (this.openedModalsStack.includes(modalRef)) return;

            if (this.openedModalsStack.length > 0) {
                const topModalRef = this.openedModalsStack[this.openedModalsStack.length - 1];
                const topModalEl = this.$refs[topModalRef];
                if (topModalEl) topModalEl.style.display = "none";
            }

            modalEl.style.display = "block";

            if (!this[modalRef + "Instance"]) {
                this[modalRef + "Instance"] = new bootstrap.Modal(modalEl, {
                    backdrop: "static",
                    keyboard: false,
                });

                modalEl.addEventListener("hidden.bs.modal", () => {
                    this.closeModal(modalRef);
                });
            }

            this[modalRef + "Instance"].show();
            this.openedModalsStack.push(modalRef);
        },

        closeModal(modalRef) {
            if (!modalRef) return;

            const modalEl = this.$refs[modalRef];
            if (!modalEl) return;

            if (this[modalRef + "Instance"]) {
                this[modalRef + "Instance"].hide();
            }

            this.openedModalsStack = this.openedModalsStack.filter((m) => m !== modalRef);

            modalEl.style.display = "none";

            if (this.openedModalsStack.length > 0) {
                const topModalRef = this.openedModalsStack[this.openedModalsStack.length - 1];
                const topModalEl = this.$refs[topModalRef];
                if (topModalEl) topModalEl.style.display = "block";
            }
        },

        openCreateTicketModal(project) {
            this.projectForNewTicket = project;
            this.openModal("createTicketModal");
        },
        closeCreateTicketModal() {
            this.projectForNewTicket = null;
            this.closeModal("createTicketModal");
        },
        onTicketCreated() {
            this.closeCreateTicketModal();
            this.submitFilters();
        },

        openTicketModal(ticket) {
            this.selectedTicket = ticket;
            this.openModal("ticketModal");
        },
        closeTicketModal() {
            this.selectedTicket = null;
            this.closeModal("ticketModal");
        },

        openDeleteModal(projectId) {
            this.projectToDelete = projectId;
            this.openModal("deleteModal");
        },
        closeDeleteModal() {
            this.projectToDelete = null;
            this.closeModal("deleteModal");
        },
        deleteProject() {
            if (!this.projectToDelete) return;

            Inertia.delete(`/projects/${this.projectToDelete}`, {
                onSuccess: () => {
                    this.closeDeleteModal();
                    this.submitFilters();
                },
            });
        },

        openDeleteTicketModal(ticket) {
            this.ticketToDelete = ticket;
            this.openModal("deleteTicketModal");
        },
        closeDeleteTicketModal() {
            this.ticketToDelete = null;
            this.closeModal("deleteTicketModal");
        },
        deleteTicket() {
            if (!this.ticketToDelete) return;

            Inertia.delete(`/tickets/${this.ticketToDelete.id}`, {
                onSuccess: () => {
                    this.closeDeleteTicketModal();
                    this.submitFilters();
                },
            });
        },

        openAllTicketsModal(project) {
            this.allTicketsProject = project;
            this.allTicketsSearch = "";
            this.openModal("allTicketsModal");
        },
        closeAllTicketsModal() {
            this.allTicketsProject = null;
            this.allTicketsSearch = "";
            this.closeModal("allTicketsModal");
        },
    },
};
