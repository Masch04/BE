<?php

namespace App\Http\Controllers;

use App\Mail\ThanhToanHoaDonMail;
use App\Models\ChiTietThuePhong;
use App\Models\GiaoDich;
use App\Models\HoaDon;
use App\Models\ChiTietDichVu;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GiaoDichController extends Controller
{
    public function index()
    {
        $client = new Client();
        $payload = [
            "USERNAME"      => "0357989225",
            "PASSWORD"      => "TuanCuong@7",
            "DAY_BEGIN"     => Carbon::today()->format('d/m/Y'),
            "DAY_END"       => Carbon::today()->format('d/m/Y'),
            "NUMBER_MB"     => "8460120971103"
        ];

        try {
            $response = $client->post('https://api-mb.dzmid.io.vn/mb', [
                'json' => $payload
            ]);

            $dataApi = json_decode($response->getBody(), true);
            $duLieu = $dataApi['data'] ?? [];

            foreach ($duLieu as $value) {
                $check = GiaoDich::where('refNo', $value['refNo'])->first();
                if ($check) continue;

                GiaoDich::create([
                    'creditAmount'  => $value['creditAmount'],
                    'description'   => $value['description'],
                    'refNo'         => $value['refNo'],
                ]);

                if (preg_match('/TTDP2/', $value['description']) && preg_match('/\d+/', $value['description'], $m)) {
                    $id_hoa_don = $m[0];

                    $hoaDon = HoaDon::join('khach_hangs', 'hoa_dons.id_khach_hang', '=', 'khach_hangs.id')
                        ->where('hoa_dons.id', $id_hoa_don)
                        ->select('khach_hangs.ho_lot', 'khach_hangs.ten', 'khach_hangs.email', 'hoa_dons.*')
                        ->first();

                    if (!$hoaDon) continue;

                    // KIỂM TRA SỐ TIỀN ĐỦ → TÍNH CẢ DỊCH VỤ
                    $dsDichVu = ChiTietDichVu::where('id_hoa_don', $hoaDon->id)->get();
                    $tong_tien_dich_vu = $dsDichVu->sum(fn($dv) => $dv->don_gia * $dv->so_luong);
                    $tong_tien_thuc_te = $hoaDon->tong_tien + $tong_tien_dich_vu;

                    if ($value['creditAmount'] >= $tong_tien_thuc_te) {
                        HoaDon::where('id', $id_hoa_don)->update(['is_thanh_toan' => 1]);
                        ChiTietThuePhong::where('id_hoa_don', $id_hoa_don)->update(['tinh_trang' => 3]);

                        // LẤY DANH SÁCH PHÒNG
                        $data = HoaDon::join('chi_tiet_thue_phongs', 'hoa_dons.id', '=', 'chi_tiet_thue_phongs.id_hoa_don')
                            ->join('phongs', 'chi_tiet_thue_phongs.id_phong', '=', 'phongs.id')
                            ->join('loai_phongs', 'phongs.id_loai_phong', '=', 'loai_phongs.id')
                            ->where('chi_tiet_thue_phongs.id_hoa_don', $hoaDon->id)
                            ->select(
                                'loai_phongs.hinh_anh',
                                'loai_phongs.ten_loai_phong',
                                'phongs.ten_phong',
                                'hoa_dons.ngay_den',
                                'hoa_dons.ngay_di',
                                DB::raw('SUM(chi_tiet_thue_phongs.gia_thue) as tong_gia_thue')
                            )
                            ->groupBy('loai_phongs.hinh_anh', 'loai_phongs.ten_loai_phong', 'phongs.ten_phong', 'hoa_dons.ngay_den', 'hoa_dons.ngay_di')
                            ->get();

                        // TÍNH LẠI TỔNG TIỀN ĐỂ GỬI MAIL
                        $tong_tien_phong = $data->sum('tong_gia_thue');
                        $tong_tien_thuc_te = $tong_tien_phong + $tong_tien_dich_vu;

                        $bien_1 = [
                            'ma_hoa_don'     => $hoaDon->ma_hoa_don,
                            'ten_nguoi_nhan' => $hoaDon->ho_lot . " " . $hoaDon->ten,
                            'tong_tien'      => $tong_tien_thuc_te,
                            'email'          => $hoaDon->email,
                        ];

                        Mail::to($bien_1['email'])->send(new ThanhToanHoaDonMail($bien_1, $data, $dsDichVu));

                        Log::info("ĐÃ GỬI EMAIL CHO HÓA ĐƠN ID: $id_hoa_don - TỔNG: $tong_tien_thuc_te");
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Lỗi MB Bank: ' . $e->getMessage());
        }
    }
}