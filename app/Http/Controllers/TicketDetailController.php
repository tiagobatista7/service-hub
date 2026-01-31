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
    public function __construct(protected TicketDetailService $ticketDetailService)
    {
        //
    }

    public function create(Ticket $ticket)
    {
        return Inertia::render('TicketDetails/Create', [
            'ticket' => $ticket,
        ]);
    }

    public function show(int $ticketDetailId)
    {
        $ticketDetail = $this->ticketDetailService->findDetailOrFail($ticketDetailId);

        return Inertia::render('TicketDetails/Show', [
            'ticketDetail' => $ticketDetail,
        ]);
    }

    public function store(StoreTicketDetailRequest $request, Ticket $ticket)
    {
        $this->ticketDetailService->createDetail($ticket, $request->validated());

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Detalhes do ticket criados com sucesso!');
    }

    public function update(StoreTicketDetailRequest $request, TicketDetail $ticketDetail)
    {
        $this->ticketDetailService->updateDetail($ticketDetail, $request->validated());

        return redirect()->route('tickets.show', $ticketDetail->ticket_id)
            ->with('success', 'Detalhes do ticket atualizados com sucesso!');
    }

    public function updateStatus(UpdateTicketDetailStatusRequest $request, TicketDetail $ticketDetail)
    {
        $this->ticketDetailService->updateStatus($ticketDetail, $request->status);

        return redirect()->back()
            ->with('success', 'Status do detalhe do ticket atualizado com sucesso!');
    }

    public function destroy(int $ticketDetailId)
    {
        $ticketId = $this->ticketDetailService->deleteDetail($ticketDetailId);

        return redirect()->route('tickets.show', $ticketId)
            ->with('success', 'Detalhes do ticket excluídos com sucesso!');
    }

    public function allDetails(Request $request, int $ticketId)
    {
        $details = $this->ticketDetailService->getDetailsByTicket($ticketId, $request->input('search'));

        return response()->json($details);
    }
}
