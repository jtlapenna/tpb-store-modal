<?php

namespace UpsFreeVendor\Octolize\Ups\RestApi;

use UpsFreeVendor\GuzzleHttp\Client;
use UpsFreeVendor\GuzzleHttp\Exception\ClientException;
use UpsFreeVendor\Psr\Http\Message\ResponseInterface;
use UpsFreeVendor\Psr\Log\LoggerAwareInterface;
use UpsFreeVendor\Psr\Log\LoggerAwareTrait;
use UpsFreeVendor\Psr\Log\LoggerInterface;
use UpsFreeVendor\Psr\Log\NullLogger;
class RestApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    const TRANSACTION_SRC = 'Octolize Plugin';
    const API_VERSION = 'v2205';
    /**
     * @var Client
     */
    private $http_client;
    /**
     * @var RestApiToken|null
     */
    private ?RestApiToken $token;
    /**
     * @var false|mixed
     */
    private $is_testing;
    public function __construct(Client $http_client, ?RestApiToken $token, LoggerInterface $logger = null, $is_testing = \false)
    {
        $this->http_client = $http_client;
        $this->token = $token;
        $this->logger = $logger ?? new NullLogger();
        $this->is_testing = $is_testing;
    }
    public static function create(?RestApiToken $token = null, bool $is_testing = \false): self
    {
        $http_client = new Client(['timeout' => 30]);
        return new self($http_client, $token, null, $is_testing);
    }
    public function set_token(RestApiToken $token): void
    {
        $this->token = $token;
    }
    private function get_api_host(): string
    {
        return $this->is_testing ? 'wwwcie.ups.com' : 'onlinetools.ups.com';
    }
    private function get_api_url($endpoint): string
    {
        return sprintf('https://%s/%s', $this->get_api_host(), $endpoint);
    }
    public function shipment_void(string $tracking_number): \stdClass
    {
        $url = $this->get_api_url(sprintf('api/shipments/%s/void/cancel/%s', self::API_VERSION, $tracking_number));
        $transId = md5($tracking_number . time() . rand(0, 1000));
        $headers = ['Authorization' => 'Bearer ' . $this->token->get_token(), 'Content-Type' => 'application/json', 'transId' => $transId, 'transactionSrc' => self::TRANSACTION_SRC];
        $this->logger->debug('UPS API Request', ['url' => $url]);
        $response = $this->delete_request($url, $headers);
        $this->logger->debug('UPS API Response', ['response' => json_encode($response)]);
        return $response;
    }
    public function shipment_send(array $payload, $additional_address_validation = ''): \stdClass
    {
        $url = $this->get_api_url(sprintf('api/shipments/%s/ship?additionaladdressvalidation=%s', self::API_VERSION, $additional_address_validation));
        $transId = md5(json_encode($payload) . time() . rand(0, 1000));
        $headers = ['Authorization' => 'Bearer ' . $this->token->get_token(), 'Content-Type' => 'application/json', 'transId' => $transId, 'transactionSrc' => self::TRANSACTION_SRC];
        $this->logger->debug('UPS API Request', ['url' => $url, 'body' => json_encode($payload)]);
        $response = $this->post_request($url, json_encode($payload));
        $this->logger->debug('UPS API Response', ['response' => json_encode($response)]);
        return $response;
    }
    public function shipment_recover_label(array $payload): \stdClass
    {
        $url = $this->get_api_url(sprintf('api/labels/%s/recovery', self::API_VERSION));
        $response = $this->post_request($url, json_encode($payload));
        return $response;
    }
    public function rating(string $request_option, array $payload, string $additional_info = ''): \stdClass
    {
        $url = $this->get_api_url(sprintf('api/rating/%s/%s?additionalinfo=%s', self::API_VERSION, $request_option, $additional_info));
        return $this->post_request($url, json_encode($payload));
    }
    public function locator(string $request_option, array $payload): \stdClass
    {
        $url = $this->get_api_url(sprintf('api/locations/%s/search/availabilities/%s', self::API_VERSION, $request_option));
        return $this->post_request($url, json_encode($payload));
    }
    public function create_token(string $client_id, string $client_secret): array
    {
        $url = $this->get_api_url('security/v1/oauth/token');
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded', 'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret)];
        try {
            $response = $this->http_client->post($url, ['headers' => $headers, 'form_params' => ['grant_type' => 'client_credentials']]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $this->throw_exception_from_response($e->getResponse());
            }
            $this->logger->error($e->getMessage());
            throw new RestApiException($e->getMessage());
        }
        if ($response->getStatusCode() !== 200) {
            $this->throw_exception_from_response($response);
        }
        $content = json_decode($response->getBody()->getContents(), \true);
        $this->logger->debug('UPS API Response', ['response' => json_encode($content)]);
        return $content;
    }
    private function post_request($url, $body): \stdClass
    {
        $headers = ['Authorization' => 'Bearer ' . $this->token->get_token(), 'Content-Type' => 'application/json', 'transId' => md5($body . time() . rand(0, 1000)), 'transactionSrc' => self::TRANSACTION_SRC];
        $this->logger->debug('UPS API Request', ['url' => $url, 'body' => $body, 'headers' => $headers]);
        try {
            $response = $this->http_client->post($url, ['headers' => $headers, 'body' => $body]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $this->throw_exception_from_response($e->getResponse());
            }
            $this->logger->error($e->getMessage());
            throw new RestApiException($e->getMessage());
        }
        if ($response->getStatusCode() !== 200) {
            $this->throw_exception_from_response($response);
        }
        $content = json_decode($response->getBody()->getContents(), \false);
        $this->logger->debug('UPS API Response', ['response' => json_encode($content)]);
        return $content;
    }
    private function delete_request($url, $headers): \stdClass
    {
        try {
            $response = $this->http_client->delete($url, ['headers' => $headers]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $this->throw_exception_from_response($e->getResponse());
            }
            $this->logger->error($e->getMessage());
            throw new RestApiException($e->getMessage());
        }
        if ($response->getStatusCode() !== 200) {
            $this->throw_exception_from_response($response);
        }
        return json_decode($response->getBody()->getContents(), \false);
    }
    private function throw_exception_from_response(ResponseInterface $response): void
    {
        $body = json_decode($response->getBody()->getContents(), \true);
        if (!is_array($body) && !empty($body)) {
            throw new RestApiException(sprintf(__('Invalid response: %s', 'flexible-shipping-ups'), print_r($body, \true)), $response->getStatusCode());
        }
        if (is_array($body) && isset($body['errors'])) {
            $message = '';
            foreach ($body['errors'] as $error) {
                $message .= $error['message'] . ', ';
            }
            throw new RestApiException(trim($message, ', '));
        }
        $headers = $response->getHeaders();
        if ($response->getStatusCode() !== 200 && isset($headers['errordescription']) && is_array($headers['errordescription'])) {
            $message = '';
            $code = '';
            foreach ($headers['errordescription'] as $key => $error) {
                if (isset($headers['errorcode'][$key])) {
                    $code = $headers['errorcode'][$key];
                    $message .= $headers['errorcode'][$key] . ': ';
                }
                $message .= $error . ', ';
            }
            throw new RestApiException(trim($message, ', '), $code);
        }
    }
}
