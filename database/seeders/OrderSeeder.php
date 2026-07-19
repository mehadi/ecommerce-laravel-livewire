<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\Tenancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generates a realistic, multi-month order history (not just a handful of
 * demo rows) so every dashboard/reporting view — revenue trends, cohort
 * metrics, product profitability, inventory velocity, coupon usage, and the
 * fulfillment/SLA report — has enough volume and variety to look and behave
 * like a real store instead of a nearly-empty one.
 *
 * Status transitions are written directly to order_status_histories (not via
 * repeated Order::update() calls) so each transition can carry its own
 * backdated timestamp — otherwise every seeded transition would be logged at
 * "now" by OrderObserver, collapsing months of simulated history into a
 * single instant.
 */
class OrderSeeder extends Seeder
{
    private const ORDER_COUNT = 180;

    private const MAX_DAYS_BACK = 150;

    private array $areas = [
        'Dhanmondi, Dhaka', 'Gulshan 2, Dhaka', 'Uttara Sector 7, Dhaka', 'Banani, Dhaka',
        'Mirpur 10, Dhaka', 'Agrabad, Chittagong', 'Zindabazar, Sylhet', 'New Market, Sylhet',
        'Shantipur, Rajshahi', 'Boyra, Khulna', 'Rupatoli, Barisal', 'Station Road, Rangpur',
        'College Road, Mymensingh', 'Sadarghat, Chittagong', 'Bashundhara R/A, Dhaka',
    ];

    private array $customers = [
        ['name' => 'Ahmed Rahman', 'email' => 'ahmed.rahman@example.com', 'phone' => '+8801712345601'],
        ['name' => 'Fatima Begum', 'email' => 'fatima.begum@example.com', 'phone' => '+8801812345602'],
        ['name' => 'Karim Uddin', 'email' => 'karim.uddin@example.com', 'phone' => '+8801912345603'],
        ['name' => 'Nusrat Jahan', 'email' => 'nusrat.jahan@example.com', 'phone' => '+8801712345604'],
        ['name' => 'Rashidul Islam', 'email' => 'rashidul.islam@example.com', 'phone' => '+8801812345605'],
        ['name' => 'Sadia Ahmed', 'email' => 'sadia.ahmed@example.com', 'phone' => '+8801912345606'],
        ['name' => 'Mohammad Hasan', 'email' => 'mohammad.hasan@example.com', 'phone' => '+8801712345607'],
        ['name' => 'Kamrul Islam', 'email' => 'kamrul.islam@example.com', 'phone' => '+8801812345608'],
        ['name' => 'Rehana Parvin', 'email' => 'rehana.parvin@example.com', 'phone' => '+8801912345609'],
        ['name' => 'Shakil Ahmed', 'email' => 'shakil.ahmed@example.com', 'phone' => '+8801712345610'],
        ['name' => 'Taslima Akter', 'email' => 'taslima.akter@example.com', 'phone' => '+8801812345611'],
        ['name' => 'Jashim Uddin', 'email' => 'jashim.uddin@example.com', 'phone' => '+8801912345612'],
        ['name' => 'Ruma Khatun', 'email' => 'ruma.khatun@example.com', 'phone' => '+8801712345613'],
        ['name' => 'Anisur Rahman', 'email' => 'anisur.rahman@example.com', 'phone' => '+8801812345614'],
        ['name' => 'Shirin Sultana', 'email' => 'shirin.sultana@example.com', 'phone' => '+8801912345615'],
        ['name' => 'Delwar Hossain', 'email' => 'delwar.hossain@example.com', 'phone' => '+8801712345616'],
        ['name' => 'Nazma Begum', 'email' => 'nazma.begum@example.com', 'phone' => '+8801812345617'],
        ['name' => 'Faruk Ahmed', 'email' => 'faruk.ahmed@example.com', 'phone' => '+8801912345618'],
        ['name' => 'Munira Sultana', 'email' => 'munira.sultana@example.com', 'phone' => '+8801712345619'],
        ['name' => 'Habibur Rahman', 'email' => 'habibur.rahman@example.com', 'phone' => '+8801812345620'],
        ['name' => 'Ayesha Siddiqua', 'email' => 'ayesha.siddiqua@example.com', 'phone' => '+8801912345621'],
        ['name' => 'Jahangir Alam', 'email' => 'jahangir.alam@example.com', 'phone' => '+8801712345622'],
        ['name' => 'Salma Khatun', 'email' => 'salma.khatun@example.com', 'phone' => '+8801812345623'],
        ['name' => 'Mizanur Rahman', 'email' => 'mizanur.rahman@example.com', 'phone' => '+8801912345624'],
    ];

    public function run(): void
    {
        $products = Product::where('is_active', true)->get();
        $cities = City::all();
        $coupons = Coupon::where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No products found. Please run ProductSeeder first.');

            return;
        }

        $tenantId = Tenancy::id();
        $historyRows = [];
        $now = Carbon::now();

        for ($i = 0; $i < self::ORDER_COUNT; $i++) {
            $daysAgo = $this->weightedDaysAgo();
            $createdAt = $now->copy()->subDays($daysAgo)->subHours(fake()->numberBetween(0, 23))->subMinutes(fake()->numberBetween(0, 59));

            $customer = fake()->randomElement($this->customers);
            $city = $cities->isNotEmpty() ? $cities->random() : null;

            $lineItemCount = min(fake()->numberBetween(1, 4), $products->count());
            $lineProducts = $products->random($lineItemCount);

            $items = $lineProducts->map(function (Product $product) {
                $quantity = fake()->numberBetween(1, 5);
                $price = (float) $product->price;

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name_en,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => round($price * $quantity, 2),
                ];
            });

            $subtotal = round($items->sum('subtotal'), 2);

            $coupon = ($coupons->isNotEmpty() && fake()->boolean(20)) ? $coupons->random() : null;
            $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0.0;

            $shippingCost = fake()->randomFloat(2, 40, 150);
            $total = round($subtotal - $discount + $shippingCost, 2);

            $paymentMethod = fake()->randomElement(['cod', 'cod', 'cod', 'cod', 'cod', 'cod', 'online', 'online', 'bank_transfer', 'mobile_banking']);
            $advancePayment = 0.0;
            $advanceMethod = null;
            $transactionId = null;

            if ($paymentMethod !== 'cod') {
                $advanceMethod = fake()->randomElement(['bkash', 'nagad', 'rocket', 'bank_transfer']);
                $transactionId = 'TXN-'.strtoupper(Str::random(10));
                $advancePayment = fake()->boolean(65) ? $total : round($total * fake()->randomFloat(2, 0.3, 0.7), 2);
            }

            [$finalStatus, $timeline] = $this->buildStatusTimeline($createdAt, $daysAgo, $now);
            $lastChangedAt = end($timeline)['changed_at'];

            $order = Order::withoutEvents(function () use (
                $tenantId, $customer, $city, $subtotal, $discount, $shippingCost, $total,
                $advancePayment, $transactionId, $advanceMethod, $paymentMethod, $finalStatus,
                $createdAt, $lastChangedAt
            ) {
                return Order::create([
                    'tenant_id' => $tenantId,
                    'order_number' => 'ORD-'.strtoupper(Str::random(8)),
                    'customer_name' => $customer['name'],
                    'customer_email' => $customer['email'],
                    'customer_phone' => $customer['phone'],
                    'shipping_address' => 'House '.fake()->numberBetween(1, 99).', Road '.fake()->numberBetween(1, 20).', '.fake()->randomElement($this->areas),
                    'shipping_city' => $city?->name ?? 'Dhaka',
                    'shipping_postal_code' => (string) fake()->numberBetween(1000, 9999),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'advance_payment' => $advancePayment,
                    'transaction_id' => $transactionId,
                    'advance_payment_method' => $advanceMethod,
                    'payment_method' => $paymentMethod,
                    'status' => $finalStatus,
                    'notes' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $lastChangedAt,
                ]);
            });

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'attribute_data' => null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            foreach ($timeline as $entry) {
                $historyRows[] = [
                    'tenant_id' => $tenantId,
                    'order_id' => $order->id,
                    'status' => $entry['status'],
                    'changed_by' => null,
                    'changed_at' => $entry['changed_at'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($historyRows, 500) as $chunk) {
            DB::table('order_status_histories')->insert($chunk);
        }

        $this->command?->info(self::ORDER_COUNT.' realistic orders seeded across the last '.self::MAX_DAYS_BACK.' days, with full status history.');
    }

    /**
     * Skews toward recent days (more orders happened "yesterday" than "5
     * months ago") rather than a flat distribution, matching a store that's
     * been steadily growing.
     */
    private function weightedDaysAgo(): int
    {
        $roll = fake()->randomFloat(4, 0, 1);
        $skewed = $roll ** 2; // squaring a uniform [0,1] value concentrates mass near 0 (recent)

        return (int) round($skewed * self::MAX_DAYS_BACK);
    }

    /**
     * Builds a (status, changed_at) sequence starting from $createdAt. Orders
     * older than 2 weeks always resolve to a terminal state (delivered/
     * cancelled) — a real store wouldn't still have orders open that long.
     * Orders within the last 2 weeks may be genuinely "stuck" partway through
     * (the Fulfillment/SLA report's reason for existing). Same-day orders are
     * still early in the pipeline.
     *
     * @return array{0: string, 1: list<array{status: string, changed_at: Carbon}>}
     */
    private function buildStatusTimeline(Carbon $createdAt, int $daysAgo, Carbon $now): array
    {
        $timeline = [['status' => 'pending', 'changed_at' => $createdAt->copy()]];
        $cursor = $createdAt->copy();

        $advance = function (string $status, int $minHours, int $maxHours) use (&$cursor, &$timeline, $now) {
            $cursor = $cursor->copy()->addHours(fake()->numberBetween($minHours, $maxHours));
            if ($cursor->gt($now)) {
                $cursor = $now->copy();
            }
            $timeline[] = ['status' => $status, 'changed_at' => $cursor->copy()];
        };

        if ($daysAgo < 1) {
            $roll = fake()->numberBetween(1, 100);
            if ($roll <= 55) {
                return ['pending', $timeline];
            }
            $advance('confirmed', 1, 6);
            if ($roll <= 80) {
                return ['confirmed', $timeline];
            }
            $advance('processing', 1, 6);

            return ['processing', $timeline];
        }

        if ($daysAgo <= 14) {
            $roll = fake()->numberBetween(1, 100);
            if ($roll <= 6) {
                $advance('cancelled', 2, 48);

                return ['cancelled', $timeline];
            }
            $advance('confirmed', 2, 12);
            if ($roll <= 18) {
                return ['confirmed', $timeline];
            }
            $advance('processing', 4, 24);
            if ($roll <= 32) {
                return ['processing', $timeline];
            }
            $advance('shipped', 8, 36);
            if ($roll <= 45) {
                return ['shipped', $timeline];
            }
            $advance('delivered', 12, 60);

            return ['delivered', $timeline];
        }

        // Older than 2 weeks: always resolved by now.
        if (fake()->numberBetween(1, 100) <= 9) {
            $advance('cancelled', 2, 48);

            return ['cancelled', $timeline];
        }

        $advance('confirmed', 2, 12);
        $advance('processing', 4, 24);
        $advance('shipped', 8, 36);
        $advance('delivered', 12, 72);

        return ['delivered', $timeline];
    }
}
