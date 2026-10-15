@extends('layouts.app')
@section('title', 'Our Courses | Jerin’s Coaching')
@section('meta_description', 'Explore comprehensive NEET Biology, Class 11, Class 12, Foundation and Test Series courses at Jerin’s Coaching. Learn without fear.')

@section('content')
<style>
  /* All Courses Catalogue Page Styling (Matching Image 2 Reference) */
  .catalogue-wrapper {
    background: #ffffff;
    padding: 32px 0 64px;
    min-height: 80vh;
  }
  .catalogue-header {
    margin-bottom: 28px;
  }
  .catalogue-title {
    font-size: 34px;
    font-weight: 800;
    color: #082c50;
    margin: 0 0 6px;
    letter-spacing: -0.03em;
    line-height: 1.15;
  }
  .catalogue-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
  }
  .catalogue-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s ease;
  }
  .catalogue-breadcrumb a:hover {
    color: #0868bd;
  }
  .breadcrumb-sep {
    color: #94a3b8;
    font-size: 12px;
  }
  .breadcrumb-current {
    color: #334155;
    font-weight: 600;
  }

  /* Two Column Layout */
  .catalogue-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 32px;
    align-items: start;
  }

  /* Left Sidebar Filters */
  .catalogue-sidebar {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 14px;
    padding: 24px 20px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
  }
  .filter-section {
    margin-bottom: 24px;
  }
  .filter-section:last-child {
    margin-bottom: 0;
  }
  .filter-heading {
    font-size: 17px;
    font-weight: 700;
    color: #082c50;
    margin: 0 0 14px;
    letter-spacing: -0.01em;
  }
  .filter-divider {
    border: none;
    border-top: 1px solid #edf2f7;
    margin: 22px 0;
  }

  /* Category List */
  .category-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .category-link {
    display: block;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #334155;
    text-decoration: none;
    transition: background 0.15s ease, color 0.15s ease;
  }
  .category-link:hover {
    background: #f8fafc;
    color: #0868bd;
  }
  .category-link.is-active {
    background: #e8f2ff;
    color: #0868bd;
    font-weight: 700;
  }

  /* Dual Range Slider (Price Filter) */
  .range-slider-container {
    padding: 10px 4px 6px;
  }
  .range-track-wrapper {
    position: relative;
    height: 24px;
    display: flex;
    align-items: center;
  }
  .range-base-track {
    position: absolute;
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
  }
  .range-fill-track {
    position: absolute;
    height: 4px;
    background: #0868bd;
    border-radius: 2px;
    pointer-events: none;
  }
  .range-input {
    position: absolute;
    width: 100%;
    height: 4px;
    background: transparent;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
  }
  .range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    pointer-events: auto;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ffffff;
    border: 3.5px solid #0868bd;
    box-shadow: 0 2px 5px rgba(8, 104, 189, 0.25);
    cursor: pointer;
    transition: transform 0.1s ease;
  }
  .range-input::-webkit-slider-thumb:hover {
    transform: scale(1.15);
  }
  .range-input::-moz-range-thumb {
    pointer-events: auto;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ffffff;
    border: 3.5px solid #0868bd;
    box-shadow: 0 2px 5px rgba(8, 104, 189, 0.25);
    cursor: pointer;
  }
  .price-readout {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
  }

  /* Level Checkboxes */
  .level-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .level-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    user-select: none;
  }
  .level-checkbox {
    width: 18px;
    height: 18px;
    accent-color: #0868bd;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    cursor: pointer;
    margin: 0;
  }

  /* Right Main Catalogue Area */
  .catalogue-main {
    min-width: 0;
  }

  /* Search & Sort Controls Bar */
  .catalogue-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
  }
  .search-wrapper {
    position: relative;
    flex: 1;
    max-width: 620px;
  }
  .search-icon-inside {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: #64748b;
    pointer-events: none;
  }
  .catalogue-search-input {
    width: 100%;
    height: 44px;
    padding: 0 14px 0 42px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .catalogue-search-input::placeholder {
    color: #94a3b8;
  }
  .catalogue-search-input:focus {
    border-color: #0868bd;
    box-shadow: 0 0 0 3px rgba(8, 104, 189, 0.1);
  }

  /* Sort Dropdown */
  .sort-wrapper {
    position: relative;
    min-width: 200px;
  }
  .sort-select {
    width: 100%;
    height: 44px;
    padding: 0 36px 0 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .sort-select:focus {
    border-color: #0868bd;
    box-shadow: 0 0 0 3px rgba(8, 104, 189, 0.1);
  }
  .sort-chevron {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: #64748b;
    pointer-events: none;
  }

  /* Course Cards Grid (3 Columns on Desktop matching Image 2) */
  .catalogue-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
  }

  /* Individual Course Card */
  .course-catalogue-card {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .course-catalogue-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(8, 44, 80, 0.08);
    border-color: #dce5ef;
  }
  .card-thumbnail-wrap {
    position: relative;
    width: 100%;
    height: 185px;
    overflow: hidden;
    background: #f1f5f9;
  }
  .card-thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s ease;
  }
  .course-catalogue-card:hover .card-thumbnail-img {
    transform: scale(1.03);
  }
  .card-details {
    padding: 18px 18px 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }
  .card-course-title {
    font-size: 17px;
    font-weight: 700;
    color: #082c50;
    line-height: 1.35;
    margin: 0 0 10px;
    min-height: 46px;
  }
  .card-course-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.15s ease;
  }
  .card-course-title a:hover {
    color: #0868bd;
  }
  .card-rating-line {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
  }
  .stars-gold {
    color: #f59e0b;
    font-size: 15px;
    letter-spacing: 1px;
    line-height: 1;
  }
  .rating-score {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
  }
  .card-price-value {
    font-size: 22px;
    font-weight: 800;
    color: #0868bd;
    margin-bottom: 16px;
    letter-spacing: -0.02em;
    line-height: 1.1;
  }
  .btn-enroll-card {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 11px 16px;
    background: #0868bd;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    border: none;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(8, 104, 189, 0.2);
    transition: background 0.15s ease, transform 0.15s ease;
  }
  .btn-enroll-card:hover {
    background: #0754a0;
    transform: translateY(-1px);
    color: #ffffff;
  }

  /* Empty State */
  .catalogue-empty {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 14px;
    padding: 56px 24px;
    text-align: center;
    margin: 20px 0;
  }
  .empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #e8f2ff;
    color: #0868bd;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
  }
  .empty-title {
    font-size: 19px;
    font-weight: 700;
    color: #082c50;
    margin: 0 0 8px;
  }
  .empty-desc {
    color: #64748b;
    font-size: 14px;
    max-width: 420px;
    margin: 0 auto 20px;
    line-height: 1.55;
  }

  /* Mobile Filter Toggle Button */
  .mobile-filter-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    margin-bottom: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    color: #082c50;
    cursor: pointer;
  }

  /* Responsive Breakpoints */
  @media (max-width: 1080px) {
    .catalogue-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
    }
  }

  @media (max-width: 860px) {
    .catalogue-layout {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .mobile-filter-toggle {
      display: flex;
    }
    .catalogue-sidebar {
      display: none;
    }
    .catalogue-sidebar.mobile-open {
      display: block;
    }
    .catalogue-controls {
      flex-direction: column;
      align-items: stretch;
    }
    .search-wrapper {
      max-width: 100%;
    }
    .sort-wrapper {
      width: 100%;
    }
  }

  @media (max-width: 580px) {
    .catalogue-grid {
      grid-template-columns: 1fr;
    }
    .catalogue-title {
      font-size: 28px;
    }
    .card-thumbnail-wrap {
      height: 200px;
    }
  }
</style>

<section class="catalogue-wrapper">
  <div class="container">
    <!-- Header: Title and Breadcrumb -->
    <div class="catalogue-header">
      <h1 class="catalogue-title">{{ __('Our Courses') }}</h1>
      <nav class="catalogue-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
        <span class="breadcrumb-sep">&gt;</span>
        <span class="breadcrumb-current">{{ __('Courses') }}</span>
      </nav>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <button type="button" class="mobile-filter-toggle" id="filterToggleBtn" onclick="toggleMobileFilters()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="21" x2="4" y2="14"></line>
        <line x1="4" y1="10" x2="4" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12" y2="3"></line>
        <line x1="20" y1="21" x2="20" y2="16"></line>
        <line x1="20" y1="12" x2="20" y2="3"></line>
        <line x1="1" y1="14" x2="7" y2="14"></line>
        <line x1="9" y1="8" x2="15" y2="8"></line>
        <line x1="17" y1="16" x2="23" y2="16"></line>
      </svg>
      <span>{{ __('Filter & Refine Courses') }}</span>
    </button>

    <div class="catalogue-layout">
      <!-- Left Sidebar: Filters -->
      <aside class="catalogue-sidebar" id="catalogueSidebar">
        <!-- 1. Categories Filter -->
        <div class="filter-section">
          <h3 class="filter-heading">{{ __('Categories') }}</h3>
          <ul class="category-list">
            <li>
              <a href="{{ route('courses.index', array_merge(request()->except(['category', 'page']))) }}"
                 class="category-link {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'is-active' : '' }}">
                {{ __('All Courses') }}
              </a>
            </li>
            @foreach($categories as $cat)
              <li>
                <a href="{{ route('courses.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                   class="category-link {{ $selectedCategory === $cat->slug ? 'is-active' : '' }}">
                  {{ __($cat->name) }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        <hr class="filter-divider">

        <!-- Filter Form for Price & Levels -->
        <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
          <!-- Hidden inputs to preserve category, search, and sort -->
          @if($selectedCategory && $selectedCategory !== 'all')
            <input type="hidden" name="category" value="{{ $selectedCategory }}">
          @endif
          @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
          @endif
          @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
          @endif

          <!-- 2. Price Range Filter -->
          <div class="filter-section">
            <h3 class="filter-heading">{{ __('Price Range') }}</h3>
            <div class="range-slider-container">
              <div class="range-track-wrapper">
                <div class="range-base-track"></div>
                <div class="range-fill-track" id="rangeFill"></div>
                <input type="range" id="sliderMin" name="min_price" min="0" max="25000" step="100"
                       value="{{ $minPrice ?? 0 }}" class="range-input" aria-label="Minimum Price">
                <input type="range" id="sliderMax" name="max_price" min="0" max="25000" step="100"
                       value="{{ $maxPrice ?? 25000 }}" class="range-input" aria-label="Maximum Price">
              </div>
              <div class="price-readout">
                <span>৳ <span id="labelMin">{{ bn_number(number_format($minPrice ?? 0)) }}</span></span>
                <span>-</span>
                <span>৳<span id="labelMax">{{ bn_number(number_format($maxPrice ?? 25000)) }}</span></span>
              </div>
            </div>
          </div>

          <hr class="filter-divider">

          <!-- 3. Level Filter -->
          <div class="filter-section">
            <h3 class="filter-heading">{{ __('Level') }}</h3>
            <div class="level-list">
              @php($levels = ['Beginner', 'Intermediate', 'Advanced'])
              @foreach($levels as $lvl)
                <label class="level-item">
                  <input type="checkbox" name="levels[]" value="{{ $lvl }}"
                         class="level-checkbox"
                         {{ in_array($lvl, (array)($selectedLevels ?? [])) ? 'checked' : '' }}
                         onchange="document.getElementById('filterForm').submit()">
                  <span>{{ __($lvl) }}</span>
                </label>
              @endforeach
            </div>
          </div>

          @if(request()->hasAny(['category', 'q', 'min_price', 'max_price', 'levels', 'sort']))
            <div style="margin-top:20px;text-align:center">
              <a href="{{ route('courses.index') }}" style="font-size:12px;color:#64748b;text-decoration:underline">
                {{ __('Clear all filters') }}
              </a>
            </div>
          @endif
        </form>
      </aside>

      <!-- Right Column: Search, Sort, and Courses Grid -->
      <main class="catalogue-main">
        <!-- Top Controls: Search and Sort Bar -->
        <div class="catalogue-controls">
          <!-- Search Form -->
          <form method="GET" action="{{ route('courses.index') }}" class="search-wrapper" id="searchForm">
            @if($selectedCategory && $selectedCategory !== 'all')
              <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            @if(request('min_price'))
              <input type="hidden" name="min_price" value="{{ request('min_price') }}">
            @endif
            @if(request('max_price'))
              <input type="hidden" name="max_price" value="{{ request('max_price') }}">
            @endif
            @if(request('sort'))
              <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @foreach((array)request('levels', []) as $lvl)
              <input type="hidden" name="levels[]" value="{{ $lvl }}">
            @endforeach

            <svg class="search-icon-inside" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('Search courses...') }}"
                   class="catalogue-search-input"
                   aria-label="Search courses">
          </form>

          <!-- Sort Dropdown Form -->
          <form method="GET" action="{{ route('courses.index') }}" class="sort-wrapper" id="sortForm">
            @if($selectedCategory && $selectedCategory !== 'all')
              <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            @if(request('q'))
              <input type="hidden" name="q" value="{{ request('q') }}">
            @endif
            @if(request('min_price'))
              <input type="hidden" name="min_price" value="{{ request('min_price') }}">
            @endif
            @if(request('max_price'))
              <input type="hidden" name="max_price" value="{{ request('max_price') }}">
            @endif
            @foreach((array)request('levels', []) as $lvl)
              <input type="hidden" name="levels[]" value="{{ $lvl }}">
            @endforeach

            <select name="sort" class="sort-select" onchange="this.form.submit()" aria-label="Sort courses">
              <option value="popularity" {{ ($sort ?? 'popularity') === 'popularity' ? 'selected' : '' }}>{{ __('Sort by: Popularity') }}</option>
              <option value="price_asc" {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>{{ __('Sort by: Price: Low to High') }}</option>
              <option value="price_desc" {{ ($sort ?? '') === 'price_desc' ? 'selected' : '' }}>{{ __('Sort by: Price: High to Low') }}</option>
              <option value="newest" {{ ($sort ?? '') === 'newest' ? 'selected' : '' }}>{{ __('Sort by: Newest') }}</option>
            </select>
            <svg class="sort-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </form>
        </div>

        <!-- 3-Column Course Cards Grid -->
        @if($courses->isNotEmpty())
          <div class="catalogue-grid">
            @foreach($courses as $course)
              <article class="course-catalogue-card">
                <a href="{{ route('courses.show', $course) }}" class="card-thumbnail-wrap" title="View {{ $course->title }}">
                  <img src="{{ $course->thumbnail_url }}"
                       alt="{{ $course->title }}"
                       class="card-thumbnail-img"
                       loading="lazy">
                </a>
                <div class="card-details">
                  <h2 class="card-course-title">
                    <a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a>
                  </h2>

                  <div class="card-rating-line">
                    <span class="stars-gold" aria-hidden="true">★★★★★</span>
                    <span class="rating-score">
                      {{ bn_number(number_format($course->rating, 1)) }} ({{ bn_number($course->review_count) }})
                    </span>
                  </div>

                  <div class="card-price-value">
                    {{ bdt($course->price_paise, true) }}
                  </div>

                  @auth
                    @php($enrolled = auth()->user()->enrollments()->where('course_id', $course->id)->where('status', 'active')->exists())
                    @if($enrolled)
                      <a href="{{ route('student.courses') }}" class="btn-enroll-card" style="background:#078451">
                        {{ __('Go to Course →') }}
                      </a>
                    @else
                      <form method="POST" action="{{ route('checkout.start', $course) }}" style="margin:0;width:100%">
                        @csrf
                        <button type="submit" class="btn-enroll-card">
                          {{ __('Enroll Now') }}
                        </button>
                      </form>
                    @endif
                  @else
                    <a href="{{ route('courses.show', $course) }}" class="btn-enroll-card">
                      {{ __('Enroll Now') }}
                    </a>
                  @endauth
                </div>
              </article>
            @endforeach
          </div>

          <!-- Pagination if needed -->
          @if($courses->hasPages())
            <div style="margin-top:36px;display:flex;justify-content:center">
              {{ $courses->links() }}
            </div>
          @endif
        @else
          <!-- Polished Empty State -->
          <div class="catalogue-empty">
            <div class="empty-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </div>
            <h2 class="empty-title">No courses match your criteria</h2>
            <p class="empty-desc">
              We couldn't find any courses matching your selected filters or search terms. Try loosening your price range or clearing category filters.
            </p>
            <a href="{{ route('courses.index') }}" class="btn" style="padding:10px 24px;font-size:14px">
              Reset All Filters
            </a>
          </div>
        @endif
      </main>
    </div>
  </div>
</section>

<script>
  // Dual Range Slider Logic
  const sliderMin = document.getElementById('sliderMin');
  const sliderMax = document.getElementById('sliderMax');
  const labelMin = document.getElementById('labelMin');
  const labelMax = document.getElementById('labelMax');
  const rangeFill = document.getElementById('rangeFill');
  const filterForm = document.getElementById('filterForm');

  const minGap = 500;
  const sliderLimit = 25000;
  let debounceTimeout = null;

  function updateSliderFill() {
    let minVal = parseInt(sliderMin.value);
    let maxVal = parseInt(sliderMax.value);

    if (maxVal - minVal < minGap) {
      if (document.activeElement === sliderMin) {
        sliderMin.value = maxVal - minGap;
        minVal = maxVal - minGap;
      } else {
        sliderMax.value = minVal + minGap;
        maxVal = minVal + minGap;
      }
    }

    const percentMin = (minVal / sliderLimit) * 100;
    const percentMax = (maxVal / sliderLimit) * 100;

    rangeFill.style.left = percentMin + '%';
    rangeFill.style.width = (percentMax - percentMin) + '%';

    labelMin.textContent = minVal.toLocaleString('en-IN');
    labelMax.textContent = maxVal.toLocaleString('en-IN');
  }

  function handleSliderChange() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      filterForm.submit();
    }, 450);
  }

  if (sliderMin && sliderMax) {
    sliderMin.addEventListener('input', () => {
      updateSliderFill();
    });
    sliderMax.addEventListener('input', () => {
      updateSliderFill();
    });
    sliderMin.addEventListener('change', handleSliderChange);
    sliderMax.addEventListener('change', handleSliderChange);
    updateSliderFill();
  }

  // Mobile Filter Drawer Toggle
  function toggleMobileFilters() {
    const sidebar = document.getElementById('catalogueSidebar');
    if (sidebar) {
      sidebar.classList.toggle('mobile-open');
    }
  }
</script>
@endsection
