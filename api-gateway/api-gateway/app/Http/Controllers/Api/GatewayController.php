<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    // Proxy requests to User Service// Update these methods in GatewayController
    public function userProxy(Request $request, $path = null)
    {
        // Get the current route path after /api/
        $currentPath = $request->route()->uri();
        $servicePath = str_replace('api/', '', $currentPath);

        $url = 'http://localhost:8001/api/' . $servicePath;

        // Replace {id} with actual ID value
        if ($request->route('id')) {
            $url = str_replace('{id}', $request->route('id'), $url);
        }

        return $this->proxyRequest($request, $url);
    }

    // Proxy requests to Product Service // Proxy requests to Product Service
    public function productProxy(Request $request, $path = null)
    {
        // Get the current route path after /api/
        $currentPath = $request->route()->uri();
        $servicePath = str_replace('api/', '', $currentPath);
        
        $url = 'http://localhost:8002/api/' . $servicePath;
        
        // Replace {id} with actual ID value
        if ($request->route('id')) {
            $url = str_replace('{id}', $request->route('id'), $url);
        }
        
        return $this->proxyRequest($request, $url);
    }
    // Proxy requests to Order Service
    // Proxy requests to Order Service
    public function orderProxy(Request $request, $path = null)
    {
        // Get the current route path after /api/
        $currentPath = $request->route()->uri();
        $servicePath = str_replace('api/', '', $currentPath);
        
        $url = 'http://localhost:8003/api/' . $servicePath;
        
        // Replace {id} with actual ID value
        if ($request->route('id')) {
            $url = str_replace('{id}', $request->route('id'), $url);
        }
        
        return $this->proxyRequest($request, $url);
    }

    // Helper method to proxy HTTP requests
    private function proxyRequest(Request $request, $url)
    {
        try {
            $method = strtolower($request->method());
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            // Forward authorization header if present
            if ($request->hasHeader('Authorization')) {
                $headers['Authorization'] = $request->header('Authorization');
            }

            $httpClient = Http::withHeaders($headers);

            switch ($method) {
                case 'get':
                    $response = $httpClient->get($url, $request->query());
                    break;
                case 'post':
                    $response = $httpClient->post($url, $request->all());
                    break;
                case 'put':
                    $response = $httpClient->put($url, $request->all());
                    break;
                case 'delete':
                    $response = $httpClient->delete($url, $request->all());
                    break;
                default:
                    return response()->json(['error' => 'Method not supported'], 405);
            }

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Service unavailable',
                'message' => $e->getMessage(),
                'url' => $url // Debug: show what URL was called
            ], 503);
        }
    }

    // Combined dashboard endpoint
    public function dashboard(Request $request)
    {
        try {
            // Get users
            $usersResponse = Http::get('http://localhost:8001/api/users');

            // Get products  
            $productsResponse = Http::get('http://localhost:8002/api/products?per_page=5');

            // Get recent orders
            $ordersResponse = Http::get('http://localhost:8003/api/orders?per_page=5');

            return response()->json([
                'recent_users' => $usersResponse->successful() ? $usersResponse->json() : null,
                'recent_products' => $productsResponse->successful() ? $productsResponse->json() : null,
                'recent_orders' => $ordersResponse->successful() ? $ordersResponse->json() : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}