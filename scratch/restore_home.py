import re

with open(r"D:\rthquick-0.2 (AI Studio Frontend)\index.html", "r", encoding="utf-8") as f:
    content = f.read()

# 1. Replace header placeholder with @include('partials.navbar')
content = content.replace('<div id="site-header"></div>', "@include('partials.navbar')")

# 2. Replace footer placeholder with @include('partials.footer')
content = content.replace('<div id="site-footer"></div>', "@include('partials.footer')")

# 3. Replace CSS link
content = re.sub(r'href="css/style\.css(?:\?[^"]*)?"', 'href="{{ asset(\'css/style.css\') }}"', content)

# 4. Replace script links
content = re.sub(r'src="js/script\.js(?:\?[^"]*)?"', 'src="{{ asset(\'js/script.js\') }}"', content)
content = re.sub(r'src="js/layout\.js(?:\?[^"]*)?"', 'src="{{ asset(\'js/layout.js\') }}"', content)

# 5. Replace image src paths: src="images/...(?:\?v=\d+)?" -> src="{{ asset('images/...') }}"
def replace_img_src(match):
    full_match = match.group(0)
    path = match.group(1)
    # clean query string like ?v=3 from the path
    clean_path = path.split('?')[0]
    return f'src="{{{{ asset(\'{clean_path}\') }}}}"'

content = re.sub(r'src="(images/[^"]+)"', replace_img_src, content)

# 6. Replace category and static page links
replacements = [
    # Pages
    ('href="pages/women.html"', 'href="{{ route(\'category.show\', \'women\') }}"'),
    ('href="pages/saree.html"', 'href="{{ url(\'/shop/women/saree\') }}"'),
    ('href="pages/three-piece.html"', 'href="{{ url(\'/shop/women/three-piece\') }}"'),
    ('href="pages/two-piece.html"', 'href="{{ url(\'/shop/women/two-piece\') }}"'),
    ('href="pages/bags.html"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/men.html"', 'href="{{ route(\'category.show\', \'men\') }}"'),
    ('href="pages/kids.html"', 'href="{{ route(\'category.show\', \'kids\') }}"'),
    ('href="pages/ornaments.html"', 'href="{{ route(\'category.show\', \'ornaments\') }}"'),
    ('href="pages/home-decor.html"', 'href="{{ route(\'category.show\', \'home-decor\') }}"'),
    ('href="pages/about.html"', 'href="{{ route(\'about\') }}"'),
    ('href="pages/about.html#brand-ecosystem"', 'href="{{ route(\'about\') }}#brand-ecosystem"'),
    ('href="pages/about.html#contact-support"', 'href="{{ route(\'about\') }}#contact-support"'),
    ('href="pages/about.html#help-faq"', 'href="{{ route(\'about\') }}#help-faq"'),
    ('href="index.html"', 'href="{{ route(\'home\') }}"'),
    
    # Specific Product Links
    ('href="pages/product.html?id=saree-01"', 'href="{{ url(\'/product/crimson-heirloom-jamdani\') }}"'),
    ('href="pages/product.html?id=saree-02"', 'href="{{ url(\'/product/midnight-indigo-tantuj-drape\') }}"'),
    ('href="pages/product.html?id=saree-03"', 'href="{{ url(\'/product/royal-champagne-half-silk\') }}"'),
    ('href="pages/product.html?id=saree-04"', 'href="{{ url(\'/product/emerald-rajshahi-pure-silk\') }}"'),
    ('href="pages/product.html?id=saree-06"', 'href="{{ url(\'/shop/women/saree\') }}"'),
    
    ('href="pages/product.html?id=three-piece-01"', 'href="{{ url(\'/product/ivory-organza-embroidered-set\') }}"'),
    ('href="pages/product.html?id=three-piece-02"', 'href="{{ url(\'/product/blush-pink-hand-embroidered-kameez\') }}"'),
    ('href="pages/product.html?id=three-piece-03"', 'href="{{ url(\'/shop/women/three-piece\') }}"'),
    ('href="pages/product.html?id=three-piece-05"', 'href="{{ url(\'/shop/women/three-piece\') }}"'),
    ('href="pages/product.html?id=three-piece-06"', 'href="{{ url(\'/shop/women/three-piece\') }}"'),
    
    ('href="pages/product.html?id=two-piece-01"', 'href="{{ url(\'/product/minimalist-sand-linen-co-ord\') }}"'),
    ('href="pages/product.html?id=two-piece-02"', 'href="{{ url(\'/product/ochre-terracotta-kurti-culotte\') }}"'),
    ('href="pages/product.html?id=two-piece-03"', 'href="{{ url(\'/shop/women/two-piece\') }}"'),
    ('href="pages/product.html?id=two-piece-04"', 'href="{{ url(\'/shop/women/two-piece\') }}"'),
    
    ('href="pages/product.html?id=bag-01"', 'href="{{ url(\'/product/artisanal-terracotta-leather-tote-bag\') }}"'),
    ('href="pages/product.html?id=bag-02"', 'href="{{ url(\'/product/saddle-brown-crossbody-sling\') }}"'),
    ('href="pages/product.html?id=bag-03"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-04"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-05"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-06"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-07"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-08"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-09"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
    ('href="pages/product.html?id=bag-10"', 'href="{{ route(\'category.show\', \'bags\') }}"'),
]

for old, new in replacements:
    content = content.replace(old, new)

# Write to resources/views/home.blade.php
with open(r"resources\views\home.blade.php", "w", encoding="utf-8") as f:
    f.write(content)

print("Successfully restored home.blade.php from exact index.html prototype!")

