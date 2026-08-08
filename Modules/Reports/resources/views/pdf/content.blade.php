<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Content Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        p { margin: 0; color: #6b7280; }
        .meta { margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
        .content { font-size: 11px; color: #374151; }
        .status { font-size: 10px; font-weight: bold; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Content Report</h1>
    <p class="meta">{{ $business->name }} · {{ $range[0]->format('M j, Y') }} – {{ $range[1]->format('M j, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 14%;">Scheduled</th>
                <th style="width: 56%;">Content</th>
                <th style="width: 15%;">Platforms</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->scheduled_at->format('M j, Y g:i A') }}</td>
                    <td class="content">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 200) }}
                    </td>
                    <td>
                        @foreach ($post->accounts as $account)
                            {{ ucfirst($account->platform) }}<br>
                        @endforeach
                    </td>
                    <td class="status">{{ ucfirst($post->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No posts scheduled in this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated {{ $generated_at->format('M j, Y g:i A') }} · Social Media Scheduler</div>
</body>
</html>
