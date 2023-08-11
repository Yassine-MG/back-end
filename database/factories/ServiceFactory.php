<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Freelancer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $categories = ['Developer', 'Data', 'Writing & Translation', 'Business', 'Video & Animation', 'Designer'];

        $deliveryTimes = [
            '1 Day Delivery',
            '2 Days Delivery',
            '3 Days Delivery',
            '4 Days Delivery',
            '5 Days Delivery',
            '6 Days Delivery',
            '7 Days Delivery',
            '8 Days Delivery',
            '9 Days Delivery',
            '10 Days Delivery',
            '11 Days Delivery',
            '12 Days Delivery',
            '13 Days Delivery',
            '14 Days Delivery',
            '21 Days Delivery',
            '30 Days Delivery'
        ];

        $freelancer = Freelancer::inRandomOrder()->first();

        if (!$freelancer) {
            $freelancer = Freelancer::factory()->create();
        }
        return [
            'title' => $this->faker->name(),
            'description' => $this->faker->text(),
            'like'=>$this->faker->numberBetween(1, 300),
            'details' => $this->faker->text(),
            'offer_name' => $this->faker->name(),
            'image1' => Storage::url($this->getRandomImageFromFolder('public/media/pictures')),
            'image2' => Storage::url($this->getRandomImageFromFolder('public/media/pictures')),
            'image3' => Storage::url($this->getRandomImageFromFolder('public/media/pictures')),
            'video' => Storage::url($this->getRandomVideoFromFolder('public/media/videos')),
            'price' => $this->faker->randomFloat(2, 0, 999),
            'category' => $this->faker->randomElement($categories),
            'skills' => json_encode(['skill1', 'skill2']), // Replace ['skill1', 'skill2'] with your desired skills array
            'delevery' => $this->faker->randomElement($deliveryTimes),
            'tags' => $this->faker->name(),
            'dynamic_inputs' => json_encode(['input1' => 'value1', 'input2' => 'value2']), // Replace ['input1' => 'value1', 'input2' => 'value2'] with your desired dynamic inputs
            'freelancer_id' => function () use ($freelancer) {
                return $freelancer->id;
            },
        ];
    }

/**
 * Get a random image file path from the specified folder.
 *
 * @param string $folder
 * @return string|null
 */
protected function getRandomImageFromFolder($folder)
{
    $files = Storage::files($folder);
    return count($files) > 0 ? str_replace('public/', '', $files[array_rand($files)]) : null;
}

/**
 * Get a random video file path from the specified folder.
 *
 * @param string $folder
 * @return string|null
 */
protected function getRandomVideoFromFolder($folder)
{
    $files = Storage::files($folder);
    $videoFiles = preg_grep('/\.(mp4|mov|avi|wmv)$/i', $files);
    return count($videoFiles) > 0 ? str_replace('public/', '', $videoFiles[array_rand($videoFiles)]) : null;
}
}