<?php

namespace Owlcoder\Taxonomy\Tests;

use Owlcoder\Taxonomy\Facades\Taxonomy;
use Owlcoder\Taxonomy\ServiceProvider;
use Orchestra\Testbench\TestCase;

class TaxonomyTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'taxonomy' => Taxonomy::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
