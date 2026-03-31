<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Merchant;
use App\Models\MerchantWebsite;
use App\Models\Offer;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'prename'  => 'Super',
            'email'    => 'admin@prixtunisix.tn',
            'password' => Hash::make('Admin@12345'),
            'role'     => 'admin',
        ]);

        // ── Employee ───────────────────────────────────────────────────────
        $empUser = User::create([
            'name'     => 'Ben Ali',
            'prename'  => 'Sami',
            'email'    => 'employee@prixtunisix.tn',
            'password' => Hash::make('Employee@12345'),
            'role'     => 'employee',
        ]);
        Employee::create(['user_id' => $empUser->id, 'position' => 'Product Curator']);

        // ── Demo client ────────────────────────────────────────────────────
        $clientUser = User::create([
            'name'     => 'Nefzi',
            'prename'  => 'Mohamed',
            'email'    => 'client@prixtunisix.tn',
            'password' => Hash::make('Client@12345'),
            'role'     => 'client',
        ]);
        Client::create(['user_id' => $clientUser->id, 'phone' => '+21698000001']);

        // ── Merchant websites (scraped sources) ────────────────────────────
        $mytek = MerchantWebsite::create([
            'name'      => 'MyTek',
            'base_url'  => 'https://www.mytek.tn',
            'logo_url'  => 'https://www.mytek.tn/skin/frontend/mytek2019/default/images/logo.png',
            'is_active' => true,
        ]);
        $tunisianet = MerchantWebsite::create([
            'name'      => 'Tunisianet',
            'base_url'  => 'https://www.tunisianet.com.tn',
            'logo_url'  => 'https://www.tunisianet.com.tn/img/logo.png',
            'is_active' => true,
        ]);
        MerchantWebsite::create([
            'name'      => 'SFax Computer',
            'base_url'  => 'https://www.sfaxcomputer.com.tn',
            'is_active' => true,
        ]);

        // ── Categories ─────────────────────────────────────────────────────
        $electronics = Category::create(['name' => 'Electronique', 'slug' => 'electronique']);
        $laptops     = Category::create(['name' => 'PC Portables',  'slug' => 'pc-portables',  'parent_id' => $electronics->id]);
        $phones      = Category::create(['name' => 'Smartphones',   'slug' => 'smartphones',   'parent_id' => $electronics->id]);
        $tablets     = Category::create(['name' => 'Tablettes',     'slug' => 'tablettes',     'parent_id' => $electronics->id]);
        $components  = Category::create(['name' => 'Composants PC', 'slug' => 'composants-pc', 'parent_id' => $electronics->id]);

        // ── Brands ─────────────────────────────────────────────────────────
        $hp     = Brand::create(['name' => 'HP',      'slug' => 'hp']);
        $dell   = Brand::create(['name' => 'Dell',    'slug' => 'dell']);
        $lenovo = Brand::create(['name' => 'Lenovo',  'slug' => 'lenovo']);
        $apple  = Brand::create(['name' => 'Apple',   'slug' => 'apple']);
        $samsung= Brand::create(['name' => 'Samsung', 'slug' => 'samsung']);

        // ── Products ───────────────────────────────────────────────────────
        $laptop1 = Product::create([
            'name'         => 'HP Laptop 15-fc0097nf',
            'slug'         => 'hp-laptop-15-fc0097nf',
            'category_id'  => $laptops->id,
            'brand_id'     => $hp->id,
            'is_validated' => true,
            'specifications' => [
                'cpu'     => 'AMD Ryzen 5 7520U',
                'ram'     => '8 GB DDR5',
                'storage' => '512 GB SSD',
                'screen'  => '15.6 pouces FHD',
                'gpu'     => 'AMD Radeon 610M',
            ],
        ]);

        $laptop2 = Product::create([
            'name'         => 'Lenovo IdeaPad 3 15IAU7',
            'slug'         => 'lenovo-ideapad-3-15iau7',
            'category_id'  => $laptops->id,
            'brand_id'     => $lenovo->id,
            'is_validated' => true,
            'specifications' => [
                'cpu'     => 'Intel Core i5-1235U',
                'ram'     => '8 GB DDR4',
                'storage' => '256 GB SSD',
                'screen'  => '15.6 pouces FHD',
            ],
        ]);

        // ── Offers from MyTek ──────────────────────────────────────────────
        $offer1 = Offer::create([
            'product_id'          => $laptop1->id,
            'merchant_website_id' => $mytek->id,
            'raw_title'           => 'PC Portable HP 15-fc0097nf Ryzen 5 8Go 512Go',
            'price'               => 1299.000,
            'is_available'        => true,
            'merchant_url'        => 'https://www.mytek.tn/hp-15-fc0097nf.html',
            'scraped_at'          => now()->subDays(1),
        ]);

        $offer2 = Offer::create([
            'product_id'          => $laptop1->id,
            'merchant_website_id' => $tunisianet->id,
            'raw_title'           => 'HP 15-fc0097nf AMD Ryzen5 8Go/512SSD',
            'price'               => 1249.000,
            'is_available'        => true,
            'merchant_url'        => 'https://www.tunisianet.com.tn/hp-15-fc0097nf.html',
            'scraped_at'          => now()->subDays(1),
        ]);

        $offer3 = Offer::create([
            'product_id'          => $laptop2->id,
            'merchant_website_id' => $mytek->id,
            'raw_title'           => 'Lenovo IdeaPad 3 i5 8Go 256Go SSD',
            'price'               => 1099.000,
            'is_available'        => true,
            'merchant_url'        => 'https://www.mytek.tn/lenovo-ideapad-3-15iau7.html',
            'scraped_at'          => now(),
        ]);

        // ── Price history (seed some history for charts) ───────────────────
        foreach ([$offer1, $offer2, $offer3] as $offer) {
            for ($i = 7; $i >= 0; $i--) {
                $variation = rand(-50, 50);
                PriceHistory::create([
                    'offer_id'    => $offer->id,
                    'price'       => $offer->price + $variation,
                    'recorded_at' => now()->subDays($i),
                ]);
            }
        }
    }
}
