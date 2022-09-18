<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Loads test json located in tests/files
     */
    protected function loadTestJson($filename): object
    {
        return json_decode($this->loadTestFile($filename));
    }

    protected function loadTestFile($filename): string
    {
        return file_get_contents(test_path('files/'.$filename));
    }

}
