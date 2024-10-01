<?php

use App\Services\MeetingProvider\Zoom\ZoomService;
return [
    'default_gateway' => 'zoom',
    'providers' => [
        [
            'name' => 'zoom',
            'company_url' => 'https://zoom.us/',
            'baseClass' => ZoomService::class,
            'is_active' => true,
            'fields' => [
                'account_id' => [
                    'type' => 'text',
                    'required' => true,
                    'secret' => false,
                ],
                'client_id' => [
                    'type' => 'text',
                    'secret' => false,
                    'required' => true,
                ],
                'client_secret' => [
                    'type' => 'text',
                    'secret' => true,
                    'required' => true,
                ],
            ]
        ],
        [
            'name' => 'Agora',
            'is_active' => false,
            'company_url' => 'https://docs.agora.io/en/cloud-recording',
            'fields' => [
                'app_id' => [
                    'type' => 'text',
                    'secret' => false,
                ],
                'app_certificate' => [
                    'type' => 'text',
                    'secret' => true,
                ]
            ]
        ],
        [
            'name' => 'Jitsi',
            'company_url' => 'https://jitsi.org/',
            'is_active' => false,
            'fields' => [
                'api_key',
                'api_secret'
            ]
        ],
        [
            'name' => 'Webex',
            'is_active' => false,
            'fields' => [
                'webex_token'
            ]
        ],
        [
            'name' => 'BigBlueButton',
            'is_active' => false,
            'fields' => [
                'api_key',
                'api_secret'
            ]
        ]
    ]
];