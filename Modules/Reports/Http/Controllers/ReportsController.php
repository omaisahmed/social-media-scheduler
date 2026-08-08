<?php

declare(strict_types=1);

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Reports\Models\ReportExport;
use Modules\Reports\Services\ReportsService;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportsController
{
    public function __construct(protected ReportsService $reports)
    {
    }

    public function index(Request $request): View
    {
        $exports = ReportExport::query()
            ->where('business_id', $request->user()->business_id)
            ->latest()
            ->paginate(15);

        return view('reports::index', ['exports' => $exports]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', [ReportExport::TYPE_ANALYTICS, ReportExport::TYPE_CONTENT])],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $export = ReportExport::create([
            'business_id' => $request->user()->business_id,
            'user_id' => $request->user()->getKey(),
            'type' => $validated['type'],
            'filters' => ['from' => $validated['from'], 'to' => $validated['to']],
            'status' => ReportExport::STATUS_PENDING,
        ]);

        try {
            $this->reports->generate($export);

            return redirect()->route('reports.index')->with('status', 'report-ready');
        } catch (\Throwable) {
            return redirect()->route('reports.index')->with('status', 'report-failed');
        }
    }

    public function download(Request $request, int $exportId): RedirectResponse|StreamedResponse
    {
        $export = ReportExport::query()
            ->where('business_id', $request->user()->business_id)
            ->findOrFail($exportId);

        abort_unless($export->completed(), 404);

        return response()->streamDownload(function () use ($export) {
            echo \Illuminate\Support\Facades\Storage::disk('public')->get($export->file_path);
        }, basename($export->file_path), ['Content-Type' => 'application/pdf']);
    }

    public function destroy(Request $request, int $exportId): RedirectResponse
    {
        $export = ReportExport::query()
            ->where('business_id', $request->user()->business_id)
            ->findOrFail($exportId);

        if ($export->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($export->file_path);
        }

        $export->delete();

        return redirect()->route('reports.index')->with('status', 'report-deleted');
    }
}
