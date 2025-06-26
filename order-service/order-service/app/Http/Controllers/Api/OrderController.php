<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Create Order (POST /api/orders)
    public function store(Request $request)
    {
        // Add debug information
        \Illuminate\Support\Facades\Log::info('Order creation request:', [
            'all_data' => $request->all(),
            'items_type' => gettype($request->input('items')),
            'items_value' => $request->input('items'),
        ]);

        $validator = Validator::make($request->all(), [
            'buyer_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'payment_method' => 'required|string|in:stripe,paypal,credit_card',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'received_data' => $request->all(), // Debug info
            ], 422);
        }

        // Rest of your code remains the same...
        DB::beginTransaction();

        try {
            // First, let's check if we have any products in our Product Service
            // For now, let's create a simple mock response to test the order creation
            $productDetails = [
                [
                    'id' => 1,
                    'name' => 'Test Product',
                    'price' => 29.99,
                    'seller_id' => 1,
                ]
            ];

            // Calculate totals
            $totalAmount = 0;
            $platformFeeRate = 0.10; // 10% platform fee

            foreach ($productDetails as $product) {
                $totalAmount += $product['price'];
            }

            $platformFee = $totalAmount * $platformFeeRate;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'buyer_id' => $request->buyer_id,
                'total_amount' => $totalAmount,
                'platform_fee' => $platformFee,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Create order items
            foreach ($productDetails as $product) {
                $sellerAmount = $product['price'] * (1 - $platformFeeRate);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'seller_id' => $product['seller_id'],
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'seller_amount' => $sellerAmount,
                ]);
            }

            // Process payment (simplified)
            $paymentResult = $this->processPayment($order, $request->payment_method);

            if ($paymentResult['success']) {
                $order->update([
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                    'completed_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Order created successfully',
                    'order' => $order->load('items')->toApiArray()
                ], 201);
            } else {
                $order->update([
                    'status' => 'failed',
                    'payment_status' => 'failed',
                ]);

                DB::rollback();

                return response()->json([
                    'message' => 'Payment failed',
                    'error' => $paymentResult['error']
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get User Orders (GET /api/orders)
    public function index(Request $request)
    {
        $query = Order::with('items');

        // Filter by buyer
        if ($request->has('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort by newest first
        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'orders' => collect($orders->items())->map(fn($order) => $order->toApiArray()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    // Get Single Order (GET /api/orders/{id})
    public function show($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order->toApiArray());
    }

    // Get Order by Order Number (GET /api/orders/number/{orderNumber})
    public function showByNumber($orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order->toApiArray());
    }

    // Helper method to fetch product details from Product Service
    private function fetchProductDetails($items)
    {
        try {
            $productIds = collect($items)->pluck('product_id')->toArray();

            // Call Product Service API
            $response = Http::get('http://localhost:8002/api/internal/products', [
                'ids' => implode(',', $productIds)
            ]);

            if ($response->successful()) {
                return $response->json()['products'] ?? $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Simplified payment processing (in real app, integrate with Stripe/PayPal)
    private function processPayment($order, $paymentMethod)
    {
        // Simulate payment processing
        $success = rand(1, 100) <= 95; // 95% success rate for testing

        if ($success) {
            return [
                'success' => true,
                'transaction_id' => 'txn_' . uniqid(),
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Payment processing failed'
            ];
        }
    }

    // Generate download tokens for purchased products
    private function generateDownloadTokens($order)
    {
        foreach ($order->items as $item) {
            // Fetch product files from Product Service
            $productFiles = $this->fetchProductFiles($item->product_id);

            if ($productFiles) {
                foreach ($productFiles as $file) {
                    // Only create download tokens for non-preview files
                    if (!$file['is_preview']) {
                        Download::create([
                            'order_item_id' => $item->id,
                            'buyer_id' => $order->buyer_id,
                            'product_id' => $item->product_id,
                            'file_id' => $file['id'],
                            'download_token' => Download::generateToken(),
                            'max_downloads' => 5, // Allow 5 downloads per purchase
                            'expires_at' => now()->addDays(30), // Expire in 30 days
                        ]);
                    }
                }
            }
        }
    }

    // Helper method to fetch product files from Product Service
    private function fetchProductFiles($productId)
    {
        try {
            $response = Http::get("http://localhost:8002/api/products/{$productId}");

            if ($response->successful()) {
                $product = $response->json();
                return $product['files'] ?? [];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    // Add these methods to OrderController

    // Update Order Status (PUT /api/orders/{id})
    // Update the update method in OrderController with more flexible transitions
    public function update(Request $request, $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
            'payment_transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // More flexible status transitions - allow admin to change most statuses
        $restrictedTransitions = [
            // Only prevent these specific problematic transitions
            'refunded' => ['pending', 'completed'], // Can't go back from refunded to pending/completed
        ];

        if ($request->has('status')) {
            $newStatus = $request->status;
            $currentStatus = $order->status;

            // Check if this transition is restricted
            if (
                isset($restrictedTransitions[$currentStatus]) &&
                in_array($newStatus, $restrictedTransitions[$currentStatus])
            ) {
                return response()->json([
                    'message' => "Cannot change status from {$currentStatus} to {$newStatus}. Consider creating a new order instead."
                ], 400);
            }
        }

        // Prepare update data
        $updateData = [];

        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        if ($request->has('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
        }

        if ($request->has('payment_transaction_id')) {
            $updateData['payment_transaction_id'] = $request->payment_transaction_id;
        }

        // Update completed_at timestamp if status changes to completed
        if ($request->status === 'completed' && $order->status !== 'completed') {
            $updateData['completed_at'] = now();
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->toApiArray()
        ]);
    }
    // Cancel/Delete Order (DELETE /api/orders/{id})
    // Update the destroy method in OrderController
    public function destroy($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Define deletion rules based on order status
        $deletionRules = [
            'pending' => 'cancel', // Cancel pending orders
            'failed' => 'delete',  // Delete failed orders completely
            'refunded' => 'delete', // Delete refunded orders (cleanup)
            'completed' => 'refund_required' // Cannot delete completed orders directly
        ];

        $action = $deletionRules[$order->status] ?? 'not_allowed';

        switch ($action) {
            case 'cancel':
                // Cancel pending order
                $order->update([
                    'status' => 'failed',
                    'payment_status' => 'failed'
                ]);

                // Delete associated downloads since order is cancelled
                foreach ($order->items as $item) {
                    $item->downloads()->delete();
                }

                return response()->json([
                    'message' => 'Order cancelled successfully'
                ]);

            case 'delete':
                // Permanently delete failed or refunded orders
                foreach ($order->items as $item) {
                    $item->downloads()->delete();
                }

                $order->delete();

                return response()->json([
                    'message' => 'Order deleted successfully'
                ]);

            case 'refund_required':
                return response()->json([
                    'message' => 'Cannot delete completed orders. Please refund the order first, then delete it.'
                ], 400);

            default:
                return response()->json([
                    'message' => 'Cannot delete order with current status: ' . $order->status
                ], 400);
        }
    }
    // Refund Order (POST /api/orders/{id}/refund)
    public function refund($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== 'completed') {
            return response()->json([
                'message' => 'Can only refund completed orders'
            ], 400);
        }

        // Update order status
        $order->update([
            'status' => 'refunded',
            'payment_status' => 'refunded'
        ]);

        // In a real system, you would process the actual refund here

        return response()->json([
            'message' => 'Order refunded successfully',
            'order' => $order->toApiArray()
        ]);
    }
}