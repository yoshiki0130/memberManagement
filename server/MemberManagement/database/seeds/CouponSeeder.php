<?php

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        factory(Coupon::class, 10)->create()
            ->each(function ($coupon) use ($users) {
                $coupon->users()->sync(
                    // couponに3～6個のuser（DBに登録されている）を紐付け
                    $users->random(rand(3, 6))->pluck('id')->toArray()
                );
            });
    }
}
