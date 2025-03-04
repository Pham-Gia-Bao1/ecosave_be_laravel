<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB; 
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'store_name' => "Lotte Mart Đà Nẵng",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuwJveohkd40gaomjYSyhSFGiOQKUNZBzRaQ&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSS6FZ8YeS13yRIsUir3I9V8ap47iKU8WR7QA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' => "Lottedn@gmail.com",
                'contact_phone' => "0958494223",
                'latitude' => '16.0685', 
                'longitude' => '108.2093', 
                'soft_description' => "Lotte Mart - Siêu thị đa quốc gia với đa dạng sản phẩm từ thực phẩm tươi sống đến đồ gia dụng, thời trang và điện tử.",
                'description' => "Lotte Mart là một trong những chuỗi siêu thị lớn nhất đến từ Hàn Quốc, mang đến trải nghiệm mua sắm hiện đại và tiện lợi. Với không gian rộng rãi, sản phẩm đa dạng từ thực phẩm tươi sống, đồ gia dụng, thời trang đến điện tử, Lotte Mart đáp ứng mọi nhu cầu của khách hàng. Đặc biệt, chúng tôi luôn có các chương trình khuyến mãi hấp dẫn và dịch vụ chăm sóc khách hàng tận tâm. Hãy đến và trải nghiệm sự khác biệt tại Lotte Mart!",
                'address' => '255 Hùng Vương, Vĩnh Trung, Thanh Khê',
                'user_id' => 4,
            ],
            [
                'store_name' => "WinMart Nguyễn Tất Thành",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjBQbMAeKHY-sDradaglax-7TkPI-iDphFwQ&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdtdTbVG8flKakpYEjtBLb9FyTiwYD1odnzg&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "wimartntt@gmail.com",
                'contact_phone' => "0958494223",
                'latitude' => '16.0605', 
                'longitude' => '108.2180',  
                'soft_description' => "WinMart - Siêu thị tiện lợi với giá cả phải chăng, đa dạng sản phẩm và dịch vụ thân thiện.",
                'description' => "WinMart là điểm đến lý tưởng cho những ai yêu thích sự tiện lợi và tiết kiệm. Với hệ thống siêu thị rộng khắp, WinMart mang đến cho khách hàng những sản phẩm chất lượng với giá cả phải chăng. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, WinMart luôn đáp ứng mọi nhu cầu hàng ngày của bạn. Đặc biệt, chúng tôi thường xuyên có các chương trình khuyến mãi hấp dẫn, giúp bạn tiết kiệm hơn khi mua sắm. Hãy ghé thăm WinMart để tận hưởng trải nghiệm mua sắm tuyệt vời!",
                'address' => '180 Nguyễn Tất Thành, Hải Châu, Đà Nẵng 550000, Vietnam',
                'user_id' => 5,
            ],
            [
                'store_name' => "Co.opMart Nguyễn Văn Linh",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS253RMfl7KTPpoyDX2G2KLtO6Zw_Z4B_7fAg&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "co.opmartnvl@gmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0612', 
                'longitude' => '108.2145',  
                'soft_description' => "Co.opMart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Co.opMart tự hào là một trong những chuỗi siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '02 Nguyễn Văn Linh, Hải Châu, Đà Nẵng 550000, Vietnam',
                'user_id' => 6,
            ],
            [
                'store_name' => "Siêu thị Go Đà Nẵng",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTHAJoYKwa5HX6r7WmTlsdNBJAdbCo3ipP6Dw&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJgUHDyoDDO7ze2B86n3NAfxxJWNJFOTaqyg&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "go@gmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0680', 
                'longitude' => '108.2090',   
                'soft_description' => "GO! - Siêu thị tiện ích với đa dạng sản phẩm, giá cả hợp lý và dịch vụ thân thiện.",
                'description' => "GO! là chuỗi siêu thị tiện ích hàng đầu tại Việt Nam, mang đến cho khách hàng trải nghiệm mua sắm hiện đại và tiện lợi. Với hệ thống sản phẩm đa dạng từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, GO! đáp ứng mọi nhu cầu hàng ngày của gia đình bạn. Chúng tôi cam kết mang đến những sản phẩm chất lượng cao với giá cả hợp lý, cùng các chương trình khuyến mãi hấp dẫn. Đặc biệt, GO! luôn chú trọng đến dịch vụ khách hàng thân thiện và không gian mua sắm thoải mái. Hãy đến GO! để tận hưởng trải nghiệm mua sắm tuyệt vời và tiết kiệm!",
                'address' => '255-257 Hùng Vương, Vĩnh Trung, Đà Nẵng 550000, Vietnam',
                'user_id' => 7,
            ],
            [
                'store_name' => "WinMart Nguyễn Hữu Thọ",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjBQbMAeKHY-sDradaglax-7TkPI-iDphFwQ&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdtdTbVG8flKakpYEjtBLb9FyTiwYD1odnzg&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "winmartnht@gmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0600', 
                'longitude' => '108.2150',    
                'soft_description' => "WinMart - Siêu thị tiện lợi với giá cả phải chăng, đa dạng sản phẩm và dịch vụ thân thiện.",
                'description' => "WinMart là điểm đến lý tưởng cho những ai yêu thích sự tiện lợi và tiết kiệm. Với hệ thống siêu thị rộng khắp, WinMart mang đến cho khách hàng những sản phẩm chất lượng với giá cả phải chăng. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, WinMart luôn đáp ứng mọi nhu cầu hàng ngày của bạn. Đặc biệt, chúng tôi thường xuyên có các chương trình khuyến mãi hấp dẫn, giúp bạn tiết kiệm hơn khi mua sắm. Hãy ghé thăm WinMart để tận hưởng trải nghiệm mua sắm tuyệt vời!",
                'address' => '123 Nguyễn Hữu Thọ, Hòa Thuận Tây',
                'user_id' => 8,
            ],
            [
                'store_name' => "Co.opMart Lê Đình Dương",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS253RMfl7KTPpoyDX2G2KLtO6Zw_Z4B_7fAg&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "coopmartldd@gmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0620', 
                'longitude' => '108.2130',   
                'soft_description' => "Co.opMart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Co.opMart tự hào là một trong những chuỗi siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '89 Lê Đình Dương, Hải Châu, Đà Nẵng',
                'user_id' => 9,
            ],
            [
                'store_name' => "Co.opMart Lê Duẩn",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS253RMfl7KTPpoyDX2G2KLtO6Zw_Z4B_7fAg&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "coopmartld@gmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0605', 
                'longitude' => '108.2110',   
                'soft_description' => "Co.opMart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Co.opMart tự hào là một trong những chuỗi siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '15 Lê Duẩn, Hải Châu',
                'user_id' => 10,
            ],
            [
                'store_name' => "Co.opMart Nguyễn Chánh",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS253RMfl7KTPpoyDX2G2KLtO6Zw_Z4B_7fAg&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "coopmartncgmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0635', 
                'longitude' => '108.2190',   
                'soft_description' => "Co.opMart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Co.opMart tự hào là một trong những chuỗi siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '150 Nguyễn Chánh, Sơn Trà',
                'user_id' => 11,
            ],
            [
                'store_name' => "Otis Mart",
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "otismartgmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0640', 
                'longitude' => '108.2210',   
                'soft_description' => "Otis Mart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Otis Mart tự hào là một trong những siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '100 Nguyễn Văn Thoại, Sơn Trà',
                'user_id' => 12,
            ],
            [
                'store_name' => "Co.opMart Lê Đại Hành",
                'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS253RMfl7KTPpoyDX2G2KLtO6Zw_Z4B_7fAg&s",//ảnh bìa
                'logo' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVu-wvR42zx77ivThLjN6Tz3lyJYT369pGbA&s",
                'store_type' => 'Siêu thị',
                'opening_hours' => '9:00 AM - 9:00 PM',
                'status' => 'active',
                'contact_email' =>  "coopmartldhgmail.com",
                'contact_phone' => '0958494223',
                'latitude' => '16.0660', 
                'longitude' => '108.2080',  
                'soft_description' => "Co.opMart - Siêu thị uy tín với sản phẩm chất lượng, giá cả hợp lý và dịch vụ chuyên nghiệp.",
                'description' => "Co.opMart tự hào là một trong những chuỗi siêu thị hàng đầu tại Việt Nam, mang đến cho khách hàng những sản phẩm chất lượng với giá cả hợp lý. Với phương châm 'Vì lợi ích cộng đồng', Co.opMart luôn chú trọng đến việc cung cấp các sản phẩm tươi ngon, an toàn và thân thiện với môi trường. Từ thực phẩm tươi sống, đồ khô, đồ gia dụng đến các sản phẩm chăm sóc sức khỏe, Co.opMart đáp ứng mọi nhu cầu của gia đình bạn. Hãy đến và trải nghiệm dịch vụ mua sắm chuyên nghiệp tại Co.opMart!",
                'address' => '150 Nguyễn Chánh, Sơn Trà',
                'user_id' => 13,
            ],
        ];

        DB::table('stores')->insert($data);
    }
}
