@if($paginator->hasPages())
  <style>
    .eq-polished-pagination {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      width: 100%;
      margin-top: 1.75rem;
      padding-top: 1.25rem;
      border-top: 1px solid var(--eq-line);
    }

    .eq-polished-pagination__summary {
      margin: 0;
      color: var(--eq-charcoal-soft);
      font-size: 0.78rem;
      white-space: nowrap;
    }

    .eq-polished-pagination__summary strong {
      color: var(--eq-navy);
      font-weight: 600;
    }

    .eq-polished-pagination__controls {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 0.35rem;
    }

    .eq-polished-pagination__item {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 36px;
      height: 36px;
      padding: 0 0.65rem;
      border: 1px solid var(--eq-line);
      border-radius: 7px;
      background: var(--eq-white, #ffffff);
      color: var(--eq-charcoal);
      font-size: 0.8rem;
      font-weight: 500;
      line-height: 1;
      text-decoration: none;
      box-shadow: 0 1px 2px rgba(18, 40, 52, 0.04);
      transition: border-color 0.18s ease, background-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    a.eq-polished-pagination__item:hover {
      border-color: var(--eq-gold);
      background: var(--eq-gold-light, #f9f3e5);
      color: var(--eq-navy);
      transform: translateY(-1px);
    }

    .eq-polished-pagination__item.is-current {
      border-color: var(--eq-navy);
      background: var(--eq-navy);
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(27, 58, 75, 0.16);
    }

    .eq-polished-pagination__item.is-disabled {
      color: var(--eq-charcoal-muted);
      background: var(--eq-cream, #f7f2e9);
      opacity: 0.55;
    }

    .eq-polished-pagination__item.is-gap {
      min-width: 28px;
      padding: 0;
      border-color: transparent;
      background: transparent;
      box-shadow: none;
    }

    .eq-polished-pagination__arrow {
      gap: 0.4rem;
    }

    @media (max-width: 640px) {
      .eq-polished-pagination {
        flex-direction: column;
        align-items: stretch;
        gap: 0.8rem;
        margin-top: 1.25rem;
        padding-top: 1rem;
      }

      .eq-polished-pagination__summary {
        text-align: center;
        white-space: normal;
      }

      .eq-polished-pagination__controls {
        justify-content: center;
        gap: 0.25rem;
      }

      .eq-polished-pagination__item {
        min-width: 34px;
        height: 34px;
        padding: 0 0.5rem;
        font-size: 0.76rem;
      }

      .eq-polished-pagination__arrow-label {
        display: none;
      }
    }
  </style>

  <nav class="eq-polished-pagination" aria-label="Pagination Navigation">
    <p class="eq-polished-pagination__summary">
      Showing <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
      of <strong>{{ $paginator->total() }}</strong>
    </p>

    <div class="eq-polished-pagination__controls">
      @if($paginator->onFirstPage())
        <span class="eq-polished-pagination__item eq-polished-pagination__arrow is-disabled" aria-disabled="true">
          <span aria-hidden="true">&larr;</span>
          <span class="eq-polished-pagination__arrow-label">Previous</span>
        </span>
      @else
        <a class="eq-polished-pagination__item eq-polished-pagination__arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev">
          <span aria-hidden="true">&larr;</span>
          <span class="eq-polished-pagination__arrow-label">Previous</span>
        </a>
      @endif

      @php
        $visiblePages = collect([1, $paginator->lastPage()])
          ->merge(range(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)))
          ->unique()
          ->sort()
          ->values();
        $previousVisiblePage = null;
      @endphp

      @foreach($visiblePages as $page)
        @if($previousVisiblePage !== null && $page > $previousVisiblePage + 1)
          <span class="eq-polished-pagination__item is-gap" aria-hidden="true">&hellip;</span>
        @endif

        @if($page === $paginator->currentPage())
          <span class="eq-polished-pagination__item is-current" aria-current="page">{{ $page }}</span>
        @else
          <a class="eq-polished-pagination__item" href="{{ $paginator->url($page) }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
        @endif

        @php($previousVisiblePage = $page)
      @endforeach

      @if($paginator->hasMorePages())
        <a class="eq-polished-pagination__item eq-polished-pagination__arrow" href="{{ $paginator->nextPageUrl() }}" rel="next">
          <span class="eq-polished-pagination__arrow-label">Next</span>
          <span aria-hidden="true">&rarr;</span>
        </a>
      @else
        <span class="eq-polished-pagination__item eq-polished-pagination__arrow is-disabled" aria-disabled="true">
          <span class="eq-polished-pagination__arrow-label">Next</span>
          <span aria-hidden="true">&rarr;</span>
        </span>
      @endif
    </div>
  </nav>
@endif
