<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testVideoIndex()
    {
    	$category = $this->createCategory();
    	$this->visit('/videos')
    		->seePageIs('videos');
    		// ->see('MsDotNet');
    }

    public function testVideoShowWithoutAuthUser()
    {
    	$this->createVideo();
    	$this->visit('videos/1')
                // ->see('roy')
    			// ->see('A Introduction to MsDotNet')
    			;	
        // $this->countElements('.video-user', 1);
    }

    public function testAddVideoLinkFailsForNoAuth()
    {
        $page = $this->visit('videos/add')
                    ->seePageIs('login')
                    ;
    }

    public function testAddVideoSucceeds()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $page = $this->actingAs($user)
                    ->visit('videos/add')
                    ->seePageIs('videos/add')
                    ->type('A new title', 'title')
                    ->type('https://www.youtube.com/watch?v=3oT9PQcFZKc', 'url')
                    ->type('A new description', 'description')
                    ->select(1, 'category_id')
                    ->press('Add')
                    ->seePageIs('dashboard')
                    // ->see('A new title')
                    ;
    }
}
