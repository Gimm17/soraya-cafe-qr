<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    protected $signature = 'payments:check-pending
                            {--hours=24 : Only check orders created within this many hours}
                            {--limit=10 : Maximum orders to check per run}';

    protected $description = 'Check pending Midtrans payments and sync their status (cron fallback)';

    public function handle(PaymentService $paymentService): int
    {
        $hours = (int) $this->option('hours');
        $limit = (int) $this->option('limit');

        $pendingOrders = Order::where('payment_status', 'PENDING')
            ->where('created_at', '>=', now()->subHours($hours))
            ->whereHas('payment', fn($q) => $q->where('gateway', 'midtrans'))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending payments to check.');
            return self::SUCCESS;
        }

        $this->info("Checking {$pendingOrders->count()} pending order(s)...");
        $updated = 0;

        foreach ($pendingOrders as $order) {
            try {
                $status = $paymentService->syncPaymentStatus($order);

                $this->line("  [{$order->order_code}] → {$status}");

                if (in_array($status, ['PAID', 'FAILED', 'EXPIRED'])) {
                    $updated++;
                }

                // Delay between API calls to avoid rate limiting
                if ($pendingOrders->count() > 1) {
                    sleep(1);
                }
            } catch (\Throwable $e) {
                Log::error('CheckPendingPayments error', [
                    'order' => $order->order_code,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  [{$order->order_code}] ERROR: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$updated} order(s) updated.");

        return self::SUCCESS;
    }
}
