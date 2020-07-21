<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\User;

class CouponController extends Controller
{
    /**
     * クーポン一覧
     */
    public function list($mode)
    {
        if ($mode === 'manager') {
            $coupon = Coupon::all();

            foreach ($coupon as $item) {
                $item['distributuion_count'] = count($item->users);
            }
        } elseif ($mode === 'user') {
            $coupon = User::find(session('id'))->coupons;
        } else {
            // 
        }

        return view('coupon/couponlist')->with([
            'mode' => $mode,
            'data' => $coupon,
        ]);
    }

    /**
     * クーポン作成確認
     */
    public function confirm(Request $request)
    {
        $searchResult = User::search($request);
        return view('coupon/confirm')->with([
            'input'=> $request->all(),
            'userlist' => $searchResult['userlist'],
        ]);
    }

    /**
     * クーポン作成実行
     */
    public function store(Request $request)
    {
        try {
            $coupon = new Coupon();
            $coupon->title = $request->title;
            $coupon->content = $request->content;
            $coupon->expiration_date = $request->expiration_date;
            $coupon->save();
            $coupon->users()->sync($request->user_id);

            return redirect('/manager/coupon/')->with('message', 'クーポンを新規作成しました');
        } catch (\Throwable $th) {
            dump($th);
            return;
        }
    }
}
