<?php

use Carnival\Entity\Article;
use Lampion\Entity\EntityManager;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase {

    public $em;

    public function __construct() {
        $this->em = new EntityManager();
    }
    
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function findByReturnsFalseWhenNothingIsFound() {
        $articles = $this->em->findBy(Article::class, [
            'title' => 'Lorem Ipsum'
        ]);

        $this->assertCount($articles, 0);
    }

}