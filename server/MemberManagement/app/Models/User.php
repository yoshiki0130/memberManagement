<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Gender;
use App\Models\Prefecture;

class User extends Model
{
    public function prefecture()
    {
        return $this->belongsTo('App\Models\Prefecture');
    }

    public function gender()
    {
        return $this->belongsTo('App\Models\Gender');
    }

    public function coupons()
    {
        return $this->belongsToMany('App\Models\Coupon');
    }

    /**
     * 会員検索
     * 
     * リクエストそのまま受け取って検索結果配列と検索条件を返す
     */
    public static function search(Request $request)
    {
        $query = User::query();
        $searchCriteria = $request->only([
            'name1', 'name2', 'gender_id', 'prefecture_id', 'min', 'max'
        ]);

        foreach (array_intersect_key($searchCriteria, array_flip(['name1', 'name2'])) as $k => $v) {
            if (is_null($v)) continue;
            $query->where($k, 'like', '%' . $v . '%');
        }
        foreach (array_intersect_key($searchCriteria, array_flip(['gender_id', 'prefecture_id'])) as $k => $v) {
            if (is_null($v)) continue;
            $query->where($k, $v);
        }
        if (isset($searchCriteria['min']) && !is_null($searchCriteria['min'])) {
            $query->where('created_at', '>=', $searchCriteria['min']);
        }
        if (isset($searchCriteria['max']) && !is_null($searchCriteria['max'])) {
            $query->where('created_at', '<=', $searchCriteria['max']);
        }

        $userlist = $query->get();

        return compact('userlist', 'searchCriteria');
    }

    /**
     * グラフ用
     * 
     */
    public static function getChartData()
    {
        $data = [];
        $data['alluser'] = User::all()->count();
        // 性別ごとの人数
        $data['genders']['counts']  = User::select('gender_id', DB::raw('count(gender_id) as user_count'))
            ->groupBy('gender_id')
            ->get()
            ->toArray();
        $data['genders']['counts'] = array_column($data['genders']['counts'], 'user_count');
        $data['genders']['labels'] = array_column(Gender::select('name')->get()->toArray(), 'name');

        // 住所（都道府県）ごとの人数
        $users_each_prefectures = User::select(DB::raw('count(prefecture_id) as user_count'), 'prefecture_id')
            ->groupBy('prefecture_id')
            ->get()
            ->toArray();
        $users_each_prefectures = array_column($users_each_prefectures, 'user_count', 'prefecture_id');
        $data['prefectures']['labels'] = array_column(Prefecture::select('name')->get()->toArray(), 'name');

        // 人数0の都道府県はキーが存在しないのでデータを足す
        for ($i = 0; $i < Prefecture::all()->count(); $i++) {
            if (isset($users_each_prefectures[$i+1])) {
                $data['prefectures']['counts'][$i] = $users_each_prefectures[$i+1];
            } else {
                $data['prefectures']['counts'][$i] = 0;
            }
        }

        // 年数ごとの登録期間→棒

        return $data;
    }
}
