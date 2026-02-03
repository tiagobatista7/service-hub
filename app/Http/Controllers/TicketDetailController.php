<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketDetailRequest;
use App\Http\Requests\UpdateTicketDetailStatusRequest;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Services\TicketDetailService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketDetailController extends Controller
{
    public function __construct(protected TicketDetailService $ticketDetailService) {}

    public function create(Ticket $ticket)
    {
        return Inertia::render('Tickets/Create', [
            'ticket' => $ticket,
        ]);
    }

    public function show(TicketDetail $ticketDetail)
    {
        return Inertia::render('Tickets/Show', [
            'ticketDetail' => $ticketDetail,
        ]);
    }

    public function store(StoreTicketDetailRequest $request, Ticket $ticket)
    {
        $this->ticketDetailService->createDetail($ticket, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Detalhes do ticket criados com sucesso!');
    }

    public function update(StoreTicketDetailRequest $request, TicketDetail $ticketDetail)
    {
        $data = $request->validated();

        $ticketDetail->technical_data = $data['technical_data'] ?? [];
        $ticketDetail->details_text = $data['details_text'] ?? null;

        $ticketDetail->save();

        return redirect()->route('projects.index')
            ->with('success', 'Detalhes do ticket atualizados com sucesso!');
    }

    public function updateStatus(UpdateTicketDetailStatusRequest $request, TicketDetail $ticketDetail)
    {
        $this->ticketDetailService->updateStatus($ticketDetail, $request->status);

        return redirect()->back()
            ->with('success', 'Status do detalhe do ticket atualizado com sucesso!');
    }

    public function destroy(TicketDetail $ticketDetail)
    {
        $this->ticketDetailService->deleteDetail($ticketDetail->id);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Detalhes do ticket excluídos com sucesso!');
    }

    public function allDetails(Request $request, Ticket $ticket)
    {
        return response()->json(
            $this->ticketDetailService->getDetailsByTicket(
                $ticket->id,
                $request->input('search')
            )
        );
    }
}
