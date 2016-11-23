<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WebPagesTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $non_admin;

    public function setUp(){
        parent::setUp();
        $this->admin = User::where('is_admin',1)->first();
        $this->non_admin = User::where('is_admin',0)->first();
    }

    /**
     * @test
     * @group webPages
     */
    public function landing_page(){
        $this->visit('/')
            ->see('Login')
            ->see('Register')
            ->see('Application Screenshot')
            ->click('Login')
            ->seePageIs('/login')
            ->visit('/')
            ->click('Register')
            ->seePageIs('/register');
    }

    /**
     * @test
     * @group webPages
     */
    public function login_home_manage_logout(){
        $this->visit('/login')
                ->see('E-mail')
                ->see('Password')
                ->type('user1@gmail.com','email')
                ->type('user1pwd','password')
                ->press('Login')
                ->seePageIs('/home')
                ->see('Home')
                ->see('Alexandros Gougousis')
                ->see('Search')
                ->click('Logout')
                ->seePageIs('/');
    }

    /**
     * @test
     * @group webPages
     */
    public function manage_page(){
        $this->be($this->admin);

        $this->visit('/manage')
                ->see('Build tree')
                ->see('Add name')
                ->see('Seeding')
                ->see('Clear cache')
                ->see('Instructions');
    }

    /**
     * @test
     * @group webPages
     */
    public function registration(){
        $this->visit('/register')
                ->type('Antonis','firstname')
                ->type('Toskas','lastname')
                ->type('toskas@yahoo.com','email')
                ->type('klonos8!!','password')
                ->type('klonos8!!','password_confirmation')
                ->press('Register')
                ->seePageIs('/home')
                ->see('Antonis Toskas');
    }

}