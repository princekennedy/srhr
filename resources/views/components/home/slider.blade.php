@php
  $slides = collect();

  if (\Illuminate\Support\Facades\Schema::hasTable('sliders')) {
    $slides = \App\Models\Slider::query()
      ->where('is_active', true)
      ->orderBy('sort_order')
      ->get()
      ->map(function (\App\Models\Slider $slide): array {
        return [
          'image' => $slide->imageUrl() ?: asset('seed/hero-slide-1.svg'),
          'kicker' => $slide->kicker,
          'title' => $slide->title,
          'desc' => $slide->caption,
          'buttons' => collect([
            filled($slide->primary_button_text) ? ['text' => $slide->primary_button_text, 'link' => $slide->primary_button_link ?: '#', 'class' => 'bg-indigo-600 hover:bg-indigo-700'] : null,
            filled($slide->secondary_button_text) ? ['text' => $slide->secondary_button_text, 'link' => $slide->secondary_button_link ?: '#', 'class' => 'border border-white/40 hover:bg-white/10'] : null,
          ])->filter()->values()->all(),
        ];
      })
      ->values();
  }

  if ($slides->isEmpty()) {
    $slides = collect([
      [
        'image' => asset('seed/hero-slide-1.svg'),
        'kicker' => 'Modern digital experiences',
        'title' => 'Build a beautiful online presence that grows your brand',
        'desc' => 'Launch faster with a clean landing page, elegant navigation, and a polished image slider that makes your business stand out.',
        'buttons' => [
          ['text' => 'Start Project', 'link' => '#', 'class' => 'bg-indigo-600 hover:bg-indigo-700'],
          ['text' => 'Explore Features', 'link' => '#features', 'class' => 'border border-white/40 hover:bg-white/10']
        ]
      ],
      [
        'image' => asset('seed/hero-slide-2.svg'),
        'kicker' => 'Creative and responsive',
        'title' => 'Design that looks premium on every screen',
        'desc' => 'Use Tailwind CSS to create responsive layouts, dropdown menus, and eye-catching sections with minimal effort.',
        'buttons' => [
          ['text' => 'View Demo', 'link' => '#', 'class' => 'bg-indigo-600 hover:bg-indigo-700'],
          ['text' => 'Talk to Us', 'link' => '#contact', 'class' => 'border border-white/40 hover:bg-white/10']
        ]
      ],
      [
        'image' => asset('seed/hero-slide-3.svg'),
        'kicker' => 'Simple. Elegant. Effective.',
        'title' => 'Showcase your services with confidence',
        'desc' => 'Present your products, services, and value clearly with a page structure that is clean, modern, and conversion-focused.',
        'buttons' => [
          ['text' => 'Get Quote', 'link' => '#', 'class' => 'bg-indigo-600 hover:bg-indigo-700'],
          ['text' => 'Learn More', 'link' => '#features', 'class' => 'border border-white/40 hover:bg-white/10']
        ]
      ],
    ]);
  }
@endphp
<section id="home" class="relative overflow-hidden">
  <div class="relative h-[85vh] min-h-[560px] w-full">
    @foreach ($slides as $index => $slide)
    <div class="slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
      <img src="{{ $slide['image'] }}" class="h-full w-full object-cover" alt="{{ $slide['title'] ?: 'Slide '.($index + 1) }}" />
      <div class="absolute inset-0 bg-slate-900/55"></div>
      <div class="absolute inset-0 flex items-center">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
          <div class="max-w-2xl text-white">
            <span class="inline-flex rounded-full bg-white/15 px-4 py-1 text-sm backdrop-blur">{{ $slide['kicker'] }}</span>
            <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">{{ $slide['title'] }}</h1>
            <p class="mt-6 text-lg text-slate-200">{{ $slide['desc'] }}</p>
            <div class="mt-8 flex flex-wrap gap-4">
              @foreach ($slide['buttons'] as $btn)
              <a href="{{ $btn['link'] }}" class="rounded-full px-6 py-3 font-semibold text-white {{ $btn['class'] }}">{{ $btn['text'] }}</a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

    <button id="prevBtn" class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❮</button>
    <button id="nextBtn" class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❯</button>

    <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-3">
      @foreach ($slides as $index => $slide)
        <button class="dot h-3 w-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"></button>
      @endforeach
    </div>
  </div>

  <script>
    (() => {
      const container = document.currentScript.closest('section');

      if (!container) {
        return;
      }

      const slides = container.querySelectorAll('.slide');
      const dots = container.querySelectorAll('.dot');
      const prevBtn = container.querySelector('#prevBtn');
      const nextBtn = container.querySelector('#nextBtn');
      let current = 0;

      const showSlide = (index) => {
        slides.forEach((slide, slideIndex) => {
          slide.classList.toggle('opacity-100', slideIndex === index);
          slide.classList.toggle('opacity-0', slideIndex !== index);
          dots[slideIndex].classList.toggle('bg-white', slideIndex === index);
          dots[slideIndex].classList.toggle('bg-white/50', slideIndex !== index);
        });

        current = index;
      };

      const nextSlide = () => showSlide((current + 1) % slides.length);
      const prevSlide = () => showSlide((current - 1 + slides.length) % slides.length);

      if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
      }

      dots.forEach((dot, index) => {
        dot.addEventListener('click', () => showSlide(index));
      });

      if (slides.length > 0) {
        setInterval(nextSlide, 5000);
      }
    })();
  </script>
</section>