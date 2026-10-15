<?php

namespace App\Models;

use App\Core\Model;

class WorkoutClass extends Model
{
    /**
     * Get schedule of available group workouts and workshops
     */
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'time' => 'Today, 18:00',
                'title' => 'HYROX Athletic Conditioning',
                'coach' => 'Marek Kowalski (Olympic Specialist)',
                'zone' => 'Endurance & Hyrox Arena',
                'spots_left' => 4,
                'category' => 'Cardio & Strength'
            ],
            [
                'id' => 2,
                'time' => 'Tomorrow, 08:30',
                'title' => 'Mobility & Spine Decompression',
                'coach' => 'Kasia Dąbrowska (Lead Physio)',
                'zone' => 'Movement & Zen Suite',
                'spots_left' => 8,
                'category' => 'Recovery & Rehab'
            ],
            [
                'id' => 3,
                'time' => 'Tomorrow, 19:15',
                'title' => 'Biomechanical Heavy Iron Squat Workshop',
                'coach' => 'Dr. Elena Rostova',
                'zone' => 'Biomechanics Floor',
                'spots_left' => 2,
                'category' => 'Strength & Power'
            ],
            [
                'id' => 4,
                'time' => 'Thursday, 12:00',
                'title' => 'Power Cycle & Heart Rate HIIT',
                'coach' => 'Filip Nowak',
                'zone' => 'Cycle Theater',
                'spots_left' => 11,
                'category' => 'High Intensity'
            ]
        ];
    }
}
