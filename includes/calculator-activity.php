<?php
declare(strict_types=1);

function calculator_event_labels(): array
{
    return ['view'=>'Calculator visits', 'engaged'=>'Visits with interaction', 'change'=>'Option changes', 'share_attempt'=>'Share attempts', 'share_success'=>'Native shares completed', 'copy_success'=>'Share links copied', 'pdf_request'=>'PDF / print requests', 'shared_visit'=>'Shared-link visits'];
}

// Aggregate counters and only the latest 100 events; no names, IPs or contact details.
function calculator_activity(?array $event = null, ?string $path = null): array
{
    $path = $path ?? dirname(__DIR__) . '/data/calculator-activity.json';
    $empty = ['totals'=>[], 'days'=>[], 'recent'=>[]];
    if ($event === null && !file_exists($path)) return $empty;
    $handle = @fopen($path, $event === null ? 'rb' : 'c+');
    if (!$handle) throw new RuntimeException('Calculator activity storage is unavailable.');
    try {
        if (!flock($handle, $event === null ? LOCK_SH : LOCK_EX)) throw new RuntimeException('Cannot lock activity storage.');
        $raw = stream_get_contents($handle);
        $data = $raw === '' ? $empty : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if ($event !== null) {
            foreach ($data['recent'] as $previous) {
                if ($previous['id'] === $event['id']) return $data;
            }
            $now = time();
            $recentCount = 0;
            foreach ($data['recent'] as $previous) {
                if ($previous['visit'] === $event['visit'] && strtotime($previous['at']) > $now - 60) $recentCount++;
            }
            if ($recentCount >= 60) return $data;
            $event['at'] = gmdate('c', $now);
            $day = gmdate('Y-m-d', $now);
            $type = $event['event'];
            $data['totals'][$type] = ($data['totals'][$type] ?? 0) + 1;
            $data['days'][$day][$type] = ($data['days'][$day][$type] ?? 0) + 1;
            array_unshift($data['recent'], $event);
            $data['recent'] = array_slice($data['recent'], 0, 100);
            $encoded = json_encode($data, JSON_THROW_ON_ERROR);
            rewind($handle);
            if (fwrite($handle, $encoded) !== strlen($encoded) || !ftruncate($handle, strlen($encoded)) || !fflush($handle)) throw new RuntimeException('Cannot save calculator activity.');
        }
        return $data;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
