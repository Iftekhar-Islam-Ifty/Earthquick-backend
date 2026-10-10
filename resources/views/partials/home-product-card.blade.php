<article class="eq-product-card{{ isset($cardClass) ? ' '.$cardClass : '' }}" id="{{ $cardId ?? 'home-product-'.$product->id }}" data-id="{{ $product->id }}">
  <a href="{{ route('product.show', $product->slug) }}" class="eq-product-card__link">
    <div class="eq-product-card__frame">
      @if($product->badge)
        <span class="eq-badge">{{ $product->badge }}</span>
      @elseif($product->is_new_arrival)
        <span class="eq-badge">New</span>
      @endif
      <img src="{{ asset($product->optimized_image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" />
      @if($product->optimized_alt_image)
        <img class="eq-product-card__img--alt" src="{{ asset($product->optimized_alt_image) }}" alt="" loading="lazy" decoding="async" />
      @endif
      <span class="eq-product-card__quick-add">View product</span>
    </div>
    <div class="eq-product-card__body">
      @if($showCategory ?? false)
        <span class="eq-product-card__category">{{ $product->subcategory?->name ?? $product->category?->name }}</span>
      @endif
      <h3 class="eq-product-card__name">{{ $product->name }}</h3>
      <p class="eq-product-card__price">
        @if($product->old_price && $product->old_price > $product->price)
          <span class="eq-price--old">৳{{ number_format($product->old_price) }}</span>
        @endif
        ৳{{ number_format($product->price) }}
      </p>
    </div>
  </a>
</article>
