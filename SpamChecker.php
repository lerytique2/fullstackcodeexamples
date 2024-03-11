<?php

declare(strict_types=1);

namespace App\Spam;

use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface for spam check strategies.
 * Defines a contract for classes that implement spam checking logic.
 */
interface SpamCheckStrategy
{
    public function isSpam(string $email): bool;
}

/**
 * Regex-based spam check strategy.
 * Uses regular expressions to determine if an email is spam.
 */
class RegexSpamCheckStrategy implements SpamCheckStrategy
{
    private array $patterns;

    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    public function isSpam(string $email): bool
    {
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return true;
            }
        }
        return false;
    }
}

/**
 * API-based spam check strategy.
 * Uses an external API to determine if an email is spam.
 */
class ApiSpamCheckStrategy implements SpamCheckStrategy
{
    private string $apiEndpoint;

    public function __construct(string $apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }

    public function isSpam(string $email): bool
    {
        $response = @file_get_contents($this->apiEndpoint . '?email=' . urlencode($email));
        if ($response === false) {
            throw new RuntimeException('Failed to contact spam check API');
        }
        $result = json_decode($response, true);
        return $result['isSpam'] ?? false;
    }
}

/**
 * SpamChecker class that uses multiple strategies to check for spam.
 * Aggregates different spam check strategies and logs spam detections.
 */
class SpamChecker
{
    private array $strategies;
    private LoggerInterface $logger;

    public function __construct(array $strategies, LoggerInterface $logger)
    {
        $this->strategies = $strategies;
        $this->logger = $logger;
    }

    public function isSpam(string $email): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->isSpam($email)) {
                $this->logger->info("Email $email detected as spam.");
                return true;
            }
        }
        return false;
    }
}
