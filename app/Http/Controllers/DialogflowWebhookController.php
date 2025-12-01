<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\LoaiPhong;
use App\Models\HoaDon;
use App\Models\ChiTietThuePhong; 
use App\Models\DichVu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            case 'Chào_Hỏi': 
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

    $tienIchArray = [];
    foreach ($tienIchList as $item) {
        $tienIchArray[] = "✅ {$item}";
    }

    // Lấy URL hình ảnh
    $imageUrl = $loaiPhong->hinh_anh ?? 'https://via.placeholder.com/400x200?text=No+Image';

    $frontendUrl = "http://localhost:5173"; 
    $linkChiTiet = $frontendUrl . "/chi-tiet-phong/" . $loaiPhong->id;

    return [
        'fulfillmentMessages' => [
            [
                'payload' => [
                    'richContent' => [
                        [
                            // Card thông tin cơ bản
                            [
                                'type' => 'info',
                                'title' => "{$loaiPhong->ten_loai_phong}",
                                'subtitle' => "🛏️ {$loaiPhong->so_giuong} giường | 👥 {$loaiPhong->so_nguoi_lon} người lớn" .
                                                ($loaiPhong->so_tre_em > 0 ? " + {$loaiPhong->so_tre_em} trẻ em" : "") .
                                                " | 📐 {$loaiPhong->dien_tich}m²",
                                'actionLink' => $linkChiTiet 
                            ],
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
                            ],
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

protected function handleHoiGiaPhongTheoLoai(array $parameters): array
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
        $giaMacDinhFormatted = number_format($phong->gia_mac_dinh, 0, ',', '.') . " VND";
        $frontendUrl = "http://localhost:5173"; 
        $linkChiTiet = $frontendUrl . "/chi-tiet-phong/" . $loaiPhong->id;
        $hinhAnh = $loaiPhong->hinh_anh ?? 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png';

        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'info',
                                    'title' => "Giá phòng {$loaiPhong->ten_loai_phong}",
                                    'subtitle' => "💰 Giá tham khảo: {$giaMacDinhFormatted} / đêm",
                                    'image' => [
                                        'src' => ['rawUrl' => $hinhAnh]
                                    ],
                                    'actionLink' => $linkChiTiet
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];

    } else {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => ["Rất tiếc, hiện tại chưa có giá cập nhật cho loại phòng này."]]]
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
    // Lấy dữ liệu (Đảm bảo đã use App\Models\DichVu ở trên)
    $dichVus = DichVu::where('tinh_trang', 1)->get();

    //  Xử lý trường hợp không có dịch vụ
    if ($dichVus->isEmpty()) {
        return response()->json([
            "fulfillmentText" => "Hiện tại khách sạn chưa có dịch vụ nào đang hoạt động."
        ]);
    }

    //  Chuẩn bị dữ liệu cho Chips và Text
    $options = []; 
    $nameList = [];

    foreach ($dichVus as $dv) {
        $options[] = [
            "text" => $dv->ten_dich_vu,

        ];
        $nameList[] = $dv->ten_dich_vu;
    }

    $danhSachString = implode(', ', $nameList);

 
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

    // Trả về JSON
    return response()->json([

        "fulfillmentText" => "Khách sạn hiện có các dịch vụ: " . $danhSachString . ". Bạn muốn biết chi tiết về dịch vụ nào?",

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
    try {
        $frontendUrl = "http://localhost:5173"; 
        
        $dateInputRaw = $parameters['date'] ?? null;
        $roomTypeInput = $parameters['room_type'] ?? null; // 
        $queryText = mb_strtolower(request()->input('queryResult.queryText', ''), 'UTF-8');

        $timezone = 'Asia/Ho_Chi_Minh';
        $now = \Carbon\Carbon::now($timezone);

        // Xử lý ngày tháng
        if ($dateInputRaw) {
            $date = \Carbon\Carbon::parse($dateInputRaw)->setTimezone($timezone);
            $msgDate = $date->format('d/m/Y');
        } else {
            if (strpos($queryText, 'mai') !== false) {
                $date = $now->copy()->addDay();
                $msgDate = "ngày mai (" . $date->format('d/m') . ")";
            } elseif (strpos($queryText, 'mốt') !== false || strpos($queryText, 'kia') !== false) {
                $date = $now->copy()->addDays(2);
                $msgDate = "ngày mốt (" . $date->format('d/m') . ")";
            } else {
                $date = $now->copy();
                $msgDate = "hôm nay (" . $date->format('d/m') . ")";
            }
        }

        if ($date->copy()->startOfDay()->lt($now->copy()->startOfDay())) {
            return ["fulfillmentText" => "Ngày bạn chọn đã qua rồi ạ."];
        }

        $ngayCanTim = $date->format('Y-m-d');

        //  Truy vấn cơ bản
        $query = \App\Models\ChiTietThuePhong::join('phongs', 'chi_tiet_thue_phongs.id_phong', '=', 'phongs.id')
            ->join('loai_phongs', 'phongs.id_loai_phong', '=', 'loai_phongs.id')
            ->whereDate('chi_tiet_thue_phongs.ngay_thue', $ngayCanTim)
            ->where('chi_tiet_thue_phongs.tinh_trang', 1)
            ->select(
                'loai_phongs.id',
                'loai_phongs.ten_loai_phong',
                'loai_phongs.hinh_anh',
                \DB::raw('AVG(chi_tiet_thue_phongs.gia_thue) as gia_trung_binh'),
                \DB::raw('COUNT(chi_tiet_thue_phongs.id) as so_luong_trong')
            )
            ->groupBy('loai_phongs.id', 'loai_phongs.ten_loai_phong', 'loai_phongs.hinh_anh');

        //  Bộ lọc thông minh & Sắp xếp
        $messageIntro = "Dạ, danh sách phòng trống {$msgDate} đây ạ:";
        $isSorted = false;

        // Nếu Dialogflow nhận diện được loại phòng 
        if ($roomTypeInput) {
            $query->where('loai_phongs.ten_loai_phong', 'like', '%' . $roomTypeInput . '%');
            $messageIntro = "Dạ, loại phòng '{$roomTypeInput}' còn trống vào {$msgDate} đây ạ:";
        }
        $featureWords = ['biển', 'view', 'đôi', 'đơn', 'vip', 'suite', 'deluxe', 'family', 'gia đình'];
        
        foreach ($featureWords as $word) {
            // Chỉ tìm nếu chưa có roomTypeInput (để tránh lọc trùng lặp)
            if (!$roomTypeInput && strpos($queryText, $word) !== false) {
                $query->where('loai_phongs.ten_loai_phong', 'like', "%{$word}%");
                $messageIntro = "Em tìm thấy các phòng '{$word}' vào {$msgDate} ạ:";
            }
        }

        // Lọc giá rẻ
        $cheapWords = ['rẻ', 'bèo', 'hạt dẻ', 'mềm'];
        if ($this->containsAny($queryText, $cheapWords)) {
            $query->orderBy('gia_trung_binh', 'asc');
            $messageIntro = "Em lọc được mấy phòng giá tốt nhất cho mình nè:";
            $isSorted = true;
        }

        // Lọc giá sang
        $luxuryWords = ['xịn', 'sang', 'cao cấp'];
        if ($this->containsAny($queryText, $luxuryWords)) {
            $query->orderBy('gia_trung_binh', 'desc');
            $isSorted = true;
        }

        if (!$isSorted) {
            $query->orderBy('gia_trung_binh', 'asc');
        }

        $ketQua = $query->take(10)->get();

        // 4. Trả kết quả
        if ($ketQua->isEmpty()) {
            return ["fulfillmentText" => "Tiếc quá, ngày {$msgDate} bên em đã hết loại phòng bạn tìm rồi ạ."];
        }

        $richContent = [];
        foreach ($ketQua as $phong) {
            $giaTien = number_format($phong->gia_trung_binh, 0, ',', '.');
            $hinhAnh = !empty($phong->hinh_anh) ? $phong->hinh_anh : 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png';
            $linkChiTiet = $frontendUrl . "/chi-tiet-phong/" . $phong->id;

            $richContent[] = [
                "type" => "info",
                "title" => $phong->ten_loai_phong,
                "subtitle" => "💰 {$giaTien}đ | 🔥 Còn {$phong->so_luong_trong} phòng",
                "image" => ["src" => ["rawUrl" => $hinhAnh]],
                "actionLink" => $linkChiTiet
            ];
            $richContent[] = ["type" => "divider"];
        }

        return [
            "fulfillmentMessages" => [
                ["text" => ["text" => [$messageIntro]]],
                ["payload" => ["richContent" => [$richContent]]]
            ]
        ];

    } catch (\Exception $e) {
        \Log::error('Lỗi: ' . $e->getMessage());
        return ["fulfillmentText" => "Lỗi hệ thống: " . $e->getMessage()];
    }
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
        //  CHUẨN BỊ DỮ LIỆU ---
        $getValue = function ($val) {
            return is_array($val) ? ($val[0] ?? '') : $val;
        };

        $rawAmount = $getValue($parameters['amount'] ?? '');
        $rawCondition = $getValue($parameters['condition'] ?? '');

        // Lấy câu chat gốc & chuẩn hóa
        $originalText = mb_strtolower(request()->input('queryResult.queryText', ''), 'UTF-8');
        $originalInput = $originalText; // Lưu lại để debug
        
        //  XỬ LÝ DẤU PHẨY 
        $originalText = str_replace(',', '.', $originalText);
        
        //  XỬ LÝ "RƯỠI" (ĐÃ FIX) 
        
        // CASE 1: Xử lý dạng "3 củ rưỡi", "3 triệu rưỡi" -> chuyển thành "3.5 củ", "3.5 triệu"
        // Regex này tìm: Số + (Khoảng trắng) + Đơn vị tiền + (Khoảng trắng) + Rưỡi
        $unitsPattern = 'tr|triệu|trieu|củ|cu|m|lít|lit|loét|lốp|k|nghìn|nghin|ngàn|cành';
        $ruoiPattern = 'rưỡi|rươi|ruoi';
        
        $originalText = preg_replace(
            "/(\d+)\s*($unitsPattern)\s*($ruoiPattern)/ui", 
            '$1.5 $2', 
            $originalText
        );

        // CASE 2: Xử lý dạng "3 rưỡi" (không có đơn vị ở giữa) -> chuyển thành "3.5"
        // Sau khi chạy Case 1, các trường hợp còn sót lại sẽ là dạng số đứng liền chữ rưỡi
        $originalText = preg_replace(
            "/(\d+)\s*($ruoiPattern)/ui", 
            '$1.5', 
            $originalText
        );
        
        //  XỬ LÝ "X tr Y", "X củ Y" 
        if (!preg_match('/\d+\.\d+/', $originalText)) {
            $originalText = preg_replace('/(\d+)\s*(tr|triệu|trieu)\s+(\d+)/u', '$1.$3 $2', $originalText);
            $originalText = preg_replace('/(\d+)\s*(củ|cu)\s+(\d+)/u', '$1.$3 $2', $originalText);
        }

        // --- HÀM PARSE TIỀN ---
        $parseMoney = function($num, $unitContext = '') use ($originalText) {
            $num = (float)str_replace(',', '.', trim($num));
            if ($num <= 0) return 0;
            
            $unitContext = mb_strtolower(trim($unitContext), 'UTF-8');

            // Đơn vị triệu
            $trieuUnits = ['tr', 'triệu', 'trieu', 'củ', 'cu', 'm'];
            // Đơn vị trăm nghìn
            $tramNghinUnits = ['lít', 'lit', 'loét', 'loet', 'lốp', 'lop', 'lớp', 'lopd'];
            // Đơn vị nghìn
            $nghinUnits = ['k', 'nghìn', 'nghin', 'ngàn', 'ngan', 'cành', 'canh'];

            // ƯU TIÊN 1: Đơn vị trực tiếp
            if (in_array($unitContext, $trieuUnits)) {
                return (int)round($num * 1000000);
            }
            if (in_array($unitContext, $tramNghinUnits)) {
                return (int)round($num * 100000);
            }
            if (in_array($unitContext, $nghinUnits)) {
                return (int)round($num * 1000);
            }

            // ƯU TIÊN 2: Context trong câu
            if (preg_match('/(củ|cu|triệu|trieu|tr)\b/u', $originalText)) {
                if ($num < 100) return (int)round($num * 1000000);
            }
            
            if (preg_match('/(lốp|lít|lớp|loet|lit|lop|loét)/u', $originalText)) {
                if ($num < 1000) return (int)round($num * 100000);
            }
            
            if (preg_match('/\d+\s*k\b/u', $originalText) || 
                preg_match('/\b(nghìn|nghin|ngàn|ngan|cành|canh)\b/u', $originalText)) {
                return (int)round($num * 1000);
            }

            // ƯU TIÊN 3: Auto-detect
            if ($num >= 50000) return (int)round($num);
            if ($num >= 100) return (int)round($num * 1000);
            if ($num >= 10) return (int)round($num * 100000);
            return (int)round($num * 1000000);
        };

        // Khởi tạo biến
        $amount = 0;
        $amount2 = 0;
        $searchMode = 'normal';
        $msgIntro = "";

        // - XỬ LÝ KHOẢNG GIÁ (RANGE) 
        // Regex CẢI TIẾN: Bắt số thập phân đúng cách
        // Pattern: (Số1)(Đơn vị1?) ... (từ khóa range) ... (Số2)(Đơn vị2?)
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*([a-zàáảãạăắằẳẵặâấầẩẫậèéẻẽẹêếềểễệìíỉĩịòóỏõọôốồổỗộơớờởỡợùúủũụưứừửữựỳýỷỹỵđ]+)?\s+(?:đến|tới|den)\s+(\d+(?:[.,]\d+)?)\s*([a-zàáảãạăắằẳẵặâấầẩẫậèéẻẽẹêếềểễệìíỉĩịòóỏõọôốồổỗộơớờởỡợùúủũụưứừửữựỳýỷỹỵđ]+)?/ui', $originalText, $matches)) {
            $searchMode = 'range';
            
            $num1 = $matches[1];
            $unit1 = $matches[2] ?? '';
            $num2 = $matches[3];
            $unit2 = $matches[4] ?? '';
            
            $amount = $parseMoney($num1, $unit1);
            $amount2 = $parseMoney($num2, $unit2);

            // Đảm bảo amount <= amount2
            if ($amount > $amount2) {
                list($amount, $amount2) = [$amount2, $amount];
            }
        }

        //  XỬ LÝ 1 SỐ CỤ THỂ (NORMAL) ---
        if ($searchMode == 'normal') {
            $val = 0;
            $unit = '';

        
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*([a-zàáảãạăắằẳẵặâấầẩẫậèéẻẽẹêếềểễệìíỉĩịòóỏõọôốồổỗộơớờởỡợùúủũụưứừửữựỳýỷỹỵđ]+)?/ui', $originalText, $m)) {
                $val = (float)str_replace(',', '.', $m[1]);
                $unit = $m[2] ?? '';
            }

            if ($val <= 0) {
                if (!empty($rawAmount) && is_numeric(preg_replace('/[^0-9.]/', '', $rawAmount))) {
                    $val = (float)preg_replace('/[^0-9.]/', '', $rawAmount);
                } elseif (!empty($rawCondition) && is_numeric(preg_replace('/[^0-9.]/', '', $rawCondition))) {
                    $val = (float)preg_replace('/[^0-9.]/', '', $rawCondition);
                }
            }

            if ($val > 0) {
                $amount = $parseMoney($val, $unit);
            }
        }


        if ($amount <= 0) {
            $cheapKeywords = ['rẻ', 're', 'bèo', 'beo', 'hạt dẻ', 'hat de', 'sinh viên', 'sinh vien', 'mềm', 'mem', 'thấp nhất', 'tiết kiệm', 'bình dân'];
            $luxuryKeywords = ['đắt', 'dat', 'đat', 'sang', 'xịn', 'xin', 'cao cấp', 'cao cap', 'vip', 'ngon', 'thương gia'];

            $hasKeyword = function($text, $keywords) {
                foreach ($keywords as $kw) {
                    if (strpos($text, $kw) !== false) return true;
                }
                return false;
            };

            if ($hasKeyword($originalText, $cheapKeywords)) {
                $searchMode = 'cheapest';
            } elseif ($hasKeyword($originalText, $luxuryKeywords)) {
                $searchMode = 'luxury';
            } else {
                return [
                    'fulfillmentMessages' => [[
                        'text' => ['text' => ["Mình chưa nghe rõ mức giá. Bạn nhập lại ví dụ: 'từ 1 củ đến 2 củ' hoặc '500k' nhé."]]
                    ]]
                ];
            }
        }
         $query = Phong::join('loai_phongs', 'phongs.id_loai_phong', '=', 'loai_phongs.id')
            ->select('loai_phongs.id', 'loai_phongs.ten_loai_phong', 'loai_phongs.hinh_anh', 'phongs.gia_mac_dinh');

        switch ($searchMode) {
            case 'range':
                $query->whereBetween('phongs.gia_mac_dinh', [$amount, $amount2]);
                $msgIntro = "Tìm thấy các phòng có giá từ " . number_format($amount) . " đến " . number_format($amount2) . " VNĐ:";
                break;

            case 'cheapest':
                $query->orderBy('phongs.gia_mac_dinh', 'asc')->limit(3);
                $msgIntro = "Top các hạng phòng giá tốt nhất cho bạn:";
                break;

            case 'luxury':
                $query->orderBy('phongs.gia_mac_dinh', 'desc')->limit(3);
                $msgIntro = "Các hạng phòng sang trọng nhất tại khách sạn:";
                break;

            default: // Normal
                $condition = mb_strtolower((string)$rawCondition, 'UTF-8');
                
                $arrDuoi = ['duoi', 'dưới', 'rẻ hơn', 're hon', 'thấp hơn', 'nhỏ hơn', 'under', 'đổ lại', 'do lai', 'quay đầu', 'quay dau'];
                $arrTren = ['tren', 'trên', 'đắt hơn', 'cao hơn', 'lớn hơn', 'over'];
                $arrXungQuanh = ['tầm', 'tam', 'khoảng', 'khoang', 'cỡ', 'co', 'gần', 'gan', 'around', 'xung quanh'];

                $isDuoi = false;
                $isTren = false;
                $isXungQuanh = false;

                foreach ($arrDuoi as $kw) {
                    if (strpos($originalText, $kw) !== false) {
                        $isDuoi = true;
                        break;
                    }
                }

                foreach ($arrTren as $kw) {
                    if (strpos($originalText, $kw) !== false) {
                        $isTren = true;
                        break;
                    }
                }

                foreach ($arrXungQuanh as $kw) {
                    if (strpos($originalText, $kw) !== false) {
                        $isXungQuanh = true;
                        break;
                    }
                }

                if (!empty($condition)) {
                    if (in_array($condition, $arrDuoi)) $isDuoi = true;
                    if (in_array($condition, $arrTren)) $isTren = true;
                    if (in_array($condition, $arrXungQuanh)) $isXungQuanh = true;
                }

                if ($isDuoi) {
                    $query->where('phongs.gia_mac_dinh', '<=', $amount);
                    $msgIntro = "Tìm thấy các phòng giá RẺ HƠN hoặc BẰNG " . number_format($amount) . " VNĐ:";
                } elseif ($isTren) {
                    $query->where('phongs.gia_mac_dinh', '>', $amount);
                    $msgIntro = "Tìm thấy các phòng giá CAO HƠN " . number_format($amount) . " VNĐ:";
                } else {
                    $margin = $amount * 0.2;
                    if ($margin < 100000) $margin = 100000;
                    
                    $min = $amount - $margin;
                    if ($min < 0) $min = 0;
                    $max = $amount + $margin;

                    $query->whereBetween('phongs.gia_mac_dinh', [$min, $max]);
                    $msgIntro = "Tìm thấy các phòng giá XUNG QUANH " . number_format($amount) . " VNĐ";
                }
                break;
        }

        // THỰC THI QUERY & TRẢ VỀ 
        $ketQua = $query->orderBy('phongs.gia_mac_dinh', 'asc')
            ->get()
            ->unique('ten_loai_phong')
            ->take(10);

        if ($ketQua->isEmpty()) {
            return [
                'fulfillmentMessages' => [[
                    'text' => ['text' => ["Rất tiếc, không tìm thấy phòng nào phù hợp với mức giá này."]]
                ]]
            ];
        }


        $frontendUrl = "http://localhost:5173"; 
        $richContent = [];
        foreach ($ketQua as $phong) {
            $gia = number_format($phong->gia_mac_dinh, 0, ',', '.');
            
            // Xử lý ảnh mặc định nếu thiếu
            $img = !empty($phong->hinh_anh) 
                ? $phong->hinh_anh 
                : 'https://cdn-icons-png.flaticon.com/512/3009/3009489.png';

            // Tạo link dẫn tới trang chi tiết
            $linkChiTiet = $frontendUrl . "/chi-tiet-phong/" . $phong->id;

            $richContent[] = [
                "type" => "info",
                "title" => $phong->ten_loai_phong,
                "subtitle" => "💰 Giá: {$gia} VNĐ",
                "image" => ["src" => ["rawUrl" => $img]],
                "actionLink" => $linkChiTiet 
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
        \Log::error('handleTimKiemPhongTheoMucGia Error: ' . $e->getMessage());
        return [
            'fulfillmentMessages' => [[
                'text' => ['text' => ["Đã xảy ra lỗi khi tìm kiếm phòng. Vui lòng thử lại sau."]]
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

  private function containsAny($str, array $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($str, $keyword)) return true;
        }
        return false;
    }
    
}