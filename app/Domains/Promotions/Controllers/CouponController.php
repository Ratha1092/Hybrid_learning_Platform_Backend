<?php

namespace App\Domains\Promotions\Controllers;

use App\Domains\Courses\Models\Course;
use App\Domains\Promotions\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $course = Course::where('id', $validated['course_id'])
            ->where('is_published', true)
            ->firstOrFail();

        $coupon = Coupon::where('code', strtoupper(trim($validated['code'])))->first();

        if (!$coupon) {
            return ApiResponse::error('Invalid coupon code', 422);
        }

        $error = $coupon->validateFor($request->user(), (float) $course->price);

        if ($error) {
            return ApiResponse::error($error, 422);
        }

        $discount = $coupon->calculateDiscount((float) $course->price);

        return ApiResponse::success([
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => $discount,
            'final_amount' => round((float) $course->price - $discount, 2),
        ], 'Coupon is valid');
    }
}
