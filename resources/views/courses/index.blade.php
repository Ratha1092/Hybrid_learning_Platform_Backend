<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses — HybridLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <a href="{{ route('home') }}" class="font-semibold text-xl text-slate-900">HybridLearn</a>
                    <p class="text-sm text-slate-500 mt-1">Find published courses and start learning.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100">Sign In</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600">Get Started</a>
                </div>
            </div>
        </header>

        <main class="flex-1 mx-auto max-w-7xl px-4 py-10">
            <div class="mb-8">
                <h1 class="text-4xl font-semibold text-slate-900">Available Courses</h1>
                <p class="mt-3 text-slate-600 max-w-2xl">Browse the latest published courses we offer. Sign in or register to enroll and track your learning progress.</p>
            </div>

            @if($courses->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="text-slate-500">No courses are published yet. Please check back soon.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($courses as $course)
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">{{ $course->level ?? 'All levels' }}</span>
                                <span class="text-sm text-slate-500">{{ optional($course->category)->name ?? 'General' }}</span>
                            </div>
                            <h2 class="text-xl font-semibold text-slate-900">{{ $course->title }}</h2>
                            <p class="mt-3 text-slate-600 text-sm leading-6">{{ $course->short_description ?? 'A fresh course to expand your skills.' }}</p>
                            <div class="mt-6 flex items-center justify-between text-sm text-slate-600">
                                <span>{{ $course->duration ?? 'Self-paced' }}</span>
                                <span class="font-semibold text-slate-900">{{ $course->price ? '$'.number_format($course->price, 2) : 'Free' }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</body>
</html>
