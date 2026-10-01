<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_homepage(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_page(): void
    {
        $response = $this->get('/login-page');
        $response->assertStatus(200);
    }

    public function test_register_page(): void
    {
        $response = $this->get('/register-page');
        $response->assertStatus(200);
    }

    public function test_provider_register(): void
    {
        $response = $this->get('/provider-register');
        $response->assertStatus(200);
    }

    public function test_forgot_password(): void
    {
        $response = $this->get('/forgotpassword-page');
        $response->assertStatus(200);
    }

    public function test_category_list(): void
    {
        $response = $this->get('/category-list');
        $response->assertStatus(200);
    }

    public function test_service_list(): void
    {
        $response = $this->get('/service-list');
        $response->assertStatus(200);
    }

    public function test_blog_list(): void
    {
        $response = $this->get('/blog-list');
        $response->assertStatus(200);
    }

    public function test_privacy_policy(): void
    {
        $response = $this->get('/privacy-policy');
        $response->assertStatus(200);
    }

    public function test_term_conditions(): void
    {
        $response = $this->get('/term-conditions');
        $response->assertStatus(200);
    }

    public function test_about_us(): void
    {
        $response = $this->get('/about-us');
        $response->assertStatus(200);
    }

    public function test_refund_policy(): void
    {
        $response = $this->get('/refund-policy');
        $response->assertStatus(200);
    }

    public function test_help_support(): void
    {
        $response = $this->get('/help-support');
        $response->assertStatus(200);
    }

    public function test_service_packages(): void
    {
        $response = $this->get('/service-packages');
        $response->assertStatus(200);
    }

    public function test_provider_list(): void
    {
        $response = $this->get('/provider-list');
        $response->assertStatus(200);
    }
}
