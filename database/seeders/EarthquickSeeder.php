<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EarthquickSeeder extends Seeder
{
    public function run(): void
    {
        // Clear previous entries
        $usesMySql = DB::connection()->getDriverName() === 'mysql';
        if ($usesMySql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        ProductImage::truncate();
        Product::truncate();
        Subcategory::truncate();
        Category::truncate();
        if ($usesMySql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        /* =========================================================================
         * 1. PRIMARY CATEGORIES
         * Seed foundational shop departments with assets and display order.
         * ========================================================================= */
        $men = Category::create([
            'name' => 'Men',
            'slug' => 'men',
            'image' => 'images/categories/men.svg',
            'description' => 'Heritage punjabis, casual wear and accessories for men.',
            'sort_order' => 1,
        ]);

        $women = Category::create([
            'name' => 'Women',
            'slug' => 'women',
            'image' => 'images/categories/women.jpg',
            'description' => 'Handcrafted Sarees, Three-piece and Two-piece sets by Nous Telos.',
            'sort_order' => 2,
        ]);

        $kids = Category::create([
            'name' => 'Kids',
            'slug' => 'kids',
            'image' => 'images/categories/kids.svg',
            'description' => 'Comfortable and festive artisanal wear for children.',
            'sort_order' => 3,
        ]);

        $ornaments = Category::create([
            'name' => 'Ornaments',
            'slug' => 'ornaments',
            'image' => 'images/categories/ornaments.svg',
            'description' => 'Artisanal brass, silver, and clay handmade jewelry.',
            'sort_order' => 4,
        ]);

        $bags = Category::create([
            'name' => 'Bags',
            'slug' => 'bags',
            'image' => 'images/categories/bags.jpg',
            'description' => 'Genuine leather totes, handcrafted jute clutches and slings.',
            'sort_order' => 5,
        ]);

        $homeDecor = Category::create([
            'name' => 'Home Decor',
            'slug' => 'home-decor',
            'image' => 'images/categories/home-decor.svg',
            'description' => 'Nakshi Kantha, cotton bedsheets and artistic cushion covers.',
            'sort_order' => 6,
        ]);

        /* =========================================================================
         * 2. WOMEN SUBCATEGORIES
         * Authentic fashion divisions: Saree, Three Piece, Two Piece ensembles.
         * ========================================================================= */
        $subSaree = Subcategory::create([
            'category_id' => $women->id,
            'name' => 'Saree',
            'slug' => 'saree',
            'sort_order' => 1,
        ]);

        $subThreePiece = Subcategory::create([
            'category_id' => $women->id,
            'name' => 'Three Piece',
            'slug' => 'three-piece',
            'sort_order' => 2,
        ]);

        $subTwoPiece = Subcategory::create([
            'category_id' => $women->id,
            'name' => 'Two Piece',
            'slug' => 'two-piece',
            'sort_order' => 3,
        ]);

        /* =========================================================================
         * 3. HOME DECOR SUBCATEGORIES
         * Living sanctuary collections: Kantha quilts, bedsheets, cushion covers.
         * ========================================================================= */
        $subKantha = Subcategory::create([
            'category_id' => $homeDecor->id,
            'name' => 'Kantha',
            'slug' => 'kantha',
            'sort_order' => 1,
        ]);

        $subBedsheet = Subcategory::create([
            'category_id' => $homeDecor->id,
            'name' => 'Bedsheet',
            'slug' => 'bedsheet',
            'sort_order' => 2,
        ]);

        $subCushion = Subcategory::create([
            'category_id' => $homeDecor->id,
            'name' => 'Cushion Cover',
            'slug' => 'cushion-cover',
            'sort_order' => 3,
        ]);

        /* =========================================================================
         * 4. CATALOG INVENTORY DATASET
         * Curated collection of handlooms, garments, bags, and artisan pieces.
         * ========================================================================= */
        $products = [
            // Flagship Atelier Showcase: Sarees (Jamdani, Tantuj, Half Silk, Pure Silk)
            [
                'category_id' => $women->id,
                'subcategory_id' => $subSaree->id,
                'name' => 'Crimson Heirloom Jamdani',
                'slug' => 'crimson-heirloom-jamdani',
                'sku' => 'NT-SAR-001',
                'price' => 18500.00,
                'old_price' => 22000.00,
                'fabric' => 'Jamdani',
                'short_desc' => 'Intricately hand-woven Jamdani cotton with gold zari motifs by heirloom weavers of Narayanganj.',
                'description' => 'Intricate floral jaal motifs meticulously hand-woven on pure 100-count breathable muslin cotton pit looms. Comes with authentic Nous Telos certification tag and silk storage case.',
                'image' => 'images/saree/saree-01.jpg',
                'alt_image' => 'images/saree/saree-01-alt.jpg',
                'badge' => 'Handloom',
                'badge_type' => 'handloom',
                'rating' => 4.9,
                'reviews_count' => 48,
                'stock_quantity' => 3,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],
            [
                'category_id' => $women->id,
                'subcategory_id' => $subSaree->id,
                'name' => 'Midnight Indigo Tantuj Drape',
                'slug' => 'midnight-indigo-tantuj-drape',
                'sku' => 'NT-SAR-002',
                'price' => 7800.00,
                'old_price' => null,
                'fabric' => 'Tangail',
                'short_desc' => 'Traditional Tangail handloom weave with contrast temple border and indigo body.',
                'description' => 'Woven thread by thread on pit looms in Tangail, this drape showcases fine cotton yarn dyed in rich natural indigo.',
                'image' => 'images/saree/saree-02.jpg',
                'alt_image' => 'images/saree/saree-02-alt.jpg',
                'badge' => 'Tangail Weave',
                'badge_type' => 'ready',
                'rating' => 4.8,
                'reviews_count' => 19,
                'stock_quantity' => 8,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => true,
            ],
            [
                'category_id' => $women->id,
                'subcategory_id' => $subSaree->id,
                'name' => 'Royal Champagne Half Silk',
                'slug' => 'royal-champagne-half-silk',
                'sku' => 'NT-SAR-003',
                'price' => 12400.00,
                'old_price' => 14500.00,
                'fabric' => 'Half Silk',
                'short_desc' => 'Champagne gold half silk saree with fine golden zari pallu work.',
                'description' => 'Silky smooth drape with light cotton-silk blend, suitable for celebratory gatherings and formal evening dinners.',
                'image' => 'images/saree/saree-03.jpg',
                'alt_image' => 'images/saree/saree-03-alt.jpg',
                'badge' => 'Festive Exclusive',
                'badge_type' => 'exclusive',
                'rating' => 5.0,
                'reviews_count' => 27,
                'stock_quantity' => 5,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => true,
            ],
            [
                'category_id' => $women->id,
                'subcategory_id' => $subSaree->id,
                'name' => 'Emerald Rajshahi Pure Silk',
                'slug' => 'emerald-rajshahi-pure-silk',
                'sku' => 'NT-SAR-004',
                'price' => 24500.00,
                'old_price' => 28000.00,
                'fabric' => 'Pure Silk',
                'short_desc' => 'Pure Rajshahi mulberry silk saree with rich woven border and unstitched blouse.',
                'description' => 'Pure 100% Rajshahi mulberry silk. Features rich lustrous emerald fall, heavy festive palla, and soft natural texture.',
                'image' => 'images/saree/saree-04.jpg',
                'alt_image' => 'images/saree/saree-04-alt.jpg',
                'badge' => 'Pure Silk',
                'badge_type' => 'bestseller',
                'rating' => 4.9,
                'reviews_count' => 35,
                'stock_quantity' => 2,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],

            // Women's Collection: Three Piece Ensembles
            [
                'category_id' => $women->id,
                'subcategory_id' => $subThreePiece->id,
                'name' => 'Ivory Organza Embroidered Set',
                'slug' => 'ivory-organza-embroidered-set',
                'sku' => 'NT-3PC-001',
                'price' => 8900.00,
                'old_price' => 10500.00,
                'fabric' => 'Mulmul',
                'short_desc' => 'Three piece ensemble featuring fine aari embroidery and pure organza dupatta.',
                'description' => 'Breathable mulmul lining with artisanal needlework along neckline and hem. Includes tailored trouser and flowing dupatta.',
                'image' => 'images/three-piece/three-piece-01.jpg',
                'alt_image' => 'images/three-piece/three-piece-01-alt.jpg',
                'badge' => 'New Arrival',
                'badge_type' => 'exclusive',
                'rating' => 4.8,
                'reviews_count' => 16,
                'stock_quantity' => 6,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => true,
            ],
            [
                'category_id' => $women->id,
                'subcategory_id' => $subThreePiece->id,
                'name' => 'Blush Pink Hand-Embroidered Kameez',
                'slug' => 'blush-pink-hand-embroidered-kameez',
                'sku' => 'NT-3PC-002',
                'price' => 9400.00,
                'old_price' => null,
                'fabric' => 'Cotton',
                'short_desc' => 'Soft rose cotton-silk kameez with delicate tone-on-tone thread embroidery.',
                'description' => 'Elegantly tailored with relaxed side slits and scalloped dupatta border. Designed for festive dawats and daywear.',
                'image' => 'images/three-piece/three-piece-02.jpg',
                'alt_image' => 'images/three-piece/three-piece-02-alt.jpg',
                'badge' => 'Artisanal',
                'badge_type' => 'ready',
                'rating' => 4.9,
                'reviews_count' => 14,
                'stock_quantity' => 4,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],

            // Women's Collection: Two Piece Co-ord Ensembles
            [
                'category_id' => $women->id,
                'subcategory_id' => $subTwoPiece->id,
                'name' => 'Minimalist Sand Linen Co-ord',
                'slug' => 'minimalist-sand-linen-co-ord',
                'sku' => 'NT-2PC-001',
                'price' => 5200.00,
                'old_price' => 6200.00,
                'fabric' => 'Linen',
                'short_desc' => 'Pure linen tunic and culotte co-ord set in understated sand beige tone.',
                'description' => 'Lightweight European flax linen tailored for hot humid days. Relaxed modern silhouette suitable from work to evening dinner.',
                'image' => 'images/two-piece/two-piece-01.jpg',
                'alt_image' => 'images/two-piece/two-piece-01-alt.jpg',
                'badge' => 'Daily Luxury',
                'badge_type' => 'ready',
                'rating' => 4.8,
                'reviews_count' => 22,
                'stock_quantity' => 14,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],
            [
                'category_id' => $women->id,
                'subcategory_id' => $subTwoPiece->id,
                'name' => 'Ochre Terracotta Kurti & Culotte',
                'slug' => 'ochre-terracotta-kurti-culotte',
                'sku' => 'NT-2PC-002',
                'price' => 5800.00,
                'old_price' => null,
                'fabric' => 'Cotton',
                'short_desc' => 'Handspun khadi cotton tunic set inspired by Bengal terracotta crafts.',
                'description' => 'Soft organic cotton dyed with natural earthen pigment, offering supreme breathability and comfortable loose fit.',
                'image' => 'images/two-piece/two-piece-02.jpg',
                'alt_image' => 'images/two-piece/two-piece-02-alt.jpg',
                'badge' => 'Bestseller',
                'badge_type' => 'bestseller',
                'rating' => 4.9,
                'reviews_count' => 18,
                'stock_quantity' => 7,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],

            // Artisan Accessories: Handcrafted Leather & Canvas Bags
            [
                'category_id' => $bags->id,
                'subcategory_id' => null,
                'name' => 'Artisanal Terracotta Leather Tote Bag',
                'slug' => 'artisanal-terracotta-leather-tote-bag',
                'sku' => 'NT-BAG-001',
                'price' => 3400.00,
                'old_price' => 3900.00,
                'fabric' => 'Leather',
                'short_desc' => 'Full-grain vegetable tanned genuine leather daily carry tote with solid brass fittings.',
                'description' => 'Spacious interior with brass hardware fittings and dedicated laptop compartment. Crafted by master leather smiths.',
                'image' => 'images/bags/bag-01.jpg',
                'alt_image' => 'images/bags/bag-01-alt.jpg',
                'badge' => 'Bestseller',
                'badge_type' => 'signature',
                'rating' => 4.9,
                'reviews_count' => 31,
                'stock_quantity' => 5,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => true,
            ],
            [
                'category_id' => $bags->id,
                'subcategory_id' => null,
                'name' => 'Saddle Brown Crossbody Sling',
                'slug' => 'saddle-brown-crossbody-sling',
                'sku' => 'NT-BAG-002',
                'price' => 2800.00,
                'old_price' => null,
                'fabric' => 'Leather',
                'short_desc' => 'Structured genuine leather compact sling with adjustable shoulder strap.',
                'description' => 'Minimalist silhouette built for everyday city commuting with secure magnetic closure and hidden card slots.',
                'image' => 'images/bags/bag-02.jpg',
                'alt_image' => 'images/bags/bag-02-alt.jpg',
                'badge' => 'Ready to Ship',
                'badge_type' => 'ready',
                'rating' => 4.7,
                'reviews_count' => 19,
                'stock_quantity' => 10,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],

            // Living Sanctuary: Home Decor & Nakshi Kantha
            [
                'category_id' => $homeDecor->id,
                'subcategory_id' => $subKantha->id,
                'name' => 'Heirloom Nakshi Kantha Quilt',
                'slug' => 'heirloom-nakshi-kantha-quilt',
                'sku' => 'NT-DEC-001',
                'price' => 4800.00,
                'old_price' => 5500.00,
                'fabric' => 'Cotton',
                'short_desc' => 'Traditional hand-embroidered Bengali Nakshi Kantha quilt in vintage folk motifs.',
                'description' => 'Layered soft cotton stitched patiently by rural women artisans of Jashore. Each piece narrates a tale of folk heritage.',
                'image' => 'images/categories/decor-kantha.svg',
                'alt_image' => null,
                'badge' => 'Handmade',
                'badge_type' => 'gold',
                'rating' => 4.9,
                'reviews_count' => 14,
                'stock_quantity' => 4,
                'in_stock' => true,
                'is_featured' => true,
                'is_new_arrival' => false,
            ],
            [
                'category_id' => $homeDecor->id,
                'subcategory_id' => $subBedsheet->id,
                'name' => 'Artisanal Percale Cotton Bedsheet Set',
                'slug' => 'artisanal-percale-cotton-bedsheet-set',
                'sku' => 'NT-DEC-002',
                'price' => 3800.00,
                'old_price' => 4200.00,
                'fabric' => 'Cotton',
                'short_desc' => 'Breathable 300-thread-count pure cotton king bedsheet with 2 pillow covers.',
                'description' => 'Pre-washed combed percale weave ensuring cool, crisp sleep comfort throughout summer months.',
                'image' => 'images/categories/decor-bedsheet.svg',
                'alt_image' => null,
                'badge' => 'Living Line',
                'badge_type' => 'ready',
                'rating' => 4.8,
                'reviews_count' => 11,
                'stock_quantity' => 9,
                'in_stock' => true,
                'is_featured' => false,
                'is_new_arrival' => true,
            ],
            [
                'category_id' => $homeDecor->id,
                'subcategory_id' => $subCushion->id,
                'name' => 'Handloom Embroidered Cushion Cover',
                'slug' => 'handloom-embroidered-cushion-cover',
                'sku' => 'NT-DEC-003',
                'price' => 1200.00,
                'old_price' => null,
                'fabric' => 'Cotton',
                'short_desc' => 'Handcrafted geometric motif cushion accent with concealed zipper.',
                'description' => 'Tactile textured handloom fabric bringing warm artisanal accents to your living room sanctuary.',
                'image' => 'images/categories/decor-cushion.svg',
                'alt_image' => null,
                'badge' => 'Accent',
                'badge_type' => 'ready',
                'rating' => 4.7,
                'reviews_count' => 8,
                'stock_quantity' => 15,
                'in_stock' => true,
                'is_featured' => false,
                'is_new_arrival' => false,
            ],
        ];

        $nousTelos = Vendor::where('slug', 'nous-telos')->first();
        foreach ($products as $prod) {
            if ($nousTelos) {
                $prod['vendor_id'] = $nousTelos->id;
            }
            Product::create($prod);
        }
    }
}
