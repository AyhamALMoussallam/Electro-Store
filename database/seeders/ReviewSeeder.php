<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 0)->get();

        if ($users->isEmpty()) {
            return;
        }

        $comments = [
            'Excellent product, exactly as described.',
            'Fast delivery and great packaging.',
            'Good value for money. Would buy again.',
            'Works perfectly. Very satisfied.',
            'Solid build quality and performance.',
            'Better than I expected for this price.',
            'Easy to set up and use every day.',
            'Recommended for anyone upgrading their setup.',
        ];

        Product::withCount('review')
            ->orderBy('id')
            ->chunk(50, function ($products) use ($users, $comments) {
                foreach ($products as $product) {
                    $target = fake()->numberBetween(3, 5);
                    $needed = max(0, $target - $product->review_count);

                    if ($needed === 0) {
                        continue;
                    }

                    $usedUserIds = Review::where('product_id', $product->id)
                        ->pluck('user_id')
                        ->all();

                    for ($i = 0; $i < $needed; $i++) {
                        $available = $users->whereNotIn('id', $usedUserIds);

                        if ($available->isEmpty()) {
                            $available = $users;
                        }

                        $user = $available->random();

                        Review::create([
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'rating' => fake()->numberBetween(3, 5),
                            'comment' => fake()->randomElement($comments),
                        ]);

                        $usedUserIds[] = $user->id;
                    }
                }
            });
    }
}
