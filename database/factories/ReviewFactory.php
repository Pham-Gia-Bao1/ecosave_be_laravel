<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        // Get users with role = 1 or create one if none exists
        $user = User::where('role', 1)->inRandomOrder()->first() 
            ?? User::factory()->create(['role' => 1]);
            
        return [
            'user_id' => $user->id,
            'product_id' => Product::inRandomOrder()->value('id'),
            'rating' => $this->faker->numberBetween(1, 5),
            'review_content' => $this->faker->randomElement([
                "Chất lượng khá ổn, hương vị dễ chịu, không quá ngọt hay quá mặn. Rất phù hợp cho bữa ăn hàng ngày.",
                
                "Sản phẩm có bao bì đẹp, dễ sử dụng. Mùi hương tự nhiên, không quá nồng, rất thích hợp để dùng lâu dài.",
                
                "Mình thấy giá cả hợp lý so với chất lượng. Dùng thử thấy khá hài lòng, chắc chắn sẽ mua lại lần sau.",
                
                "Hương vị không quá đặc biệt nhưng vẫn đủ ngon. Phù hợp cho những ai thích sự đơn giản và tiện lợi.",
                
                "Kết cấu khá ổn, không bị vỡ hay nát trong quá trình vận chuyển. Nếu bảo quản tốt thì có thể dùng được lâu.",
                
                "Dùng cũng được, không quá xuất sắc nhưng đáp ứng được nhu cầu cơ bản. Nếu cải thiện một chút thì sẽ hoàn hảo hơn.",
                
                "Mình thích vì dễ bảo quản, không mất quá nhiều công đoạn chuẩn bị. Hương vị ổn định, không bị thay đổi giữa các lần mua.",
                
                "Khá tiện lợi cho những người bận rộn, chỉ cần một vài bước là có thể sử dụng ngay. Chất lượng ổn, không có gì để phàn nàn.",
                
                "Hài lòng với chất lượng, đúng như mô tả. Nếu có thêm nhiều lựa chọn hơn thì sẽ càng tốt.",
                
                "Sản phẩm tốt, nhưng có thể cần điều chỉnh một chút để phù hợp hơn với khẩu vị của từng người. Nhìn chung thì đáng để thử."
            ]),
            'image_url' => $this->faker->optional(0.3)->imageUrl(), 
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}