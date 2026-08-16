<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductStockExport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('company_id', auth()->user()->company_id);

        if ($request->has('search') && $request->search) {
            $search = addcslashes($request->search, '%_\\');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('supplier', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status == 'low_stock') {
                $query->whereRaw('stock_quantity <= min_stock_level AND stock_quantity > 0');
            } elseif ($request->stock_status == 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status == 'in_stock') {
                $query->whereRaw('stock_quantity > min_stock_level');
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code',
            'category' => 'required|in:yedek_parca,arac_gerec,kimyasal,elektronik,mekanik',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [], ['code' => 'Ürün markası']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Product::create(array_merge($request->all(), ['company_id' => auth()->user()->company_id]));

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla eklendi.');
    }

    public function show(Product $product)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$product->exists) {
            abort(404, 'Ürün bulunamadı.');
        }
        abort_if($product->company_id !== auth()->user()->company_id, 403);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$product->exists) {
            abort(404, 'Ürün bulunamadı.');
        }
        abort_if($product->company_id !== auth()->user()->company_id, 403);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$product->exists) {
            abort(404, 'Ürün bulunamadı.');
        }
        abort_if($product->company_id !== auth()->user()->company_id, 403);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'category' => 'required|in:yedek_parca,arac_gerec,kimyasal,elektronik,mekanik',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [], ['code' => 'Ürün markası']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product->update($request->except('company_id'));

        return redirect()->route('products.index')->with('success', 'Ürün bilgileri güncellendi.');
    }

    public function destroy(Product $product)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$product->exists) {
            abort(404, 'Ürün bulunamadı.');
        }
        abort_if($product->company_id !== auth()->user()->company_id, 403);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Ürün silindi.');
    }

    public function bulkStockPage()
    {
        $products = Product::where('company_id', auth()->user()->company_id)
                          ->orderBy('name', 'asc')
                          ->get();

        return view('products.bulk-stock', compact('products'));
    }

    public function bulkUpdateStock(Request $request)
    {
        $validated = $request->validate([
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0'
        ]);

        $companyId = auth()->user()->company_id;
        $updatedCount = 0;

        foreach ($validated['stocks'] as $productId => $newStock) {
            $product = Product::where('id', $productId)
                              ->where('company_id', $companyId)
                              ->first();

            if ($product && $product->stock_quantity != $newStock) {
                $product->update(['stock_quantity' => $newStock]);
                $updatedCount++;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} ürünün stok bilgisi güncellendi."
            ]);
        }

        return redirect()->route('products.bulk-stock')
                        ->with('success', "{$updatedCount} ürünün stok bilgisi güncellendi.");
    }

    public function exportStockPdf()
    {
        $products = Product::where('company_id', auth()->user()->company_id)
                          ->orderBy('name', 'asc')
                          ->get();

        $company = auth()->user()->company;
        $date = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('products.stock-pdf', compact('products', 'company', 'date'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('urun-stok-raporu-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportStockExcel()
    {
        return Excel::download(
            new ProductStockExport(auth()->user()->company_id),
            'urun-stok-raporu-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
