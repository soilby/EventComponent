<?php

return [
    'service_manager' => [
        'factories' => [
            'Soilby\EventComponent\Service\EventLogger' => 'Soilby\EventComponent\Service\EventLoggerFactory',
            'Soilby\EventComponent\Service\GearmanClient' => 'Soilby\EventComponent\Service\GearmanClientFactory'
        ]
    ]
];