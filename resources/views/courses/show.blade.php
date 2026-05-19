<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — HybridLearn</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <a href="{{ route('home') }}" class="font-semibold text-xl text-slate-900">HybridLearn</a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100">All Courses</a>
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600">Sign In</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-10">
            <div class="bg-white rounded-3xl shadow-sm p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="text-sm text-slate-500">{{ $course->category?->name ?? 'General' }}</div>
                        <h1 class="text-4xl font-semibold text-slate-900 mt-2">{{ $course->title }}</h1>
                        <p class="mt-4 text-slate-600">{{ $course->short_description ?? 'A premium course to help you grow your skills.' }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 px-6 py-4 text-right">
                        <div class="text-sm text-slate-500">Price</div>
                        <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $course->price == 0 ? 'Free' : '$'.number_format($course->price, 2) }}</div>
                    </div>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm text-slate-500">Instructor</div>
                        <div class="mt-2 font-semibold text-slate-900">{{ $course->instructor?->name ?? 'Instructor' }}</div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm text-slate-500">Students</div>
                        <div class="mt-2 font-semibold text-slate-900">{{ number_format($course->enrollments_count) }}</div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm text-slate-500">Rating</div>
                        <div class="mt-2 font-semibold text-slate-900">{{ number_format($course->reviews_avg_rating ?? 0, 1) }} / 5</div>
                    </div>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <h2 class="text-xl font-semibold text-slate-900">What you'll learn</h2>
                        <p class="mt-3 text-slate-600">{{ $course->description ?? 'This course covers key concepts and practical examples to help you succeed.' }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <h2 class="text-xl font-semibold text-slate-900">Course details</h2>
                        <ul class="mt-4 space-y-3 text-slate-600">
                            <li><strong>Level:</strong> {{ ucfirst($course->level ?? 'All levels') }}</li>
                            <li><strong>Language:</strong> {{ $course->language ?? 'English' }}</li>
                            <li><strong>Duration:</strong> {{ $course->duration ?? 'Self-paced' }}</li>
                            <li><strong>Category:</strong> {{ $course->category?->name ?? 'General' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
