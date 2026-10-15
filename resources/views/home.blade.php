@extends('layouts.app')
@section('title', "Jerin's Coaching | Learn Biology Without Fear")
@section('meta_description', "Explore Biology courses, study resources and structured learning at Jerin's Coaching.")
@section('content')
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <div class="eyebrow">Learn Biology the right way</div>
      <h1>Learn <span>Biology</span><br>Without Fear</h1>
      <p>Expert guidance, structured courses and personal support for your success.</p>
      <div class="hero-buttons"><a class="btn btn-gold" href="{{ route('courses.index') }}">Explore Courses →</a><a class="btn btn-outline" href="#about">▶ &nbsp; Watch Intro</a></div>
      <div class="hero-stats"><div class="stat"><span class="stat-icon">♙</span><span><strong>Learn</strong>Student focused</span></div><div class="stat"><span class="stat-icon">🏆</span><span><strong>Practice</strong>Regular tests</span></div><div class="stat"><span class="stat-icon">▤</span><span><strong>Study</strong>Learning resources</span></div></div>
    </div>
    <div class="hero-person">
      <img src="{{ asset('images/hero-reference-crop.png') }}?v={{ file_exists(public_path('images/hero-reference-crop.png')) ? filemtime(public_path('images/hero-reference-crop.png')) : time() }}" alt="Dr. Tanjina Jerin - Biology Educator">
    </div>
    <div class="hero-note">Your Biology<br>Journey Starts Here!</div>
  </div>
</section>

<section class="section section-blue" id="courses">
 <div class="container">
  <div class="section-title-row"><div><h2>Our Courses</h2><p class="section-subtitle">Build strong concepts with structured Biology learning.</p></div><a class="text-link" href="{{ route('courses.index') }}">View All Courses →</a></div>
  <div class="course-grid">
    @php
      $courseVisuals = ['course-dna.svg', 'course-cell.svg', 'course-plant.svg', 'course-brain.svg'];
      $courseLabels = ['Popular', 'Bestseller', 'Class 11', 'Featured'];
      $fallbackCourses = [
        ['title' => 'NEET Biology Complete Course', 'category' => 'NEET Biology', 'price' => '2,999', 'lessons' => '80+ Lessons', 'tag' => 'Full Syllabus'],
        ['title' => 'Class 12 Biology Full Course', 'category' => 'Class 12 Biology', 'price' => '1,499', 'lessons' => '60+ Lessons', 'tag' => 'Board Exam'],
        ['title' => 'Class 11 Biology Full Course', 'category' => 'Class 11 Biology', 'price' => '1,499', 'lessons' => '50+ Lessons', 'tag' => 'NCERT Based'],
        ['title' => 'Human Physiology', 'category' => 'Biology', 'price' => '899', 'lessons' => '40+ Lessons', 'tag' => 'Focused Learning'],
      ];
    @endphp
    @forelse($courses as $index => $course)
      <article class="course-card">
        <div class="course-image">
          <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('images/'.($courseVisuals[$index % count($courseVisuals)])) }}" alt="{{ $course->title }}">
          @if($index < 2)<span class="course-ribbon">{{ $courseLabels[$index] }}</span>@endif
        </div>
        <div class="course-body">
          <span class="course-label">{{ $course->category?->name ?? 'Biology' }}</span>
          <h3>{{ $course->title }}</h3>
          <div class="price-row"><span class="price">{{ bdt($course->price_paise, true) }}</span><span class="discount">{{ __('Course') }}</span></div>
          <a class="btn" href="{{ route('courses.show', $course) }}">{{ __('View Course') }}</a>
        </div>
      </article>
    @empty
      @foreach($fallbackCourses as $index => $item)
        <article class="course-card">
          <div class="course-image">
            <img src="{{ asset('images/'.$courseVisuals[$index]) }}" alt="{{ $item['category'] }}">
            @if($index < 2)<span class="course-ribbon">{{ $courseLabels[$index] }}</span>@endif
          </div>
          <div class="course-body">
            <span class="course-label">{{ $item['category'] }}</span>
            <h3>{{ $item['title'] }}</h3>
            <div class="course-meta"><span>▣ {{ $item['lessons'] }}</span><span>▤ {{ $item['tag'] }}</span></div>
            <div class="price-row"><span class="price">{{ bdt(str_replace(',', '', $item['price'])) }}</span><span class="discount">Demo</span></div>
            <a class="btn" href="{{ route('courses.index') }}">View Courses</a>
          </div>
        </article>
      @endforeach
    @endforelse
  </div>
 </div>
</section>

<section class="section section-mint benefits">
 <div class="container"><h2>Why Choose Jerin's Coaching?</h2><p class="section-subtitle">We make Biology simple, interesting and easy to understand.</p>
  <div class="benefit-grid">
   <article class="benefit"><div class="benefit-icon">♧</div><h3>Expert Guidance</h3><p>Learn with clear guidance and supportive teaching.</p></article>
   <article class="benefit"><div class="benefit-icon">▤</div><h3>Structured Courses</h3><p>Well-organized topics and detailed study materials.</p></article>
   <article class="benefit"><div class="benefit-icon">▥</div><h3>Concept Clarity</h3><p>Easy explanations with relatable examples.</p></article>
   <article class="benefit"><div class="benefit-icon">♧</div><h3>Personalized Support</h3><p>Doubt clearing and individual attention.</p></article>
   <article class="benefit"><div class="benefit-icon">▤</div><h3>Regular Tests</h3><p>Practice tests to build confidence.</p></article>
   <article class="benefit"><div class="benefit-icon">🏆</div><h3>Progress Focus</h3><p>Keep learning and improving step by step.</p></article>
  </div>
 </div>
</section>

<section class="section" id="about">
 <div class="container about-grid">
  <div class="about-photo"><img src="{{ asset('images/biology-teacher-about.svg') }}" alt="Biology teaching at Jerin's Coaching"></div>
  <div class="about-copy"><div class="eyebrow">About Jerin's Coaching</div><h2 style="margin-top:12px">A Supportive Place to Learn Biology</h2><p>Founded by <strong>Dr. Tanjina Jerin</strong>, Jerin's Coaching is built around one simple idea: Biology should feel understandable, engaging and approachable. We focus on clear explanations, structured courses and supportive learning that helps students move forward with confidence.</p><a class="text-link" href="#footer">Discover Jerin's Coaching →</a><div class="about-stats"><div class="about-stat"><strong>Learn</strong><span>With confidence</span></div><div class="about-stat"><strong>Understand</strong><span>Core concepts</span></div><div class="about-stat"><strong>Progress</strong><span>Step by step</span></div></div></div>
  <aside class="quote-card"><div class="quote-mark">“</div><p>“My goal is to make Biology feel clear, engaging and less intimidating, so every student can learn with confidence.”</p><strong>Dr. Tanjina Jerin</strong><div style="font-size:11px;color:var(--muted)">Founder &amp; CEO · Jerin's Coaching</div></aside>
 </div>
</section>

<section class="section section-pink">
 <div class="container"><div class="center"><h2>What Our Students Say</h2><p class="section-subtitle">A welcoming learning experience, built around clarity and confidence.</p></div>
  <div class="testimonial-grid">
   <article class="testimonial"><div class="avatar" style="display:grid;place-items:center;background:#e5f1ff;font-size:25px">👩‍🎓</div><div><div class="stars">★★★★★</div><p>“The lessons helped me understand Biology topics more clearly.”</p><strong>Student review placeholder</strong><small>Replace with an approved student testimonial</small><span class="review-tag">Sample layout content</span></div></article>
   <article class="testimonial"><div class="avatar" style="display:grid;place-items:center;background:#e3f8ec;font-size:25px">👨‍🎓</div><div><div class="stars">★★★★★</div><p>“Structured learning and practice make revision easier.”</p><strong>Student review placeholder</strong><small>Replace with an approved student testimonial</small><span class="review-tag">Sample layout content</span></div></article>
   <article class="testimonial"><div class="avatar" style="display:grid;place-items:center;background:#fff0d8;font-size:25px">👩‍🎓</div><div><div class="stars">★★★★★</div><p>“A friendly way to build confidence in Biology.”</p><strong>Student review placeholder</strong><small>Replace with an approved student testimonial</small><span class="review-tag">Sample layout content</span></div></article>
  </div>
 </div>
</section>

<section class="section section-blue">
 <div class="container"><div class="center"><h2>How It Works</h2><p class="section-subtitle">Your path to confident Biology learning in four simple steps.</p></div>
  <div class="steps">
   <article class="step"><span class="step-number">1</span><span class="step-icon">♙</span><div><strong>Create an Account</strong><p>Sign up and complete your profile.</p></div></article>
   <article class="step"><span class="step-number">2</span><span class="step-icon">▤</span><div><strong>Choose a Course</strong><p>Select the course that suits your goal.</p></div></article>
   <article class="step"><span class="step-number">3</span><span class="step-icon">▣</span><div><strong>Make Payment</strong><p>Secure checkout in test mode.</p></div></article>
   <article class="step"><span class="step-number">4</span><span class="step-icon">▶</span><div><strong>Start Learning</strong><p>Access lessons and study resources.</p></div></article>
  </div>
 </div>
</section>

<section class="section section-mint" id="resources">
 <div class="container"><div class="center"><h2>Free Learning Resources</h2><p class="section-subtitle">Get a feel for our Biology learning materials before you begin.</p></div>
  <div class="resource-grid">
   <article class="resource"><div class="resource-icon">▤</div><div><h3>Sample Notes</h3><p>Review sample Biology notes.</p><a class="mini-btn" href="{{ route('courses.index') }}">Explore</a></div></article>
   <article class="resource"><div class="resource-icon">▶</div><div><h3>Sample Videos</h3><p>Preview a lesson format.</p><a class="mini-btn" href="{{ route('courses.index') }}">Watch Soon</a></div></article>
   <article class="resource"><div class="resource-icon">▧</div><div><h3>Practice Papers</h3><p>Prepare with practice questions.</p><a class="mini-btn" href="{{ route('courses.index') }}">Explore</a></div></article>
   <article class="resource"><div class="resource-icon">☷</div><div><h3>Sample Quiz</h3><p>Test your understanding.</p><a class="mini-btn" href="{{ route('courses.index') }}">Try Soon</a></div></article>
  </div>
 </div>
</section>
@endsection
