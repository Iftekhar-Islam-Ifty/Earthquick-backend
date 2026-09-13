/* =====================================================================
   EARTHQUICK — CATEGORY & CATALOG LOGIC (REUSABLE ARCHITECTURE)
   Powers filtering, sorting, grid rendering, and reactive controls.
   Designed for future Laravel Blade / API integration.
   ===================================================================== */

(function () {
  'use strict';

  // ===================================================================
  // 1. DATA CATALOG (REPRESENTING NOUS TELOS & RTHQUICK COLLECTIONS)
  // In Laravel, this data will be served by MySQL via Product Eloquent model:
  // e.g., $products = Product::with(['category', 'images'])->paginate(12);
  // ===================================================================
  const CATALOG_PRODUCTS = [
    // --- SAREE (WOMEN) ---
    {
      id: "saree-01",
      name: "Crimson Heirloom Jamdani",
      category: "women",
      subCategory: "jamdani",
      categoryLabel: "Saree",
      subLabel: "Jamdani",
      price: 18500,
      oldPrice: 22000,
      image: "images/saree/saree-01.jpg",
      imageAlt: "images/saree/saree-01-alt.jpg",
      badge: "Handloom",
      badgeType: "handloom",
      fabric: "Jamdani Cotton",
      color: "crimson",
      colorName: "Crimson",
      inStock: true,
      stockCount: 3,
      rating: 4.9,
      isNew: true,
      url: "pages/product.html?id=saree-01"
    },
    {
      id: "saree-02",
      name: "Midnight Indigo Tantuj Drape",
      category: "women",
      subCategory: "tantuj",
      categoryLabel: "Saree",
      subLabel: "Tantuj & Tangail",
      price: 7800,
      oldPrice: null,
      image: "images/saree/saree-02.jpg",
      imageAlt: "images/saree/saree-02-alt.jpg",
      badge: "Tangail Weave",
      badgeType: "ready",
      fabric: "Tangail Tant",
      color: "indigo",
      colorName: "Indigo",
      inStock: true,
      stockCount: 8,
      rating: 4.8,
      isNew: false,
      url: "pages/product.html?id=saree-02"
    },
    {
      id: "saree-03",
      name: "Royal Champagne Half Silk",
      category: "women",
      subCategory: "half-silk",
      categoryLabel: "Saree",
      subLabel: "Half Silk",
      price: 12400,
      oldPrice: 14500,
      image: "images/saree/saree-03.jpg",
      imageAlt: "images/saree/saree-03-alt.jpg",
      badge: "Festive Exclusive",
      badgeType: "exclusive",
      fabric: "Half Silk",
      color: "champagne",
      colorName: "Champagne Gold",
      inStock: true,
      stockCount: 5,
      rating: 5.0,
      isNew: true,
      url: "pages/product.html?id=saree-03"
    },
    {
      id: "saree-04",
      name: "Emerald Rajshahi Pure Silk",
      category: "women",
      subCategory: "full-silk",
      categoryLabel: "Saree",
      subLabel: "Pure Silk",
      price: 24500,
      oldPrice: 28000,
      image: "images/saree/saree-04.jpg",
      imageAlt: "images/saree/saree-04-alt.jpg",
      badge: "Pure Silk",
      badgeType: "bestseller",
      fabric: "Pure Silk",
      color: "emerald",
      colorName: "Emerald Green",
      inStock: true,
      stockCount: 2,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=saree-04"
    },
    {
      id: "saree-05",
      name: "Onyx Zari Border Jamdani",
      category: "women",
      subCategory: "jamdani",
      categoryLabel: "Saree",
      subLabel: "Jamdani",
      price: 21000,
      oldPrice: 25000,
      image: "images/saree/saree-05.jpg",
      imageAlt: "images/saree/saree-05-alt.jpg",
      badge: "Masterpiece",
      badgeType: "handloom",
      fabric: "Jamdani Cotton",
      color: "noir",
      colorName: "Noir Black",
      inStock: true,
      stockCount: 4,
      rating: 4.9,
      isNew: true,
      url: "pages/product.html?id=saree-05"
    },
    {
      id: "saree-06",
      name: "Mustard Gold Heritage Tant",
      category: "women",
      subCategory: "tantuj",
      categoryLabel: "Saree",
      subLabel: "Tantuj & Tangail",
      price: 6500,
      oldPrice: 7200,
      image: "images/saree/saree-06.jpg",
      imageAlt: "images/saree/saree-06-alt.jpg",
      badge: "Everyday Craft",
      badgeType: "ready",
      fabric: "Tangail Tant",
      color: "gold",
      colorName: "Mustard Gold",
      inStock: true,
      stockCount: 12,
      rating: 4.7,
      isNew: false,
      url: "pages/product.html?id=saree-06"
    },

    // --- THREE PIECE (WOMEN) ---
    {
      id: "three-piece-01",
      name: "Ivory Organza Embroidered Set",
      category: "women",
      subCategory: "three-piece",
      categoryLabel: "Ready to Wear",
      subLabel: "Three Piece",
      price: 8900,
      oldPrice: 10500,
      image: "images/three-piece/three-piece-01.jpg",
      imageAlt: "images/three-piece/three-piece-01-alt.jpg",
      badge: "New Arrival",
      badgeType: "exclusive",
      fabric: "Organza & Mulmul",
      color: "champagne",
      colorName: "Ivory",
      inStock: true,
      stockCount: 6,
      rating: 4.8,
      isNew: true,
      url: "pages/product.html?id=tp-01"
    },
    {
      id: "three-piece-02",
      name: "Blush Pink Hand-Embroidered Kameez",
      category: "women",
      subCategory: "three-piece",
      categoryLabel: "Ready to Wear",
      subLabel: "Three Piece",
      price: 9400,
      oldPrice: null,
      image: "images/three-piece/three-piece-02.jpg",
      imageAlt: "images/three-piece/three-piece-02-alt.jpg",
      badge: "Artisanal",
      badgeType: "ready",
      fabric: "Cotton Silk",
      color: "crimson",
      colorName: "Blush Rose",
      inStock: true,
      stockCount: 4,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=tp-02"
    },
    {
      id: "three-piece-03",
      name: "Sage Green Block-Print Ensemble",
      category: "women",
      subCategory: "three-piece",
      categoryLabel: "Ready to Wear",
      subLabel: "Three Piece",
      price: 7600,
      oldPrice: 8500,
      image: "images/three-piece/three-piece-03.jpg",
      imageAlt: "images/three-piece/three-piece-03-alt.jpg",
      badge: "Summer Breathable",
      badgeType: "ready",
      fabric: "Mulmul Cotton",
      color: "emerald",
      colorName: "Sage Green",
      inStock: true,
      stockCount: 9,
      rating: 4.7,
      isNew: false,
      url: "pages/product.html?id=tp-03"
    },
    {
      id: "three-piece-04",
      name: "Royal Navy Zardozi Formal Set",
      category: "women",
      subCategory: "three-piece",
      categoryLabel: "Ready to Wear",
      subLabel: "Three Piece",
      price: 13500,
      oldPrice: 16000,
      image: "images/three-piece/three-piece-04.jpg",
      imageAlt: "images/three-piece/three-piece-04-alt.jpg",
      badge: "Occasion Wear",
      badgeType: "bestseller",
      fabric: "Pure Silk",
      color: "indigo",
      colorName: "Navy",
      inStock: true,
      stockCount: 3,
      rating: 5.0,
      isNew: true,
      url: "pages/product.html?id=tp-04"
    },

    // --- TWO PIECE (WOMEN) ---
    {
      id: "two-piece-01",
      name: "Minimalist Sand Linen Co-ord",
      category: "women",
      subCategory: "two-piece",
      categoryLabel: "Contemporary Lounge",
      subLabel: "Two Piece",
      price: 5200,
      oldPrice: 6200,
      image: "images/two-piece/two-piece-01.jpg",
      imageAlt: "images/two-piece/two-piece-01-alt.jpg",
      badge: "Daily Luxury",
      badgeType: "ready",
      fabric: "Pure Linen",
      color: "champagne",
      colorName: "Sand Beige",
      inStock: true,
      stockCount: 14,
      rating: 4.8,
      isNew: false,
      url: "pages/product.html?id=2pc-01"
    },
    {
      id: "two-piece-02",
      name: "Ochre Terracotta Kurti & Culotte",
      category: "women",
      subCategory: "two-piece",
      categoryLabel: "Contemporary Lounge",
      subLabel: "Two Piece",
      price: 5800,
      oldPrice: null,
      image: "images/two-piece/two-piece-02.jpg",
      imageAlt: "images/two-piece/two-piece-02-alt.jpg",
      badge: "Bestseller",
      badgeType: "bestseller",
      fabric: "Khadi Cotton",
      color: "gold",
      colorName: "Terracotta",
      inStock: true,
      stockCount: 7,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=2pc-02"
    },
    {
      id: "two-piece-03",
      name: "Slate Indigo Asymmetric Tunic Set",
      category: "women",
      subCategory: "two-piece",
      categoryLabel: "Contemporary Lounge",
      subLabel: "Two Piece",
      price: 6100,
      oldPrice: 7000,
      image: "images/two-piece/two-piece-03.jpg",
      imageAlt: "images/two-piece/two-piece-03-alt.jpg",
      badge: "Contemporary Cut",
      badgeType: "exclusive",
      fabric: "Cotton Silk Blend",
      color: "indigo",
      colorName: "Slate Indigo",
      inStock: true,
      stockCount: 5,
      rating: 4.8,
      isNew: true,
      url: "pages/product.html?id=2pc-03"
    },

    // --- BAGS ---
    {
      id: "bag-01",
      name: "Artisan Leather Tote — Saddle Tan",
      category: "bags",
      subCategory: "bags",
      categoryLabel: "Handcrafted Bags",
      subLabel: "Totes",
      price: 6800,
      oldPrice: 7900,
      image: "images/bags/bag-1.jpg",
      imageAlt: "images/bags/bag-01-alt.jpg",
      badge: "Full Grain Leather",
      badgeType: "exclusive",
      fabric: "Genuine Leather",
      color: "gold",
      colorName: "Tan Brown",
      inStock: true,
      stockCount: 6,
      rating: 4.9,
      isNew: true,
      url: "pages/product.html?id=bag-01"
    },
    {
      id: "bag-02",
      name: "Structured Jute & Leather Crossbody",
      category: "bags",
      subCategory: "bags",
      categoryLabel: "Handcrafted Bags",
      subLabel: "Crossbody",
      price: 4200,
      oldPrice: null,
      image: "images/bags/bag-2.jpg",
      imageAlt: "images/bags/bag-02-alt.jpg",
      badge: "Eco Craft",
      badgeType: "ready",
      fabric: "Jute & Leather",
      color: "champagne",
      colorName: "Natural",
      inStock: true,
      stockCount: 11,
      rating: 4.8,
      isNew: false,
      url: "pages/product.html?id=bag-02"
    },
    {
      id: "bag-03",
      name: "Noir Minimalist Shoulder Bag",
      category: "bags",
      subCategory: "bags",
      categoryLabel: "Handcrafted Bags",
      subLabel: "Shoulder Bags",
      price: 5400,
      oldPrice: 6200,
      image: "images/bags/bag-3.jpg",
      imageAlt: "images/bags/bag-03-alt.jpg",
      badge: "Timeless",
      badgeType: "bestseller",
      fabric: "Full Grain Leather",
      color: "noir",
      colorName: "Noir Black",
      inStock: true,
      stockCount: 4,
      rating: 5.0,
      isNew: false,
      url: "pages/product.html?id=bag-03"
    },
    {
      id: "bag-04",
      name: "Terracotta Structured Handbag",
      category: "bags",
      subCategory: "bags",
      categoryLabel: "Handcrafted Bags",
      subLabel: "Handbags",
      price: 6100,
      oldPrice: 7000,
      image: "images/bags/bag-4.jpg",
      imageAlt: "images/bags/bag-04-alt.jpg",
      badge: "Architectural",
      badgeType: "handloom",
      fabric: "Full Grain Leather",
      color: "crimson",
      colorName: "Terracotta",
      inStock: true,
      stockCount: 7,
      rating: 4.9,
      isNew: true,
      url: "pages/product.html?id=bag-04"
    },

    // --- MEN ---
    {
      id: "men-01",
      name: "Heritage Fine Cotton Panjabi — Ivory",
      category: "men",
      subCategory: "panjabi",
      categoryLabel: "Men's Collection",
      subLabel: "Panjabi",
      price: 4600,
      oldPrice: 5200,
      image: "images/hero/hero-main.jpg",
      imageAlt: "images/hero/hero-main-saree-2.jpg",
      badge: "Hand-Embroidered",
      badgeType: "handloom",
      fabric: "Fine Egyptian Cotton",
      color: "champagne",
      colorName: "Ivory",
      inStock: true,
      stockCount: 8,
      rating: 4.8,
      isNew: true,
      url: "pages/product.html?id=men-01"
    },
    {
      id: "men-02",
      name: "Midnight Silk Blend Festive Panjabi",
      category: "men",
      subCategory: "panjabi",
      categoryLabel: "Men's Collection",
      subLabel: "Panjabi",
      price: 6200,
      oldPrice: null,
      image: "images/two-piece/2pc-1.jpg",
      imageAlt: "images/two-piece/2pc-2.jpg",
      badge: "Festive Ready",
      badgeType: "exclusive",
      fabric: "Silk Cotton Blend",
      color: "indigo",
      colorName: "Midnight Navy",
      inStock: true,
      stockCount: 5,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=men-02"
    },
    {
      id: "men-03",
      name: "Charcoal Textured Khadi Kurta Set",
      category: "men",
      subCategory: "panjabi",
      categoryLabel: "Men's Collection",
      subLabel: "Panjabi",
      price: 5400,
      oldPrice: 6200,
      image: "images/two-piece/two-piece-04.jpg",
      imageAlt: "images/two-piece/two-piece-04-alt.jpg",
      badge: "Handloom Khadi",
      badgeType: "handloom",
      fabric: "Fine Handspun Cotton",
      color: "noir",
      colorName: "Charcoal",
      inStock: true,
      stockCount: 7,
      rating: 4.8,
      isNew: true,
      url: "pages/product.html?id=men-03"
    },

    // --- KIDS ---
    {
      id: "kids-01",
      name: "Festive Mini Jamdani Kurta Set",
      category: "kids",
      subCategory: "clothing",
      categoryLabel: "Kids Collection",
      subLabel: "Kurta Sets",
      price: 2800,
      oldPrice: 3200,
      image: "images/saree/saree-5.jpg",
      imageAlt: "images/saree/saree-2.jpg",
      badge: "Soft Touch",
      badgeType: "ready",
      fabric: "Mulmul Cotton",
      color: "gold",
      colorName: "Yellow Gold",
      inStock: true,
      stockCount: 15,
      rating: 4.9,
      isNew: true,
      url: "pages/product.html?id=kids-01"
    },
    {
      id: "kids-02",
      name: "Artisan Heritage Embroidered Festive Frock",
      category: "kids",
      subCategory: "clothing",
      categoryLabel: "Kids Collection",
      subLabel: "Dresses",
      price: 2400,
      oldPrice: 2900,
      image: "images/three-piece/three-piece-02.jpg",
      imageAlt: "images/three-piece/three-piece-02-alt.jpg",
      badge: "Pure Cotton",
      badgeType: "exclusive",
      fabric: "Mulmul Cotton",
      color: "crimson",
      colorName: "Blush Rose",
      inStock: true,
      stockCount: 9,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=kids-02"
    },

    // --- ORNAMENTS ---
    {
      id: "ornament-01",
      name: "Handcrafted Brass Filigree Choker",
      category: "ornaments",
      subCategory: "ornaments",
      categoryLabel: "Artisanal Ornaments",
      subLabel: "Jewelry",
      price: 3400,
      oldPrice: 4000,
      image: "images/saree/saree-03-detail.jpg",
      imageAlt: "images/bags/bag-01-detail.jpg",
      badge: "Artisan Metalwork",
      badgeType: "exclusive",
      fabric: "Antique Brass & Stone",
      color: "gold",
      colorName: "Antique Gold",
      inStock: true,
      stockCount: 9,
      rating: 5.0,
      isNew: true,
      url: "pages/product.html?id=orn-01"
    },
    {
      id: "ornament-02",
      name: "Royal Heritage Zari Statement Jhumka",
      category: "ornaments",
      subCategory: "ornaments",
      categoryLabel: "Artisanal Ornaments",
      subLabel: "Earrings",
      price: 2600,
      oldPrice: 3100,
      image: "images/saree/saree-05-detail.jpg",
      imageAlt: "images/saree/saree-01-detail.jpg",
      badge: "Masterpiece",
      badgeType: "handloom",
      fabric: "Antique Brass & Pearls",
      color: "gold",
      colorName: "Royal Gold",
      inStock: true,
      stockCount: 12,
      rating: 4.9,
      isNew: false,
      url: "pages/product.html?id=orn-02"
    },

    // --- HOME DECOR (KANTHA, BEDSHEET, CUSHION COVER) ---
    {
      id: "decor-kantha-01",
      name: "Heirloom Botanical Nakshi Kantha Quilt",
      category: "home-decor",
      subCategory: "kantha",
      categoryLabel: "Living Atelier",
      subLabel: "Kantha Quilt",
      price: 8500,
      oldPrice: 9800,
      image: "images/hero/story-craft.jpg",
      imageAlt: "images/hero/story-heritage.jpg",
      badge: "Pure Kantha Stitch",
      badgeType: "handloom",
      fabric: "Organic Bengal Cotton",
      color: "emerald",
      colorName: "Botanical Green",
      inStock: true,
      stockCount: 4,
      rating: 4.9,
      isNew: true,
      url: "product.html?id=decor-kantha-01"
    },
    {
      id: "decor-kantha-02",
      name: "Floral Geometric Hand-Stitched Kantha Throw",
      category: "home-decor",
      subCategory: "kantha",
      categoryLabel: "Living Atelier",
      subLabel: "Kantha Throw",
      price: 6200,
      oldPrice: null,
      image: "images/hero/story-heritage.jpg",
      imageAlt: "images/hero/story-final.jpg",
      badge: "Artisan Quilted",
      badgeType: "ready",
      fabric: "Mulmul & Silk Thread",
      color: "crimson",
      colorName: "Crimson Maroon",
      inStock: true,
      stockCount: 6,
      rating: 4.8,
      isNew: false,
      url: "product.html?id=decor-kantha-02"
    },
    {
      id: "decor-kantha-03",
      name: "Vintage Royal Zari Border Kantha Dohar",
      category: "home-decor",
      subCategory: "kantha",
      categoryLabel: "Living Atelier",
      subLabel: "Kantha Dohar",
      price: 7400,
      oldPrice: 8500,
      image: "images/saree/saree-01-detail.jpg",
      imageAlt: "images/hero/story-craft.jpg",
      badge: "Heritage Stitch",
      badgeType: "exclusive",
      fabric: "Handspun Cotton",
      color: "gold",
      colorName: "Mustard Gold",
      inStock: true,
      stockCount: 3,
      rating: 5.0,
      isNew: true,
      url: "product.html?id=decor-kantha-03"
    },
    {
      id: "decor-bedsheet-01",
      name: "Royal Indigo Hand-Blocked Percale Bedsheet Set",
      category: "home-decor",
      subCategory: "bedsheet",
      categoryLabel: "Living Atelier",
      subLabel: "Bedsheet Set",
      price: 5400,
      oldPrice: 6200,
      image: "images/hero/story-final.jpg",
      imageAlt: "images/two-piece/2pc-1.jpg",
      badge: "300 Thread Count",
      badgeType: "ready",
      fabric: "Percale Cotton",
      color: "indigo",
      colorName: "Midnight Indigo",
      inStock: true,
      stockCount: 9,
      rating: 4.9,
      isNew: true,
      url: "product.html?id=decor-bedsheet-01"
    },
    {
      id: "decor-bedsheet-02",
      name: "Champagne Sateen Silk-Cotton King Bedsheet",
      category: "home-decor",
      subCategory: "bedsheet",
      categoryLabel: "Living Atelier",
      subLabel: "Bedsheet Set",
      price: 7900,
      oldPrice: 9000,
      image: "images/saree/saree-03.jpg",
      imageAlt: "images/hero/hero-saree-page.jpg",
      badge: "Luxury Sateen",
      badgeType: "exclusive",
      fabric: "Silk Cotton Blend",
      color: "champagne",
      colorName: "Champagne Ivory",
      inStock: true,
      stockCount: 5,
      rating: 4.8,
      isNew: false,
      url: "product.html?id=decor-bedsheet-02"
    },
    {
      id: "decor-bedsheet-03",
      name: "Terracotta Floral Screen-Printed Cotton Sheet",
      category: "home-decor",
      subCategory: "bedsheet",
      categoryLabel: "Living Atelier",
      subLabel: "Bedsheet Set",
      price: 4200,
      oldPrice: null,
      image: "images/two-piece/2pc-3.jpg",
      imageAlt: "images/two-piece/2pc-2.jpg",
      badge: "Everyday Craft",
      badgeType: "ready",
      fabric: "Fine Washed Cotton",
      color: "crimson",
      colorName: "Terracotta Red",
      inStock: true,
      stockCount: 12,
      rating: 4.7,
      isNew: false,
      url: "product.html?id=decor-bedsheet-03"
    },
    {
      id: "decor-cushion-01",
      name: "Embroidered Zari Velvet Cushion Cover Set (Pair)",
      category: "home-decor",
      subCategory: "cushion-cover",
      categoryLabel: "Living Atelier",
      subLabel: "Cushion Cover",
      price: 2600,
      oldPrice: 3200,
      image: "images/saree/saree-04.jpg",
      imageAlt: "images/saree/saree-04-detail.jpg",
      badge: "Set of 2",
      badgeType: "handloom",
      fabric: "Handloom Velvet & Zari",
      color: "emerald",
      colorName: "Emerald Moss",
      inStock: true,
      stockCount: 8,
      rating: 4.9,
      isNew: true,
      url: "product.html?id=decor-cushion-01"
    },
    {
      id: "decor-cushion-02",
      name: "Nakshi Applique Raw Silk Cushion Cover Set",
      category: "home-decor",
      subCategory: "cushion-cover",
      categoryLabel: "Living Atelier",
      subLabel: "Cushion Cover",
      price: 2950,
      oldPrice: null,
      image: "images/saree/saree-05.jpg",
      imageAlt: "images/saree/saree-05-detail.jpg",
      badge: "Set of 2",
      badgeType: "exclusive",
      fabric: "Raw Silk & Applique",
      color: "gold",
      colorName: "Antique Gold",
      inStock: true,
      stockCount: 7,
      rating: 5.0,
      isNew: true,
      url: "product.html?id=decor-cushion-02"
    },
    {
      id: "decor-cushion-03",
      name: "Minimalist Noir & Indigo Handwoven Cushion Covers",
      category: "home-decor",
      subCategory: "cushion-cover",
      categoryLabel: "Living Atelier",
      subLabel: "Cushion Cover",
      price: 1950,
      oldPrice: 2400,
      image: "images/two-piece/two-piece-03.jpg",
      imageAlt: "images/two-piece/two-piece-03-detail.jpg",
      badge: "Set of 2",
      badgeType: "ready",
      fabric: "Handwoven Cotton",
      color: "noir",
      colorName: "Noir & Indigo",
      inStock: true,
      stockCount: 11,
      rating: 4.8,
      isNew: false,
      url: "product.html?id=decor-cushion-03"
    }
  ];

  // Expose to window for global access (Product Details, Cart, Search, etc.)
  window.CATALOG_PRODUCTS = CATALOG_PRODUCTS;

  // ===================================================================
  // 2. CATEGORY METADATA CONFIGURATION
  // In Laravel, this maps to the `Category` model table
  // ===================================================================
  const CATEGORY_META = {
    "women": {
      title: "Women's Collection",
      eyebrow: "NOUS TELOS ATELIER",
      desc: "Artisanal sarees woven on heritage wooden looms, alongside impeccably tailored three-piece and modern co-ord ensembles.",
      subCategories: [
        { key: "all", label: "All Women" },
        { 
          key: "saree", 
          label: "Saree",
          nested: [
            { key: "all", label: "All Sarees" },
            { key: "jamdani", label: "Jamdani" },
            { key: "tantuj", label: "Tantuj & Tangail" },
            { key: "half-silk", label: "Half Silk" },
            { key: "full-silk", label: "Pure Silk" }
          ]
        },
        { 
          key: "three-piece", 
          label: "Three Piece",
          nested: [
            { key: "all", label: "All Three-Piece" }
          ]
        },
        { 
          key: "two-piece", 
          label: "Two Piece",
          nested: [
            { key: "all", label: "All Two-Piece" }
          ]
        }
      ]
    },
    "saree": {
      title: "Saree",
      eyebrow: "HERITAGE HANDLOOMS",
      desc: "Centuries of weaving mastery from Tangail, Narayanganj and Rajshahi. Handloom Jamdani, pure silks, and everyday festive drapes.",
      subCategories: [
        { key: "all", label: "All Sarees" },
        { key: "jamdani", label: "Jamdani" },
        { key: "tantuj", label: "Tantuj & Tangail" },
        { key: "half-silk", label: "Half Silk" },
        { key: "full-silk", label: "Pure Silk" }
      ]
    },
    "three-piece": {
      title: "Three Piece Sets",
      eyebrow: "READY TO WEAR",
      desc: "Thoughtfully coordinated kameez, trousers and artisanal dupattas tailored with breathable fabrics and delicate zardozi needlework.",
      subCategories: [
        { key: "all", label: "All Three-Piece" }
      ]
    },
    "two-piece": {
      title: "Two Piece Ensembles",
      eyebrow: "CONTEMPORARY SILHOUETTES",
      desc: "Relaxed co-ords, linen tunics, and versatile culotte sets created for casual elegance and daily luxury.",
      subCategories: [
        { key: "all", label: "All Two-Piece" }
      ]
    },
    "men": {
      title: "Men's Collection",
      eyebrow: "SARTORIAL HERITAGE",
      desc: "Distinctive fine cotton and silk panjabis tailored for festive occasions, Friday prayers, and understated everyday poise.",
      subCategories: [
        { key: "all", label: "All Men" },
        { key: "panjabi", label: "Panjabi" }
      ]
    },
    "kids": {
      title: "Kids Collection",
      eyebrow: "GENTLE ARTISANAL CRAFT",
      desc: "Soft mulmul kurtas and comfortable festive coordinates designed with delicate, skin-friendly fabrics for young ones.",
      subCategories: [
        { key: "all", label: "All Kids" }
      ]
    },
    "ornaments": {
      title: "Artisanal Ornaments",
      eyebrow: "STATEMENT JEWELRY",
      desc: "Handcrafted filigree, antique brass jewelry and semi-precious ornaments made by master metal artisans.",
      subCategories: [
        { key: "all", label: "All Ornaments" }
      ]
    },
    "bags": {
      title: "Handcrafted Bags",
      eyebrow: "TASTEFUL UTILITY",
      desc: "Full-grain leather totes, structured crossbody bags and natural jute satchels designed for longevity and timeless styling.",
      subCategories: [
        { key: "all", label: "All Bags" }
      ]
    },
    "home-decor": {
      title: "Home Decor & Living",
      eyebrow: "LIFESTYLE CRAFT",
      desc: "Hand-stitched Nakshi Kantha quilts, artisanal bedsheets, and plush cushion covers crafted to bring warmth, comfort, and timeless poise to your living sanctuary.",
      subCategories: [
        { key: "all", label: "All Home Decor" },
        { key: "kantha", label: "Kantha" },
        { key: "bedsheet", label: "Bedsheet" },
        { key: "cushion-cover", label: "Cushion Cover" }
      ]
    }
  };

  // ===================================================================
  // 3. STATE MANAGEMENT
  // Tracks active category, subcategory, price, fabrics, colors, sorting
  // ===================================================================
  const state = {
    activeCategory: "women",
    activeSubCategory: "all",
    activeNestedSubCategory: "all",
    priceMax: 30000,
    selectedFabrics: new Set(),
    inStockOnly: false,
    sortBy: "featured",
    gridColumns: 3,
    currentPage: 1,
    perPage: 12
  };

  // ===================================================================
  // 4. URL QUERY PARSING
  // Allows direct linking: e.g. category.html?cat=home-decor&sub=kantha
  // ===================================================================
  function parseUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const cat = params.get("cat");
    const sub = params.get("sub");
    
    // Also support filename-based detection
    const pathName = window.location.pathname.toLowerCase();
    
    if (cat && CATEGORY_META[cat]) {
      state.activeCategory = cat;
    } else if (pathName.includes("women.html")) {
      state.activeCategory = "women";
    } else if (pathName.includes("/men.html") || pathName.endsWith("men.html")) {
      state.activeCategory = "men";
    } else if (pathName.includes("kids.html")) {
      state.activeCategory = "kids";
    } else if (pathName.includes("ornaments.html")) {
      state.activeCategory = "ornaments";
    } else if (pathName.includes("bags.html")) {
      state.activeCategory = "bags";
    } else if (pathName.includes("home-decor-kantha.html")) {
      state.activeCategory = "home-decor";
      state.activeSubCategory = "kantha";
    } else if (pathName.includes("home-decor-bedsheet.html")) {
      state.activeCategory = "home-decor";
      state.activeSubCategory = "bedsheet";
    } else if (pathName.includes("home-decor-cushion.html")) {
      state.activeCategory = "home-decor";
      state.activeSubCategory = "cushion-cover";
    } else if (pathName.includes("home-decor.html")) {
      state.activeCategory = "home-decor";
    } else if (pathName.includes("saree-jamdani.html")) {
      state.activeCategory = "saree";
      state.activeSubCategory = "jamdani";
    } else if (pathName.includes("saree-tantuj.html")) {
      state.activeCategory = "saree";
      state.activeSubCategory = "tantuj";
    } else if (pathName.includes("saree-half-silk.html")) {
      state.activeCategory = "saree";
      state.activeSubCategory = "half-silk";
    } else if (pathName.includes("saree-full-silk.html")) {
      state.activeCategory = "saree";
      state.activeSubCategory = "full-silk";
    } else if (pathName.includes("saree.html")) {
      state.activeCategory = "saree";
    } else if (pathName.includes("three-piece.html")) {
      state.activeCategory = "three-piece";
    } else if (pathName.includes("two-piece.html")) {
      state.activeCategory = "two-piece";
    } else {
      state.activeCategory = "women";
    }

    if (sub) {
      if (state.activeCategory === "women" && ["jamdani", "tantuj", "half-silk", "full-silk"].includes(sub)) {
        state.activeSubCategory = "saree";
        state.activeNestedSubCategory = sub;
      } else {
        state.activeSubCategory = sub;
      }
    }

    const type = params.get("type");
    if (type) {
      state.activeNestedSubCategory = type;
    }
  }

  // ===================================================================
  // 5. ASSET & URL PATH RESOLVERS
  // Prevents 404 image errors and broken product links regardless of directory depth
  // ===================================================================
  function resolveAssetPath(relPath) {
    if (!relPath) return "";
    if (relPath.startsWith("http://") || relPath.startsWith("https://") || relPath.startsWith("/") || relPath.startsWith("../")) {
      return relPath;
    }
    const isInsidePages = window.location.pathname.includes('/pages/') || window.location.pathname.startsWith('/pages');
    return isInsidePages ? `../${relPath}` : relPath;
  }

  function resolveProductUrl(product) {
    const isInsidePages = window.location.pathname.includes('/pages/') || window.location.pathname.startsWith('/pages');
    return isInsidePages ? `product.html?id=${product.id}` : `pages/product.html?id=${product.id}`;
  }

  // ===================================================================
  // 6. BANNER & BREADCRUMB UPDATE
  // ===================================================================
  function renderHeaderMeta() {
    const meta = CATEGORY_META[state.activeCategory] || CATEGORY_META["women"];
    
    // Update Title & Description
    const titleEl = document.querySelector("#cat-banner-title");
    const eyebrowEl = document.querySelector("#cat-banner-eyebrow");
    const descEl = document.querySelector("#cat-banner-desc");
    const breadcrumbCurrent = document.querySelector("#breadcrumb-current");
    const breadcrumbParent = document.querySelector("#breadcrumb-parent");

    // Dynamic Title based on SubCategory if active
    let displayTitle = meta.title;
    let displayEyebrow = meta.eyebrow;
    let displayDesc = meta.desc;

    if (state.activeCategory === "women") {
      if (state.activeSubCategory === "saree") {
        displayEyebrow = "HERITAGE HANDLOOMS";
        if (state.activeNestedSubCategory && state.activeNestedSubCategory !== "all") {
          const sareeSubMeta = CATEGORY_META["saree"].subCategories.find(s => s.key === state.activeNestedSubCategory);
          displayTitle = sareeSubMeta ? `${sareeSubMeta.label} — Saree` : "Saree";
        } else {
          displayTitle = "Saree — Women's Collection";
        }
        displayDesc = CATEGORY_META["saree"].desc;
      } else if (state.activeSubCategory === "three-piece") {
        displayTitle = "Three Piece Sets — Women's Collection";
        displayEyebrow = CATEGORY_META["three-piece"].eyebrow;
        displayDesc = CATEGORY_META["three-piece"].desc;
      } else if (state.activeSubCategory === "two-piece") {
        displayTitle = "Two Piece Ensembles — Women's Collection";
        displayEyebrow = CATEGORY_META["two-piece"].eyebrow;
        displayDesc = CATEGORY_META["two-piece"].desc;
      }
    } else if (state.activeSubCategory && state.activeSubCategory !== "all") {
      const activeSubObj = meta.subCategories ? meta.subCategories.find(s => s.key === state.activeSubCategory) : null;
      if (activeSubObj) {
        displayTitle = `${activeSubObj.label} — ${meta.title}`;
      }
    }

    if (titleEl) titleEl.textContent = displayTitle;
    if (eyebrowEl) eyebrowEl.textContent = displayEyebrow;
    if (descEl) descEl.textContent = displayDesc;

    // Robust Breadcrumb Trail following Women > Saree > Subcategories hierarchy
    const breadcrumbList = document.querySelector(".eq-breadcrumbs ol");
    if (breadcrumbList) {
      let bHtml = `<li><a href="../index.html">Home</a></li>`;
      if (state.activeCategory === "saree") {
        bHtml += `<li><a href="women.html">Women</a></li>`;
        if (state.activeSubCategory && state.activeSubCategory !== "all") {
          bHtml += `<li><a href="saree.html">Saree</a></li>`;
          const activeSubObj = meta.subCategories ? meta.subCategories.find(s => s.key === state.activeSubCategory) : null;
          bHtml += `<li aria-current="page" id="breadcrumb-current">${activeSubObj ? activeSubObj.label : "Saree"}</li>`;
        } else {
          bHtml += `<li aria-current="page" id="breadcrumb-current">${meta.title}</li>`;
        }
      } else if (state.activeCategory === "three-piece") {
        bHtml += `<li><a href="women.html">Women</a></li>`;
        bHtml += `<li aria-current="page" id="breadcrumb-current">Three Piece Sets</li>`;
      } else if (state.activeCategory === "two-piece") {
        bHtml += `<li><a href="women.html">Women</a></li>`;
        bHtml += `<li aria-current="page" id="breadcrumb-current">Two Piece Ensembles</li>`;
      } else if (state.activeCategory === "women") {
        if (state.activeSubCategory === "saree") {
          bHtml += `<li><a href="women.html">Women</a></li>`;
          if (state.activeNestedSubCategory && state.activeNestedSubCategory !== "all") {
            bHtml += `<li><a href="javascript:void(0)" class="eq-breadcrumb-saree-link">Saree</a></li>`;
            const sareeSubMeta = CATEGORY_META["saree"].subCategories.find(s => s.key === state.activeNestedSubCategory);
            bHtml += `<li aria-current="page" id="breadcrumb-current">${sareeSubMeta ? sareeSubMeta.label : "Saree"}</li>`;
          } else {
            bHtml += `<li aria-current="page" id="breadcrumb-current">Saree</li>`;
          }
        } else if (state.activeSubCategory === "three-piece") {
          bHtml += `<li><a href="women.html">Women</a></li>`;
          bHtml += `<li aria-current="page" id="breadcrumb-current">Three Piece Sets</li>`;
        } else if (state.activeSubCategory === "two-piece") {
          bHtml += `<li><a href="women.html">Women</a></li>`;
          bHtml += `<li aria-current="page" id="breadcrumb-current">Two Piece Ensembles</li>`;
        } else {
          bHtml += `<li aria-current="page" id="breadcrumb-current">${meta.title}</li>`;
        }
      } else if (state.activeCategory === "home-decor") {
        if (state.activeSubCategory && state.activeSubCategory !== "all") {
          bHtml += `<li><a href="home-decor.html">Home Decor</a></li>`;
          const activeSubObj = meta.subCategories ? meta.subCategories.find(s => s.key === state.activeSubCategory) : null;
          bHtml += `<li aria-current="page" id="breadcrumb-current">${activeSubObj ? activeSubObj.label : "Home Decor"}</li>`;
        } else {
          bHtml += `<li aria-current="page" id="breadcrumb-current">${meta.title}</li>`;
        }
      } else {
        bHtml += `<li aria-current="page" id="breadcrumb-current">${meta.title}</li>`;
      }
      breadcrumbList.innerHTML = bHtml;

      const bcSareeLink = breadcrumbList.querySelector(".eq-breadcrumb-saree-link");
      if (bcSareeLink) {
        bcSareeLink.addEventListener("click", (e) => {
          e.preventDefault();
          state.activeNestedSubCategory = "all";
          state.currentPage = 1;
          renderAll();
        });
      }
    }

    // Update document title
    document.title = `${displayTitle} — Earthquick by Nous Telos`;

    // Render Subcategory Filter Pills (Primary Row)
    const pillsContainer = document.querySelector("#cat-pills-container");
    if (pillsContainer) {
      pillsContainer.innerHTML = "";
      if (meta.subCategories && meta.subCategories.length > 1) {
        meta.subCategories.forEach(sub => {
          const pill = document.createElement("button");
          pill.type = "button";
          pill.className = `eq-cat-pill ${state.activeSubCategory === sub.key ? "is-active" : ""}`;
          pill.textContent = sub.label;
          pill.addEventListener("click", () => {
            state.activeSubCategory = sub.key;
            state.activeNestedSubCategory = "all";
            state.currentPage = 1;
            renderAll();
          });
          pillsContainer.appendChild(pill);
        });
      }
    }

    // Render Nested Subcategory Filter Pills (Secondary Row for Saree, etc.)
    let nestedContainer = document.querySelector("#cat-nested-pills-container");
    if (!nestedContainer && pillsContainer && pillsContainer.parentNode) {
      nestedContainer = document.createElement("div");
      nestedContainer.id = "cat-nested-pills-container";
      nestedContainer.className = "eq-cat-nested-pills-wrap";
      pillsContainer.parentNode.insertBefore(nestedContainer, pillsContainer.nextSibling);
    }

    if (nestedContainer) {
      let nestedItems = null;
      let nestedLabel = "";

      if (state.activeCategory === "women" && state.activeSubCategory === "saree") {
        const sareeSub = meta.subCategories ? meta.subCategories.find(s => s.key === "saree") : null;
        nestedItems = (sareeSub && sareeSub.nested) ? sareeSub.nested : (CATEGORY_META["saree"] ? CATEGORY_META["saree"].subCategories : null);
        nestedLabel = "Saree Weaves:";
      } else if (state.activeCategory === "women") {
        const activeSubObj = meta.subCategories ? meta.subCategories.find(s => s.key === state.activeSubCategory) : null;
        if (activeSubObj && activeSubObj.nested && activeSubObj.nested.length > 1) {
          nestedItems = activeSubObj.nested;
          nestedLabel = `${activeSubObj.label} Types:`;
        }
      }

      if (nestedItems && nestedItems.length > 1) {
        nestedContainer.style.display = "inline-flex";
        nestedContainer.innerHTML = `<span class="eq-cat-nested-label">${nestedLabel}</span>`;
        nestedItems.forEach(nItem => {
          const subPill = document.createElement("button");
          subPill.type = "button";
          subPill.className = `eq-cat-subpill ${state.activeNestedSubCategory === nItem.key ? "is-active" : ""}`;
          subPill.textContent = nItem.label;
          subPill.addEventListener("click", () => {
            state.activeNestedSubCategory = nItem.key;
            state.currentPage = 1;
            renderAll();
          });
          nestedContainer.appendChild(subPill);
        });
      } else {
        nestedContainer.style.display = "none";
        nestedContainer.innerHTML = "";
      }
    }
  }

  // ===================================================================
  // 7. FILTER & SORT ENGINE
  // ===================================================================
  function getFilteredProducts() {
    return CATALOG_PRODUCTS.filter(item => {
      // 1. Category Matching
      if (state.activeCategory === "women") {
        // "women" includes saree, three-piece, two-piece
        if (item.category !== "women") return false;
      } else if (state.activeCategory === "saree") {
        // Saree specific
        if (item.category !== "women" || (item.subCategory !== "jamdani" && item.subCategory !== "tantuj" && item.subCategory !== "half-silk" && item.subCategory !== "full-silk")) {
          return false;
        }
      } else {
        if (item.category !== state.activeCategory) return false;
      }

      // 2. Subcategory Matching
      if (state.activeCategory === "women") {
        if (state.activeSubCategory === "saree") {
          const isSaree = ["jamdani", "tantuj", "half-silk", "full-silk"].includes(item.subCategory) || (item.categoryLabel && item.categoryLabel.toLowerCase().includes("saree"));
          if (!isSaree) return false;
          // Check nested saree subcategory filter
          if (state.activeNestedSubCategory && state.activeNestedSubCategory !== "all") {
            if (item.subCategory !== state.activeNestedSubCategory) return false;
          }
        } else if (state.activeSubCategory !== "all") {
          if (item.subCategory !== state.activeSubCategory) return false;
        }
      } else {
        if (state.activeSubCategory !== "all") {
          if (item.subCategory !== state.activeSubCategory) return false;
        }
      }

      // 3. Price Filter
      if (item.price > state.priceMax) return false;

      // 4. Fabric Filter
      if (state.selectedFabrics.size > 0) {
        let hasMatch = false;
        state.selectedFabrics.forEach(fab => {
          if (item.fabric.toLowerCase().includes(fab.toLowerCase())) hasMatch = true;
        });
        if (!hasMatch) return false;
      }

      // 5. Stock Filter
      if (state.inStockOnly && !item.inStock) return false;

      return true;
    }).sort((a, b) => {
      // Sorting
      if (state.sortBy === "price-low") return a.price - b.price;
      if (state.sortBy === "price-high") return b.price - a.price;
      if (state.sortBy === "newest") return (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0);
      if (state.sortBy === "rating") return b.rating - a.rating;
      return 0; // featured / default
    });
  }

  // ===================================================================
  // 8. PRODUCT CARD HTML GENERATOR
  // EXACT REUSABLE PATTERN FOR LARAVEL BLADE COMPONENT
  // `resources/views/components/product-card.blade.php`
  // ===================================================================
  function createProductCardHTML(product) {
    const formattedPrice = `৳${product.price.toLocaleString()}`;
    const formattedOldPrice = product.oldPrice ? `<span class="eq-price--old">৳${product.oldPrice.toLocaleString()}</span>` : "";
    const mainImgSrc = resolveAssetPath(product.image);
    const altImgSrc = product.imageAlt ? resolveAssetPath(product.imageAlt) : null;
    const targetUrl = resolveProductUrl(product);

    // Check if item has an alternate angle image
    const altImgHTML = altImgSrc ? `<img src="${altImgSrc}?v=3" alt="${product.name} alternate view" class="eq-product-card__img--alt" loading="lazy" />` : "";

    return `
      <article class="eq-product-card" id="card-${product.id}" data-id="${product.id}" data-category="${product.category}">
        <div class="eq-product-card__frame">
          <!-- Status Badge -->
          ${product.badge ? `<span class="eq-product-badge eq-product-badge--${product.badgeType || 'ready'}">${product.badge}</span>` : ""}
          
          <!-- Wishlist Toggle -->
          <button type="button" class="eq-card-wishlist-btn" aria-label="Add ${product.name} to wishlist" data-wishlist-id="${product.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
          </button>

          <!-- Primary & Alternate Images with Smooth Hover Zoom -->
          <img src="${mainImgSrc}?v=3" alt="${product.name}" loading="lazy" />
          ${altImgHTML}

          <!-- Quick View / Add Button -->
          <button type="button" class="eq-product-card__quick-add" data-action="quick-view">
            Quick Inspect &bull; Add
          </button>
        </div>

        <div class="eq-product-card__body">
          <span class="eq-product-card__category">${product.categoryLabel} &bull; ${product.fabric}</span>
          <h3 class="eq-product-card__name">
            <a href="${targetUrl}" class="eq-product-card__link">${product.name}</a>
          </h3>
          <div class="eq-product-card__price">
            ${formattedOldPrice}
            <span>${formattedPrice}</span>
          </div>
          <span class="eq-product-stock-tag">${product.stockCount <= 3 ? `⚡ Only ${product.stockCount} left in atelier` : 'Ready to Ship'}</span>
        </div>
      </article>
    `;
  }

  // ===================================================================
  // 8. RENDER GRID & ACTIVE CHIPS
  // ===================================================================
  function renderProducts() {
    const grid = document.querySelector("#catalog-grid");
    const countEl = document.querySelector("#catalog-counter");
    const activeFiltersEl = document.querySelector("#active-filter-chips");

    if (!grid) return;

    const filtered = getFilteredProducts();

    // Update results counter
    if (countEl) {
      countEl.innerHTML = `Showing <strong>${filtered.length}</strong> handcrafted pieces`;
    }

    // Render Active Filter Badges
    if (activeFiltersEl) {
      activeFiltersEl.innerHTML = "";
      let hasFilters = false;

      if (state.activeSubCategory !== "all") {
        hasFilters = true;
        createActiveChip(activeFiltersEl, `Category: ${state.activeSubCategory}`, () => {
          state.activeSubCategory = "all";
          renderAll();
        });
      }

      if (state.priceMax < 30000) {
        hasFilters = true;
        createActiveChip(activeFiltersEl, `Under ৳${state.priceMax.toLocaleString()}`, () => {
          state.priceMax = 30000;
          updatePriceSliderUI(30000);
          renderAll();
        });
      }

      state.selectedFabrics.forEach(fab => {
        hasFilters = true;
        createActiveChip(activeFiltersEl, `Fabric: ${fab}`, () => {
          state.selectedFabrics.delete(fab);
          updateCheckboxUI();
          renderAll();
        });
      });

      if (state.inStockOnly) {
        hasFilters = true;
        createActiveChip(activeFiltersEl, "In Stock Only", () => {
          state.inStockOnly = false;
          updateCheckboxUI();
          renderAll();
        });
      }

      if (hasFilters) {
        const clearBtn = document.createElement("button");
        clearBtn.type = "button";
        clearBtn.className = "eq-clear-all-btn";
        clearBtn.textContent = "Clear All";
        clearBtn.addEventListener("click", resetAllFilters);
        activeFiltersEl.appendChild(clearBtn);
      }

      // Calculate active filter count for mobile trigger badge
      let activeFilterCount = 0;
      if (state.activeSubCategory !== "all") activeFilterCount++;
      if (state.priceMax < 30000) activeFilterCount++;
      activeFilterCount += state.selectedFabrics.size;
      if (state.inStockOnly) activeFilterCount++;

      // Update mobile filter trigger badge
      const mobileFilterTrigger = document.querySelector("#mobile-filter-trigger");
      if (mobileFilterTrigger) {
        let badge = mobileFilterTrigger.querySelector(".eq-mobile-filter-badge");
        if (activeFilterCount > 0) {
          if (!badge) {
            badge = document.createElement("span");
            badge.className = "eq-mobile-filter-badge";
            mobileFilterTrigger.appendChild(badge);
          }
          badge.textContent = activeFilterCount;
        } else if (badge) {
          badge.remove();
        }
      }

      // Update mobile drawer apply button label with current filtered count
      const applyBtn = document.querySelector("#filter-footer-apply");
      if (applyBtn) {
        applyBtn.textContent = `Show ${filtered.length} Product${filtered.length === 1 ? "" : "s"}`;
      }
    }

    // Render Products or Empty State
    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="eq-catalog-empty">
          <svg class="eq-catalog-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            <line x1="8" y1="11" x2="14" y2="11"></line>
          </svg>
          <h3 class="eq-catalog-empty__title">No matching pieces found</h3>
          <p class="eq-catalog-empty__text">Try adjusting your price range or clearing fabric filters to discover other curated items.</p>
          <button type="button" class="eq-btn eq-btn--primary eq-btn--pill" id="empty-reset-filters">
            Reset All Filters
          </button>
        </div>
      `;
      const resetBtn = grid.querySelector("#empty-reset-filters");
      if (resetBtn) resetBtn.addEventListener("click", resetAllFilters);
    } else {
      grid.innerHTML = filtered.map(product => createProductCardHTML(product)).join("");
      attachCardInteractions(grid);
    }
  }

  function createActiveChip(container, text, onRemove) {
    const chip = document.createElement("span");
    chip.className = "eq-active-chip";
    chip.innerHTML = `<span>${text}</span> <button type="button" aria-label="Remove filter">&times;</button>`;
    chip.querySelector("button").addEventListener("click", onRemove);
    container.appendChild(chip);
  }

  // ===================================================================
  // 9. CARD INTERACTIONS (QUICK VIEW, WISHLIST, TOASTS)
  // ===================================================================
  function attachCardInteractions(container) {
    // Quick View
    container.querySelectorAll('[data-action="quick-view"]').forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const card = btn.closest(".eq-product-card");
        if (card && typeof window.openQuickView === "function") {
          window.openQuickView(card);
        } else if (typeof Toast !== "undefined") {
          Toast.show("Added piece to bag.");
        }
      });
    });

    // Wishlist Toggle
    container.querySelectorAll('.eq-card-wishlist-btn').forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const isFav = btn.classList.toggle("is-favorited");
        const card = btn.closest(".eq-product-card");
        const title = card ? card.querySelector(".eq-product-card__name")?.textContent.trim() : "Item";
        if (typeof Toast !== "undefined") {
          Toast.show(isFav ? `Added "${title}" to your wishlist.` : `Removed "${title}" from wishlist.`);
        }
      });
    });
  }

  // ===================================================================
  // 10. FILTER CONTROLS & UI EVENT HANDLERS
  // ===================================================================
  function initFilterControls() {
    // 1. Accordion Toggles
    document.querySelectorAll(".eq-filter-group__header").forEach(header => {
      header.addEventListener("click", () => {
        const group = header.closest(".eq-filter-group");
        if (group) group.classList.toggle("is-collapsed");
      });
    });

    // 2. Price Range Slider
    const priceSlider = document.querySelector("#filter-price-slider");
    const priceMaxInput = document.querySelector("#filter-price-max");

    if (priceSlider && priceMaxInput) {
      priceSlider.addEventListener("input", (e) => {
        const val = parseInt(e.target.value, 10);
        state.priceMax = val;
        priceMaxInput.value = val;
        renderProducts();
      });

      priceMaxInput.addEventListener("change", (e) => {
        const val = Math.min(30000, Math.max(2000, parseInt(e.target.value, 10) || 30000));
        state.priceMax = val;
        priceSlider.value = val;
        renderProducts();
      });
    }

    // 3. Fabric Checkboxes
    document.querySelectorAll('[data-filter="fabric"]').forEach(checkbox => {
      checkbox.addEventListener("change", (e) => {
        const val = e.target.value;
        if (e.target.checked) {
          state.selectedFabrics.add(val);
        } else {
          state.selectedFabrics.delete(val);
        }
        renderProducts();
      });
    });

    // 4. In Stock Checkbox
    const inStockCheckbox = document.querySelector("#filter-in-stock");
    if (inStockCheckbox) {
      inStockCheckbox.addEventListener("change", (e) => {
        state.inStockOnly = e.target.checked;
        renderProducts();
      });
    }

    // 6. Sorting Select
    const sortSelect = document.querySelector("#catalog-sort-select");
    if (sortSelect) {
      sortSelect.addEventListener("change", (e) => {
        state.sortBy = e.target.value;
        renderProducts();
      });
    }

    // 7. Grid Columns Switcher (2, 3, 4 columns)
    document.querySelectorAll(".eq-grid-view-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".eq-grid-view-btn").forEach(b => b.classList.remove("is-active"));
        btn.classList.add("is-active");
        const cols = parseInt(btn.getAttribute("data-cols"), 10);
        state.gridColumns = cols;
        const grid = document.querySelector("#catalog-grid");
        if (grid) {
          grid.classList.remove("view-2col", "view-3col", "view-4col");
          if (cols === 2) grid.classList.add("view-2col");
          if (cols === 3) grid.classList.add("view-3col");
          if (cols === 4) grid.classList.add("view-4col");
        }
      });
    });

    // 8. Mobile Filter Drawer Trigger & Backdrop (Safe)
    const mobileTrigger = document.querySelector("#mobile-filter-trigger");
    const sidebar = document.querySelector("#filter-sidebar");
    const backdrop = document.querySelector("#filter-backdrop");

    if (sidebar) {
      // Ensure close button exists in sidebar header for mobile drawer
      const header = sidebar.querySelector(".eq-filter-sidebar__header");
      if (header && !header.querySelector("#filter-sidebar-close")) {
        const closeBtn = document.createElement("button");
        closeBtn.type = "button";
        closeBtn.className = "eq-filter-sidebar__close";
        closeBtn.id = "filter-sidebar-close";
        closeBtn.setAttribute("aria-label", "Close filters");
        closeBtn.innerHTML = `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;
        header.appendChild(closeBtn);
      }

      // Ensure footer exists in sidebar for mobile drawer apply/reset actions
      if (!sidebar.querySelector(".eq-filter-sidebar__footer")) {
        const footer = document.createElement("div");
        footer.className = "eq-filter-sidebar__footer";
        footer.id = "filter-sidebar-footer";
        footer.innerHTML = `
          <button type="button" class="eq-filter-sidebar__footer-reset" id="filter-footer-reset">Reset All</button>
          <button type="button" class="eq-filter-sidebar__footer-apply" id="filter-footer-apply">Show Products</button>
        `;
        sidebar.appendChild(footer);

        const footerReset = footer.querySelector("#filter-footer-reset");
        const footerApply = footer.querySelector("#filter-footer-apply");
        if (footerReset) footerReset.addEventListener("click", resetAllFilters);
        if (footerApply) footerApply.addEventListener("click", () => closeDrawer());
      }
    }

    const openDrawer = () => {
      if (sidebar) {
        sidebar.classList.add("is-open");
        sidebar.setAttribute("aria-hidden", "false");
      }
      if (backdrop) backdrop.classList.add("is-open");
      document.body.style.overflow = "hidden";
    };

    const closeDrawer = () => {
      if (sidebar) {
        sidebar.classList.remove("is-open");
        sidebar.setAttribute("aria-hidden", "true");
      }
      if (backdrop) backdrop.classList.remove("is-open");
      document.body.style.overflow = "";
    };

    if (mobileTrigger) mobileTrigger.addEventListener("click", openDrawer);
    if (backdrop) backdrop.addEventListener("click", closeDrawer);

    if (sidebar) {
      sidebar.setAttribute("aria-hidden", "true");
      sidebar.addEventListener("click", (e) => {
        if (e.target.closest("#filter-sidebar-close")) {
          closeDrawer();
        }
      });
    }

    // Keyboard accessibility: Escape key to close filter drawer
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && sidebar && sidebar.classList.contains("is-open")) {
        closeDrawer();
      }
    });

    // 9. Reset All in Sidebar
    const resetSidebarBtn = document.querySelector("#filter-sidebar-reset");
    if (resetSidebarBtn) resetSidebarBtn.addEventListener("click", resetAllFilters);
  }

  function resetAllFilters() {
    state.activeSubCategory = "all";
    state.priceMax = 30000;
    state.selectedFabrics.clear();
    state.inStockOnly = false;
    state.sortBy = "featured";

    updatePriceSliderUI(30000);
    updateCheckboxUI();

    const sortSelect = document.querySelector("#catalog-sort-select");
    if (sortSelect) sortSelect.value = "featured";

    renderAll();
  }

  function updatePriceSliderUI(val) {
    const slider = document.querySelector("#filter-price-slider");
    const input = document.querySelector("#filter-price-max");
    if (slider) slider.value = val;
    if (input) input.value = val;
  }

  function updateCheckboxUI() {
    document.querySelectorAll('[data-filter="fabric"]').forEach(cb => {
      cb.checked = state.selectedFabrics.has(cb.value);
    });
    const inStockCb = document.querySelector("#filter-in-stock");
    if (inStockCb) inStockCb.checked = state.inStockOnly;
  }

  function renderAll() {
    renderHeaderMeta();
    renderProducts();
  }

  // ===================================================================
  // 11. INITIALIZATION
  // ===================================================================
  document.addEventListener("DOMContentLoaded", () => {
    parseUrlParams();
    renderAll();
    initFilterControls();
  });

})();
