<?php

return [

    /*
    | Statutory weekly obligation (hours, "équivalent cours") that a permanent
    | teacher must teach to be paid. 192h / 24 weeks = 8h.
    */
    'weekly_obligation_hours' => 8,

    /*
    | Maximum paid hours (équivalent cours) per week and contract type.
    | Permanent: 8h obligation + 12h supplementary = 20h. Hours above the cap are not paid.
    */
    'weekly_max_hours' => [
        'permanent' => 20,
        'vacataire' => 12,
    ],

    /*
    | ISO weekdays (1 = Monday ... 7 = Sunday) that never count as teaching days.
    */
    'excluded_iso_weekdays' => [5],

];
