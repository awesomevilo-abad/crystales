<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(DB::table('companies')->count() == 0){

            DB::table('companies')->insert([

                [
                    "company_code" => "00",
                    "company_description" => "Red Dragon Group",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "10",
                    "company_description" => "RDF Corporate Services",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],


                [
                    "company_code" => "20",
                    "company_description" => "Animal Production",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "21",
                    "company_description" => "Lodestar Feedmill & Veterinary Medicines",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "22",
                    "company_description" => "Red Dragon Farm",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "23",
                    "company_description" => "E-Pig Farms",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "30",
                    "company_description" => "RDF Meatshop, Inc.",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    "company_code" => "31",
                    "company_description" => "Fresh Options",
                    "is_active" => 1,
                    "created_at"=>Carbon::now()->format('Y-m-d H:i:s'),
                    "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
                ]

            ]);

        } else { echo "Table is not empty, therefore NOT "; }
    }
}
