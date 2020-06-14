<?php

use PHPUnit\Framework\TestCase;
use Lampion\Entity\EntityCreator;

class EntityCreatorTest extends TestCase {

    /** @test */
    public function testQueryStringCreate() {
        $ec = new EntityCreator();

        $config = [
            'Blog' => [
                'general' => [
                    'title' => 'Blog',
                    'icon' => 'blog.svg',
                    'description' => 'This is a test of some sorts...',
                    'permission' => ['ROLE_ADMIN', 'ROLE_AUTHOR']
                ],
                'fields' => [
                    'title' => [
                        'type' => 'varchar',
                        'metadata' => [
                            'length'   => 255,
                            'nullable' => false
                        ]
                    ],
                    'content' => [
                        'type' => 'text',
                        'metadata' => [
                            'nullable' => false
                        ]
                    ],
                    'bannerTest' => [
                        'type' => 'file',
                        'metadata' => [
                            'nullable' => false
                        ]
                    ],
                    'tags' => [
                        'type' => 'entity',
                        'metadata' => [
                            'nullable' => false,
                            'entity' => 'Carnival\\Entity\\Tag',
                            'mappedBy' => 'tags',
                            'multiple' => true
                        ]
                    ],
                    'user' => [
                        'type' => 'entity',
                        'metadata' => [
                            'nullable' => false,
                            'entity'   => 'Carnival\\Entity\\User',
                            'mappedBy' => 'user_id'
                        ]
                    ]
                ]
            ]
        ];

        /*
        $config = [
            'Tag' => [
                'general' => [
                    'title' => 'Tag',
                    'icon'  => 'text-document.svg',
                    'permission' => ['ROLE_ADMIN']
                ],
                'fields' => [
                    'title' => [
                        'type' => 'varchar',
                        'metadata' => [
                            'length' => 255,
                            'nullable' => false
                        ]
                    ],
                    'user' => [
                        'type' => 'entity',
                        'metadata' => [
                            'mappedBy' => 'user_id',
                            'entity' => 'Carnival\\Entity\\User',
                            'nullable' => false
                        ]
                    ]
                ]
            ]
        ];
        */

        $this->assertTrue($ec->create($config));
    }

}