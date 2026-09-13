<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

/* =========================================================================
 * ADMIN CONTROLLER
 * Executive management for orders, status transitions, and inventory stock.
 * Protected by 'auth' and 'admin' middleware guards.
 * ========================================================================= */
class AdminController extends Controller
{
    /**
     * Display executive dashboard overview with KPIs and recent order stream.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Calculate core performance indicators
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();

        // Retrieve latest 10 customer transactions
        $recentOrders = Order::with('items')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'pendingOrdersCount',
            'totalProducts',
            'recentOrders'
        ));
    }

    /**
     * Display paginated orders portfolio with status filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function orders(Request $request)
    {
        $status = $request->input('status');

        $query = Order::with(['items', 'user'])->latest();

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

        return view('admin.orders', compact('orders', 'status', 'statusCounts'));
    }

    /**
     * Display full order invoice, customer shipping address, and item list.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showOrder($id)
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);

        return view('admin.order-detail', compact('order'));
    }

    /**
     * Update customer order lifecycle status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,in_transit,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with(
            'success',
            "Order #{$order->order_number} status updated from '{$oldStatus}' to '{$order->status}' successfully."
        );
    }

    /**
     * Display product catalog inventory with stock status controls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function products(Request $request)
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
    public function toggleStock($id)
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
}
