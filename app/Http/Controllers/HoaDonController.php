<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\ChiTietPhanQuyen;
use App\Models\ChiTietThuePhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class HoaDonController extends Controller
{
    public function timKiem(Request $request)
    {
        $id_chuc_nang   = 62;
        $user           = Auth::guard('sanctum')->user();
        $check          = ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'    => false,
                'message'   => 'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $noi_dung   = '%' . $request->noi_dung_tim . '%';

        $data   = HoaDon::where('ma_hoa_don', 'like', $noi_dung)
            ->orWhere('tong_tien', 'like', $noi_dung)
            ->orWhere('ngay_den', 'like', $noi_dung)
            ->orWhere('ngay_di', 'like', $noi_dung)
            ->get();

        return response()->json(['data' => $data]);
    }

    public function timKiemKhachHang(Request $request)
    {
        $noi_dung   = '%' . $request->noi_dung_tim . '%';
        $user       = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            $data = HoaDon::where(function ($query) use ($noi_dung) {
                $query->where('ma_hoa_don', 'like', $noi_dung)
                      ->orWhere('tong_tien', 'like', $noi_dung)
                      ->orWhere('ngay_den', 'like', $noi_dung)
                      ->orWhere('ngay_di', 'like', $noi_dung);
            })
            ->where('id_khach_hang', $user->id)
            ->get();

            return response()->json(['data' => $data]);
        }
    }

    // HÀM CHÍNH – ĐÃ SỬA HOÀN CHỈNH ĐỂ CÓ TIỀN DỊCH VỤ
    public function datPhong(Request $request)
{
    $khach_hang = Auth::guard('sanctum')->user();

    // 1. Tạo hoá đơn
    $hoaDon = HoaDon::create([
        'ma_hoa_don'    => Str::uuid(),
        'id_khach_hang' => $khach_hang->id,
        'ngay_den'      => $request->tt_dat_phong['ngay_den'],
        'ngay_di'       => $request->tt_dat_phong['ngay_di'],
        'tong_tien'     => 0
    ]);

    $tongTienPhong = 0;
    $ngay_den = Carbon::parse($request->tt_dat_phong['ngay_den']);
    $ngay_di  = Carbon::parse($request->tt_dat_phong['ngay_di']);
    $temp = $ngay_den->copy();

    // 2. ĐẶT PHÒNG – CHỈ UPDATE TỪ 1 → 2 (CÁCH TỐT NHẤT, KHÔNG SINH RÁC)
    while ($temp->lt($ngay_di)) {
        foreach ($request->tt_loai_phong ?? [] as $loai) {
            if (empty($loai['so_phong_dat']) || $loai['so_phong_dat'] <= 0) {
                continue;
            }

            $soPhongCanDat = (int)$loai['so_phong_dat'];
            $loaiPhongId   = $loai['id'];

            // Lấy ID chi_tiet_thue_phong của các phòng trống (tinh_trang = 1)
            $dsPhongTrongIds = ChiTietThuePhong::join('phongs', 'chi_tiet_thue_phongs.id_phong', '=', 'phongs.id')
                ->where('phongs.id_loai_phong', $loaiPhongId)
                ->whereDate('chi_tiet_thue_phongs.ngay_thue', $temp->toDateString())
                ->where('chi_tiet_thue_phongs.tinh_trang', 1)
                ->limit($soPhongCanDat)
                ->pluck('chi_tiet_thue_phongs.id');

            if ($dsPhongTrongIds->isNotEmpty()) {
                // Update trực tiếp từ trống → đặt cọc
                ChiTietThuePhong::whereIn('id', $dsPhongTrongIds)
                    ->update([
                        'tinh_trang' => 2,
                        'id_hoa_don' => $hoaDon->id
                    ]);

                // Tính tiền phòng
                $tongTienPhong += ChiTietThuePhong::whereIn('id', $dsPhongTrongIds)->sum('gia_thue');
            }
        }
        $temp->addDay();
    }

    // 3. DỊCH VỤ BỔ SUNG
    $tongTienDichVu = 0;
    $chiTietDichVu  = [];

    if ($request->has('ds_dich_vu') && is_array($request->ds_dich_vu)) {
        foreach ($request->ds_dich_vu as $dv) {
            $id_dich_vu = $dv['id'] ?? 0;
            $don_gia    = (int)($dv['don_gia'] ?? 0);
            if ($id_dich_vu > 0 && $don_gia > 0) {
                DB::table('dich_vu_hoa_don')->insert([
                    'id_hoa_don' => $hoaDon->id,
                    'id_dich_vu' => $id_dich_vu,
                    'so_luong'   => 1,
                    'thanh_tien' => $don_gia,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $ten_dv = DB::table('dich_vus')->where('id', $id_dich_vu)->value('ten_dich_vu') ?? 'Dịch vụ';

                $chiTietDichVu[] = [
                    'ten'        => $ten_dv,
                    'so_luong'   => 1,
                    'don_gia'    => $don_gia,
                    'thanh_tien' => $don_gia
                ];
                $tongTienDichVu += $don_gia;
            }
        }
    }

    // 4. CẬP NHẬT TỔNG TIỀN
    $hoaDon->tong_tien = $tongTienPhong + $tongTienDichVu;
    $hoaDon->save();

    // 5. GỬI MAIL XÁC NHẬN
    $qrCodeUrl = null;
    try {
        $potentialQr = "https://img.vietqr.io/image/MB-1700116117118-compact.jpg?amount={$hoaDon->tong_tien}&addInfo=TTDP{$hoaDon->id}";
        if (strlen($potentialQr) < 2000) {
            $qrCodeUrl = $potentialQr;
        }
    } catch (\Throwable $e) {
        Log::warning('QR bị lỗi cho hóa đơn ' . $hoaDon->id);
    }

    $mailData = [
        'ho_va_ten'        => trim($khach_hang->ho_lot . ' ' . $khach_hang->ten),
        'tu_ngay'          => Carbon::parse($hoaDon->ngay_den)->format('d/m/Y'),
        'den_ngay'         => Carbon::parse($hoaDon->ngay_di)->format('d/m/Y'),
        'so_dem'           => $ngay_den->diffInDays($ngay_di),
        'tien_phong'       => $tongTienPhong,
        'tien_dich_vu'     => $tongTienDichVu,
        'chi_tiet_dich_vu' => $chiTietDichVu,
        'tong_tien'        => $hoaDon->tong_tien,
        'ma_qr_code'       => $qrCodeUrl,
    ];

    try {
        Mail::send('xac_nhan_don_hang', $mailData, function ($message) use ($khach_hang) {
            $message->to($khach_hang->email)->subject('XÁC NHẬN ĐẶT PHÒNG THÀNH CÔNG');
        });
    } catch (\Exception $e) {
        Log::error('Lỗi gửi mail hóa đơn ' . $hoaDon->id . ': ' . $e->getMessage());
    }

    return response()->json([
        'status'  => true,
        'message' => 'Đặt phòng thành công! Vui lòng kiểm tra email.'
    ]);
}
    public function getData()
    {
        $id_chuc_nang = 59;
        $user = Auth::guard('sanctum')->user();
        $check = ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $data = HoaDon::join('khach_hangs', 'hoa_dons.id_khach_hang', 'khach_hangs.id')
            ->select('hoa_dons.*', 'khach_hangs.ho_lot', 'khach_hangs.ten')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function getDataKhachHang()
    {
        $user = Auth::guard('sanctum')->user();
        $data = HoaDon::join('khach_hangs', 'hoa_dons.id_khach_hang', 'khach_hangs.id')
            ->where('khach_hangs.id', $user->id)
            ->select('hoa_dons.*', 'khach_hangs.ho_lot', 'khach_hangs.ten')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function chiTietThue(Request $request)
    {
        $id_chuc_nang = 60;
        $user = Auth::guard('sanctum')->user();
        $check = ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $id_hoa_don = $request->id;

        $data = ChiTietThuePhong::where('id_hoa_don', $id_hoa_don)
            ->orderBy('ngay_thue')
            ->join('phongs', 'chi_tiet_thue_phongs.id_phong', 'phongs.id')
            ->join('loai_phongs', 'phongs.id_loai_phong', 'loai_phongs.id')
            ->select('chi_tiet_thue_phongs.*', 'loai_phongs.ten_loai_phong', 'phongs.ten_phong')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function chiTietThueKhachHang(Request $request)
    {
        $id_hoa_don = $request->id;

        $data = ChiTietThuePhong::where('id_hoa_don', $id_hoa_don)
            ->orderBy('ngay_thue')
            ->join('phongs', 'chi_tiet_thue_phongs.id_phong', 'phongs.id')
            ->join('loai_phongs', 'phongs.id_loai_phong', 'loai_phongs.id')
            ->select('chi_tiet_thue_phongs.*', 'loai_phongs.ten_loai_phong', 'phongs.ten_phong')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function xacNhanDonHang(Request $request)
    {
        $id_chuc_nang = 61;
        $user = Auth::guard('sanctum')->user();
        $check = ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        if ($request->thanh_toan ?? false) {
            HoaDon::where('id', $request->id_hoa_don)->update(['is_thanh_toan' => 1]);
            ChiTietThuePhong::where('id_hoa_don', $request->id_hoa_don)->update(['tinh_trang' => 3]);
        } else {
            HoaDon::where('id', $request->id_hoa_don)->update(['is_thanh_toan' => -1]);
            ChiTietThuePhong::where('id_hoa_don', $request->id_hoa_don)->update([
                'tinh_trang' => 1,
                'id_hoa_don' => null
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đã xử lý đơn hàng thành công!',
        ]);
    }

    public function thongKe1()
    {
        $id_chuc_nang   = 63;
        $user   =  Auth::guard('sanctum')->user();
        $check  =   ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $data = HoaDon::select(
            DB::raw("SUM(tong_tien) as tong_tien_theo_ngay"),
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as ngay_tao"),
        )
            ->groupBy('ngay_tao')
            ->orderBy('created_at')
            ->get();
        $list_ngay          = [];
        $list_tong_tien     = [];

        foreach ($data as $key => $value) {
            array_push($list_ngay, $value->ngay_tao);
            array_push($list_tong_tien, $value->tong_tien_theo_ngay);
        }

        return response()->json([
            'list_ngay' => $list_ngay,
            'list_tong_tien'  => $list_tong_tien,
        ]);
    }

    public function thongKe2()
    {
        $id_chuc_nang   = 64;
        $user   =  Auth::guard('sanctum')->user();
        $check  =   ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $data = LoaiPhong::join('phongs', 'phongs.id_loai_phong', 'loai_phongs.id')
            ->join('chi_tiet_thue_phongs', 'chi_tiet_thue_phongs.id_phong', 'phongs.id')
            ->select(
                DB::raw("COUNT(chi_tiet_thue_phongs.id) as so_luong_phong"),
                'loai_phongs.ten_loai_phong'
            )
            ->where('chi_tiet_thue_phongs.tinh_trang', 2)
            ->groupBy('loai_phongs.ten_loai_phong')
            ->get();
        $list_ten          = [];
        $list_so_luong     = [];

        foreach ($data as $key => $value) {
            array_push($list_ten, $value->ten_loai_phong);
            array_push($list_so_luong, $value->so_luong_phong);
        }

        return response()->json([
            'list_ten' => $list_ten,
            'list_so_luong'  => $list_so_luong,
        ]);
    }

    public function thongKe3()
    {
        $id_chuc_nang   = 65;
        $user   =  Auth::guard('sanctum')->user();
        $check  =   ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $data = KhachHang::leftJoin('hoa_dons', 'hoa_dons.id_khach_hang', 'khach_hangs.id')
            ->select(
                DB::raw("SUM(IF(hoa_dons.is_thanh_toan = 1, hoa_dons.tong_tien, 0)) as tong_tien_da_thanh_toan"),
                'khach_hangs.ten',
                'khach_hangs.ho_lot',
            )
            ->where('hoa_dons.is_thanh_toan', 1)
            ->groupBy('khach_hangs.ten', 'khach_hangs.ho_lot')
            ->get();
        $list_ten           = [];
        $list_tong_tien     = [];

        foreach ($data as $key => $value) {
            array_push($list_ten, $value->ho_lot . ' ' . $value->ten);
            array_push($list_tong_tien, $value->tong_tien_da_thanh_toan);
        }

        return response()->json([
            'list_ten' => $list_ten,
            'list_tong_tien'  => $list_tong_tien,
        ]);
    }

    public function thongKe4()
    {
        $id_chuc_nang   = 66;
        $user   =  Auth::guard('sanctum')->user();
        $check  =   ChiTietPhanQuyen::where('id_quyen', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check) {
            return response()->json([
                'status'    =>  false,
                'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
            ]);
        }

        $data = KhachHang::leftJoin('hoa_dons', 'hoa_dons.id_khach_hang', 'khach_hangs.id')
            ->join('chi_tiet_thue_phongs', 'chi_tiet_thue_phongs.id_hoa_don', 'hoa_dons.id')
            ->select(
                DB::raw("COUNT(chi_tiet_thue_phongs.id) as so_luong_thue"),
                'khach_hangs.ten',
                'khach_hangs.ho_lot',
            )
            ->groupBy('khach_hangs.ten', 'khach_hangs.ho_lot')
            ->get();
        $list_ten           = [];
        $list_so_luong      = [];

        foreach ($data as $key => $value) {
            array_push($list_ten, $value->ho_lot . ' ' . $value->ten);
            array_push($list_so_luong, $value->so_luong_thue);
        }

        return response()->json([
            'list_ten' => $list_ten,
            'list_so_luong'  => $list_so_luong,
        ]);
    }
    // API mới – lấy đầy đủ chi tiết: phòng + dịch vụ
    public function chiTietHoaDon($id)
    {
        $hoaDon = HoaDon::with('khachHang')->findOrFail($id);

        $phong = DB::table('chi_tiet_thue_phongs as ct')
            ->join('phongs as p', 'ct.id_phong', '=', 'p.id')
            ->join('loai_phongs as lp', 'p.id_loai_phong', '=', 'lp.id')
            ->where('ct.id_hoa_don', $id)
            ->select(
                'ct.ngay_thue',
                'ct.gia_thue',
                'p.ten_phong',
                'lp.ten_loai_phong'
            )
            ->orderBy('ct.ngay_thue')
            ->get();

        $dichVu = DB::table('dich_vu_hoa_don')
            ->join('dich_vus', 'dich_vu_hoa_don.id_dich_vu', '=', 'dich_vus.id')
            ->where('dich_vu_hoa_don.id_hoa_don', $id)
            ->select('dich_vus.ten_dich_vu', 'dich_vu_hoa_don.thanh_tien', 'dich_vu_hoa_don.so_luong')
            ->get();

        return response()->json([
            'hoa_don'           => $hoaDon,
            'phong'             => $phong,
            'dich_vu'           => $dichVu,
            'tong_tien_phong'   => $phong->sum('gia_thue'),
            'tong_tien_dich_vu' => $dichVu->sum('thanh_tien'),
        ]);
    }
public function layDichVuHoaDon($id_hoa_don)
{
    $data = DB::table('dich_vu_hoa_don')
        ->join('dich_vus', 'dich_vu_hoa_don.id_dich_vu', '=', 'dich_vus.id')
        ->where('dich_vu_hoa_don.id_hoa_don', $id_hoa_don)
        ->select('dich_vus.ten_dich_vu', 'dich_vu_hoa_don.thanh_tien')
        ->get();

    return response()->json([
        'data' => $data
    ]);
}
public function chiTietFull($id)
{
    // LẤY CHI TIẾT PHÒNG
    $phong = ChiTietThuePhong::where('id_hoa_don', $id)
        ->join('phongs', 'phongs.id', 'chi_tiet_thue_phongs.id_phong')
        ->join('loai_phongs', 'loai_phongs.id', 'phongs.id_loai_phong')
        ->select(
            'chi_tiet_thue_phongs.*',
            'phongs.ten_phong',
            'loai_phongs.ten_loai_phong',

            // LẤY GIÁ THEO NGÀY (ĐÚNG VỚI DB CỦA BẠN)
            DB::raw('loai_phongs.don_gia AS gia_thue')
        )
        ->get();

    // LẤY CHI TIẾT DỊCH VỤ
    $dich_vu = DB::table('chi_tiet_su_dung_dich_vus')
        ->where('chi_tiet_su_dung_dich_vus.id_hoa_don', $id)
        ->join('dich_vus', 'dich_vus.id', 'chi_tiet_su_dung_dich_vus.id_dich_vu')
        ->select(
            'chi_tiet_su_dung_dich_vus.*',
            'dich_vus.ten_dich_vu',
            'chi_tiet_su_dung_dich_vus.thanh_tien'
        )
        ->get();

    return response()->json([
        'phong'   => $phong,
        'dich_vu' => $dich_vu,
    ]);
}

public function layDichVuTheoHoaDon($id)
{
    $data = ChiTietThuePhong::where('chi_tiet_thue_phongs.id_hoa_don', $id)
        ->join('dich_vu_khachs', 'dich_vu_khachs.id_ct_thue_phong', 'chi_tiet_thue_phongs.id')
        ->join('dich_vus', 'dich_vus.id', 'dich_vu_khachs.id_dich_vu')
        ->select(
            'dich_vu_khachs.*',
            'dich_vus.ten_dich_vu',
            'dich_vu_khachs.thanh_tien'
        )
        ->get();

    return response()->json([
        'status' => true,
        'dich_vu' => $data
    ]);
}

// LẤY DANH SÁCH PHÒNG THEO HOÁ ĐƠN
public function layPhongTheoHoaDon($id)
{
    $data = ChiTietThuePhong::where('chi_tiet_thue_phongs.id_hoa_don', $id)
        ->join('phongs', 'phongs.id', 'chi_tiet_thue_phongs.id_phong')
        ->join('loai_phongs', 'loai_phongs.id', 'phongs.id_loai_phong')
        ->select(
            'chi_tiet_thue_phongs.*',
            'phongs.ten_phong',
            'loai_phongs.ten_loai_phong',
            'phongs.gia as gia_thue'
        )
        ->get();

    return response()->json([
        'status' => true,
        'phong'  => $data
    ]);
}
}
