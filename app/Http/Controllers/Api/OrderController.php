<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Crear una orden.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($request, $validated) {

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {

                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => 'Uno de los productos no está disponible.',
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para el producto {$product->name}.",
                    ]);
                }

                $subtotal = $product->price * $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);

                $total += $subtotal;
            }

            $order->update([
                'total' => $total,
            ]);

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Orden creada correctamente.',
            'data' => $order->load('items.product'),
        ], 201);
    }

    /**
     * Consultar historial de órdenes del usuario.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'payments'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Consultar una orden específica.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product', 'payments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}
