<?php
// Global application logger and error handling configuration

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Sentry\ClientBuilder;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\State\Hub;

// Determine log level from environment, default to "warning"
$levelName = getenv('LOG_LEVEL') ?: 'warning';
$level = Logger::toMonologLevel($levelName);

// Ensure logs directory exists
$logDir = __DIR__ . '/../../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Create logger with rotating file handler (daily, 7 files retained)
$logger = new Logger('gamenight');
$rotating = new RotatingFileHandler($logDir . '/app.log', 7, $level, true, 0664);
$logger->pushHandler($rotating);

// Optional Sentry integration for error tracking
$dsn = getenv('SENTRY_DSN');
if ($dsn) {
    $hub = new Hub(ClientBuilder::create(['dsn' => $dsn])->getClient());
    $logger->pushHandler(new SentryHandler($hub, Logger::ERROR));
}

/**
 * Dispatch webhook and/or email alerts for high-severity errors.
 */
function send_alert(string $message): void
{
    $webhook = getenv('ALERT_WEBHOOK');
    if ($webhook) {
        $ch = curl_init($webhook);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['text' => $message]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 5,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    $email = getenv('ALERT_EMAIL');
    if ($email) {
        @mail($email, 'GameNight Alert', $message);
    }
}

/**
 * Simple helper to log messages from anywhere.
 */
function log_message(string $level, string $message, array $context = []): void
{
    global $logger;
    $logger->log(Logger::toMonologLevel($level), $message, $context);
}

// Convert PHP errors to log entries and optional alerts
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    global $logger;
    $map = [
        E_ERROR => Logger::ERROR,
        E_WARNING => Logger::WARNING,
        E_PARSE => Logger::CRITICAL,
        E_NOTICE => Logger::NOTICE,
        E_CORE_ERROR => Logger::ERROR,
        E_CORE_WARNING => Logger::WARNING,
        E_COMPILE_ERROR => Logger::CRITICAL,
        E_COMPILE_WARNING => Logger::WARNING,
        E_USER_ERROR => Logger::ERROR,
        E_USER_WARNING => Logger::WARNING,
        E_USER_NOTICE => Logger::NOTICE,
        E_RECOVERABLE_ERROR => Logger::ERROR,
        E_DEPRECATED => Logger::NOTICE,
        E_USER_DEPRECATED => Logger::NOTICE,
    ];
    $level = $map[$severity] ?? Logger::ERROR;
    $logger->log($level, $message, ['file' => $file, 'line' => $line]);
    if ($level >= Logger::ERROR) {
        send_alert($message . ' in ' . $file . ':' . $line);
    }
    return false; // Allow PHP internal handler to run as well
});

// Handle uncaught exceptions
set_exception_handler(static function (Throwable $e): void {
    global $logger;
    $logger->error($e->getMessage(), ['exception' => $e]);
    send_alert('Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
});
