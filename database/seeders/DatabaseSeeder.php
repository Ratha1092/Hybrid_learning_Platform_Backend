<?php

namespace Database\Seeders;

use App\Domains\Analytics\Models\DailyMetric;
use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Models\Tag;
use App\Domains\Courses\Resources\LessonResource;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Models\Transaction;
use App\Domains\Users\Models\InstructorProfile;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Users\Models\StudentProfile;
use App\Domains\Users\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faker = Faker::create();

        Role::findOrCreate('admin');
        Role::findOrCreate('instructor');
        Role::findOrCreate('student');

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'instructor_status' => User::INSTRUCTOR_NONE,
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $admin->syncRoles(['admin']);

        $testUser = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'role' => 'student',
            'instructor_status' => User::INSTRUCTOR_NONE,
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $testUser->syncRoles(['student']);

        $instructors = collect();
        for ($i = 1; $i <= 10; $i++) {
            $instructor = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'instructor_status' => User::INSTRUCTOR_VERIFIED,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);

            $instructor->assignRole('instructor');

            InstructorProfile::create([
                'user_id' => $instructor->id,
                'bio' => $faker->paragraph(),
                'avatar' => null,
                'website' => $faker->optional()->url(),
                'twitter' => $faker->optional()->userName(),
                'linkedin' => $faker->optional()->url(),
                'youtube' => $faker->optional()->url(),
            ]);

            InstructorWallet::create([
                'instructor_id' => $instructor->id,
                'balance' => 0,
                'pending_balance' => 0,
                'currency' => 'USD',
            ]);

            $instructors->push($instructor);
        }

        $students = collect();
        for ($i = 1; $i <= 100; $i++) {
            $student = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'student',
                'instructor_status' => User::INSTRUCTOR_NONE,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => $faker->optional(0.9)->dateTimeThisYear(),
            ]);

            $student->assignRole('student');

            StudentProfile::create([
                'user_id' => $student->id,
                'bio' => $faker->paragraph(),
                'avatar' => null,
                'learning_goals' => $faker->sentences(2, true),
                'interests' => $faker->randomElements(['coding', 'design', 'business', 'marketing', 'finance', 'photography'], rand(1, 4)),
                'github' => $faker->optional()->url(),
                'linkedin' => $faker->optional()->url(),
            ]);

            $students->push($student);
        }

        $categories = collect();
        $categoryNames = [
            'Business',
            'Development',
            'Design',
            'Marketing',
            'Finance',
            'Health & Wellness',
            'Photography',
            'Personal Growth',
        ];

        foreach ($categoryNames as $name) {
            $categories->push(Category::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
            ]));
        }

        $tags = collect();
        for ($i = 1; $i <= 30; $i++) {
            $name = ucfirst($faker->unique()->word());
            $tags->push(Tag::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
            ]));
        }

        $courses = collect();
        for ($i = 1; $i <= 20; $i++) {
            $title = $faker->unique()->sentence(5);
            $instructor = $instructors->random();
            $category = $categories->random();
            $status = $faker->randomElement([
                Course::STATUS_PUBLISHED,
                Course::STATUS_PENDING,
                Course::STATUS_DRAFT,
            ]);

            $course = Course::create([
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $i,
                'short_description' => $faker->sentence(12),
                'description' => $faker->paragraphs(rand(3, 5), true),
                'thumbnail' => null,
                'preview_video_url' => $faker->optional(0.5)->url(),
                'price' => $faker->randomFloat(2, 0, 200),
                'level' => $faker->randomElement(['beginner', 'intermediate', 'advanced']),
                'language' => $faker->randomElement(['English', 'Spanish', 'French', 'German']),
                'duration' => $faker->numberBetween(30, 240),
                'requirements' => $faker->sentences(2, true),
                'what_you_will_learn' => $faker->sentences(3, true),
                'status' => $status,
                'visibility' => $faker->randomElement(['public', 'private']),
                'certificate_enabled' => $faker->boolean(50),
                'is_published' => $status === Course::STATUS_PUBLISHED,
                'approved_at' => $status === Course::STATUS_PUBLISHED ? now() : null,
                'approved_by' => $status === Course::STATUS_PUBLISHED ? $admin->id : null,
                'commission_percentage' => $faker->randomFloat(2, 10, 30),
            ]);

            $course->tags()->attach($tags->random(rand(2, 5))->pluck('id')->toArray());
            $courses->push($course);
        }

        $sections = collect();
        $lessons = collect();
        foreach ($courses as $course) {
            $sectionCount = rand(2, 4);
            for ($sectionIndex = 1; $sectionIndex <= $sectionCount; $sectionIndex++) {
                $section = Section::create([
                    'course_id' => $course->id,
                    'title' => $faker->sentence(3),
                    'order' => $sectionIndex,
                ]);

                $sections->push($section);
                $lessonCount = rand(2, 5);
                for ($lessonIndex = 1; $lessonIndex <= $lessonCount; $lessonIndex++) {
                    $lessonType = $faker->randomElement(['video', 'article', 'quiz']);
                    $lesson = Lesson::create([
                        'section_id' => $section->id,
                        'title' => $faker->sentence(6),
                        'content' => $faker->paragraphs(rand(1, 4), true),
                        'type' => $lessonType,
                        'video_url' => $lessonType === 'video' ? 'https://www.youtube.com/watch?v=' . Str::random(11) : null,
                        'video_path' => null,
                        'video_provider' => $lessonType === 'video' ? $faker->randomElement(['youtube', 'vimeo']) : null,
                        'attachment' => $faker->optional(0.2)->randomElement([$faker->word() . '.pdf', $faker->word() . '.zip']),
                        'attachment_name' => $faker->optional(0.2)->word() . '.pdf',
                        'quiz_data' => $lessonType === 'quiz' ? ['questions' => [
                            ['question' => $faker->sentence(), 'options' => $faker->words(4), 'answer' => 0],
                        ]] : null,
                        'duration' => $faker->numberBetween(5, 45),
                        'is_preview' => $lessonIndex === 1,
                        'order' => $lessonIndex,
                    ]);

                    $lessons->push($lesson);

                    if ($faker->boolean(40)) {
                        LessonResource::create([
                            'lesson_id' => $lesson->id,
                            'title' => $faker->sentence(4),
                            'type' => $faker->randomElement(['pdf', 'zip', 'doc']),
                            'file_path' => 'resources/' . $faker->slug() . '.' . $faker->fileExtension,
                        ]);
                    }
                }
            }
        }

        $orders = collect();
        for ($i = 1; $i <= 40; $i++) {
            $student = $students->random();
            $enrolledCourseIds = Enrollment::where('user_id', $student->id)->pluck('course_id')->toArray();
            $availableCourses = $courses->whereNotIn('id', $enrolledCourseIds)->values();

            if ($availableCourses->isEmpty()) {
                continue;
            }

            $courseSelection = $availableCourses->shuffle()->take(rand(1, min(3, $availableCourses->count())));
            $orderTotal = 0;
            $items = [];

            foreach ($courseSelection as $course) {
                $price = $course->price > 0 ? $course->price : $faker->randomFloat(2, 10, 150);
                $discount = $faker->boolean(30) ? round($price * $faker->randomFloat(2, 0.05, 0.20), 2) : 0;
                $finalAmount = round($price - $discount, 2);
                $orderTotal += $finalAmount;

                $items[] = [
                    'course' => $course,
                    'price' => $price,
                    'discount' => $discount,
                    'final' => $finalAmount,
                ];
            }

            $status = $faker->randomElement([
                OrderStatus::Completed,
                OrderStatus::Pending,
                OrderStatus::Cancelled,
                OrderStatus::Refunded,
            ]);
            $paymentStatus = $status === OrderStatus::Completed ? OrderPaymentStatus::Paid : ($status === OrderStatus::Refunded ? OrderPaymentStatus::Refunded : $faker->randomElement([OrderPaymentStatus::Pending, OrderPaymentStatus::Failed]));

            $order = Order::create([
                'order_number' => strtoupper(Str::random(10)),
                'user_id' => $student->id,
                'total_amount' => $orderTotal,
                'discount_amount' => 0,
                'final_amount' => $orderTotal,
                'currency' => 'USD',
                'status' => $status->value,
                'payment_status' => $paymentStatus->value,
                'payment_method' => $faker->randomElement(['khqr', 'stripe', 'paypal', 'aba']),
                'customer_name' => $student->name,
                'customer_email' => $student->email,
                'paid_at' => $paymentStatus === OrderPaymentStatus::Paid ? now() : null,
                'cancelled_at' => $status === OrderStatus::Cancelled ? now() : null,
                'refunded_at' => $status === OrderStatus::Refunded ? now() : null,
            ]);

            foreach ($items as $itemData) {
                $course = $itemData['course'];
                $commissionRate = $course->commission_percentage;
                $instructorAmount = round($itemData['final'] * (100 - $commissionRate) / 100, 2);
                $platformAmount = round($itemData['final'] - $instructorAmount, 2);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'instructor_id' => $course->instructor_id,
                    'course_title' => $course->title,
                    'price' => $itemData['price'],
                    'discount_amount' => $itemData['discount'],
                    'final_amount' => $itemData['final'],
                    'commission_percentage' => $commissionRate,
                    'instructor_amount' => $instructorAmount,
                    'platform_amount' => $platformAmount,
                    'is_refunded' => $status === OrderStatus::Refunded,
                    'refunded_at' => $status === OrderStatus::Refunded ? now() : null,
                ]);

                if ($status === OrderStatus::Completed) {
                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'order_id' => $order->id,
                        'source' => 'purchase',
                        'status' => 'active',
                        'progress_percentage' => 0,
                        'expires_at' => now()->addMonths(12),
                        'certificate_issued' => false,
                        'enrolled_at' => now(),
                        'completed_at' => null,
                        'last_accessed_at' => now(),
                    ]);
                }

                $revenueShare = RevenueShare::create([
                    'order_item_id' => $orderItem->id,
                    'instructor_id' => $course->instructor_id,
                    'total_amount' => $orderItem->final_amount,
                    'platform_amount' => $orderItem->platform_amount,
                    'instructor_amount' => $orderItem->instructor_amount,
                    'commission_percentage' => $orderItem->commission_percentage,
                    'status' => $status === OrderStatus::Completed ? 'distributed' : 'pending',
                ]);

                $wallet = InstructorWallet::firstOrCreate([
                    'instructor_id' => $course->instructor_id,
                ], [
                    'balance' => 0,
                    'pending_balance' => 0,
                    'currency' => 'USD',
                ]);

                if ($revenueShare->status === 'distributed') {
                    $wallet->increment('balance', $revenueShare->instructor_amount);
                } else {
                    $wallet->increment('pending_balance', $revenueShare->instructor_amount);
                }

                WalletTransaction::create([
                    'instructor_id' => $course->instructor_id,
                    'amount' => $revenueShare->instructor_amount,
                    'type' => $status === OrderStatus::Refunded ? 'debit' : 'credit',
                    'status' => 'completed',
                    'revenue_share_id' => $revenueShare->id,
                    'payout_request_id' => null,
                    'reference_id' => Str::uuid(),
                    'description' => 'Revenue for order ' . $order->order_number,
                ]);

                if ($status === OrderStatus::Completed && $faker->boolean(40)) {
                    Review::create([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'rating' => rand(3, 5),
                        'comment' => $faker->sentences(2, true),
                    ]);
                }
            }

            if ($paymentStatus === OrderPaymentStatus::Paid) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'payment_gateway' => $faker->randomElement([PaymentGateway::Stripe->value, PaymentGateway::Paypal->value, PaymentGateway::Bakong->value, PaymentGateway::Khqr->value]),
                    'transaction_id' => Str::uuid(),
                    'external_reference' => Str::uuid(),
                    'idempotency_key' => Str::uuid(),
                    'amount' => $order->final_amount,
                    'currency' => 'USD',
                    'status' => PaymentStatus::Paid->value,
                    'verification_attempts' => rand(0, 2),
                    'failure_reason' => null,
                    'khqr_payload' => null,
                    'payer_account' => null,
                    'meta' => ['source' => 'seed'],
                    'gateway_response' => ['status' => 'success'],
                    'paid_at' => now(),
                    'last_verified_at' => now(),
                    'expires_at' => now()->addDays(7),
                ]);

                Transaction::create([
                    'payment_id' => $payment->id,
                    'gateway' => $payment->payment_gateway,
                    'event_type' => 'payment.completed',
                    'status' => 'success',
                    'payload' => ['amount' => $payment->amount],
                ]);
            }

            $orders->push($order);
        }

        foreach ($students as $student) {
            $wishlisted = $courses->shuffle()->take(rand(1, 3));
            foreach ($wishlisted as $course) {
                if (Wishlist::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {
                    continue;
                }

                Wishlist::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                ]);
            }
        }

        foreach (Enrollment::cursor() as $enrollment) {
            $courseLessons = Lesson::whereHas('section', function ($query) use ($enrollment) {
                $query->where('course_id', $enrollment->course_id);
            })->get();

            foreach ($courseLessons->random(min(5, $courseLessons->count())) as $lesson) {
                LessonProgress::firstOrCreate([
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'lesson_id' => $lesson->id,
                ], [
                    'is_completed' => $faker->boolean(30),
                    'watch_time' => $faker->numberBetween(0, 1800),
                    'duration' => $lesson->duration ?? $faker->numberBetween(5, 45),
                    'last_position' => $faker->numberBetween(0, 1200),
                    'progress_percentage' => $faker->randomFloat(2, 0, 100),
                    'completed_at' => $faker->boolean(30) ? now()->subDays(rand(0, 10)) : null,
                    'last_watched_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        }

        $approvedInstructors = $instructors->shuffle()->take(4);
        foreach ($approvedInstructors as $instructor) {
            PayoutRequest::create([
                'instructor_id' => $instructor->id,
                'amount' => $faker->randomFloat(2, 50, 400),
                'currency' => 'USD',
                'payment_method' => $faker->randomElement(['bank_transfer', 'paypal', 'mobile_wallet']),
                'details' => ['account' => $faker->iban()],
                'status' => $faker->randomElement(['pending', 'processed', 'rejected']),
                'processed_at' => $faker->boolean(50) ? now()->subDays(rand(0, 7)) : null,
                'processed_by' => $faker->boolean(50) ? $admin->id : null,
                'rejection_reason' => null,
            ]);
        }

        for ($days = 0; $days < 14; $days++) {
            DailyMetric::create([
                'date' => now()->subDays($days)->toDateString(),
                'total_users' => max(0, User::count() - $days),
                'new_users' => rand(1, 8),
                'total_orders' => max(0, Order::count() - rand(0, 2) * $days),
                'total_revenue' => round(Order::sum('final_amount') * $faker->randomFloat(2, 0.01, 0.04), 2),
                'total_enrollments' => Enrollment::count(),
            ]);
        }
    }
}
