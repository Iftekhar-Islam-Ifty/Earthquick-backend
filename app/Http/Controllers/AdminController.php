<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
use App\Services\AdminImageUpload;
use App\Services\OrderCancellationService;
use App\Services\OrderRetentionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
            ->whereNull('archived_at')
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
     */
    public function exportOrders(Request $request): StreamedResponse
    {
        $status = $request->query('status');
        $folder = $request->query('folder', 'active');

        $query = Order::query()->with('paymentRecorder')->latest();
        $query->when($folder === 'archived', fn ($q) => $q->whereNotNull('archived_at'))
            ->when($folder !== 'archived', fn ($q) => $q->whereNull('archived_at'));
        if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'in_transit', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        $filename = 'rthquick_sales_orders_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM so Excel opens Bengali & special characters flawlessly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

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
                'Payment Status',
                'Paid At',
                'COD Collection Source',
                'COD Collection Note',
                'Payment Recorded By',
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
                        $order->payment_status ?? 'unknown',
                        $order->paid_at?->format('Y-m-d H:i:s') ?? '',
                        $order->cod_collection_channel ?? '',
                        $order->cod_collection_note ?? '',
                        $order->paymentRecorder?->email ?? '',
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
     */
    public function orders(Request $request): View
    {
        $status = $request->input('status');
        $search = trim($request->input('search', ''));

        $folder = $request->query('folder', 'active');
        if (! in_array($folder, ['active', 'archived', 'trash'], true)) {
            $folder = 'active';
        }
        $query = Order::with(['items', 'user'])->latest();
        if ($folder === 'trash') {
            $query->onlyTrashed();
        } elseif ($folder === 'archived') {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

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
        $countsQuery = match ($folder) {
            'trash' => Order::onlyTrashed(),
            'archived' => Order::whereNotNull('archived_at'),
            default => Order::whereNull('archived_at'),
        };
        $statusCounts = ['all' => (clone $countsQuery)->count()];
        foreach (['pending', 'confirmed', 'processing', 'in_transit', 'delivered', 'cancelled'] as $countStatus) {
            $statusCounts[$countStatus] = (clone $countsQuery)->where('status', $countStatus)->count();
        }
        $folderCounts = [
            'active' => Order::whereNull('archived_at')->count(),
            'archived' => Order::whereNotNull('archived_at')->count(),
            'trash' => Order::onlyTrashed()->count(),
        ];

        return view('admin.orders', compact('orders', 'status', 'statusCounts', 'search', 'folder', 'folderCounts'));
    }

    /**
     * Display full order invoice, customer shipping address, and item list.
     */
    public function showOrder(int $id, OrderRetentionService $retention): View
    {
        $order = Order::withTrashed()->with(['items.product', 'items.variant', 'user', 'paymentRecorder', 'statusEvents.actor', 'cancellationRequests.decisionMaker', 'returnRequests.item', 'returnRequests.decisionMaker', 'returnRequests.receiver', 'returnRequests.inspector', 'returnRequests.restocker', 'returnRequests.refund.approver', 'returnRequests.refund.completer'])->findOrFail($id);
        $canTrashOrder = $retention->canTrash($order);
        $canArchiveOrder = $retention->canArchive($order);

        if ($order->trashed()) {
            return view('admin.order-trash-detail', compact('order'));
        }

        return view('admin.order-detail', compact('order', 'canTrashOrder', 'canArchiveOrder'));
    }

    public function archiveOrder(Request $request, int $id, OrderRetentionService $retention): RedirectResponse
    {
        $retention->archive($id, $request->user());
        return redirect()->route('admin.orders', ['folder' => 'archived'])->with('success', 'Order archived. It can be restored at any time.');
    }

    public function unarchiveOrder(int $id, OrderRetentionService $retention): RedirectResponse
    {
        $retention->unarchive($id);
        return redirect()->route('admin.orders.show', $id)->with('success', 'Order returned to the active list.');
    }

    public function trashOrder(Request $request, int $id, OrderRetentionService $retention): RedirectResponse
    {
        $data = $request->validate([
            'confirm_order_number' => 'required|string|max:100',
            'deletion_reason' => 'required|string|min:10|max:500',
            'confirm_permanent' => 'accepted',
        ]);
        $retention->trash($id, $request->user(), $data['confirm_order_number'], $data['deletion_reason']);
        return redirect()->route('admin.orders', ['folder' => 'trash'])->with('success', 'Order moved to Trash. It remains restorable for at least 30 days.');
    }

    public function restoreTrashedOrder(int $id, OrderRetentionService $retention): RedirectResponse
    {
        $retention->restore($id);
        return redirect()->route('admin.orders.show', $id)->with('success', 'Order restored from Trash.');
    }

    public function purgeOrder(Request $request, int $id, OrderRetentionService $retention): RedirectResponse
    {
        $data = $request->validate([
            'confirm_order_number' => 'required|string|max:100',
            'confirm_permanent' => 'accepted',
        ]);
        $retention->purge($id, $request->user(), $data['confirm_order_number']);
        return redirect()->route('admin.orders', ['folder' => 'trash'])->with('success', 'Order permanently deleted after its 30-day recovery period. Stock was not changed.');
    }

    /**
     * Display printable packing slip and invoice for an order.
     */
    public function orderInvoice(int $id): View
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);

        return view('admin.order-invoice', compact('order'));
    }

    /**
     * Update customer order lifecycle status and fulfillment tracking logistics.
     */
    public function updateOrderStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,in_transit,delivered,cancelled',
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if ($request->status === 'cancelled') {
            $candidate = Order::findOrFail($id);
            if (! in_array($candidate->status, ['pending', 'confirmed'], true)
                || $candidate->payment_method !== 'cod'
                || $candidate->payment_status !== 'due_on_delivery'
                || $candidate->paid_at !== null) {
                throw ValidationException::withMessages([
                    'status' => 'Only an unpaid COD order before processing can be cancelled here. Review delivery or refund separately.',
                ]);
            }
            $request->validate(['admin_notes' => 'required|string|min:5|max:2000']);
            app(OrderCancellationService::class)->cancel($id, $request->user(), trim($request->admin_notes));

            return redirect()->back()->with('success', 'Order cancelled; inventory and coupon redemption reconciled.');
        }

        [$order, $oldStatus] = DB::transaction(function () use ($request, $id) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $oldStatus = $order->status;
            if ($oldStatus === 'cancelled') {
                throw ValidationException::withMessages(['status' => 'A cancelled order cannot be reopened here.']);
            }
            $stages = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'in_transit' => 3, 'delivered' => 4];
            if ($stages[$request->status] < $stages[$oldStatus]) {
                throw ValidationException::withMessages(['status' => 'A fulfilled order cannot be moved backwards through this form.']);
            }
            if ($order->payment_method === 'cod' && $order->payment_status === 'paid' && $request->status !== 'delivered') {
                throw ValidationException::withMessages([
                    'status' => 'A paid COD order must remain delivered. Contact support to correct this record.',
                ]);
            }

            $order->update([
                'status' => $request->status,
                'courier_name' => $request->courier_name,
                'tracking_number' => $request->tracking_number,
                'admin_notes' => $request->admin_notes,
            ]);

            if ($oldStatus !== $order->status) {
                $order->statusEvents()->create([
                    'actor_user_id' => auth()->id(),
                    'source' => 'admin',
                    'from_status' => $oldStatus,
                    'to_status' => $order->status,
                    'note' => $request->admin_notes,
                ]);
            }

            return [$order, $oldStatus];
        });

        if ($oldStatus !== $order->status) {
            app(\App\Services\CustomerCommunications::class)->order($order, 'status', str_replace('_', ' ', $order->status));
        }

        return redirect()->back()->with(
            'success',
            "Order #{$order->order_number} details and status updated from '{$oldStatus}' to '{$order->status}' successfully."
        );
    }

    public function decideCancellation(Request $request, int $id, int $requestId): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:approve,reject',
            'decision_note' => 'required|string|min:5|max:2000',
        ]);

        if ($data['decision'] === 'approve') {
            app(OrderCancellationService::class)->cancel($id, $request->user(), trim($data['decision_note']), $requestId);
        } else {
            DB::transaction(function () use ($id, $requestId, $data, $request) {
                $order = Order::query()->lockForUpdate()->findOrFail($id);
                $cancellation = $order->cancellationRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
                if ($cancellation->status !== 'pending') {
                    throw ValidationException::withMessages(['cancellation' => 'This request was already decided.']);
                }
                $cancellation->update([
                    'status' => 'rejected',
                    'decided_by_user_id' => $request->user()->id,
                    'decision_note' => trim($data['decision_note']),
                    'decided_at' => now(),
                ]);
            });
            app(\App\Services\CustomerCommunications::class)->order(Order::findOrFail($id), 'cancellation_rejected');
        }

        return redirect()->route('admin.orders.show', $id)
            ->with('success', $data['decision'] === 'approve' ? 'Order cancelled and request approved.' : 'Cancellation request declined.');
    }

    /** Record a reconciled COD collection separately from delivery. */
    public function markCodPaid(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'cod_collection_channel' => 'required|in:in_house,courier_remittance',
            'cod_collection_note' => 'nullable|string|max:255',
            'confirm_collected' => 'accepted',
        ]);

        DB::transaction(function () use ($data, $id) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            if ($order->payment_method !== 'cod' || $order->status !== 'delivered'
                || ! in_array($order->payment_status, ['due_on_delivery', 'unknown'], true)
                || $order->paid_at !== null) {
                throw ValidationException::withMessages([
                    'payment' => 'Only a delivered, unpaid COD order can be marked paid.',
                ]);
            }

            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'paid_recorded_by' => auth()->id(),
                'cod_collection_channel' => $data['cod_collection_channel'],
                'cod_collection_note' => $data['cod_collection_note'] ?? null,
            ]);
        });

        app(\App\Services\CustomerCommunications::class)->order(Order::findOrFail($id), 'cod_paid');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'COD collection recorded as paid.');
    }

    /* =========================================================================
     * PRODUCT CATALOG & INVENTORY LISTING
     * Displays all catalog items with filtering, stock toggles, and edit actions.
     * ========================================================================= */

    /**
     * Display product catalog inventory with stock status controls.
     */
    public function products(Request $request): View
    {
        $categorySlug = $request->input('category');
        $vendorSlug = $request->input('vendor');
        $stockFilter = $request->input('stock');

        $query = Product::with(['category', 'subcategory', 'vendor'])->latest();

        // Apply category filter
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Apply vendor filter
        if ($vendorSlug) {
            $query->whereHas('vendor', function ($q) use ($vendorSlug) {
                $q->where('slug', $vendorSlug);
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
        $vendors = Vendor::orderBy('name')->get();

        return view('admin.products', compact('products', 'categories', 'vendors', 'categorySlug', 'vendorSlug', 'stockFilter'));
    }

    /** Return current quantities for only the products visible on an admin catalog page. */
    public function stockLevels(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1|max:20',
            'ids.*' => 'required|integer|distinct|min:1',
        ]);

        $products = Product::query()->whereIn('id', $data['ids'])
            ->get(['id', 'stock_quantity', 'in_stock'])
            ->mapWithKeys(fn (Product $product) => [$product->id => [
                'units' => (int) $product->stock_quantity,
                'in_stock' => (bool) $product->in_stock,
            ]]);

        return response()->json(['products' => $products])
            ->header('Cache-Control', 'private, no-store');
    }

    /**
     * Toggle product availability status (in stock vs out of stock).
     */
    public function toggleStock(int $id): JsonResponse|RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update([
            'in_stock' => ! $product->in_stock,
        ]);

        $statusText = $product->in_stock ? 'In Stock (Ready to Ship)' : 'Out of Stock';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_stock' => $product->in_stock,
                'message' => "'{$product->name}' is now marked as {$statusText}.",
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
     */
    public function createProduct(): View
    {
        $categories = Category::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $catalogSchema = config('catalog');

        return view('admin.product-create', compact('categories', 'vendors', 'catalogSchema'));
    }

    /**
     * Store newly created product in database with uploaded image.
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'product_type' => 'nullable|in:apparel,accessories,electronics,home,general',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'fabric' => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'badge' => 'nullable|string|max:50',
            'badge_type' => 'nullable|string|max:50',
            'short_desc' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:10000',
            'specifications' => 'nullable|json',
            'variants' => 'nullable|json',
            'warranty_info' => 'nullable|string|max:255',
            'is_returnable' => 'nullable|boolean',
            'return_window_days' => 'nullable|integer|min:1|max:365',
            'return_policy_note' => 'nullable|string|max:255',
            'delivery_class' => 'nullable|in:standard,fragile,oversized',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images' => 'nullable|array|max:8',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_role' => 'nullable|in:gallery,lifestyle,detail,packaging,size_chart',
            'gallery_alt_text' => 'nullable|string|max:255',
        ]);
        $this->ensureSubcategoryMatchesCategory($request);
        $variants = $request->has('variants') ? $this->decodeVariants($request) : null;

        $vendor = null;
        if ($request->filled('vendor_id')) {
            $vendor = Vendor::find($request->vendor_id);
        }
        if (! $vendor) {
            $vendor = Vendor::where('slug', 'nous-telos')->first() ?? Vendor::first();
        }

        // Generate collision-resistant unique slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $uploads = app(AdminImageUpload::class);
        $imagePath = $uploads->store($request->file('image'), 'images/products', 'image');
        try {
            $galleryPaths = $uploads->storeMany($request->file('gallery_images', []), 'images/products', 'gallery_images');
        } catch (\Throwable $exception) {
            $uploads->remove($imagePath, 'images/products');
            throw $exception;
        }

        // Generate human-readable dynamic SKU based on vendor code
        $vendorCode = $vendor ? $vendor->vendor_code : 'EQ';
        $sku = $vendorCode.'-'.strtoupper(Str::random(3)).'-'.rand(100, 999);

        try {
            $product = DB::transaction(function () use ($request, $vendor, $slug, $sku, $variants, $imagePath, $galleryPaths) {
                $product = Product::create([
                    'name' => $request->name,
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'slug' => $slug,
                    'sku' => $sku,
                    'category_id' => $request->category_id,
                    'subcategory_id' => $request->subcategory_id ?: null,
                    'product_type' => $request->input('product_type', 'general'),
                    'price' => (float) $request->price,
                    'old_price' => $request->filled('old_price') ? (float) $request->old_price : null,
                    'fabric' => $request->filled('fabric') ? $request->fabric : null,
                    'stock_quantity' => (int) $request->input('stock_quantity', 10),
                    'in_stock' => $request->boolean('in_stock', true),
                    'is_active' => $request->boolean('is_active', true),
                    'is_featured' => $request->boolean('is_featured', false),
                    'is_new_arrival' => $request->boolean('is_new_arrival', true),
                    'badge' => $request->badge,
                    'badge_type' => $request->badge_type ?: 'ready',
                    'short_desc' => $request->short_desc,
                    'description' => $request->description,
                    'specifications' => $this->decodeSpecifications($request),
                    'warranty_info' => $request->warranty_info,
                    'is_returnable' => $request->has('is_returnable') ? $request->boolean('is_returnable') : true,
                    'return_window_days' => ($request->has('is_returnable') ? $request->boolean('is_returnable') : true)
                        ? (int) $request->input('return_window_days', 7)
                        : null,
                    'return_policy_note' => $request->return_policy_note,
                    'delivery_class' => $request->input('delivery_class', 'standard'),
                    'image' => $imagePath,
                    'rating' => 5.0,
                    'reviews_count' => 0,
                ]);

                if ($variants !== null) {
                    $this->syncVariants($product, $variants);
                }
                $this->storeGalleryImages($request, $product, $galleryPaths);

                return $product;
            });
        } catch (\Throwable $exception) {
            foreach ([$imagePath, ...$galleryPaths] as $path) {
                $uploads->remove($path, 'images/products');
            }
            throw $exception;
        }

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
     */
    public function editProduct(int $id): View
    {
        $product = Product::with(['variants', 'images'])->findOrFail($id);
        $categories = Category::with('subcategories')->orderBy('sort_order')->orderBy('name')->get();
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $catalogSchema = config('catalog');

        return view('admin.product-edit', compact('product', 'categories', 'vendors', 'catalogSchema'));
    }

    /**
     * Update existing product details in database.
     */
    public function updateProduct(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'product_type' => 'nullable|in:apparel,accessories,electronics,home,general',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'fabric' => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'badge' => 'nullable|string|max:50',
            'badge_type' => 'nullable|string|max:50',
            'short_desc' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:10000',
            'specifications' => 'nullable|json',
            'variants' => 'nullable|json',
            'warranty_info' => 'nullable|string|max:255',
            'is_returnable' => 'nullable|boolean',
            'return_window_days' => 'nullable|integer|min:1|max:365',
            'return_policy_note' => 'nullable|string|max:255',
            'delivery_class' => 'nullable|in:standard,fragile,oversized',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images' => 'nullable|array|max:8',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_role' => 'nullable|in:gallery,lifestyle,detail,packaging,size_chart',
            'gallery_alt_text' => 'nullable|string|max:255',
            'existing_media' => 'nullable|array',
            'existing_media.*.role' => 'required|in:gallery,lifestyle,detail,packaging,size_chart',
            'existing_media.*.alt_text' => 'nullable|string|max:255',
            'existing_media.*.sort_order' => 'required|integer|min:0|max:999',
            'existing_media.*.remove' => 'nullable|boolean',
        ]);
        $this->ensureSubcategoryMatchesCategory($request);
        $variants = $request->has('variants') ? $this->decodeVariants($request, $product) : null;

        $updateData = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id ?: null,
            'product_type' => $request->input('product_type', 'general'),
            'price' => (float) $request->price,
            'old_price' => $request->filled('old_price') ? (float) $request->old_price : null,
            'fabric' => $request->filled('fabric') ? $request->fabric : null,
            'stock_quantity' => (int) $request->input('stock_quantity', 0),
            'in_stock' => $request->boolean('in_stock', false),
            'is_active' => $request->boolean('is_active', false),
            'is_featured' => $request->boolean('is_featured', false),
            'is_new_arrival' => $request->boolean('is_new_arrival', false),
            'badge' => $request->badge,
            'badge_type' => $request->badge_type ?: 'ready',
            'short_desc' => $request->short_desc,
            'description' => $request->description,
            'specifications' => $this->decodeSpecifications($request),
            'warranty_info' => $request->warranty_info,
            'is_returnable' => $request->has('is_returnable')
                ? $request->boolean('is_returnable')
                : $product->is_returnable,
            'return_window_days' => $request->has('is_returnable')
                ? ($request->boolean('is_returnable') ? (int) $request->input('return_window_days', 7) : null)
                : $product->return_window_days,
            'return_policy_note' => $request->has('return_policy_note')
                ? $request->return_policy_note
                : $product->return_policy_note,
            'delivery_class' => $request->input('delivery_class', $product->delivery_class ?? 'standard'),
        ];

        if ($request->has('vendor_id')) {
            $updateData['vendor_id'] = $request->filled('vendor_id') ? $request->vendor_id : $product->vendor_id;
        }

        // If product name has changed, update slug avoiding collisions
        if ($request->name !== $product->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $updateData['slug'] = $slug;
        }

        $uploads = app(AdminImageUpload::class);
        $newImagePath = null;
        $oldImagePath = $product->image;

        // Convert before changing the product or removing its old image.
        if ($request->hasFile('image')) {
            $newImagePath = $uploads->store($request->file('image'), 'images/products', 'image');
            $updateData['image'] = $newImagePath;
        }

        try {
            $galleryPaths = $uploads->storeMany($request->file('gallery_images', []), 'images/products', 'gallery_images');
        } catch (\Throwable $exception) {
            $uploads->remove($newImagePath, 'images/products');
            throw $exception;
        }

        try {
            DB::transaction(function () use ($request, $product, $updateData, $variants, $galleryPaths) {
                $product->update($updateData);
                if ($variants !== null) {
                    $this->syncVariants($product, $variants);
                }
                $this->syncExistingMedia($request, $product);
                $this->storeGalleryImages($request, $product, $galleryPaths);
            });
        } catch (\Throwable $exception) {
            foreach (array_filter([$newImagePath, ...$galleryPaths]) as $path) {
                $uploads->remove($path, 'images/products');
            }
            throw $exception;
        }

        if ($newImagePath) {
            $uploads->remove($oldImagePath, 'images/products');
        }

        return redirect()->route('admin.products')->with(
            'success',
            "Product '{$product->name}' updated successfully."
        );
    }

    /**
     * Keep the catalog hierarchy consistent when an admin assigns a subcategory.
     */
    private function ensureSubcategoryMatchesCategory(Request $request): void
    {
        if (! $request->filled('subcategory_id')) {
            return;
        }

        $belongsToCategory = Subcategory::query()
            ->whereKey($request->input('subcategory_id'))
            ->where('category_id', $request->input('category_id'))
            ->exists();

        if (! $belongsToCategory) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'The selected subcategory does not belong to the selected category.',
            ]);
        }
    }

    /**
     * Normalize optional category-specific catalog attributes from the admin
     * JSON field while keeping the database value consistently structured.
     */
    private function decodeSpecifications(Request $request): ?array
    {
        if (! $request->filled('specifications')) {
            return null;
        }

        $specifications = json_decode($request->input('specifications'), true);

        if ($specifications === [] || $specifications === null) {
            return null;
        }

        if (array_is_list($specifications) || count($specifications) > 20) {
            throw ValidationException::withMessages([
                'specifications' => 'Specifications must be a JSON object with no more than 20 label/value pairs.',
            ]);
        }

        $normalized = [];
        foreach ($specifications as $label => $value) {
            $label = trim((string) $label);
            if ($label === '' || mb_strlen($label) > 80 || (! is_scalar($value) && $value !== null)) {
                throw ValidationException::withMessages([
                    'specifications' => 'Specification labels must be under 80 characters and values must be simple text or numbers.',
                ]);
            }

            if (is_string($value) && mb_strlen($value) > 500) {
                throw ValidationException::withMessages([
                    'specifications' => 'Specification values must be 500 characters or fewer.',
                ]);
            }

            $normalized[$label] = is_string($value) ? trim($value) : $value;
        }

        return $normalized;
    }

    /**
     * Validate and normalize the admin JSON variant editor.
     *
     * @return list<array{sku: string, label: string, attributes: ?array, price: ?float, stock_quantity: int, is_active: bool}>
     */
    private function decodeVariants(Request $request, ?Product $product = null): array
    {
        if (! $request->filled('variants')) {
            return [];
        }

        $variants = json_decode($request->input('variants'), true);
        if (! is_array($variants) || ! array_is_list($variants) || count($variants) > 100) {
            throw ValidationException::withMessages([
                'variants' => 'Variants must be a JSON array containing no more than 100 options.',
            ]);
        }

        $normalized = [];
        $seenSkus = [];

        foreach ($variants as $index => $variant) {
            if (! is_array($variant)) {
                throw ValidationException::withMessages([
                    'variants' => 'Every variant must be a JSON object.',
                ]);
            }

            $sku = strtoupper(trim((string) ($variant['sku'] ?? '')));
            $label = trim((string) ($variant['label'] ?? ''));
            $attributes = $variant['attributes'] ?? null;
            $price = $variant['price'] ?? null;
            $stock = $variant['stock_quantity'] ?? 0;

            if ($sku === '' || mb_strlen($sku) > 100 || $label === '' || mb_strlen($label) > 255) {
                throw ValidationException::withMessages([
                    'variants' => 'Each variant requires a SKU (max 100 characters) and label (max 255 characters).',
                ]);
            }

            if (isset($seenSkus[$sku])) {
                throw ValidationException::withMessages([
                    'variants' => "Variant SKU {$sku} is duplicated in the JSON list.",
                ]);
            }
            $seenSkus[$sku] = true;

            if ($attributes !== null && (! is_array($attributes) || array_is_list($attributes) || count($attributes) > 10)) {
                throw ValidationException::withMessages([
                    'variants' => 'Variant #'.($index + 1).' attributes must be a JSON object with no more than 10 label/value pairs.',
                ]);
            }

            $normalizedAttributes = [];
            foreach ($attributes ?? [] as $attribute => $value) {
                $attribute = trim((string) $attribute);
                if ($attribute === '' || mb_strlen($attribute) > 80 || (! is_scalar($value) && $value !== null)) {
                    throw ValidationException::withMessages([
                        'variants' => 'Variant attribute labels must be under 80 characters and values must be simple text or numbers.',
                    ]);
                }
                $normalizedAttributes[$attribute] = is_string($value) ? trim($value) : $value;
            }

            if ($price !== null && $price !== '' && (! is_numeric($price) || (float) $price < 0)) {
                throw ValidationException::withMessages([
                    'variants' => "Variant {$sku} price must be empty or a non-negative number.",
                ]);
            }
            if (filter_var($stock, FILTER_VALIDATE_INT) === false || (int) $stock < 0) {
                throw ValidationException::withMessages([
                    'variants' => "Variant {$sku} stock_quantity must be a non-negative integer.",
                ]);
            }

            $normalized[] = [
                'sku' => $sku,
                'label' => $label,
                'attributes' => $normalizedAttributes ?: null,
                'price' => ($price === null || $price === '') ? null : (float) $price,
                'stock_quantity' => (int) $stock,
                'is_active' => array_key_exists('is_active', $variant) ? (bool) $variant['is_active'] : true,
            ];
        }

        if ($seenSkus !== []) {
            $conflictingSku = DB::table('product_variants')
                ->whereIn('sku', array_keys($seenSkus))
                ->when($product, fn ($query) => $query->where('product_id', '!=', $product->id))
                ->value('sku');

            if ($conflictingSku) {
                throw ValidationException::withMessages([
                    'variants' => "Variant SKU {$conflictingSku} is already used by another product.",
                ]);
            }
        }

        return $normalized;
    }

    /**
     * Replace the product's variant set and keep its summary stock in sync.
     *
     * @param  list<array<string, mixed>>  $variants
     */
    private function syncVariants(Product $product, array $variants): void
    {
        DB::transaction(function () use ($product, $variants) {
            $keptIds = [];

            foreach ($variants as $variantData) {
                $variant = $product->variants()->updateOrCreate(
                    ['sku' => $variantData['sku']],
                    $variantData
                );
                $keptIds[] = $variant->id;
            }

            $deleteQuery = $product->variants();
            if ($keptIds !== []) {
                $deleteQuery->whereNotIn('id', $keptIds);
            }
            $deleteQuery->delete();

            if ($variants !== []) {
                $activeStock = (int) $product->variants()
                    ->where('is_active', true)
                    ->sum('stock_quantity');

                $product->update([
                    'stock_quantity' => $activeStock,
                    'in_stock' => $activeStock > 0,
                ]);
            }
        });
    }

    /**
     * Store optional secondary product media uploaded by the central admin.
     */
    private function storeGalleryImages(Request $request, Product $product, array $paths): void
    {
        if ($paths === []) {
            return;
        }

        $nextSortOrder = ((int) $product->images()->max('sort_order')) + 1;
        foreach ($paths as $path) {
            $product->images()->create([
                'image_path' => $path,
                'role' => $request->input('gallery_role', 'gallery'),
                'alt_text' => $request->filled('gallery_alt_text') ? trim($request->gallery_alt_text) : null,
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }

    /**
     * Update role/accessibility metadata or remove existing secondary media.
     */
    private function syncExistingMedia(Request $request, Product $product): void
    {
        $submittedMedia = $request->input('existing_media', []);

        foreach ($product->images as $image) {
            $mediaData = $submittedMedia[$image->id] ?? null;
            if (! is_array($mediaData)) {
                continue;
            }

            if (filter_var($mediaData['remove'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $this->deleteProductMediaFile($image->image_path);
                $image->delete();

                continue;
            }

            $image->update([
                'role' => $mediaData['role'],
                'alt_text' => filled($mediaData['alt_text'] ?? null) ? trim($mediaData['alt_text']) : null,
                'sort_order' => (int) $mediaData['sort_order'],
            ]);
        }
    }

    private function deleteProductMediaFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'images/products/') && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    /* =========================================================================
     * PRODUCT DELETION
     * Removes product database record and purges associated custom uploaded images.
     * ========================================================================= */

    /**
     * Delete product from database and remove custom image files.
     */
    public function deleteProduct(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;

        foreach ($product->images as $image) {
            $this->deleteProductMediaFile($image->image_path);
        }

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
     */
    public function couponCreate(): View
    {
        return view('admin.coupon-create');
    }

    /**
     * Store a newly created promotional coupon.
     */
    public function couponStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable'],
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
     */
    public function couponToggle(int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = ! $coupon->is_active;
        $coupon->save();

        $statusText = $coupon->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.coupons')->with(
            'success',
            "Campaign Coupon '{$coupon->code}' has been {$statusText}."
        );
    }

    /**
     * Delete a promotional coupon from storage.
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
