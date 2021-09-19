<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Rules\ValidProtocol;

class SiteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_create_sites()
    {
        $this->withoutExceptionHandling();

        // Create an user 
        $user = User::factory()->create();

        // make a post request to a route to create a site 
        $response = $this
            ->followingRedirects() 
            ->actingAs($user)
            ->post(route('sites.store'),
            [
                'name' => 'Google',
                'url' => 'https://google.com',
            ]);

        // make sure the site exists within the database 
        $site = Site::first();

        $this->assertEquals(1, Site::count());
        $this->assertEquals('Google', $site->name);
        $this->assertEquals('https://google.com', $site->url);
        $this->assertNull($site->is_online);
        $this->assertEquals($user->id, $site->user->id);

        // see site's name on the page 
        $response->assertSeeText('Google');
        $this->assertEquals(route('sites.show', $site), url()->current());
    }

    /** @test */
    public function it_requires_all_fields_to_be_present()
    {

        // Create an user 
        $user = User::factory()->create();

        // make a post request to a route to create a site 
        $response = $this
            ->actingAs($user)
            ->post(route('sites.store'),
            [
                'name' => '',
                'url' => '',
            ]);

        // make sure no site exists within the database
        $this->assertEquals(0, Site::count());
       
        $response->assertSessionHasErrors(['name', 'url']);
    }

    /** @test */
    public function it_requires_the_url_to_have_a_valid_protocol()
    {
         // Create an user 
         $user = User::factory()->create();

         // make a post request to a route to create a site 
         $response = $this
             ->actingAs($user)
             ->post(route('sites.store'),
             [
                 'name' => 'Google',
                 'url' => 'google.com',
             ]);
 
         // make sure no site exists within the database
         $this->assertEquals(0, Site::count());
        
         $response->assertSessionHasErrors(['url']);
    }

     /**
     * @test
     */
    public function it_only_allows_authenticated_users_to_create_sites()
    {

        // make a post request to a route to create a site 
        $response = $this
            ->followingRedirects()
            ->post(route('sites.store'),
            [
                'name' => 'Google',
                'url' => 'https://google.com',
            ]);

        // make sure no site exists in the db 
        $site = Site::first();

        $this->assertEquals(0, Site::count());
      
        // see site's name on the page 
        $response->assertSeeText('Login');
        $this->assertEquals(route('login', $site), url()->current());
    }
}
