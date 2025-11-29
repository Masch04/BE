<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\LoaiPhong;
use App\Models\HoaDon;
use App\Models\ChiTietThuePhong; 
use App\Models\DichVu;
use Carbon\Carbon;

class DialogflowWebhookController extends Controller
{
    /**
     * Xử lý các yêu cầu webhook từ Dialogflow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        // Lấy thông tin từ yêu cầu Dialogflow
        $intentName = $request->json('queryResult.intent.displayName');
        $parameters = $request->json('queryResult.parameters');
        // $queryText = $request->json('queryResult.queryText'); // Câu hỏi gốc của người dùng

        $fulfillmentText = '';
        $response = [];

        switch ($intentName) {
            case 'Chào_Hỏi': // Một intent ví dụ
                $fulfillmentText = 'Chào bạn! Tôi có thể giúp gì cho bạn về khách sạn của chúng tôi?';
                break;

            case 'HoiVeCacLoaiPhong':
                $fulfillmentText = $this->handleHoiVeCacLoaiPhong();
                break;

            case 'HoiChiTietLoaiPhong':
                $fulfillmentText = $this->handleHoiChiTietLoaiPhong($parameters);
                break;

            case 'HoiGiaPhongTheoLoai':
                $fulfillmentText = $this->handleHoiGiaPhongTheoLoai($parameters);
                break;
              case 'HoiVeDichVu':
                $fulfillmentText = $this->handleHoiVeDichVu();
                break;

            case 'HoiChiTietDichVu':
                $fulfillmentText = $this->handleHoiChiTietDichVu($parameters);
                break;

            case 'TimKiemPhongTrongTheoNgay':
            // Gọi hàm vừa viết
            $response = $this->handleTimKiemPhongTrongTheoNgay($parameters);
            return response()->json($response);
            break;
            
            case 'TimKiemPhongTheoMucGia':
                // Gọi hàm xử lý giá
                $response = $this->handleTimKiemPhongTheoMucGia($parameters);
                // Trả về JSON luôn vì hàm này sẽ return array cấu trúc Rich Content hoặc text
                return response()->json($response);
                break;

            default:
                $fulfillmentText = 'Rất tiếc, tôi không hiểu yêu cầu của bạn. Bạn có thể nói rõ hơn không?';
                break;
        }

        if (is_array($fulfillmentText) || $fulfillmentText instanceof \Illuminate\Http\JsonResponse) {
    return $fulfillmentText;
}

// Ngược lại, trả về dạng text bình thường
return response()->json([
    'fulfillmentText' => $fulfillmentText,
    'source' => 'webhook-khach-san',
]);
    }

    /**
     * Xử lý intent 'HoiVeCacLoaiPhong'.
     * Liệt kê tất cả các loại phòng hiện có.
     *
     * @return string
     */
protected function handleHoiVeCacLoaiPhong()
{
    $loaiPhongs = LoaiPhong::all();

    if ($loaiPhongs->isEmpty()) {
        return response()->json([
            "fulfillmentMessages" => [
                [
                    "text" => [
                        "text" => ["Rất tiếc, hiện tại không có thông tin về các loại phòng."]
                    ]
                ]
            ]
        ]);
    }

    // Tạo danh sách options cho chips
    $options = [];
    foreach ($loaiPhongs as $lp) {
        $options[] = [
            "text" => $lp->ten_loai_phong
            // Nếu muốn ảnh cho mỗi chip, thêm "image" => ["src" => ["rawUrl" => "https://..."]]
        ];
    }

    // Rich content: chips + 1 description
    $richContent = [
        [
            [
                "type" => "chips",
                "options" => $options
            ],
            [
                "type" => "description",
                "text" => [
                    "Bạn muốn hỏi chi tiết về loại phòng nào? Chỉ cần bấm vào tên phòng!"
                ]
            ]
        ]
    ];

    return response()->json([
        "fulfillmentMessages" => [
            [
                "payload" => [
                    "richContent" => $richContent
                ]
            ]
        ]
    ]);
}

    /**
     * Xử lý intent 'HoiChiTietLoaiPhong'.
     * Cung cấp thông tin chi tiết về một loại phòng cụ thể.
     *
     * @param array $parameters
     * @return string
     */
protected function handleHoiChiTietLoaiPhong(array $parameters)
    {
        $tenLoaiPhong = $parameters['ten_loai_phong'] ?? null;
        if (!$tenLoaiPhong) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ['Bạn muốn hỏi chi tiết về loại phòng nào? Vui lòng cung cấp tên loại phòng.']]]
                ]
            ];
        }

        $tenLoaiPhongNormalized = $this->normalizeRoomTypeName($tenLoaiPhong);
        $loaiPhong = LoaiPhong::whereRaw('LOWER(ten_loai_phong) LIKE ?', ['%' . strtolower($tenLoaiPhongNormalized) . '%'])
                                ->first();

        if (!$loaiPhong) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Rất tiếc, tôi không tìm thấy thông tin về loại phòng '{$tenLoaiPhong}'."]]]
                ]
            ];
        }

        // ===== XỬ LÝ TIỆN ÍCH =====
        $tienIch = $loaiPhong->tien_ich;
        $tienIch = str_replace('</p><p>', '|||', $tienIch);
        $tienIch = strip_tags($tienIch);
        $tienIchList = array_filter(array_map('trim', explode('|||', $tienIch)));

        // Tạo array tiện ích
        $tienIchArray = [];
        foreach ($tienIchList as $item) {
            $tienIchArray[] = "✅ {$item}";
        }

        // Lấy URL hình ảnh trực tiếp từ cột 'hinh_anh'
        // Không cần dùng asset() vì đây là URL đầy đủ từ Unsplash
        $imageUrl = $loaiPhong->hinh_anh ?? 'https://via.placeholder.com/400x200?text=No+Image';

        return [
    'fulfillmentMessages' => [
        [
            'payload' => [
                'richContent' => [
                    [
                        // Card thông tin cơ bản (KHÔNG chứa hình ảnh nữa)
                        [
                            'type' => 'info',
                            'title' => "{$loaiPhong->ten_loai_phong}",
                            'subtitle' => "🛏️ {$loaiPhong->so_giuong} giường | 👥 {$loaiPhong->so_nguoi_lon} người lớn" .
                                         ($loaiPhong->so_tre_em > 0 ? " + {$loaiPhong->so_tre_em} trẻ em" : "") .
                                         " | 📐 {$loaiPhong->dien_tich}m²"
                        ],

                        // 👉 HÌNH ẢNH CHUYỂN XUỐNG DƯỚI — nằm ngay trước “Tiện ích nổi bật”
                        [
                            'type' => 'image',
                            'rawUrl' => $imageUrl
                        ],

                        // Divider
                        [
                            'type' => 'divider'
                        ],

                        // Phần tiện ích
                        [
                            'type' => 'description',
                            'title' => '✨ Tiện ích nổi bật:',
                            'text' => $tienIchArray
                        ]
                    ]
                ]
            ]
        ]
    ]
];

    }
      /**
     * Xử lý intent 'HoiGiaPhongTheoLoai'.
     * Cung cấp giá mặc định của một loại phòng cụ thể.
     *
     * @param array $parameters
     * @return string
     */

protected function handleHoiGiaPhongTheoLoai(array $parameters): array // Thay đổi kiểu trả về thành array
{
    $tenLoaiPhong = $parameters['ten_loai_phong'] ?? null;

    if (!$tenLoaiPhong) {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => ['Bạn muốn hỏi giá của loại phòng nào? Vui lòng cung cấp tên loại phòng.']]]
            ]
        ];
    }

    $tenLoaiPhongNormalized = $this->normalizeRoomTypeName($tenLoaiPhong);

    $loaiPhong = LoaiPhong::whereRaw('LOWER(ten_loai_phong) LIKE ?', ['%' . strtolower($tenLoaiPhongNormalized) . '%'])
                            ->first();

    if (!$loaiPhong) {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => ["Rất tiếc, tôi không tìm thấy thông tin về '{$tenLoaiPhong}'. Bạn có thể kiểm tra lại tên hoặc hỏi về các loại phòng hiện có."]]]
            ]
        ];
    }

    $phong = Phong::where('id_loai_phong', $loaiPhong->id)->first();

    if ($phong && $phong->gia_mac_dinh) {
        $giaMacDinhFormatted = number_format($phong->gia_mac_dinh) . " VND mỗi đêm.";

        // --- Bắt đầu thay đổi để trả về Rich Content ---
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'info',
                                    'title' => "Giá  {$loaiPhong->ten_loai_phong}",
                                    'subtitle' => "💰: {$giaMacDinhFormatted}",
                                    // Bạn có thể thêm imageUrl nếu có hình ảnh cho loại phòng
                                    // 'image' => [
                                    //     'src' => ['rawUrl' => 'URL_HINH_ANH_CUA_BAN']
                                    // ],
                                    // Bạn có thể thêm action link nếu muốn
                                    // 'actionLink' => 'URL_DAT_PHONG'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // --- Kết thúc thay đổi ---

    } else {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => ["Rất tiếc, không có thông tin giá cho loại phòng {$loaiPhong->ten_loai_phong} vào lúc này."]]]
            ]
        ];
    }
}


/**
     * Xử lý intent 'HoiVeDichVu'.
     * Liệt kê danh sách các dịch vụ đang hoạt động.
     */
    protected function handleHoiVeDichVu()
{
    // 1. Lấy dữ liệu (Đảm bảo đã use App\Models\DichVu ở trên)
    $dichVus = DichVu::where('tinh_trang', 1)->get();

    // 2. Xử lý trường hợp không có dịch vụ
    if ($dichVus->isEmpty()) {
        return response()->json([
            "fulfillmentText" => "Hiện tại khách sạn chưa có dịch vụ nào đang hoạt động."
        ]);
    }

    // 3. Chuẩn bị dữ liệu cho Chips và Text
    $options = []; 
    $nameList = [];

    foreach ($dichVus as $dv) {
        $options[] = [
            "text" => $dv->ten_dich_vu,
            // Có thể thêm link hoặc image vào đây nếu muốn
        ];
        $nameList[] = $dv->ten_dich_vu;
    }

    $danhSachString = implode(', ', $nameList);

    // 4. Cấu trúc Rich Content (Dialogflow Messenger)
    // Lưu ý: Cấu trúc richContent là mảng lồng nhau: [ [Component1, Component2] ]
    $richContent = [
        [
            [
                "type" => "description",
                "title" => "Danh sách dịch vụ",
                "text" => [
                    "Dưới đây là các dịch vụ " . count($dichVus) . " dịch vụ chúng tôi cung cấp.",
                    "Bạn quan tâm đến dịch vụ nào?"
                ]
            ],
            [
                "type" => "chips",
                "options" => $options
            ]
        ]
    ];

    // 5. Trả về JSON
    return response()->json([
        // fulfillmentText: Hiển thị trên Test Console và các nền tảng không hỗ trợ Rich Content (Zalo, Facebook cũ)
        "fulfillmentText" => "Khách sạn hiện có các dịch vụ: " . $danhSachString . ". Bạn muốn biết chi tiết về dịch vụ nào?",
        
        // fulfillmentMessages: Hiển thị giao diện đẹp trên Web Demo / Dialogflow Messenger
        "fulfillmentMessages" => [
            [
                "payload" => [
                    "richContent" => $richContent
                ]
            ]
        ]
    ]);
}
    /**
     * Xử lý intent 'HoiChiTietDichVu'.
     * Trả về giá và thông tin của dịch vụ cụ thể.
     */
    protected function handleHoiChiTietDichVu(array $parameters)
    {
        // Giả sử trong Dialogflow bạn đặt tên tham số là 'ten_dich_vu'
        $tenDichVu = $parameters['ten_dich_vu'] ?? null;

        if (!$tenDichVu) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ['Bạn muốn biết giá của dịch vụ nào? Vui lòng nói tên dịch vụ.']]]
                ]
            ];
        }

        // Tìm kiếm tương đối (LIKE)
        $tenDichVuNormalized = mb_strtolower($tenDichVu, 'UTF-8');
        $dichVu = DichVu::whereRaw('LOWER(ten_dich_vu) LIKE ?', ['%' . $tenDichVuNormalized . '%'])
                        ->first();

        if (!$dichVu) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Rất tiếc, tôi không tìm thấy dịch vụ nào có tên là '{$tenDichVu}'. Bạn có thể hỏi 'Khách sạn có dịch vụ gì' để xem danh sách."]]]
                ]
            ];
        }

        // Format giá tiền
        $giaTien = number_format($dichVu->don_gia) . " VNĐ";
        $donVi = $dichVu->don_vi_tinh ? "/ " . $dichVu->don_vi_tinh : "";
        $ghiChu = $dichVu->ghi_chu ? "📝 Ghi chú: " . $dichVu->ghi_chu : "";

        // Trả về dạng thẻ thông tin (Info Card)
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'info',
                                    'title' => $dichVu->ten_dich_vu,
                                    'subtitle' => "💰 Giá: {$giaTien} {$donVi}",
                                ],
                                [
                                    'type' => 'description',
                                    'title' => 'Thông tin thêm:',
                                    'text' => [
                                        $ghiChu ? $ghiChu : "Dịch vụ chất lượng cao phục vụ tại phòng hoặc khu vực riêng."
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

public function handleTimKiemPhongTrongTheoNgay($parameters)
{
    // 1. Nhận tham số
    $dateInputRaw = $parameters['date'] ?? null;
    $roomTypeInput = $parameters['room_type'] ?? null;

    // CẤU HÌNH MÚI GIỜ VIỆT NAM (Quan trọng để tính "Hôm nay")
    $timezone = 'Asia/Ho_Chi_Minh';
    $now = Carbon::now($timezone);

    // 2. Xử lý ngày tháng thông minh
    if (!$dateInputRaw) {
        // Nếu khách không nói ngày -> Mặc định là HÔM NAY
        $date = $now->copy();
        $messageIntro = "Dạ, em kiểm tra phòng trống cho ngày hôm nay ({$date->format('d/m/Y')}) ạ:";
    } else {
        // Nếu khách có chọn ngày -> Parse ngày đó theo múi giờ VN
        $date = Carbon::parse($dateInputRaw)->setTimezone($timezone);
        $messageIntro = "Dạ, vào ngày {$date->format('d/m/Y')} bên em còn các phòng này ạ:";
    }

    // Đưa về đầu ngày để so sánh (00:00:00)
    $checkDate = $date->copy()->startOfDay();
    $today = $now->copy()->startOfDay();

    // Kiểm tra xem ngày có trong quá khứ không
    if ($checkDate->lt($today)) {
        return ["fulfillmentText" => "Ngày {$date->format('d/m/Y')} đã qua rồi ạ. Bạn vui lòng chọn ngày hôm nay hoặc tương lai nhé."];
    }

    $ngayCanTim = $date->format('Y-m-d');

    // 3. Truy vấn dữ liệu (Giữ nguyên Logic của bạn)
    $query = ChiTietThuePhong::join('phongs', 'chi_tiet_thue_phongs.id_phong', '=', 'phongs.id')
        ->join('loai_phongs', 'phongs.id_loai_phong', '=', 'loai_phongs.id')
        ->whereDate('chi_tiet_thue_phongs.ngay_thue', $ngayCanTim)
        ->where('chi_tiet_thue_phongs.tinh_trang', 1) // 1 = Trống
        ->select(
            'loai_phongs.ten_loai_phong',
            'loai_phongs.hinh_anh',
            'chi_tiet_thue_phongs.gia_thue'
        );

    if ($roomTypeInput) {
        // Dùng 'like' để tìm kiếm gần đúng (Ví dụ: khách nói "vip" vẫn ra "Phòng VIP")
        $query->where('loai_phongs.ten_loai_phong', 'like', '%' . $roomTypeInput . '%');
    }

    $ketQua = $query->get()->groupBy('ten_loai_phong');

    // Xử lý khi không có phòng nào trống
    if ($ketQua->isEmpty()) {
        // Gợi ý khách tìm ngày khác
        return [
            "fulfillmentMessages" => [
                [
                    "text" => ["text" => ["Rất tiếc, ngày {$date->format('d/m/Y')} bên mình đã kín phòng rồi ạ. 😭"]]
                ],
                [
                    "payload" => [
                        "richContent" => [[
                            [
                                "type" => "chips",
                                "options" => [
                                    ["text" => "Tìm ngày khác"],
                                    ["text" => "Xem các loại phòng"]
                                ]
                            ]
                        ]]
                    ]
                ]
            ]
        ];
    }

    // 4. TẠO RICH CONTENT (Kết quả trả về)
    $richContent = [];

    foreach ($ketQua as $tenLoai => $danhSachPhong) {
        $soLuongTrong = $danhSachPhong->count();
        $phongMau = $danhSachPhong->first();
        $giaTien = number_format($phongMau->gia_thue, 0, ',', '.');
        
        // Link ảnh (Fallback nếu null)
        $hinhAnh = $phongMau->hinh_anh ?? 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png'; 

        $item = [
            "type" => "info",
            "title" => "Phòng " . $tenLoai,
            "subtitle" => "💰 " . $giaTien . " VNĐ | 🔥 Còn " . $soLuongTrong . " phòng",
            "image" => [
                "src" => ["rawUrl" => $hinhAnh]
            ],
            "actionLink" => "#" 
        ];
        
        $richContent[] = $item;
        $richContent[] = ["type" => "divider"];
    }

    // Thêm các nút gợi ý (Chips) thông minh hơn
    $richContent[] = [
        "type" => "chips",
        "options" => [
            ["text" => "Tìm ngày khác"]
        ]
    ];

    return [
        "fulfillmentMessages" => [
            [
                "text" => [
                    "text" => [$messageIntro]
                ]
            ],
            [
                "payload" => [
                    "richContent" => [
                        $richContent
                    ]
                ]
            ]
        ]
    ];
}

/**
     * Xử lý intent 'TimKiemPhongTheoMucGia'.
     * Tìm phòng dựa trên so sánh giá (dưới, trên, khoảng).
     *
     * @param array $parameters
     * @return array
     */
protected function handleTimKiemPhongTheoMucGia(array $parameters)
    {
        try {
            // 1. Lấy tham số thô
            $amount = $parameters['amount'] ?? 0;
            $condition = $parameters['condition'] ?? 'duoi';

            // --- LOGIC TỰ SỬA LỖI NHẬN DIỆN NGƯỢC ---
            // Kiểm tra: Nếu 'amount' là chữ (ví dụ: "duoi") VÀ 'condition' lại là số (ví dụ: "500")
            // Thì ta tráo đổi giá trị cho nhau.
            if (!is_numeric($amount) && is_numeric($condition)) {
                $temp = $amount;      // Lưu 'duoi' vào temp
                $amount = $condition; // Gán 500 vào amount
                $condition = $temp;   // Gán 'duoi' vào condition
            }
            // ----------------------------------------

            // Xử lý amount nếu nó là mảng (đề phòng)
            if (is_array($amount)) {
                $amount = isset($amount['amount']) ? (float)$amount['amount'] : 0;
            } else {
                // Chỉ lấy số từ chuỗi
                $amount = (float)preg_replace('/[^0-9.]/', '', $amount);
            }

            // Logic nhân 1000 (500 -> 500.000)
            if ($amount > 0 && $amount < 50000) {
                $amount = $amount * 1000;
            }

            // Chuẩn hóa condition về chữ thường để so sánh
            $condition = strtolower((string)$condition);

            // 3. Khởi tạo truy vấn
            $query = Phong::join('loai_phongs', 'phongs.id_loai_phong', '=', 'loai_phongs.id')
                ->select(
                    'loai_phongs.ten_loai_phong',
                    'loai_phongs.hinh_anh',
                    'phongs.gia_mac_dinh'
                );

            $msgIntro = "";

            // 4. Xử lý điều kiện (Thêm nhiều từ đồng nghĩa để bot thông minh hơn)
            // Dùng strpos để bắt từ: ví dụ "rẻ hơn", "thấp hơn", "dưới" đều dính logic này
            if (in_array($condition, ['duoi', 'rẻ hơn', 're hon', 'thấp hơn', 'thap hon', 'nhỏ hơn', 'nho hon', 'under'])) {
                $query->where('phongs.gia_mac_dinh', '<=', $amount);
                $msgIntro = "Tìm thấy các phòng giá RẺ HƠN " . number_format($amount) . " VNĐ:";
            } 
            elseif (in_array($condition, ['tren', 'trên', 'đắt hơn', 'dat hon', 'cao hơn', 'cao hon', 'lớn hơn', 'lon hon', 'over'])) {
                $query->where('phongs.gia_mac_dinh', '>=', $amount);
                $msgIntro = "Tìm thấy các phòng giá CAO HƠN " . number_format($amount) . " VNĐ:";
            } 
            else {
                // Mặc định là tìm khoảng
                $min = $amount - 200000;
                $max = $amount + 200000;
                if ($min < 0) $min = 0;
                $query->whereBetween('phongs.gia_mac_dinh', [$min, $max]);
                $msgIntro = "Tìm thấy các phòng giá XUNG QUANH mức " . number_format($amount) . " VNĐ:";
            }

            // Truy vấn DB
            $ketQua = $query->orderBy('phongs.gia_mac_dinh', 'asc')->get()->unique('ten_loai_phong');

            // 5. Kiểm tra kết quả
            if ($ketQua->isEmpty()) {
                return [
                    'fulfillmentMessages' => [[
                        'text' => ['text' => ["Không tìm thấy phòng nào với mức giá " . number_format($amount) . " VNĐ. Bạn thử tìm mức giá khác xem sao?"]]
                    ]]
                ];
            }

            // 6. Tạo Rich Content
            $richContent = [];
            foreach ($ketQua as $phong) {
                $gia = number_format($phong->gia_mac_dinh, 0, ',', '.');
                $img = $phong->hinh_anh ?: 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png';

                $richContent[] = [
                    "type" => "info",
                    "title" => $phong->ten_loai_phong,
                    "subtitle" => "💰 {$gia} VNĐ",
                    "image" => ["src" => ["rawUrl" => $img]],
                    "actionLink" => "/"
                ];
                $richContent[] = ["type" => "divider"];
            }

            return [
                "fulfillmentMessages" => [
                    ["text" => ["text" => [$msgIntro]]],
                    ["payload" => ["richContent" => [$richContent]]]
                ]
            ];

        } catch (\Exception $e) {
            return [
                'fulfillmentMessages' => [[
                    'text' => ['text' => ["Lỗi hệ thống: " . $e->getMessage()]]
                ]]
            ];
        }
    }
    /**
     * Helper: Chuẩn hóa tên loại phòng để tìm kiếm linh hoạt hơn.
     * Có thể mở rộng để xử lý các từ đồng nghĩa hoặc lỗi chính tả nhỏ.
     *
     * @param string $inputName
     * @return string
     */
    protected function normalizeRoomTypeName(string $inputName): string
    {
        $normalized = mb_strtolower($inputName, 'UTF-8');
        return $normalized;
    }



}