<?php

namespace Tests\Feature\Notifications;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\LessonComment;
use App\Domains\Notifications\Notifications\CommentReplyNotification;
use App\Domains\Notifications\Notifications\CourseApprovedNotification;
use App\Domains\Notifications\Notifications\CourseCompletionNotification;
use App\Domains\Notifications\Notifications\CourseRejectedNotification;
use App\Domains\Notifications\Notifications\EnrollmentConfirmedNotification;
use App\Domains\Notifications\Notifications\InstructorApprovedNotification;
use App\Domains\Notifications\Notifications\MonthlyPayoutGeneratedNotification;
use App\Domains\Notifications\Notifications\PayoutApprovedNotification;
use App\Domains\Notifications\Notifications\PayoutRejectedNotification;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use Tests\TestCase;

class NotificationRedirectsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.frontend_url', 'https://frontend.example.com');
        config()->set('app.url', 'https://admin.example.com');
    }

    public function test_payout_notifications_point_to_frontend_routes(): void
    {
        $approved = new PayoutApprovedNotification(42, 125.50, 'USD', 99);
        $this->assertSame(
            'https://frontend.example.com/instructor/finance/payouts/42',
            $approved->toArray(new \stdClass())['link']
        );

        $rejected = new PayoutRejectedNotification(43, 89.00, 'USD', 100);
        $this->assertSame(
            'https://frontend.example.com/instructor/finance/payouts/43',
            $rejected->toArray(new \stdClass())['link']
        );

        $monthly = new MonthlyPayoutGeneratedNotification(44, 500.00, 'USD');
        $this->assertSame(
            'https://frontend.example.com/instructor/finance/payouts/44',
            $monthly->toArray(new \stdClass())['link']
        );
    }

    public function test_user_notifications_point_to_frontend_routes(): void
    {
        $course = new Course([
            'id' => 7,
            'title' => 'Testing Course',
            'slug' => 'testing-course',
        ]);

        $this->assertSame(
            'https://frontend.example.com/courses/testing-course',
            (new CourseApprovedNotification($course))->toArray(new \stdClass())['link']
        );

        $this->assertSame(
            'https://frontend.example.com/instructor/courses',
            (new CourseRejectedNotification($course, 'Needs work'))->toArray(new \stdClass())['link']
        );

        $this->assertSame(
            'https://frontend.example.com/library',
            (new CourseCompletionNotification(7, 'Testing Course'))->toArray(new \stdClass())['link']
        );

        $order = new Order(['id' => 11]);
        $this->assertSame(
            'https://frontend.example.com/library',
            (new EnrollmentConfirmedNotification($order, 'Testing Course'))->toArray(new \stdClass())['link']
        );

        $this->assertSame(
            'https://frontend.example.com/instructor/dashboard',
            (new InstructorApprovedNotification())->toArray(new \stdClass())['link']
        );

        $user = new User(['name' => 'Test User']);
        $this->assertSame(
            'https://frontend.example.com/profile',
            (new \App\Domains\Notifications\Notifications\RoleChangedNotification(['student']))->toArray($user)['link']
        );

        $comment = new LessonComment([
            'id' => 5,
            'parent_id' => 4,
            'lesson_id' => 9,
            'user_id' => 2,
        ]);
        $comment->setRelation('user', new User(['name' => 'Jane']));

        $this->assertSame(
            'https://frontend.example.com/learn/testing-course?lesson=9&comment=4',
            (new CommentReplyNotification($comment, 'Intro', 'testing-course'))->toArray(new \stdClass())['link']
        );
    }
}
