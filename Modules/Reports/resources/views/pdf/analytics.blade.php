<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 24px 0 8px; color: #374151; }
        p { margin: 0; color: #6b7280; }
        .meta { margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
        .grid { margin-top: 8px; }
        .stat { display: inline-block; width: 24%; vertical-align: top; }
        .stat strong { display: block; font-size: 20px; margin-bottom: 2px; }
        .stat span { font-size: 11px; color: #6b7280; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Analytics Report</h1>
    <p class="meta">{{ $business->name }} · {{ $range[0]->format('M j, Y') }} – {{ $range[1]->format('M j, Y') }}</p>

    <h2>Summary</h2>
    <div class="grid">
        <div class="stat">
            <strong>{{ number_format($summary['impressions']) }}</strong>
            <span>Impressions</span>
        </div>
        <div class="stat">
            <strong>{{ number_format($summary['reach']) }}</strong>
            <span>Reach</span>
        </div>
        <div class="stat">
            <strong>{{ number_format($summary['engagements']) }}</strong>
            <span>Engagements</span>
        </div>
        <div class="stat">
            <strong>{{ $summary['engagement_rate'] }}%</strong>
            <span>Engagement rate</span>
        </div>
    </div>

    <h2>By platform</h2>
    @if ($platforms->isEmpty())
        <p>No platform data in this range.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Platform</th>
                    <th>Impressions</th>
                    <th>Engagements</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($platforms as $platform)
                    <tr>
                        <td>{{ ucfirst($platform['platform']) }}</td>
                        <td>{{ number_format($platform['impressions']) }}</td>
                        <td>{{ number_format($platform['engagements']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">Generated {{ $generated_at->format('M j, Y g:i A') }} · Social Media Scheduler</div>
</body>
</html>
