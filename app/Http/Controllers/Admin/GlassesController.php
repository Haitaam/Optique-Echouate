<?php

namespace App\Http\Controllers\Admin;

use App\Features\Categories\Models\Category;
use App\Features\Products\Models\Product;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Gender;
use App\Models\Shape;
use App\Services\Metadata\ColorAnalyzer;
use App\Services\Metadata\FilenameParser;
use App\Services\Metadata\FolderClassifier;
use App\Services\Metadata\ImageScanner;
use App\Services\Metadata\ProductAssembler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class GlassesController extends \App\Http\Controllers\Controller
{
    public function index(ImageScanner $scanner)
    {
        $query = Product::with('categories');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        if ($brand = request('brand')) $query->where('brand', $brand);
        if ($gender = request('gender')) $query->where('gender', $gender);
        if ($category = request('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }
        if ($shape = request('frame_shape')) $query->where('frame_shape', $shape);
        if ($color = request('color')) $query->where('color', $color);

        $sortBy = request('sort_by', 'id');
        $sortOrder = request('sort_order', 'desc');

        $allowedSorts = ['id', 'name', 'price', 'stock', 'created_at', 'brand'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';

        $products = $query->orderBy($sortBy, $sortOrder)->paginate(50);
        $diskFiles = $this->getDiskFiles($scanner);

        $allProducts = Product::all();
        $validCount = $allProducts->filter(fn($p) => $p->imageExists())->count();
        $brokenCount = $allProducts->count() - $validCount;

        $stats = [
            'total' => $allProducts->count(),
            'valid' => $validCount,
            'broken' => $brokenCount,
            'categories' => Category::count(),
        ];

        $categories = Category::all(['id', 'name', 'slug']);

        $dbBrands = Brand::orderBy('name')->pluck('name');
        $dbGenders = Gender::orderBy('name')->pluck('name');
        $dbShapes = Shape::orderBy('name')->pluck('name');
        $dbColors = Color::orderBy('name')->pluck('name');

        $brands = $dbBrands->isNotEmpty() ? $dbBrands : $allProducts->pluck('brand')->unique()->sort()->values();
        $genders = $dbGenders->isNotEmpty() ? $dbGenders : collect(['Men', 'Women', 'Unisex']);
        $shapes = $dbShapes->isNotEmpty() ? $dbShapes : collect(['Round', 'Square', 'Rectangle', 'Aviator', 'Cat-eye', 'Butterfly', 'Wrap', 'Oval']);
        $colors = $dbColors->isNotEmpty() ? $dbColors : $allProducts->pluck('color')->unique()->sort()->values();

        $brandList = $this->brandList();

        return view('admin.glasses.index', compact(
            'products', 'diskFiles', 'stats', 'categories',
            'brands', 'genders', 'shapes', 'colors', 'brandList',
            'sortBy', 'sortOrder'
        ));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $genders = Gender::orderBy('name')->pluck('name');
        $shapes = Shape::orderBy('name')->pluck('name');
        $brands = $this->brandList();

        return view('admin.glasses.edit', compact('product', 'categories', 'genders', 'shapes', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'frame_shape' => 'required|string|max:50',
            'gender' => 'required|string|max:20',
            'material' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'is_luxury' => 'boolean',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'style_tags' => 'nullable|string',
            'replace_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data['style_tags'] = $data['style_tags']
            ? array_map('trim', explode(',', $data['style_tags']))
            : [];

        $data['slug'] = $product->slug ?: \Illuminate\Support\Str::slug($data['name'] . '-' . \Illuminate\Support\Str::random(6));

        if ($request->hasFile('replace_image')) {
            $file = $request->file('replace_image');
            $filename = $file->getClientOriginalName();

            $folder = $product->image
                ? dirname(ltrim($product->image, '/images/glasses/'))
                : 'men';

            if ($folder === '.') $folder = 'men';

            $targetDir = public_path('images/glasses/' . $folder);
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $data['image'] = '/images/glasses/' . $folder . '/' . $filename;
        }

        $data = $this->syncBrandColorShapeGender($data);

        $product->update($data);

        if (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        Cache::flush();

        return redirect()->route('admin.glasses.index')->with('success', 'Produit mis à jour.');
    }

    public function create()
    {
        return view('admin.glasses.create');
    }

    public function destroy(Request $request, Product $product)
    {
        $deleteFile = $request->boolean('delete_file');

        if ($deleteFile) {
            $path = public_path(ltrim($product->image, '/'));
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $product->categories()->detach();
        $product->delete();

        Cache::flush();

        $msg = $deleteFile
            ? 'Produit supprimé avec son image.'
            : 'Produit supprimé de la base de données.';

        return redirect()->route('admin.glasses.index')->with('success', $msg);
    }

    public function sync()
    {
        $exitCode = Artisan::call('glasses:sync');
        Cache::flush();

        $output = $exitCode === 0
            ? 'Catalogue synchronisé avec succès.'
            : 'Erreur lors de la synchronisation.';

        return redirect()->route('admin.glasses.index')->with(
            $exitCode === 0 ? 'success' : 'error',
            $output
        );
    }

    public function upload(Request $request, ImageScanner $scanner, FilenameParser $parser, FolderClassifier $classifier, ColorAnalyzer $colorAnalyzer, ProductAssembler $assembler)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'category' => 'required|string',
            'gender' => 'required|string|in:men,women,unisex',
            'color' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'frame_shape' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
        ]);

        $targetDir = public_path('images/glasses/' . $request->category);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $count = 0;
        foreach ($request->file('images') as $image) {
            $filename = $image->getClientOriginalName();
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $ext = $image->getClientOriginalExtension();

            $destPath = $targetDir . '/' . $filename;
            $image->move($targetDir, $filename);

            $relativeFolder = str_replace(public_path('images/glasses/'), '', $targetDir);
            $relativeFolder = ltrim($relativeFolder, '\\/');
            $imageUrl = '/images/glasses/' . ($relativeFolder ? $relativeFolder . '/' : '') . $filename;

            $folderData = $classifier->classify($relativeFolder . '/' . $filename);
            $filenameData = $parser->parse($filename, $basename);

            $detectedColor = $colorAnalyzer->analyze($destPath);
            $secondary = $colorAnalyzer->detectSecondary($destPath);

            $productData = $assembler->assemble([
                'url' => ltrim($imageUrl, '/'),
                'pathname' => $destPath,
                'filename' => $filename,
                'basename' => $basename,
                'extension' => $ext,
                'folder' => $relativeFolder,
            ], $folderData, $filenameData, $detectedColor, $secondary);

            if ($request->filled('color')) $productData['color'] = $request->color;
            if ($request->filled('price')) $productData['price'] = $request->price;
            if ($request->filled('name')) $productData['name'] = $request->name;
            if ($request->filled('frame_shape')) $productData['frame_shape'] = $request->frame_shape;
            if ($request->filled('material')) $productData['material'] = $request->material;
            if ($request->filled('tags')) {
                $productData['style_tags'] = array_map('trim', explode(',', $request->tags));
            }

            $productData = $this->syncBrandColorShapeGender($productData);

            Product::create($productData);
            $count++;
        }

        Cache::flush();

        return redirect()->route('admin.glasses.index')->with('success', "{$count} image(s) importée(s).");
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $deleteFile = $request->boolean('delete_file');
        $count = 0;

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;

            if ($deleteFile) {
                $path = public_path(ltrim($product->image, '/'));
                if (File::exists($path)) File::delete($path);
            }

            $product->categories()->detach();
            $product->delete();
            $count++;
        }

        Cache::flush();
        return response()->json(['redirect' => route('admin.glasses.index')]);
    }

    public function bulkUpdate(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $data = $request->only(['brand', 'category_id', 'tags']);

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;

            $update = [];
            if (!empty($data['brand'])) $update['brand'] = $data['brand'];
            if (!empty($data['tags'])) $update['style_tags'] = array_map('trim', explode(',', $data['tags']));
            if (!empty($update)) {
                $update = $this->syncBrandColorShapeGender($update);
                $product->update($update);
            }

            if (!empty($data['category_id'])) {
                $product->categories()->sync([(int) $data['category_id']]);
            }
        }

        Cache::flush();
        return response()->json(['redirect' => route('admin.glasses.index')]);
    }

    public function bulkSyncFix(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $count = 0;

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;

            if (!$product->imageExists()) {
                $product->categories()->detach();
                $product->delete();
                $count++;
            }
        }

        Cache::flush();
        return response()->json(['redirect' => route('admin.glasses.index')]);
    }

    public function smartFix()
    {
        $deleted = 0;
        $products = Product::all();

        foreach ($products as $product) {
            if (!$product->imageExists()) {
                $product->categories()->detach();
                $product->delete();
                $deleted++;
            }
        }

        Artisan::call('glasses:sync');
        Cache::flush();

        return response()->json(['redirect' => route('admin.glasses.index')]);
    }

    public function export()
    {
        $format = request('format', 'csv');
        $products = Product::with('categories')->get();

        if ($format === 'json') {
            return response()->streamDownload(function () use ($products) {
                echo $products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand' => $p->brand,
                    'gender' => $p->gender,
                    'color' => $p->color,
                    'frame_shape' => $p->frame_shape,
                    'material' => $p->material,
                    'price' => (float) $p->price,
                    'image' => $p->image,
                    'image_exists' => $p->imageExists(),
                    'categories' => $p->categories->pluck('name')->implode(', '),
                ])->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, 'lunettes-export.json', ['Content-Type' => 'application/json']);
        }

        $headers = ['ID', 'Nom', 'Marque', 'Genre', 'Couleur', 'Forme', 'Matière', 'Prix (MAD)', 'Image', 'Image existe', 'Catégories'];
        return response()->streamDownload(function () use ($products, $headers) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, $headers);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->name,
                    $p->brand,
                    $p->gender,
                    $p->color,
                    $p->frame_shape,
                    $p->material,
                    (float) $p->price,
                    $p->image,
                    $p->imageExists() ? 'Oui' : 'Non',
                    $p->categories->pluck('name')->implode(', '),
                ]);
            }

            fclose($handle);
        }, 'lunettes-export.csv', ['Content-Type' => 'text/csv']);
    }

    protected function getDiskFiles(ImageScanner $scanner): array
    {
        $files = $scanner->scan();
        $grouped = [];
        foreach ($files as $f) {
            $folder = $f['folder'] ?: '/';
            $grouped[$folder][] = $f['filename'];
        }
        ksort($grouped);
        return $grouped;
    }

    protected function brandList(): array
    {
        $dbBrands = Brand::orderBy('name')->pluck('name')->toArray();
        if (!empty($dbBrands)) {
            return $dbBrands;
        }

        return [
            'Armani', 'Armani Style', 'Balenciaga', 'Biaggi', 'Bolon',
            'Carrera', 'Dolce & Gabbana', 'Emporio Armani', 'Gucci',
            'Hugo Boss', 'Maui Jim', 'Michael Kors', 'Oakley', 'Persol',
            'Police', 'Porsche Design', 'Prada', 'Ray-Ban', 'Tom Ford',
            'Valentino', 'Versace', 'Victoria Beckham', 'Vogue',
        ];
    }

    private function syncBrandColorShapeGender(array $data): array
    {
        if (isset($data['brand']) && is_string($data['brand'])) {
            $brand = Brand::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['brand'])],
                ['name' => $data['brand']]
            );
            $data['brand_id'] = $brand->id;
        }

        if (isset($data['color']) && is_string($data['color'])) {
            $color = Color::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['color'])],
                ['name' => $data['color']]
            );
            $data['color_id'] = $color->id;
        }

        if (isset($data['frame_shape']) && is_string($data['frame_shape'])) {
            $shape = Shape::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['frame_shape'])],
                ['name' => $data['frame_shape']]
            );
            $data['shape_id'] = $shape->id;
        }

        if (isset($data['gender']) && is_string($data['gender'])) {
            $gender = Gender::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['gender'])],
                ['name' => $data['gender']]
            );
            $data['gender_id'] = $gender->id;
        }

        return $data;
    }
}
