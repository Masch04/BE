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
    
    // Trả về JSON ngay lập tức (Laravel response)
    return response()->json($responseArray);
    break;;

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
                                    'title' => "Giá phòng {$loaiPhong->ten_loai_phong}",
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
        // 1. Nhận tham số (Giữ nguyên)
        $dateInputRaw = $parameters['date'] ?? null;
        $roomTypeInput = $parameters['room_type'] ?? null;

        if (!$dateInputRaw) {
            return ["fulfillmentText" => "Vui lòng cho mình biết bạn muốn tìm phòng ngày nào ạ?"];
        }

        $date = Carbon::parse($dateInputRaw)->startOfDay();
        $today = Carbon::today();

        if ($date->lt($today)) {
            return ["fulfillmentText" => "Ngày {$date->format('d/m/Y')} đã qua. Vui lòng chọn ngày hôm nay hoặc tương lai."];
        }

        $ngayCanTim = $date->format('Y-m-d');

        // 2. Truy vấn dữ liệu (Giữ nguyên)
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
            $query->where('loai_phongs.ten_loai_phong', 'like', '%' . $roomTypeInput . '%');
        }

        $ketQua = $query->get()->groupBy('ten_loai_phong');

        if ($ketQua->isEmpty()) {
            return ["fulfillmentText" => "Rất tiếc, vào ngày {$date->format('d/m/Y')} bên mình đã hết phòng trống ạ."];
        }

        // 3. TẠO CUSTOM PAYLOAD CHO DIALOGFLOW MESSENGER
        $richContent = [];

        foreach ($ketQua as $tenLoai => $danhSachPhong) {
            $soLuongTrong = $danhSachPhong->count();
            $phongMau = $danhSachPhong->first();
            $giaTien = number_format($phongMau->gia_thue, 0, ',', '.');
            
            // Link ảnh (Nếu database null thì lấy ảnh mạng demo)
            $hinhAnh = $phongMau->hinh_anh ?? 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png'; 

            // Tạo Card Info
            $item = [
                "type" => "info", // Loại thẻ thông tin
                "title" => "Phòng " . $tenLoai,
                "subtitle" => "💰 " . $giaTien . " VNĐ | ✅ Còn: " . $soLuongTrong,
                "image" => [
                    "src" => [
                        "rawUrl" => $hinhAnh
                    ]
                ],
                "actionLink" => "#" // Bắt buộc phải có dòng này dù không dùng link
            ];
            
            $richContent[] = $item;
            
            // Thêm đường kẻ phân cách cho đẹp
            $richContent[] = ["type" => "divider"];
        }

        // Thêm các nút bấm (Chips) ở dưới cùng
        $richContent[] = [
            "type" => "chips",
            "options" => [
                [
                    "text" => "Tìm ngày khác"
                ]
            ]
        ];

        // 4. Trả về kết quả chuẩn Dialogflow Messenger
        return [
            "fulfillmentMessages" => [
                [
                    "text" => [
                        "text" => ["Dạ, vào ngày {$date->format('d/m/Y')} bên em còn các phòng này ạ:"]
                    ]
                ],
                [
                    "payload" => [
                        "richContent" => [
                            $richContent // Lưu ý: richContent là mảng lồng nhau
                        ]
                    ]
                ]
            ]
        ];
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

    /**
    * Xử lý intent 'TimKiemPhongTrongTheoNgay'.
    * Tìm kiếm phòng trống theo loại phòng và khoảng thời gian.
    *
    * @param array $parameters
    * @return string
    */

}