<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ExportSeederCommand extends Command
{
    protected $signature = 'db:export-seeder {--output=database/seeders/MasterSeeder.php}';
    protected $description = 'Export current database data to MasterSeeder.php';

    public function handle(): int
    {
        $this->info('Exporting database to seeder...');

        $users = User::with('store')->get();
        $categories = Category::all();
        $products = Product::with(['store', 'category', 'images'])->get();

        $seeder = $this->generateSeeder($users, $categories, $products);

        $outputPath = base_path($this->option('output'));
        File::put($outputPath, $seeder);

        $this->info("Seeder exported to: {$outputPath}");
        $this->info('Run: php artisan db:seed to import');

        return Command::SUCCESS;
    }

    private function generateSeeder($users, $categories, $products): string
    {
        $lines = [];

        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'namespace Database\Seeders;';
        $lines[] = '';
        $lines[] = 'use App\Models\Category;';
        $lines[] = 'use App\Models\Product;';
        $lines[] = 'use App\Models\ProductImage;';
        $lines[] = 'use App\Models\Store;';
        $lines[] = 'use App\Models\User;';
        $lines[] = 'use Illuminate\Database\Seeder;';
        $lines[] = 'use Illuminate\Support\Facades\Hash;';
        $lines[] = '';
        $lines[] = 'class MasterSeeder extends Seeder';
        $lines[] = '{';
        $lines[] = '    public function run(): void';
        $lines[] = '    {';

        // Users section
        $lines[] = '        // === USERS ===';
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->id] = '$' . lcfirst(ucwords(str_replace(['-', '.'], '', $user->role))) . $user->id;
            $lines[] = '';
            $lines[] = '        ' . $userMap[$user->id] . ' = User::create([';
            $lines = $this->addModelAttributes($user, $lines, ['password']); // exclude password, add manually
            $lines[] = "            'password' => Hash::make('password'),";
            $lines[] = "            'email_verified_at' => now(),";
            $lines[] = '        ]);';
        }

        // Stores section
        $lines[] = '';
        $lines[] = '        // === STORES ===';
        $storeMap = [];
        foreach ($users->whereNotNull('store')->pluck('store') as $store) {
            $storeMap[$store->id] = '$store' . $store->id;
            $lines[] = '';
            $lines[] = '        ' . $storeMap[$store->id] . ' = Store::create([';
            $lines[] = "            'user_id' => {$userMap[$store->user_id]}->id,";
            $lines = $this->addModelAttributes($store, $lines, ['user_id']);
            $lines[] = '        ]);';
        }

        // Categories section
        $lines[] = '';
        $lines[] = '        // === CATEGORIES ===';
        $categoryMap = [];
        foreach ($categories as $category) {
            $varName = '$cat' . ucfirst($this->toVariableName($category->name));
            $categoryMap[$category->id] = $varName;
            $lines[] = '';
            $lines[] = '        ' . $varName . ' = Category::create([';
            $lines = $this->addModelAttributes($category, $lines);
            $lines[] = '        ]);';
        }

        // Products section
        $lines[] = '';
        $lines[] = '        // === PRODUCTS ===';
        $productMap = [];
        foreach ($products as $product) {
            $varName = '$product' . $product->id;
            $productMap[$product->id] = $varName;
            $lines[] = '';
            $lines[] = '        ' . $varName . ' = Product::create([';
            $lines[] = "            'store_id' => {$storeMap[$product->store_id]}->id,";
            $lines[] = "            'category_id' => {$categoryMap[$product->category_id]}->id,";
            $lines = $this->addModelAttributes($product, $lines, ['store_id', 'category_id']);
            $lines[] = '        ]);';

            // Product images
            if ($product->images->isNotEmpty()) {
                $lines[] = '';
                foreach ($product->images as $image) {
                    $lines[] = "        ProductImage::create([";
                    $lines[] = "            'product_id' => {$varName}->id,";
                    $lines = $this->addModelAttributes($image, $lines, ['product_id']);
                    $lines[] = '        ]);';
                }
            }
        }

        $lines[] = '';
        $lines[] = '    }';
        $lines[] = '}';

        return implode("\n", $lines);
    }

    private function addModelAttributes($model, array $lines, array $exclude = []): array
    {
        $fillable = $model->getFillable();

        foreach ($fillable as $field) {
            if (in_array($field, $exclude)) {
                continue;
            }

            $value = $model->{$field};
            $lines[] = $this->formatAttribute($field, $value);
        }

        return $lines;
    }

    private function formatAttribute(string $field, $value): string
    {
        if ($value === null) {
            return "            '{$field}' => null,";
        }

        if (is_bool($value)) {
            return "            '{$field}' => " . ($value ? 'true' : 'false') . ",";
        }

        // Always treat as string if it looks like a phone number (starts with 0)
        // or if it's a string type
        if (is_string($value) || (is_int($value) && $field === 'phone')) {
            return "            '{$field}' => '" . addslashes((string) $value) . "',";
        }

        return "            '{$field}' => {$value},";
    }

    private function toVariableName(string $name): string
    {
        return str_replace(' ', '', ucwords(strtolower(str_replace(['-', '_'], ' ', $name))));
    }
}
