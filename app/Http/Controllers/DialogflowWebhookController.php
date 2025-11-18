<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\LoaiPhong;
use App\Models\HoaDon;
use App\Models\ChiTietThuePhong; // Đảm bảo dòng này đã có
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

            // case 'TimKiemPhongTrongTheoNgay': // THÊM DÒNG NÀY ĐỂ GỌI HÀM MỚI
            //     $fulfillmentText = $this->handleTimKiemPhongTrongTheoNgay($parameters);
            //     break;

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