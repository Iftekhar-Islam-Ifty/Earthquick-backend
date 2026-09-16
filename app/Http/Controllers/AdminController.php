<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/* =========================================================================
 * ADMIN CONTROLLER
 * Executive management for orders, status transitions, inventory stock,
 * and full product catalog CRUD (create, edit, delete, and image uploads).
 * Protected by 'auth' and 'admin' middleware guards.
 * ========================================================================= */
class AdminController extends Controller
{
    /* =========================================================================
     * EXECUTIVE DASHBOARD & KPIS
     * Aggregates gross sales, active order pipelines, and recent activity.
     * ========================================================================= */

    /**
     * Display executive dashboard overview with KPIs and recent order stream.
     * Display executive dashboard overview with KPIs, trendlines, category distributions,
     * best-selling creations, and recent order stream.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard(): View
    {
        // Calculate core performance indicators
        // 1. Core Performance Indicators (KPIs)
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = (float) Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $monthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthRevenue = (float) Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalRevenue = (float) Order::where('status', '!=', 'cancelled')->sum('total');
        $nonCancelledOrders = Order::where('status', '!=', 'cancelled')->count();
        $averageOrderValue = $nonCancelledOrders > 0 ? round($totalRevenue / $nonCancelledOrders, 2) : 0;

        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();
        $inStockProducts = Product::where('in_stock', true)->count();

        // Retrieve latest 10 customer transactions
        // 2. 14-Day Sales & Order Volume Trend Stream (for Area/Bar Chart)
        $trendLabels = [];
        $trendRevenue = [];
        $trendOrders = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = now()->subDays($i)->format('M d');
            $trendRevenue[$date] = 0;
            $trendOrders[$date] = 0;
        }

        $startDate = now()->subDays(13)->startOfDay();
        $rawTrends = Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as order_date, COUNT(*) as order_count, SUM(CASE WHEN status != "cancelled" THEN total ELSE 0 END) as daily_revenue')
            ->groupBy('order_date')
            ->get();

        foreach ($rawTrends as $row) {
            if (isset($trendRevenue[$row->order_date])) {
                $trendRevenue[$row->order_date] = (float) $row->daily_revenue;
                $trendOrders[$row->order_date] = (int) $row->order_count;
            }
        }

        $chartLabelsJson = json_encode($trendLabels);
        $chartRevenueJson = json_encode(array_values($trendRevenue));
        $chartOrdersJson = json_encode(array_values($trendOrders));

        // 3. Category Sales Distribution (for Doughnut Chart)
        $categorySalesRaw = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.total_price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $catLabels = [];
        $catData = [];
        foreach ($categorySalesRaw as $cat) {
            $catLabels[] = $cat->category_name;
            $catData[] = (float) $cat->total_sales;
        }

        $categoryLabelsJson = json_encode($catLabels);
        $categoryDataJson = json_encode($catData);

        // 4. Top 5 Best-Selling Creations
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_image',
                'categories.name as category_name',
                'products.slug as product_slug',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_image',
                'categories.name',
                'products.slug'
            )
            ->orderByDesc('units_sold')
            ->take(5)
            ->get();

        // 5. Retrieve latest 10 customer transactions
        $recentOrders = Order::with('items')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'monthOrders',
            'monthRevenue',
            'totalOrders',
            'totalRevenue',
            'averageOrderValue',
            'pendingOrdersCount',
            'totalProducts',
            'inStockProducts',
            'chartLabelsJson',
            'chartRevenueJson',
            'chartOrdersJson',
            'categoryLabelsJson',
            'categoryDataJson',
            'topProducts',
            'recentOrders'
        ));
    }

    /**
     * Stream a complete or status-filtered sales orders ledger in Excel-compatible CSV format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportOrders(Request $request): StreamedResponse
    {
        $status = $request->query('status');

        $query = Order::query()->latest();
        if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'in_transit', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        $filename = 'earthquick_sales_orders_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM so Excel opens Bengali & special characters flawlessly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Column Headers
            fputcsv($handle, [
                'Order Number',
                'Date',
                'Customer Name',
                'Phone',
                'Email',
                'District',
                'Area',
                'Full Address',
                'Courier Partner',
                'Tracking Number',
                'Payment Method',
                'Subtotal (BDT)',
                'Delivery Fee (BDT)',
                'Total (BDT)',
                'Order Status',
            ]);

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->customer_name,
                        $order->customer_phone,
                        $order->customer_email ?? 'N/A',
                        $order->district,
                        $order->area,
                        $order->address,
                        $order->courier_name ?? 'Unassigned',
                        $order->tracking_number ?? 'N/A',
                        strtoupper($order->payment_method ?? 'COD'),
                        number_format((float) $order->subtotal, 2, '.', ''),
                        number_format((float) $order->delivery_fee, 2, '.', ''),
                        number_format((float) $order->total, 2, '.', ''),
                        str_replace('_', ' ', strtoupper($order->status)),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* =========================================================================
     * ORDER MANAGEMENT & LIFECYCLE
     * Displays paginated customer orders and facilitates fulfillment updates.
     * ========================================================================= */

    /**
     * Display paginated orders portfolio with status filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function orders(Request $request): View
    {
        $status = $request->input('status');
        $search = trim($request->input('search', ''));

        $query = Order::with(['items', 'user'])->latest();

        // Apply search filter across order number, customer phone, or customer name
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter when specified
        if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'in_transit', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Aggregate counts for navigation filter tabs
        $statusCounts = [
            'all'         => Order::count(),
            'pending'     => Order::where('status', 'pending')->count(),
            'confirmed'   => Order::where('status', 'confirmed')->count(),
            'processing'  => Order::where('status', 'processing')->count(),
            'in_transit'  => Order::where('status', 'in_transit')->count(),
            'delivered'   => Order::where('status', 'delivered')->count(),
            'cancelled'   => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders', compact('orders', 'status', 'statusCounts', 'search'));
    }

    /**
     * Display full order invoice, customer shipping address, and item list.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showOrder(int $id): View
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);

        return view('admin.order-detail', compact('order'));
    }

    /**
     * Display printable packing slip and invoice for an order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function orderInvoice(int $id): View
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);

        return view('admin.order-invoice', compact('order'));
    }

    /**
     * Update customer order lifecycle status and fulfillment tracking logistics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status'          => 'required|string|in:pending,confirmed,processing,in_transit,delivered,cancelled',
            'courier_name'    => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'admin_notes'     => 'nullable|string|max:2000',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->update([
            'status'          => $request->status,
            'courier_name'    => $request->courier_name,
            'tracking_number' => $request->tracking_number,
            'admin_notes'     => $request->admin_notes,
        ]);

        return redirect()->back()->with(
            'success',
            "Order #{$order->order_number} details and status updated from '{$oldStatus}' to '{$order->status}' successfully."
        );
    }

    /* =========================================================================
     * PRODUCT CATALOG & INVENTORY LISTING
     * Displays all catalog items with filtering, stock toggles, and edit actions.
     * ========================================================================= */

    /**
     * Display product catalog inventory with stock status controls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function products(Request $request): View
    {
        $categorySlug = $request->input('category');
        $stockFilter = $request->input('stock');

        $query = Product::with(['category', 'subcategory'])->latest();

        // Apply category filter
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Apply availability filter
        if ($stockFilter === 'in_stock') {
            $query->where('in_stock', true);
        } elseif ($stockFilter === 'out_of_stock') {
            $query->where('in_stock', false);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products', compact('products', 'categories', 'categorySlug', 'stockFilter'));
    }

    /**
     * Toggle product availability status (in stock vs out of stock).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleStock(int $id): JsonResponse|RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update([
            'in_stock' => !$product->in_stock,
        ]);

        $statusText = $product->in_stock ? 'In Stock (Ready to Ship)' : 'Out of Stock';

        if (request()->wantsJson()) {
            return response()->json([
                'success'  => true,
                'in_stock' => $product->in_stock,
                'message'  => "'{$product->name}' is now marked as {$statusText}.",
            ]);
        }

        return redirect()->back()->with('success', "'{$product->name}' is now marked as {$statusText}.");
    }

    /* =========================================================================
     * PRODUCT CREATION & STORAGE
     * Form rendering, image upload handling, and database record creation.
     * ========================================================================= */

    /**
     * Show product creation form.
     *
     * @return \Illuminate\View\View
     */
    public function createProduct(): View
    {
        $categories = Category::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.product-create', compact('categories'));
    }

    /**
     * Store newly created product in database with uploaded image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'price'          => 'required|numeric|min:0',
            'old_price'      => 'nullable|numeric|min:0',
            'fabric'         => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock'       => 'nullable|boolean',
            'is_featured'    => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'badge'          => 'nullable|string|max:50',
            'badge_type'     => 'nullable|string|max:50',
            'short_desc'     => 'nullable|string|max:1000',
            'description'    => 'nullable|string|max:10000',
            'image'          => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        // Generate collision-resistant unique slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Ensure public storage destination directory exists
        $destinationPath = public_path('images/products');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $imageFile = $request->file('image');
        $filename = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move($destinationPath, $filename);
        $imagePath = 'images/products/' . $filename;

        // Generate human-readable SKU
        $sku = 'NT-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);

        $product = Product::create([
            'name'           => $request->name,
            'slug'           => $slug,
            'sku'            => $sku,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id ?: null,
            'price'          => (float) $request->price,
            'old_price'      => $request->filled('old_price') ? (float) $request->old_price : null,
            'fabric'         => $request->fabric ?: 'Handloom',
            'stock_quantity' => (int) $request->input('stock_quantity', 10),
            'in_stock'       => $request->boolean('in_stock', true),
            'is_featured'    => $request->boolean('is_featured', false),
            'is_new_arrival' => $request->boolean('is_new_arrival', true),
            'badge'          => $request->badge,
            'badge_type'     => $request->badge_type ?: 'ready',
            'short_desc'     => $request->short_desc,
            'description'    => $request->description,
            'image'          => $imagePath,
            'rating'         => 5.0,
            'reviews_count'  => 0,
        ]);

        return redirect()->route('admin.products')->with(
            'success',
            "Product '{$product->name}' created successfully with SKU {$product->sku}."
        );
    }

    /* =========================================================================
     * PRODUCT EDIT & UPDATE
     * Pre-populates existing data and updates product records with optional image swap.
     * ========================================================================= */

    /**
     * Show product edit form pre-populated with existing details.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editProduct(int $id): View
    {
        $product = Product::findOrFail($id);
        $categories = Category::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.product-edit', compact('product', 'categories'));
    }

    /**
     * Update existing product details in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProduct(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'price'          => 'required|numeric|min:0',
            'old_price'      => 'nullable|numeric|min:0',
            'fabric'         => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock'       => 'nullable|boolean',
            'is_featured'    => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'badge'          => 'nullable|string|max:50',
            'badge_type'     => 'nullable|string|max:50',
            'short_desc'     => 'nullable|string|max:1000',
            'description'    => 'nullable|string|max:10000',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $updateData = [
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id ?: null,
            'price'          => (float) $request->price,
            'old_price'      => $request->filled('old_price') ? (float) $request->old_price : null,
            'fabric'         => $request->fabric ?: 'Handloom',
            'stock_quantity' => (int) $request->input('stock_quantity', 0),
            'in_stock'       => $request->boolean('in_stock', false),
            'is_featured'    => $request->boolean('is_featured', false),
            'is_new_arrival' => $request->boolean('is_new_arrival', false),
            'badge'          => $request->badge,
            'badge_type'     => $request->badge_type ?: 'ready',
            'short_desc'     => $request->short_desc,
            'description'    => $request->description,
        ];

        // If product name has changed, update slug avoiding collisions
        if ($request->name !== $product->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $updateData['slug'] = $slug;
        }

        // Handle optional replacement image upload
        if ($request->hasFile('image')) {
            $destinationPath = public_path('images/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $imageFile = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($destinationPath, $filename);

            // Clean up previous image if it was a custom upload
            if ($product->image && str_starts_with($product->image, 'images/products/') && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }

            $updateData['image'] = 'images/products/' . $filename;
        }

        $product->update($updateData);

        return redirect()->route('admin.products')->with(
            'success',
            "Product '{$product->name}' updated successfully."
        );
    }

    /* =========================================================================
     * PRODUCT DELETION
     * Removes product database record and purges associated custom uploaded images.
     * ========================================================================= */

    /**
     * Delete product from database and remove custom image files.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProduct(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;

        // Clean up uploaded image if located in custom products folder
        if ($product->image && str_starts_with($product->image, 'images/products/') && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }

        $product->delete();

        return redirect()->route('admin.products')->with(
            'success',
            "Product '{$productName}' deleted successfully."
        );
    }

    /* =========================================================================
     * COUPON & CAMPAIGN DISCOUNT MANAGEMENT
     * Seasonal marketing promotions, discount rules, limits, and redemptions.
     * ========================================================================= */

    /**
     * Display promotional campaign coupons list with KPI overview.
     *
     * @return \Illuminate\View\View
     */
    public function coupons(): View
    {
        $coupons = Coupon::orderByDesc('created_at')->paginate(15);

        $totalCampaigns = Coupon::count();
        $activeCampaigns = Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->count();
        $totalRedemptions = Coupon::sum('used_count');
        $totalDiscountGiven = (float) Order::whereNotNull('coupon_code')
            ->where('status', '!=', 'cancelled')
            ->sum('discount_amount');

        return view('admin.coupons', compact(
            'coupons',
            'totalCampaigns',
            'activeCampaigns',
            'totalRedemptions',
            'totalDiscountGiven'
        ));
    }

    /**
     * Display the campaign coupon creation form.
     *
     * @return \Illuminate\View\View
     */
    public function couponCreate(): View
    {
        return view('admin.coupon-create');
    }

    /**
     * Store a newly created promotional coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function couponStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'name'             => ['nullable', 'string', 'max:255'],
            'type'             => ['required', 'in:percent,fixed'],
            'value'            => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount'     => ['nullable', 'numeric', 'min:0'],
            'usage_limit'      => ['nullable', 'integer', 'min:1'],
            'expires_at'       => ['nullable', 'date'],
            'is_active'        => ['nullable'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        Coupon::create($validated);

        return redirect()->route('admin.coupons')->with(
            'success',
            "Campaign Coupon '{$validated['code']}' created successfully!"
        );
    }

    /**
     * Toggle active status for a promotional coupon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function couponToggle(int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        $statusText = $coupon->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.coupons')->with(
            'success',
            "Campaign Coupon '{$coupon->code}' has been {$statusText}."
        );
    }

    /**
     * Delete a promotional coupon from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function couponDestroy(int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.coupons')->with(
            'success',
            "Campaign Coupon '{$code}' deleted successfully."
        );
    }
}
