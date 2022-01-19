<?php

namespace Uniqoders\Game\Config;

class Config
{
    // Valor de rondas por defecto
    CONST MAXIMO_RONDAS = 5;
    CONST RONDAS_GANAR  = 3;

    // Funcion para obtener armas
    public function weapons($select)
    {
        switch ($select) {
            case 'Normal':
                return [
                    0 => 'Tijeras',
                    1 => 'Piedra',
                    2 => 'Papel'
                ];
            case 'Big Bang Teory':
                return [
                    0 => 'Tijeras',
                    1 => 'Piedra',
                    2 => 'Papel',
                    3 => 'Lagarto',
                    4 => 'Spok',
                ];
        }
    }

    // Funcion para obtener reglas
    public function rules($select)
    {
        switch ($select) {
            case 'Normal':
                return [
                    0 => [2],
                    1 => [0],
                    2 => [1],
                ];

            case 'Big Bang Teory':
                return [
                    0 => [2, 3],
                    1 => [0, 3],
                    2 => [1, 4],
                    3 => [2, 4],
                    4 => [0, 1],
                ];
        }
    }

    public function players($player_name){
        return [
            'player' => [
                'name' => $player_name,
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ],
            'computer' => [
                'name' => 'Computer',
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ]
        ];
    }

    public function gameType(){
        return [
            1 => 'Normal',
            2 => 'Big Bang Teory'
        ];
    }

    public function getRondas(){
        for($i=1;  $i<=self::MAXIMO_RONDAS; $i++){
            $rondas[$i] = $i;
        }
        return $rondas;
    }
}