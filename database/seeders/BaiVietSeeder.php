<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BaiVietSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bai_viets')->truncate();

        $now = Carbon::now();

        DB::table('bai_viets')->insert([
            // === ẨM THỰC (id_chuyen_muc = 1) ===
            [
                'id_chuyen_muc' => 1,
                'ten_bai_viet' => 'Bánh mì Hội An – Hương vị di sản giữa lòng phố cổ',
                'mo_ta_ngan' => 'Không chỉ là món ăn sáng, bánh mì Hội An là cả một nghệ thuật: vỏ giòn rụm, nhân đầy đặn, nước sốt đậm đà – đủ sức chinh phục mọi tín đồ ẩm thực.',
                'mo_ta_chi_tiet' => '<p>Đến Hội An, bạn không thể bỏ qua <strong>bánh mì Phượng</strong> hay <em>bánh mì Madam Khanh</em>. Bánh mì ở đây khác biệt nhờ lớp vỏ baguette nướng than giòn tan, pate nhà làm mịn màng, thịt nướng thơm lừng và đặc biệt là thứ nước sốt bí truyền cay nồng, ngọt nhẹ.</p><p>Mỗi ổ bánh mì là một câu chuyện: từ cách chọn nguyên liệu tươi mỗi sáng, đến bí kíp ướp thịt gia truyền qua 3 thế hệ.</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/376464/pexels-photo-376464.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 1,
                'ten_bai_viet' => 'Cà phê trứng Hà Nội – Ly ký ức giữa lòng phố cổ',
                'mo_ta_ngan' => 'Một thìa cà phê béo ngậy, một ngụm đắng ngọt hòa quyện – cà phê trứng không chỉ là đồ uống, mà là biểu tượng của sự sáng tạo và tinh tế.',
                'mo_ta_chi_tiet' => '<p>Ra đời từ thời khan hiếm sữa, cà phê trứng được sáng tạo bằng lòng đỏ trứng gà đánh bông với đường và chút bơ. Khi rót cà phê đen nóng hổi vào, lớp kem trứng nổi lên như đám mây, tạo nên hương vị khó quên.</p><p>Quán Giảng, quán Đinh, quán Năng – mỗi nơi một phong cách, nhưng đều giữ được linh hồn của món đồ uống huyền thoại này.</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/312418/pexels-photo-312418.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 1,
                'ten_bai_viet' => 'Bún bò Huế – Tô lửa đỏ giữa cố đô',
                'mo_ta_ngan' => 'Nước dùng trong veo mà đậm đà, sợi bún dai mềm, miếng thịt bò tái ngọt lịm – bún bò Huế là bản giao hưởng của gia vị và tâm huyết.',
                'mo_ta_chi_tiet' => '<p>Khác với phở, bún bò Huế dùng <strong>xương ống ninh 12 tiếng</strong>, thêm mắm ruốc, sả, ớt sa tế tạo nên màu đỏ đặc trưng và mùi thơm nồng nàn. Mỗi tô bún là sự cân bằng hoàn hảo giữa cay, mặn, ngọt, chua.</p><p>Ăn kèm rau sống, giá đỗ, hoa chuối thái mỏng – tất cả hòa quyện thành một trải nghiệm ẩm thực khó quên.</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/699953/pexels-photo-699953.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now,
            ],

            // === ĐỊA ĐIỂM DU LỊCH (id_chuyen_muc = 2) ===
            [
                'id_chuyen_muc' => 2,
                'ten_bai_viet' => 'Cầu Vàng Đà Nẵng – Bàn tay khổng lồ nâng dải lụa giữa mây trời',
                'mo_ta_ngan' => 'Biểu tượng mới của du lịch Việt Nam: cây cầu cong cong nằm trong lòng bàn tay đá khổng lồ, phía dưới là rừng nguyên sinh xanh mướt.',
                'mo_ta_chi_tiet' => '<p>Ra mắt năm 2018, <strong>Cầu Vàng</strong> nhanh chóng trở thành điểm check-in hot nhất Đà Nẵng. Ở độ cao 1.400m so với mực nước biển, bạn sẽ cảm nhận được hơi thở của núi rừng, mây trôi lững lờ quanh bàn chân.</p><p>Thời điểm đẹp nhất: sáng sớm (mây mù) hoặc hoàng hôn (ánh nắng vàng rực rỡ).</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/753626/pexels-photo-753626.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subHours(8),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 2,
                'ten_bai_viet' => 'Hòn Khô Quy Nhơn – Viên ngọc ẩn giữa biển khơi',
                'mo_ta_ngan' => 'Chỉ cách đất liền 15 phút đi canoe, Hòn Khô hiện lên như một bức tranh: nước trong veo, rạn san hô rực rỡ, hải sản tươi sống.',
                'mo_ta_chi_tiet' => '<p>Khi thủy triều xuống, con đường cát trắng hiện ra nối đảo với đất liền – hiện tượng “đi bộ dưới biển” độc đáo. Lặn ngắm san hô, ăn ghẹ hấp, uống dừa tươi – tất cả chỉ trong một buổi sáng.</p><p>Lưu ý: chỉ có 2 homestay, nên đặt trước nếu muốn ngủ lại trên đảo.</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/358457/pexels-photo-358457.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 2,
                'ten_bai_viet' => '– Nơi bình yên còn sót lại',
                'mo_ta_ngan' => 'Xa trung tâm Quy Nhơn 20km, làng chài Nhơn Hải vẫn giữ nguyên vẻ hoang sơ: thuyền thúng, lưới cá, tiếng sóng vỗ và những bữa cơm hải sản 50k.',
                'mo_ta_chi_tiet' => '<p>Mỗi sáng, ngư dân gánh cá tươi lên chợ. Du khách có thể mua mực một nắng, cá nục hấp, ghẹ luộc ngay tại bãi biển. Buổi chiều, ngắm hoàng hôn từ đồi cát – không gian yên bình hiếm có.</p>',
                'hinh_anh' => 'https://mia.vn/media/uploads/blog-du-lich/lang-chai-nhon-hai-ve-dep-binh-di-noi-ngoai-o-thanh-pho-1-1679650508.jpg',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now,
            ],

            // === KHUYẾN MÃI & KINH NGHIỆM (id_chuyen_muc = 3) ===
            [
                'id_chuyen_muc' => 3,
                'ten_bai_viet' => 'Combo “Bay & Ngủ” Vinpearl Nha Trang – Chỉ từ 1.299.000đ',
                'mo_ta_ngan' => 'Vé máy bay khứ hồi + 2 đêm Vinpearl + buffet sáng + vé VinWonders – gói ưu đãi có 1-0-2 cho kỳ nghỉ hè!',
                'mo_ta_chi_tiet' => '<p>Đặt trước 30 ngày, nhận ngay combo trọn gói. Bay Vietnam Airlines, nghỉ tại Vinpearl Resort & Spa, vui chơi không giới hạn tại VinWonders, ăn buffet 50 món.</p><p>Áp dụng đến 30/09/2024. Số lượng có hạn!</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 3,
                'ten_bai_viet' => 'Check-in Grand World Phú Quốc – “Thành phố không ngủ” phiên bản Việt',
                'mo_ta_ngan' => 'Kênh đào Venice, gấu trúc khổng lồ, show thực cảnh Tinh Hoa Việt Nam – tất cả trong 1 đêm tại Grand World!',
                'mo_ta_chi_tiet' => '<p>Grand World mở cửa 24/7 với hàng trăm hoạt động: đi thuyền gondola, xem show nước, ăn uống đường phố, chụp ảnh sống ảo. Đặc biệt, show <strong>Tinh Hoa Việt Nam</strong> sử dụng công nghệ mapping 3D sống động nhất Đông Nam Á.</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/460672/pexels-photo-460672.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now,
            ],
            [
                'id_chuyen_muc' => 3,
                'ten_bai_viet' => 'Kinh nghiệm săn vé 0đ – Bí kíp từ A-Z',
                'mo_ta_ngan' => 'Làm sao để đặt được vé máy bay 0đ? Cài app nào? Canh khung giờ nào? Tất cả được bật mí trong 5 phút đọc!',
                'mo_ta_chi_tiet' => '<p><strong>Bước 1:</strong> Cài app Vietjet, Bamboo, cài thông báo. <strong>Bước 2:</strong> Canh giờ vàng: 0h, 12h, 22h. <strong>Bước 3:</strong> Chuẩn bị sẵn thông tin hành khách. <strong>Bước 4:</strong> Thanh toán ngay khi thấy giá 0đ (chưa tính thuế).</p><p>Thành công 90% nếu làm đúng!</p>',
                'hinh_anh' => 'https://images.pexels.com/photos/450063/pexels-photo-450063.jpeg?w=1600',
                'tinh_trang' => 1,
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now,
            ],
        ]);
    }
}