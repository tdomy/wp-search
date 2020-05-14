<?php

namespace App\Middleware;

use App\Middleware\Exceptions\InvalidSignatureException;
use App\Middleware\Exceptions\InvalidTimestampException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class VerificationMiddleware implements Middleware
{
    private const TIMESTAMP_HEADER = 'X-Slack-Request-Timestamp';
    private const SIGNATURE_HEADER = 'X-Slack-Signature';

    /**
     * @var string $signing_secret
     */
    private $signing_secret;

    public function __construct(string $signing_secret)
    {
        $this->signing_secret = $signing_secret;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->verifyRequestTimestamp($request);

        $this->verifySignature($request);

        return $handler->handle($request);
    }

    /**
     * Verify Request-Timestamp header
     *
     * @param Request $request
     * @return void
     * @throws InvalidTimestampException
     */
    private function verifyRequestTimestamp(Request $request): void
    {
        // If header does not contain timestamp, $timestamp is 0.
        $timestamp = (int) $request->getHeaderLine(self::TIMESTAMP_HEADER);

        if (abs(time() - $timestamp) > 60 * 5) {
            throw new InvalidTimestampException();
        }
    }

    /**
     * Verify Signature header
     *
     * @param Request $request
     * @return void
     * @throws InvalidSignatureException
     */
    private function verifySignature(Request $request): void
    {
        $body = $request->getBody();
        $base_string = sprintf("v0:%s:%s", $request->getHeaderLine(self::TIMESTAMP_HEADER), (string) $body);
        $signature = 'v0=' . hash_hmac('sha256', $base_string, $this->signing_secret);

        if ($signature !== $request->getHeaderLine(self::SIGNATURE_HEADER)) {
            throw new InvalidSignatureException();
        }
    }
}