<?php

use PHPUnit\Framework\TestCase;

use Illuminate\Database\Query\Expression;

require(__DIR__ . "/Database.php");

final class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        Visit::truncate();
    }

    public function testTop()
    {
        $this->createCity('San Francisco', 3);
        $this->createCity('Chicago', 2);
        $expected = [
            'San Francisco' => 3,
            'Chicago' => 2
        ];
        $this->assertEquals($expected, Visit::top('city'));
    }

    public function testLimit()
    {
        $this->createCity('San Francisco', 3);
        $this->createCity('Chicago', 2);
        $this->createCity('Boston', 1);
        $expected = [
            'San Francisco' => 3,
            'Chicago' => 2
        ];
        $this->assertEquals($expected, Visit::top('city', 2));
    }

    public function testNullValues()
    {
        $this->createCity('San Francisco', 3);
        $this->createCity(null, 2);
        $expected = [
            'San Francisco' => 3
        ];
        $this->assertEquals($expected, Visit::top('city'));
    }

    public function testNullOption()
    {
        $this->createCity('San Francisco', 3);
        $this->createCity(null, 2);
        $expected = [
            'San Francisco' => 3,
            '' => 2
        ];
        $this->assertEquals($expected, Visit::top('city', null: true));
    }

    public function testExpression()
    {
        $this->createCity('San Francisco');
        $expected = [
            'san francisco' => 1
        ];
        $this->assertEquals($expected, Visit::top(new Expression('lower(city)')));
    }

    public function testExpressionNoExpression()
    {
        $this->expectNeedsExpression();

        Visit::top('lower(city)');
    }

    public function testDistinct()
    {
        Visit::create(['city' => 'San Francisco', 'user_id' => '123']);
        Visit::create(['city' => 'San Francisco', 'user_id' => '123']);
        $expected = [
            'San Francisco' => 1
        ];
        $this->assertEquals($expected, Visit::top('city', distinct: 'user_id'));
    }

    public function testDistinctExpression()
    {
        Visit::create(['city' => 'San Francisco', 'user_id' => 'A']);
        Visit::create(['city' => 'San Francisco', 'user_id' => 'a']);
        Visit::create(['city' => 'San Francisco', 'user_id' => 'B']);
        $expected = [
            'San Francisco' => 2
        ];
        $this->assertEquals($expected, Visit::top('city', distinct: new Expression('lower(user_id)')));
    }

    public function testDistinctExpressionNoExpression()
    {
        $this->expectNeedsExpression();

        Visit::top('city', distinct: 'lower(user_id)');
    }

    public function testMin()
    {
        $this->createCity('San Francisco', 3);
        $this->createCity('Chicago', 2);
        $expected = [
            'San Francisco' => 3
        ];
        $this->assertEquals($expected, Visit::top('city', min: 3));
    }

    public function testMinDistinct()
    {
        Visit::create(['city' => 'San Francisco', 'user_id' => '1']);
        Visit::create(['city' => 'San Francisco', 'user_id' => '1']);
        Visit::create(['city' => 'San Francisco', 'user_id' => '2']);
        Visit::create(['city' => 'Chicago', 'user_id' => '1']);
        Visit::create(['city' => 'Chicago', 'user_id' => '1']);
        $expected = [
            'San Francisco' => 2
        ];
        $this->assertEquals($expected, Visit::top('city', min: 2, distinct: 'user_id'));
    }

    public function testWhere()
    {
        $this->createCity('San Francisco');
        $this->createCity('Chicago');
        $expected = [
            'San Francisco' => 1
        ];
        $this->assertEquals($expected, Visit::where('city', 'San Francisco')->top('city'));
    }

    private function createCity($city, $count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            Visit::create(['city' => $city]);
        }
    }

    private function expectNeedsExpression()
    {
        $adapter = getenv('ADAPTER');
        if ($adapter == 'pgsql') {
            $this->expectExceptionMessage('Undefined column');
        } elseif ($adapter == 'mysql') {
            $this->expectExceptionMessage('Unknown column');
        } else {
            // sqlite allows double-quoted string literals by default
            // https://www.sqlite.org/quirks.html
            $this->assertTrue(true);
        }
    }
}
