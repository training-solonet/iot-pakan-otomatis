<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'api';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // jadwal waktu pakan otomatis
        $schedule = [
            [
                'start' => '08:00:00',
                'end'   => '08:05:00',
                'delay' => '1000'
            ],
            [
                'start' => '16:00:00',
                'end'   => '16:05:00',
                'delay' => '1000'
            ],
        ];

        // if current time is in schedule
        foreach ($schedule as $value) {
            if (now()->between(now()->setTimeFromTimeString($value['start']), now()->setTimeFromTimeString($value['end']))) {
                // check if already logged between start and end
                if (Log::whereBetween('timestamp', [now()->setTimeFromTimeString($value['start']), now()->setTimeFromTimeString($value['end'])])->exists()) {
                    // return response
                    return response()->json([
                        'status'    => false,
                        'delay'     => '0',
                    ]);
                }else{
                    // save to log
                    Log::create([
                        'timestamp' => now(),
                        'delay'     => $value['delay'],
                    ]);

                    // send notification to discord
                    Http::post('https://discordapp.com/api/webhooks/1028152947017777212/nHg5K5p2XPQMqguUW-6E-mZgKFnI0Qc03JxoYz38R7UVcU1CcXMhH1f8Ou57mVFU6kyi', [
                        'content' => "IOT-PAKAN-OTOMATIS",
                        'embeds' => [
                            [
                                'title'         => 'INFO',
                                'description'   => 'Pemberian pakan otomatis telah dilakukan',
                                'color'         => '7506394',
                                'fields'        => [
                                    [
                                        'name'  => 'Waktu',
                                        'value' => now()->format('d-m-Y H:i:s'),
                                    ],
                                    [
                                        'name'  => 'Delay',
                                        'value' => $value['delay'],
                                    ],
                                ],
                            ]
                        ],
                    ]);

                    // return response
                    return response()->json([
                        'status'    => true,
                        'delay'     => $value['delay'],
                    ]);
                }
            }
        }

        // return response
        return response()->json([
            'status'    => false,
            'delay'     => '0',
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
