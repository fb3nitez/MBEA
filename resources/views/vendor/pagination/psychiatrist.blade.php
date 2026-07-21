@php
    if (! $paginator->hasPages()) {
        return;
    }

    $onFirstPage = $paginator->onFirstPage();
    $onLastPage = ! $paginator->hasMorePages();
    $from = $paginator->firstItem();
    $to = $paginator->lastItem();
    $total = $paginator->total();
@endphp

<nav class="psych-pagination" role="navigation" aria-label="Pagination">
  <div class="psych-pagination-meta">
    Showing <strong>{{ $from }}</strong>–<strong>{{ $to }}</strong> of <strong>{{ $total }}</strong>
  </div>

  <ul class="psych-pagination-list">
    <li>
      @if ($onFirstPage)
        <span class="psych-page-btn is-disabled" aria-disabled="true">Prev</span>
      @else
        <a class="psych-page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a>
      @endif
    </li>

    @foreach ($elements as $element)
      @if (is_string($element))
        <li><span class="psych-page-btn is-ellipsis">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          <li>
            @if ($page == $paginator->currentPage())
              <span class="psych-page-btn is-active" aria-current="page">{{ $page }}</span>
            @else
              <a class="psych-page-btn" href="{{ $url }}">{{ $page }}</a>
            @endif
          </li>
        @endforeach
      @endif
    @endforeach

    <li>
      @if ($onLastPage)
        <span class="psych-page-btn is-disabled" aria-disabled="true">Next</span>
      @else
        <a class="psych-page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
      @endif
    </li>
  </ul>
</nav>
