<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\Product;

class ImageSeeder extends Seeder
{
    public function run()
    {
        $images = [
            [
                'product_id' => 1,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/2/2/2240410000001-1.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 1,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/2/0/2097770000000-1.jpg.webp',
                'image_order' => 2,
            ],     
            [
                'product_id' => 2,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/12/10281591-20241205101653.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 2,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/08/ba-chi-kho-tau-hinh-fb-20230810044741.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 2,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/08/thit-nuong-la-moc-mat-hinh-fb-20230810044741.jpg',
                'image_order' => 3,
            ],
            [
                'product_id' => 3,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/12/10617963-20241205102004.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 3,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/12/10617963-20241205102004.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 3,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/08/sup-thit-heo-xay-hinh-fb-20230810043837.jpg',
                'image_order' => 3,
            ],
            [
                'product_id' => 4,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/2/0/2082590000002-1-bb.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 4,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/2/0/2082590000002-2.jpg.webp',
                'image_order' => 2,
            ],
            [
                'product_id' => 4,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/2/0/2082590000002-3.jpg.webp',
                'image_order' => 3,
            ],
            [
                'product_id' => 5,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/8782/309115/bhx/ca-hoi-cat-khuc-ngon-202312251424281891.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 6,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/8782/326100/bhx/ca-chot-lam-sach-500g-50-70-con-202407151209300077.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 6,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/8782/326100/bhx/ca-chot-lam-sach-500g-50-70-con-202407151209296266.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 6,
                'image_url' => 'https://cdnv2.tgdd.vn/bhx-static/bhx/Products/Images/8782/326100/bhx/326100-1_202408291338413768.jpg',
                'image_order' => 3,
            ],
            [
                'product_id' => 7,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8936029480030-1-bb.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 7,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8936029480030-1-bb.jpg.webp',
                'image_order' => 2,
            ],
            [
                'product_id' => 8,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8936013680095.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 8,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8936013680095-5.jpg.webp',
                'image_order' => 2,
            ],
            [
                'product_id' => 9,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8936029480054-1-bb.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 10,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/7/8759635832280-1-c_.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 10,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/7/8759635832280-3.jpg.webp',
                'image_order' => 2,
            ],
            [
                'product_id' => 10,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/7/8759635832280-4.jpg.webp',
                'image_order' => 3,
            ],
            [
                'product_id' => 11,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/8788/228931/bhx/tao-ninh-thuan-202312251458132407.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 12,
                'image_url' => 'https://cdnv2.tgdd.vn/bhx-static/bhx/Products/Images/8788/334113/bhx/thung-tao-story-mini-phap-25kg_202502182312070054.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 12,
                'image_url' => 'https://cdnv2.tgdd.vn/bhx-static/bhx/Products/Images/8788/334113/bhx/thung-tao-story-mini-phap-25kg_202502182312078301.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 13,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/7618/316577/bhx/xuc-xich-my-le-gourmet-goi-500g-202311140840070316.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 13,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/7618/316577/bhx/sellingpoint.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 13,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/7618/316577/bhx/xuc-xich-my-le-gourmet-goi-500g-202311140840077904.jpg',
                'image_order' => 3,
            ],
            [
                'product_id' => 14,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/10778/322872/bhx/sellingpoint.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 14,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/10778/322872/bhx/banh-pizza-3-loai-thit-pho-mai-kitkool-hop-140g-202403251004107330.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 15,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/10778/225508/bhx/sellingpoint.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 15,
                'image_url' => 'https://cdn.tgdd.vn/Products/Images/10778/225508/bhx/banh-bao-nhan-thit-heo-trung-cut-la-cusina-400g-202305150920260081.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 16,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/0/4/0400255680000.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 17,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/06/kim-chi-cai-thao-cat-lat-it-cay-bibigo-hop-500g-202304130913496941-20230627074422.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 18,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/0/4/0400255720003-1.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 19,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/05/packg_meizan-2l-sbo-new-20230526031537.png',
                'image_order' => 1,
            ],
            [
                'product_id' => 20,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/11/10603569-20241109092542.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 20,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/11/10603569-20241109092542.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 21,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/05/8934707027478_1-20230424070251-removebg-preview-20230523030132.png',
                'image_order' => 1,
            ],
            [
                'product_id' => 21,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/05/8934707027478_3-20230424070250-20230523030141.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 22,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8938516870164-1.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 22,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8938516870164-2.jpg.webp',
                'image_order' => 2,
            ],
            [
                'product_id' => 23,
                'image_url' => 'https://hcm.fstorage.vn/images/2022/162427539911710008076-t-do-choi-dat-nan-regis-726e-2-og.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 23,
                'image_url' => 'https://hcm.fstorage.vn/images/2022/mi-tom-chua-cay-reeva-goi-65g-20221014063017-og.jpg',
                'image_order' => 2,
            ],
            [
                'product_id' => 24,
                'image_url' => 'https://hcm.fstorage.vn/images/2023/05/mi-tom-chua-cay-kokomi-65g-20230511021234.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 25,
                'image_url' => 'https://hcm.fstorage.vn/images/2022/tao-cuon-com-godbawee-20g130-20221007080831-og.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 26,
                'image_url' => 'https://hcm.fstorage.vn/images/2022/pate-gan-hop-halong-canfoco-hop-170g-20221007080821-og.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 27,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8938501141019.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 28,
                'image_url' => 'https://hcm.fstorage.vn/images/2022/sua-tuoi-tiet-trung-khong-duong-dutch-lady-hop-1-lit-202211250847253334.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 29,
                'image_url' => 'https://hcm.fstorage.vn/images/2024/11/10005342-20241109110745.jpg',
                'image_order' => 1,
            ],
            [
                'product_id' => 30,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8934673613828-1-1.jpg.webp',
                'image_order' => 1,
            ],
            [
                'product_id' => 30,
                'image_url' => 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8934673613828-2-1.jpg.webp',
                'image_order' => 2,
            ],
        ];

        foreach ($images as $imagesData) {
            Image::create($imagesData);
        }
    }
}
