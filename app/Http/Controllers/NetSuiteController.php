<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class NetSuiteController extends Controller
{
    /**
     * Execute a SuiteQL query against NetSuite
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function executeSuiteQL(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }

        try {
            $response = $this->sendSuiteQLQuery($query);
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a SuiteQL query to NetSuite REST API
     *
     * @param string $query
     * @return array
     * @throws Exception
     */
    public function sendSuiteQLQuery(string $query): array
    {
        $url = config('netsuite.host') . '/services/rest/query/v1/suiteql';

        // Build authorization header
        $authHeader = $this->buildOAuthHeader($url, 'POST');

        // Make the request to NetSuite
        $response = Http::withHeaders([
            'Authorization' => $authHeader,
            'Content-Type' => 'application/json',
            'Prefer' => 'transient'
        ])->post($url, [
            'q' => $query
        ]);

        if (!$response->successful()) {
            throw new Exception('NetSuite API Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Build OAuth 1.0 authorization header for NetSuite
     *
     * @param string $url
     * @param string $method
     * @return string
     */
    protected function buildOAuthHeader(string $url, string $method = 'POST'): string
    {
        $account = config('netsuite.account');
        $consumerKey = config('netsuite.consumer_key');
        $consumerSecret = config('netsuite.consumer_secret');
        $tokenKey = config('netsuite.token_key');
        $tokenSecret = config('netsuite.token_secret');

        // Generate OAuth 1.0 signature
        $timestamp = time();
        $nonce = bin2hex(random_bytes(16));

        $oauthParams = [
            'oauth_consumer_key' => $consumerKey,
            'oauth_token' => $tokenKey,
            'oauth_signature_method' => 'HMAC-SHA256',
            'oauth_timestamp' => $timestamp,
            'oauth_nonce' => $nonce,
            'oauth_version' => '1.0',
        ];

        // Build the signature base string
        $baseString = strtoupper($method) . '&' . rawurlencode($url) . '&';

        ksort($oauthParams);
        $params = [];
        foreach ($oauthParams as $key => $value) {
            $params[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        $baseString .= rawurlencode(implode('&', $params));

        // Create the signing key
        $signingKey = rawurlencode($consumerSecret) . '&' . rawurlencode($tokenSecret);

        // Generate the signature
        $signature = base64_encode(hash_hmac('sha256', $baseString, $signingKey, true));
        $oauthParams['oauth_signature'] = $signature;

        // Build the Authorization header
        $authHeader = 'OAuth realm="' . $account . '"';
        foreach ($oauthParams as $key => $value) {
            $authHeader .= ', ' . $key . '="' . rawurlencode($value) . '"';
        }

        return $authHeader;
    }
}

