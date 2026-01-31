<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketDetailController extends Controller
{
    public function create(Ticket $ticket)
    {
        return Inertia::render('TicketDetails/Create', [
            'ticket' => $ticket,
        ]);
    }

    public function show($ticketDetailId)
    {
        $ticketDetail = TicketDetail::findOrFail($ticketDetailId);
        return Inertia::render('TicketDetails/Show', [
            'ticketDetail' => $ticketDetail,
        ]);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'technical_data' => 'nullable|json',
            'status' => 'required|string|max:50',
        ]);

        $ticketDetail = $ticket->details()->create([
            'technical_data' => $data['technical_data'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Detalhes do ticket criados com sucesso!');
    }

    public function update(Request $request, TicketDetail $ticketDetail)
    {
        $data = $request->validate([
            'technical_data' => 'nullable|json',
            'status' => 'required|string|max:50',
        ]);

        $ticketDetail->update([
            'technical_data' => $data['technical_data'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('tickets.show', $ticketDetail->ticket_id)
            ->with('success', 'Detalhes do ticket atualizados com sucesso!');
    }

    public function updateStatus(Request $request, TicketDetail $ticketDetail)
    {
        $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $ticketDetail->status = $request->status;
        $ticketDetail->save();

        return redirect()->back()
            ->with('success', 'Status do detalhe do ticket atualizado com sucesso!');
    }

    public function destroy($ticketDetailId)
    {
        $ticketDetail = TicketDetail::findOrFail($ticketDetailId);
        $ticketId = $ticketDetail->ticket_id;

        $ticketDetail->delete();

        return redirect()->route('tickets.show', $ticketId)
            ->with('success', 'Detalhes do ticket excluídos com sucesso!');
    }

    public function allDetails(Request $request, $ticketId)
    {
        $query = TicketDetail::where('ticket_id', $ticketId);

        if ($request->filled('search')) {
            $query->where('status', 'like', '%' . $request->search . '%');
        }

        $details = $query->orderBy('id', 'desc')->paginate(20);

        return response()->json($details);
    }
}
