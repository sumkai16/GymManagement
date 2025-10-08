<?php
/**
 * Sidebar Configuration
 * Centralized configuration for sidebar menus across different user roles
 */

return [
    'admin' => [
        'title' => 'Admin Dashboard',
        'welcome_text' => 'Welcome,',
        'items' => [
            [
                'icon' => 'bx-home-alt',
                'text' => 'Dashboard',
                'url' => 'admin_dashboard.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-user',
                'text' => 'Members',
                'url' => 'members.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-dumbbell',
                'text' => 'Trainers',
                'url' => 'trainers.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-calendar',
                'text' => 'Schedules',
                'url' => 'schedules.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-credit-card',
                'text' => 'Payments',
                'url' => 'payments.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-chart',
                'text' => 'Reports',
                'url' => 'reports.php',
                'permissions' => ['admin']
            ],
            [
                'icon' => 'bx-cog',
                'text' => 'Settings',
                'url' => 'settings.php',
                'permissions' => ['admin']
            ]
        ]
    ],
    
    'trainer' => [
        'title' => 'Trainer Dashboard',
        'welcome_text' => 'Welcome,',
        'items' => [
            [
                'icon' => 'bx-home-alt',
                'text' => 'Dashboard',
                'url' => 'trainers_dashboard.php',
                'permissions' => ['trainer']
            ],
            [
                'icon' => 'bx-user',
                'text' => 'My Members',
                'url' => 'my_members.php',
                'permissions' => ['trainer']
            ],
            [
                'icon' => 'bx-dumbbell',
                'text' => 'Workouts',
                'url' => 'workouts.php',
                'permissions' => ['trainer']
            ],
            [
                'icon' => 'bx-calendar',
                'text' => 'Schedule',
                'url' => 'schedule.php',
                'permissions' => ['trainer']
            ],
            [
                'icon' => 'bx-chart',
                'text' => 'Progress',
                'url' => 'progress.php',
                'permissions' => ['trainer']
            ],
            [
                'icon' => 'bx-message',
                'text' => 'Messages',
                'url' => 'messages.php',
                'permissions' => ['trainer']
            ]
        ]
    ],
    
    'member' => [
        'title' => 'Member Dashboard',
        'welcome_text' => 'Welcome,',
        'items' => [
            [
                'icon' => 'bx-home-alt',
                'text' => 'Dashboard',
                'url' => 'member_dashboard.php',
                'permissions' => ['member']
            ],
            [
                'icon' => 'bx-dumbbell',
                'text' => 'Workout',
                'type' => 'dropdown',
                'permissions' => ['member'],
                'submenu' => [
                    [
                        'text' => 'Track Workout',
                        'url' => 'workout.php',
                        'permissions' => ['member']
                    ],
                    [
                        'text' => 'Routines',
                        'url' => 'workout_routines.php',
                        'permissions' => ['member']
                    ]
                ]
            ],
            [
                'icon' => 'bx-bowl-rice',
                'text' => 'Nutrition',
                'type' => 'dropdown',
                'permissions' => ['member'],
                'submenu' => [
                    [
                        'text' => 'Food',
                        'url' => 'nutrition_food.php',
                        'permissions' => ['member']
                    ],
                    [
                        'text' => 'Supplement',
                        'url' => 'nutrition-supplement.php',
                        'permissions' => ['member']
                    ]
                ]
            ],
            [
                'icon' => 'bx-group',
                'text' => 'Coaches',
                'url' => 'coaches.php',
                'permissions' => ['member']
            ],
            [
                'icon' => 'bx-user',
                'text' => 'Profile',
                'url' => 'profile.php',
                'permissions' => ['member']
            ]
        ]
    ],
    
    'guest' => [
        'title' => 'Guest Dashboard',
        'welcome_text' => 'Welcome,',
        'items' => [
            [
                'icon' => 'bx-home-alt',
                'text' => 'Home',
                'url' => '#home',
                'permissions' => ['guest']
            ],
            [
                'icon' => 'bx-credit-card',
                'text' => 'Subscription Plans',
                'url' => '#subscription',
                'permissions' => ['guest']
            ],
            [
                'icon' => 'bx-dumbbell',
                'text' => 'Available Trainers',
                'url' => '#trainers',
                'permissions' => ['guest']
            ],
            [
                'icon' => 'bx-clipboard',
                'text' => 'Trial Workout Log',
                'url' => '#trial',
                'permissions' => ['guest']
            ],
            [
                'icon' => 'bx-phone',
                'text' => 'Contact & Support',
                'url' => '#contact',
                'permissions' => ['guest']
            ]
        ]
    ]
];
