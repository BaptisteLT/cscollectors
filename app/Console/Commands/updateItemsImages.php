<?php

namespace App\Console\Commands;

use App\Models\Skin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class updateItemsImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-items-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the items images, run this command by using "php artisan app:update-items-images"';

    /**
     * Execute the console command.
     */
    public function handle(Http $http)
    {
        $response = $http::get('https://bymykel.github.io/CSGO-API/api/en/skins.json');
        $data = json_decode($response);

        foreach($data as $item)
        {
            DB::table('skins')
            ->where('name', $item->name)
            ->update(['image' => $item->image]); // Replace 'your_column' and 'new_value' with the actual column and value you want to update
        }

        echo 'Finished updating images';
    }
}
