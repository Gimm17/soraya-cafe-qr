<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemMod;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createFromCart(
        int $tableId,
        string $customerName,
        string $fulfillmentType,
        array $cart,
        array $totals
    ): Order {
        return DB::transaction(function () use ($tableId, $customerName, $fulfillmentType, $cart, $totals) {
            $orderCode = $this->generateOrderCode();

            $order = Order::create([
                'order_code' => $orderCode,
                'table_id' => $tableId,
                'customer_name' => $customerName,
                'fulfillment_type' => $fulfillmentType,
                'order_status' => 'DITERIMA',
                'payment_status' => 'UNPAID',
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax'],
                'service_amount' => $totals['service'],
                'discount_amount' => $totals['discount'],
                'grand_total' => $totals['grand_total'],
            ]);

            foreach ($cart as $item) {
                $lineTotal = ((int)$item['unit_price']) * ((int)$item['qty']);
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'line_total' => $lineTotal,
                ]);

                // Save modifiers for this order item
                if (!empty($item['modifiers'])) {
                    foreach ($item['modifiers'] as $mod) {
                        OrderItemMod::create([
                            'order_item_id' => $orderItem->id,
                            'mod_option_id' => $mod['id'],
                            'mod_group_name' => $mod['group_name'],
                            'mod_option_name' => $mod['option_name'],
                            'price_modifier' => $mod['price_modifier'],
                        ]);
                    }
                }
            }

            return $order;
        });
    }

    /**
     * Create order from POS (admin/kasir).
     */
    public function createPosOrder(
        ?int $tableId,
        string $customerName,
        string $fulfillmentType,
        array $cart,
        array $totals,
        string $paymentMethod,
        ?int $cashReceived = null
    ): Order {
        return DB::transaction(function () use ($tableId, $customerName, $fulfillmentType, $cart, $totals, $paymentMethod, $cashReceived) {
            $orderCode = $this->generateOrderCode('K');

            $order = Order::create([
                'order_code' => $orderCode,
                'order_source' => 'POS',
                'table_id' => $tableId,
                'customer_name' => $customerName,
                'fulfillment_type' => $fulfillmentType,
                'order_status' => 'DITERIMA',
                'payment_status' => $paymentMethod === 'CASH' ? 'PAID' : 'UNPAID',
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax'] ?? 0,
                'service_amount' => $totals['service'] ?? 0,
                'discount_amount' => $totals['discount'] ?? 0,
                'grand_total' => $totals['grand_total'],
            ]);

            // Create items
            foreach ($cart as $item) {
                $lineTotal = ((int)$item['unit_price']) * ((int)$item['qty']);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                    'note' => $item['note'] ?? null,
                    'line_total' => $lineTotal,
                ]);

                if (!empty($item['modifiers'])) {
                    foreach ($item['modifiers'] as $mod) {
                        OrderItemMod::create([
                            'order_item_id' => $orderItem->id,
                            'mod_option_id' => $mod['id'],
                            'mod_group_name' => $mod['group_name'],
                            'mod_option_name' => $mod['option_name'],
                            'price_modifier' => $mod['price_modifier'],
                        ]);
                    }
                }
            }

            // Create payment record
            $cashChange = ($paymentMethod === 'CASH' && $cashReceived) ? $cashReceived - $totals['grand_total'] : null;
            Payment::create([
                'order_id' => $order->id,
                'gateway' => $paymentMethod, // CASH or QRIS
                'status' => $paymentMethod === 'CASH' ? 'settlement' : 'pending',
                'cash_received' => $paymentMethod === 'CASH' ? $cashReceived : null,
                'cash_change' => $cashChange,
            ]);

            return $order;
        });
    }

    private function generateOrderCode(string $prefix = 'A'): string
    {
        $date = now()->format('Ymd');
        $rand = strtoupper(Str::random(6));
        return "{$prefix}-{$date}-{$rand}";
    }
}
