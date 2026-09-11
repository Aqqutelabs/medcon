<?php
declare(strict_types=1);

function site_activity_db(?string $path = null): PDO
{
    $db = new PDO('sqlite:' . ($path ?? dirname(__DIR__) . '/data/site-activity.sqlite'), null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $db->exec('PRAGMA busy_timeout=5000');
    $db->exec('CREATE TABLE IF NOT EXISTS settings (name TEXT PRIMARY KEY, value TEXT NOT NULL)');
    $db->exec('CREATE TABLE IF NOT EXISTS events (id TEXT PRIMARY KEY, visitor TEXT NOT NULL, page TEXT NOT NULL, kind TEXT NOT NULL, button TEXT NOT NULL, at INTEGER NOT NULL)');
    $db->exec('CREATE INDEX IF NOT EXISTS events_at ON events(at)');
    $db->exec('CREATE INDEX IF NOT EXISTS events_visitor ON events(visitor, at)');
    $statement = $db->prepare('INSERT OR IGNORE INTO settings VALUES (?, ?)');
    $statement->execute(['secret', bin2hex(random_bytes(32))]);
    return $db;
}

function site_activity_page(string $path): ?string
{
    if (!preg_match('~^/[a-zA-Z0-9/_-]*(?:\.php)?/?$~D', $path)) return null;
    $path = preg_replace('~(?:/index(?:\.php)?/?|/)$~', '/', $path);
    $path = preg_replace('~\.php$~', '', $path);
    $path = $path === '' ? '/' : $path;
    $first = explode('/', ltrim($path, '/'))[0];
    if (in_array(strtolower($first), ['app','includes','data','hooks','scripts','assets','docs','output','bxck'], true)) return null;
    $root = dirname(__DIR__);
    $candidate = $root . $path;
    if (is_dir($candidate)) { $candidate = rtrim($candidate, '/\\') . '/index.php'; $path = rtrim($path, '/') . '/'; }
    else $candidate .= '.php';
    $resolved = realpath($candidate);
    if (!$resolved || strpos(str_replace('\\', '/', $resolved), str_replace('\\', '/', $root) . '/') !== 0) return null;
    // Only rendered pages, never data collectors or processing endpoints.
    $source = file_get_contents($resolved);
    if (!preg_match('/render_header|includes\/(?:analytics|school-page)\.php|<html/i', $source)) return null;
    return $path;
}

function site_activity_record(PDO $db, array $event): bool
{
    $db->exec('BEGIN IMMEDIATE');
    try {
        $limit = $db->prepare('SELECT COUNT(*) FROM events WHERE visitor=? AND at>=?');
        $limit->execute([$event['visitor'], time()-60]);
        if ((int)$limit->fetchColumn() >= 120) { $db->exec('COMMIT'); return false; }
        $statement = $db->prepare('INSERT OR IGNORE INTO events (id,visitor,page,kind,button,at) VALUES (?,?,?,?,?,?)');
        $statement->execute([$event['id'],$event['visitor'],$event['page'],$event['kind'],$event['button'],time()]);
        $inserted = $statement->rowCount() === 1;
        $db->exec('DELETE FROM events WHERE at < ' . (time()-90*86400));
        $db->exec('COMMIT');
        return $inserted;
    } catch (Throwable $error) { $db->exec('ROLLBACK'); throw $error; }
}

function site_activity_markup(): string
{
    if (in_array($_SESSION['user']['role'] ?? '', ['admin','super_admin'], true)) return '';
    $root = str_replace('\\', '/', dirname(__DIR__));
    $filename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $relative = substr($filename, strlen($root));
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if ($relative === '' || substr($script, -strlen($relative)) !== $relative) return '';
    if (strpos($relative, '/app/') === 0) return '';
    $base = substr($script, 0, strlen($script)-strlen($relative));
    return '<script defer src="' . htmlspecialchars($base . '/scripts/internal-analytics.js', ENT_QUOTES, 'UTF-8') . '"></script>';
}
