<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $cities = City::all();
        $coupons = Coupon::where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Order 1: Pending order with single product
        $order1 = Order::create([
            'customer_name' => 'Ahmed Rahman',
            'customer_email' => 'ahmed.rahman@example.com',
            'customer_phone' => '+8801712345678',
            'shipping_address' => 'House 45, Road 12, Dhanmondi',
            'shipping_city' => $cities->first()?->name ?? 'Dhaka',
            'shipping_postal_code' => '1209',
            'subtotal' => 350.00,
            'discount' => 0.00,
            'shipping_cost' => 50.00,
            'total' => 400.00,
            'advance_payment' => 0.00,
            'payment_method' => 'cod',
            'status' => 'pending',
            'notes' => 'Please deliver in the morning',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products->first()->id,
            'product_name' => $products->first()->name_en,
            'attribute_data' => null,
            'price' => 350.00,
            'quantity' => 1,
            'subtotal' => 350.00,
        ]);

        // Order 2: Confirmed order with multiple products and coupon discount
        $product2 = $products->skip(1)->first();
        $product3 = $products->skip(2)->first();
        $subtotal2 = ($product2->price * 2) + ($product3->price * 1);
        $coupon = $coupons->first();
        $discount2 = $coupon ? $coupon->calculateDiscount($subtotal2) : 0;
        $shipping2 = 80.00;
        $total2 = $subtotal2 - $discount2 + $shipping2;

        $order2 = Order::create([
            'customer_name' => 'Fatima Begum',
            'customer_email' => 'fatima.begum@example.com',
            'customer_phone' => '+8801812345679',
            'shipping_address' => 'Flat 3B, Building 12, Gulshan 2',
            'shipping_city' => $cities->skip(1)->first()?->name ?? 'Chittagong',
            'shipping_postal_code' => '1212',
            'subtotal' => $subtotal2,
            'discount' => $discount2,
            'shipping_cost' => $shipping2,
            'total' => $total2,
            'advance_payment' => 0.00,
            'payment_method' => 'cod',
            'status' => 'confirmed',
            'notes' => 'Customer requested evening delivery',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product2->id,
            'product_name' => $product2->name_en,
            'attribute_data' => null,
            'price' => $product2->price,
            'quantity' => 2,
            'subtotal' => $product2->price * 2,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product3->id,
            'product_name' => $product3->name_en,
            'attribute_data' => null,
            'price' => $product3->price,
            'quantity' => 1,
            'subtotal' => $product3->price,
        ]);

        // Order 3: Processing order with advance payment
        $product4 = $products->skip(3)->first();
        $product5 = $products->skip(4)->first();
        $subtotal3 = ($product4->price * 3) + ($product5->price * 2);
        $shipping3 = 100.00;
        $total3 = $subtotal3 + $shipping3;
        $advance3 = 500.00;

        $order3 = Order::create([
            'customer_name' => 'Karim Uddin',
            'customer_email' => 'karim.uddin@example.com',
            'customer_phone' => '+8801912345680',
            'shipping_address' => 'Shop 15, New Market, Sylhet',
            'shipping_city' => $cities->skip(2)->first()?->name ?? 'Sylhet',
            'shipping_postal_code' => '3100',
            'subtotal' => $subtotal3,
            'discount' => 0.00,
            'shipping_cost' => $shipping3,
            'total' => $total3,
            'advance_payment' => $advance3,
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'advance_payment_method' => 'bkash',
            'payment_method' => 'cod',
            'status' => 'processing',
            'notes' => 'Advance payment received via bKash',
            'created_at' => Carbon::now()->subHours(12),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $product4->id,
            'product_name' => $product4->name_en,
            'attribute_data' => ['Weight' => '1 kg'],
            'price' => $product4->price,
            'quantity' => 3,
            'subtotal' => $product4->price * 3,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $product5->id,
            'product_name' => $product5->name_en,
            'attribute_data' => ['Size' => 'Large'],
            'price' => $product5->price,
            'quantity' => 2,
            'subtotal' => $product5->price * 2,
        ]);

        // Order 4: Shipped order
        $product6 = $products->skip(5)->first();
        $subtotal4 = $product6->price * 1;
        $shipping4 = 60.00;
        $total4 = $subtotal4 + $shipping4;

        $order4 = Order::create([
            'customer_name' => 'Nusrat Jahan',
            'customer_email' => 'nusrat.jahan@example.com',
            'customer_phone' => '+8801712345681',
            'shipping_address' => 'Village: Shantipur, Post: Rajshahi',
            'shipping_city' => $cities->skip(3)->first()?->name ?? 'Rajshahi',
            'shipping_postal_code' => '6000',
            'subtotal' => $subtotal4,
            'discount' => 0.00,
            'shipping_cost' => $shipping4,
            'total' => $total4,
            'advance_payment' => 0.00,
            'payment_method' => 'cod',
            'status' => 'shipped',
            'notes' => 'Order shipped via courier service',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $product6->id,
            'product_name' => $product6->name_en,
            'attribute_data' => null,
            'price' => $product6->price,
            'quantity' => 1,
            'subtotal' => $product6->price,
        ]);

        // Order 5: Delivered order with full advance payment
        $product7 = $products->skip(6)->first();
        $product8 = $products->skip(7)->first();
        $subtotal5 = ($product7->price * 2) + ($product8->price * 1);
        $coupon2 = $coupons->skip(1)->first();
        $discount5 = $coupon2 ? $coupon2->calculateDiscount($subtotal5) : 0;
        $shipping5 = 90.00;
        $total5 = $subtotal5 - $discount5 + $shipping5;

        $order5 = Order::create([
            'customer_name' => 'Rashidul Islam',
            'customer_email' => 'rashidul.islam@example.com',
            'customer_phone' => '+8801812345682',
            'shipping_address' => 'House 78, Road 5, Khulna',
            'shipping_city' => $cities->skip(4)->first()?->name ?? 'Khulna',
            'shipping_postal_code' => '9000',
            'subtotal' => $subtotal5,
            'discount' => $discount5,
            'shipping_cost' => $shipping5,
            'total' => $total5,
            'advance_payment' => $total5,
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'advance_payment_method' => 'nagad',
            'payment_method' => 'online',
            'status' => 'delivered',
            'notes' => 'Fully paid online. Delivered successfully.',
            'created_at' => Carbon::now()->subDays(7),
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $product7->id,
            'product_name' => $product7->name_en,
            'attribute_data' => ['Color' => 'Red'],
            'price' => $product7->price,
            'quantity' => 2,
            'subtotal' => $product7->price * 2,
        ]);

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $product8->id,
            'product_name' => $product8->name_en,
            'attribute_data' => null,
            'price' => $product8->price,
            'quantity' => 1,
            'subtotal' => $product8->price,
        ]);

        // Order 6: Cancelled order
        $product9 = $products->skip(8)->first() ?? $products->first();
        $subtotal6 = $product9->price * 1;
        $shipping6 = 50.00;
        $total6 = $subtotal6 + $shipping6;

        $order6 = Order::create([
            'customer_name' => 'Sadia Ahmed',
            'customer_email' => 'sadia.ahmed@example.com',
            'customer_phone' => '+8801912345683',
            'shipping_address' => 'Flat 2A, Building 8, Barisal',
            'shipping_city' => $cities->skip(5)->first()?->name ?? 'Barisal',
            'shipping_postal_code' => '8200',
            'subtotal' => $subtotal6,
            'discount' => 0.00,
            'shipping_cost' => $shipping6,
            'total' => $total6,
            'advance_payment' => 0.00,
            'payment_method' => 'cod',
            'status' => 'cancelled',
            'notes' => 'Order cancelled by customer',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => $product9->id,
            'product_name' => $product9->name_en,
            'attribute_data' => null,
            'price' => $product9->price,
            'quantity' => 1,
            'subtotal' => $product9->price,
        ]);

        // Order 7: Large order with multiple items
        $subtotal7 = 0;
        $orderItems7 = [];

        foreach ($products->take(5) as $index => $product) {
            $quantity = $index + 1;
            $subtotal7 += $product->price * $quantity;
            $orderItems7[] = [
                'product_id' => $product->id,
                'product_name' => $product->name_en,
                'price' => $product->price,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ];
        }

        $shipping7 = 120.00;
        $total7 = $subtotal7 + $shipping7;

        $order7 = Order::create([
            'customer_name' => 'Mohammad Hasan',
            'customer_email' => 'mohammad.hasan@example.com',
            'customer_phone' => '+8801712345684',
            'shipping_address' => 'Shop 22, Main Road, Rangpur',
            'shipping_city' => $cities->skip(6)->first()?->name ?? 'Rangpur',
            'shipping_postal_code' => '5400',
            'subtotal' => $subtotal7,
            'discount' => 0.00,
            'shipping_cost' => $shipping7,
            'total' => $total7,
            'advance_payment' => 1000.00,
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'advance_payment_method' => 'rocket',
            'payment_method' => 'cod',
            'status' => 'processing',
            'notes' => 'Bulk order for retail shop',
            'created_at' => Carbon::now()->subHours(6),
        ]);

        foreach ($orderItems7 as $item) {
            OrderItem::create([
                'order_id' => $order7->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'attribute_data' => null,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Order 8: Recent pending order
        $productFirst = $products->first();
        $subtotal8 = $productFirst->price * 1;
        $shipping8 = 50.00;
        $total8 = $subtotal8 + $shipping8;

        $order8 = Order::create([
            'customer_name' => 'Kamrul Islam',
            'customer_email' => 'kamrul.islam@example.com',
            'customer_phone' => '+8801812345685',
            'shipping_address' => 'House 12, Road 3, Mymensingh',
            'shipping_city' => $cities->skip(7)->first()?->name ?? 'Mymensingh',
            'shipping_postal_code' => '2200',
            'subtotal' => $subtotal8,
            'discount' => 0.00,
            'shipping_cost' => $shipping8,
            'total' => $total8,
            'advance_payment' => 0.00,
            'payment_method' => 'cod',
            'status' => 'pending',
            'notes' => null,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order8->id,
            'product_id' => $productFirst->id,
            'product_name' => $productFirst->name_en,
            'attribute_data' => null,
            'price' => $productFirst->price,
            'quantity' => 1,
            'subtotal' => $productFirst->price,
        ]);
    }
}

