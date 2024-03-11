<?php

require_once '../../vendor/autoload.php';

use App\Spam\{SpamChecker, RegexSpamCheckStrategy, ApiSpamCheckStrategy};
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

header('Content-Type: application/json');

$logger = new Logger('spam_checker');
$logger->pushHandler(new StreamHandler(__DIR__ . '/spam_checker.log', Logger::INFO));

try {
    $data = json_decode(file_get_contents('php://input'), true, flags: JSON_THROW_ON_ERROR);
    $email = $data['email'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException('Invalid email format');
    }

    $regexStrategy = new RegexSpamCheckStrategy(['/spam-pattern/', '/another-pattern/']);
    $apiStrategy = new ApiSpamCheckStrategy('https://example.com/spam-check-api');

    $spamChecker = new SpamChecker([$regexStrategy, $apiStrategy], $logger);
    $isSpam = $spamChecker->isSpam($email);

    echo json_encode(['isSpam' => $isSpam]);
} catch (\InvalidArgumentException $e) {
    http_response_code(Response::HTTP_BAD_REQUEST);
    echo json_encode(['error' => $e->getMessage()]);
} catch (JsonException $e) {
    http_response_code(Response::HTTP_BAD_REQUEST);
    echo json_encode(['error' => 'Invalid JSON input']);
} catch (Throwable $e) {
    $logger->error("Internal server error: {$e->getMessage()}");
    http_response_code(Response::HTTP_INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => 'Internal server error']);
}
