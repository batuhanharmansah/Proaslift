<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    private function serialize(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'description' => $product->description,
            'category' => $product->category,
            'category_label' => $product->category_label,
            'unit' => $product->unit,
            'cost_price' => (float) $product->cost_price,
            'sale_price' => (float) $product->sale_price,
            'stock_quantity' => (int) $product->stock_quantity,
            'min_stock_level' => (int) $product->min_stock_level,
            'supplier' => $product->supplier,
            'location' => $product->location,
            'is_active' => (bool) $product->is_active,
            'notes' => $product->notes,
            'stock_status' => $product->stock_status,
            'stock_status_label' => $product->stock_status_label,
        ];
    }

    public function index(Request $request)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
        }

        $query = Product::where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = addcslashes($request->search, '%_\\');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('supplier', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get()->map(fn (Product $p) => $this->serialize($p));

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function show(Request $request, $id)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
        }

        $product = Product::where('company_id', $companyId)->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->serialize($product)]);
    }

    public function store(Request $request)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'code')->where(fn ($q) => $q->where('company_id', $companyId)),
            ],
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
            return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $validator->errors()], 422);
        }

        $product = Product::create(array_merge($validator->validated(), [
            'company_id' => $companyId,
            'is_active' => true,
        ]));

        return response()->json(['success' => true, 'message' => 'Ürün eklendi.', 'data' => $this->serialize($product)], 201);
    }

    public function update(Request $request, $id)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
        }

        $product = Product::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required', 'string', 'max:100',
                Rule::unique('products', 'code')->where(fn ($q) => $q->where('company_id', $companyId))->ignore($product->id),
            ],
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
            'is_active' => 'sometimes|boolean',
        ], [], ['code' => 'Ürün markası']);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Doğrulama hatası', 'errors' => $validator->errors()], 422);
        }

        $product->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Ürün güncellendi.', 'data' => $this->serialize($product->fresh())]);
    }

    public function destroy(Request $request, $id)
    {
        $companyId = $this->resolveMobileCompanyId($request);
        if ($companyId === null) {
            return response()->json(['success' => false, 'message' => 'Firma bilgisi bulunamadı'], 403);
        }

        $product = Product::where('company_id', $companyId)->findOrFail($id);
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Ürün silindi.']);
    }
}
