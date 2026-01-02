<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\GoogleServicesErrorHandlerInterface;
use App\Exceptions\GmailAuthenticationException;
use App\Exceptions\GmailQuotaExceededException;
use App\Exceptions\GmailRateLimitException;
use App\Exceptions\GoogleServicesException;
use App\Exceptions\GoogleVerificationException;
use App\Exceptions\InvalidEmailDomainException;
use App\Services\GoogleServicesErrorHandler;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Two\InvalidStateException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for GoogleServicesErrorHandler
 *
 * @see Requirements 7.1, 7.2, 7.4, 7.5
 */
class GoogleServicesErrorHandlerTest extends TestCase
{
    use RefreshDatabase;

    private GoogleServicesErrorHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new GoogleServicesErrorHandler;
    }

    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(GoogleServicesErrorHandlerInterface::class);

        $this->assertInstanceOf(GoogleServicesErrorHandler::class, $service);
    }

    #[Test]
    public function handles_invalid_email_domain_exception(): void
    {
        $exception = new InvalidEmailDomainException('test@gmail.com', ['motac.gov.my']);

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            'test@gmail.com',
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_DOMAIN, $response['error_type']);
        $this->assertEquals(GoogleServicesException::SERVICE_SSO, $response['service_type']);
    }

    #[Test]
    public function handles_invalid_state_exception(): void
    {
        $exception = new InvalidStateException('Invalid state');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            null,
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_OAUTH_STATE, $response['error_type']);
    }

    #[Test]
    public function handles_connect_exception(): void
    {
        $request = new Request('GET', 'https://accounts.google.com');
        $exception = new ConnectException('Connection failed', $request);

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            null,
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_NETWORK, $response['error_type']);
        $this->assertTrue($response['recoverable']);
    }

    #[Test]
    public function handles_google_verification_exception(): void
    {
        $exception = GoogleVerificationException::testUserRequired('test@motac.gov.my', 'testing');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_OAUTH,
            'test@motac.gov.my',
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_VERIFICATION, $response['error_type']);
        $this->assertTrue($response['fallback_available']);
    }

    #[Test]
    public function handles_gmail_quota_exceeded_exception(): void
    {
        $exception = new GmailQuotaExceededException('Quota exceeded', 500, 500);

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_GMAIL,
            null,
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        // The exception is handled and converted to a general error
        $this->assertNotEmpty($response['user_message']);
    }

    #[Test]
    public function handles_gmail_rate_limit_exception(): void
    {
        $exception = new GmailRateLimitException('Rate limit exceeded', 60);

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_GMAIL,
            null,
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
    }

    #[Test]
    public function handles_gmail_authentication_exception(): void
    {
        $exception = GmailAuthenticationException::tokenExpired('oauth');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_GMAIL,
            null,
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_AUTHENTICATION, $response['error_type']);
    }

    #[Test]
    public function handles_general_exception(): void
    {
        $exception = new \Exception('Unknown error');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            'test@motac.gov.my',
            GoogleServicesErrorHandlerInterface::RESPONSE_ARRAY
        );

        $this->assertFalse($response['success']);
        $this->assertEquals(GoogleServicesException::TYPE_GENERAL, $response['error_type']);
    }

    #[Test]
    public function returns_redirect_response_by_default(): void
    {
        $exception = new InvalidEmailDomainException('test@gmail.com');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            'test@gmail.com'
        );

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    #[Test]
    public function returns_json_response_when_requested(): void
    {
        $exception = new InvalidEmailDomainException('test@gmail.com');

        $response = $this->handler->handle(
            $exception,
            GoogleServicesException::SERVICE_SSO,
            'test@gmail.com',
            GoogleServicesErrorHandlerInterface::RESPONSE_JSON
        );

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('error', $response);
        $this->assertFalse($response['success']);
    }

    #[Test]
    public function get_error_message_returns_localized_message(): void
    {
        $message = $this->handler->getErrorMessage(GoogleServicesException::TYPE_DOMAIN);

        $this->assertNotEmpty($message);
        $this->assertIsString($message);
    }

    #[Test]
    public function get_help_text_returns_localized_help(): void
    {
        $helpText = $this->handler->getHelpText(GoogleServicesException::TYPE_DOMAIN);

        $this->assertNotEmpty($helpText);
        $this->assertIsString($helpText);
    }

    #[Test]
    public function should_trigger_fallback_for_network_errors(): void
    {
        $this->assertTrue($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_NETWORK));
        $this->assertTrue($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_SERVICE_UNAVAILABLE));
        $this->assertTrue($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_QUOTA));
        $this->assertTrue($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_RATE_LIMIT));
    }

    #[Test]
    public function should_not_trigger_fallback_for_domain_errors(): void
    {
        $this->assertFalse($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_DOMAIN));
        $this->assertFalse($this->handler->shouldTriggerFallback(GoogleServicesException::TYPE_GENERAL));
    }

    #[Test]
    public function google_services_exception_provides_user_message(): void
    {
        $exception = new GoogleServicesException(
            'Technical error message',
            GoogleServicesException::TYPE_DOMAIN,
            GoogleServicesException::SERVICE_SSO
        );

        $userMessage = $exception->getUserMessage();

        $this->assertNotEmpty($userMessage);
        $this->assertNotEquals('Technical error message', $userMessage);
    }

    #[Test]
    public function google_services_exception_provides_help_text(): void
    {
        $exception = new GoogleServicesException(
            'Technical error message',
            GoogleServicesException::TYPE_DOMAIN,
            GoogleServicesException::SERVICE_SSO
        );

        $helpText = $exception->getHelpText();

        $this->assertNotEmpty($helpText);
    }

    #[Test]
    public function google_services_exception_determines_fallback_correctly(): void
    {
        $networkException = new GoogleServicesException(
            'Network error',
            GoogleServicesException::TYPE_NETWORK,
            GoogleServicesException::SERVICE_SSO
        );

        $domainException = new GoogleServicesException(
            'Domain error',
            GoogleServicesException::TYPE_DOMAIN,
            GoogleServicesException::SERVICE_SSO
        );

        $this->assertTrue($networkException->shouldOfferFallback());
        $this->assertFalse($domainException->shouldOfferFallback());
    }

    #[Test]
    public function google_verification_exception_provides_status(): void
    {
        $exception = GoogleVerificationException::testUserRequired('test@motac.gov.my', 'testing');

        $this->assertEquals('testing', $exception->getVerificationStatus());
        $this->assertEquals('test@motac.gov.my', $exception->getEmail());
        $this->assertFalse($exception->isTestUser());
    }

    #[Test]
    public function gmail_authentication_exception_provides_reason(): void
    {
        $exception = GmailAuthenticationException::tokenExpired('oauth');

        $this->assertEquals(GmailAuthenticationException::REASON_TOKEN_EXPIRED, $exception->getReason());
        $this->assertEquals('oauth', $exception->getAuthMethod());
        $this->assertTrue($exception->canReauthenticate());
    }

    #[Test]
    public function gmail_authentication_exception_credentials_missing_cannot_reauthenticate(): void
    {
        $exception = GmailAuthenticationException::credentialsMissing();

        $this->assertEquals(GmailAuthenticationException::REASON_CREDENTIALS_MISSING, $exception->getReason());
        $this->assertFalse($exception->canReauthenticate());
    }
}
