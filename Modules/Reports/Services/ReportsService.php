<?php

declare(strict_types=1);

namespace Modules\Reports\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use Modules\Analytics\Services\AnalyticsService;
use Modules\Reports\Models\ReportExport;

final class ReportsService
{
    public function __construct(protected AnalyticsService $analytics)
    {
    }

    public function generate(ReportExport $export): void
    {
        $export->update(['status' => ReportExport::STATUS_PROCESSING]);

        try {
            $path = match ($export->type) {
                ReportExport::TYPE_ANALYTICS => $this->generateAnalyticsPdf($export),
                ReportExport::TYPE_CONTENT => $this->generateContentPdf($export),
                default => throw new \InvalidArgumentException("Unsupported report type [{$export->type}]."),
            };

            $export->update([
                'status' => ReportExport::STATUS_COMPLETED,
                'file_path' => $path,
                'generated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $export->update(['status' => ReportExport::STATUS_FAILED]);
            throw $e;
        }
    }

    protected function generateAnalyticsPdf(ReportExport $export): string
    {
        $filters = $export->filters ?? [];
        $start = CarbonImmutable::parse($filters['from'] ?? now()->subDays(30))->startOfDay();
        $end = CarbonImmutable::parse($filters['to'] ?? now())->endOfDay();

        $summary = $this->analytics->aggregate($export->business_id, $start, $end);
        $platforms = $this->analytics->byPlatform($export->business_id, $start, $end);

        $pdf = app('dompdf.wrapper')->loadView('reports::pdf.analytics', [
            'business' => $export->business,
            'generated_at' => now(),
            'range' => [$start, $end],
            'summary' => $summary,
            'platforms' => $platforms,
        ]);

        $disk = Storage::disk('public');
        $directory = 'reports/'.$export->business_id;
        $disk->makeDirectory($directory);

        $filename = sprintf('analytics-report-%s.pdf', $export->getKey());
        $disk->put($directory.'/'.$filename, $pdf->output());

        return $directory.'/'.$filename;
    }

    protected function generateContentPdf(ReportExport $export): string
    {
        $filters = $export->filters ?? [];
        $start = CarbonImmutable::parse($filters['from'] ?? now()->subDays(30))->startOfDay();
        $end = CarbonImmutable::parse($filters['to'] ?? now())->endOfDay();

        $posts = \Modules\Posts\Models\Post::query()
            ->where('business_id', $export->business_id)
            ->whereBetween('scheduled_at', [$start, $end])
            ->with('accounts')
            ->latest('scheduled_at')
            ->get();

        $pdf = app('dompdf.wrapper')->loadView('reports::pdf.content', [
            'business' => $export->business,
            'generated_at' => now(),
            'range' => [$start, $end],
            'posts' => $posts,
        ]);

        $disk = Storage::disk('public');
        $directory = 'reports/'.$export->business_id;
        $disk->makeDirectory($directory);

        $filename = sprintf('content-report-%s.pdf', $export->getKey());
        $disk->put($directory.'/'.$filename, $pdf->output());

        return $directory.'/'.$filename;
    }
}
