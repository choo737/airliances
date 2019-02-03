<?php

use Illuminate\Database\Seeder;
use App\Area;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('areas')->delete();

        $areas = [ 
            new Area(['id' => 4723, 'name' => 'Kuala Lumpur', 'countrycode' => 'MY' ]),
            new Area(['id' => 16654, 'name' => 'Taiping', 'countrycode' => 'MY' ]),
            new Area(['id' => 19799, 'name' => 'Penang', 'countrycode' => 'MY' ]),
            new Area(['id' => 23340, 'name' => 'Ipoh', 'countrycode' => 'MY' ])
        ];

        foreach ($areas as $area) {
            $area->save();
        }
    }
}
