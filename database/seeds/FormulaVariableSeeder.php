<?php

use Illuminate\Database\Seeder;

class FormulaVariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $variable = [
            [
                'variable' => 'absen',
                'persentase_nilai' => 30
            ],[
                'variable' => 'kinerja',
                'persentase_nilai' => 50
            ],[
                'variable' => 'etika',
                'persentase_nilai' => 20
            ],
        ];

        try {
            DB::table('formula_variable')->insert($variable);
        } catch (\Exception $exception){}
    }
}
