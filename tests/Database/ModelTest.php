<?php

namespace Lune\Tests\Database;

use Lune\Database\DB;
use Lune\Database\Model;
use PHPUnit\Framework\TestCase;

class MockModel extends Model {
    public function set($properties) {
        $this->setProperties($properties);
    }
}

class MockModelFillable extends MockModel {
    protected ?string $table = "mock_models";
    protected array $fillable = ["test", "name"];
}

/**
 * @requires extension mysqli
 */
class ModelTest extends TestCase {
    use RefreshDatabase;

    protected function createMockModelsTable($columns, $withTimeStamp = true) {
        $sql = "CREATE TABLE mock_models (id INT AUTO_INCREMENT PRIMARY KEY, "
            . implode(", ", array_map(fn ($c) => "$c VARCHAR(256)", $columns));

        if ($withTimeStamp) {
            $sql .= ", created_at DATETIME, updated_at DATETIME NULL";
        }

        $sql .= ")";

        DB::statement($sql);
    }

    public function testSaveBasicModelWithAttributes() {
        $this->createMockModelsTable(["test", "name"]);

        $model = new MockModel();
        $model->test = "Test";
        $model->name = "Name";
        $model->save();
        $rows = DB::statement("SELECT * FROM mock_models");

        $expected = [
            "id" => 1,
            "test" => "Test",
            "name" => "Name",
            "created_at" => date("Y-m-d H:m:s"),
            "updated_at" => null,
        ];

        $this->assertEquals(1, count($rows));
        $this->assertEquals($expected, $rows[0]);
    }

    /**
     * @depends testSaveBasicModelWithAttributes
     */
    public function testFindModel() {
        $this->createMockModelsTable(["test", "name"]);

        $expected = [
            [
                "id" => 1,
                "test" => "Test",
                "name" => "Name",
                "created_at" => date("Y-m-d H:m:s"),
                "updated_at" => null,
            ],
            [
                "id" => 2,
                "test" => "Foo",
                "name" => "Bar",
                "created_at" => date("Y-m-d H:m:s"),
                "updated_at" => null,
            ],
        ];

        foreach ($expected as $columns) {
            $model = new MockModel();
            $model->test = $columns["test"];
            $model->name = $columns["name"];
            $model->save();
        }

        foreach ($expected as $columns) {
            $model = new MockModel();
            $model->set($columns);
            $this->assertEquals($model, MockModel::find($columns["id"]));
        }

        $this->assertNull(MockModel::find(5));
    }

    /**
     * @depends testSaveBasicModelWithAttributes
     */
    public function testCreateModelWithNoFillableAttributesThrowsError() {
        $this->expectException(\BadMethodCallException::class);
        MockModel::create(["test" => "test"]);
    }

    /**
     * @depends testCreateModelWithNoFillableAttributesThrowsError
     */
    public function testCreateModel() {
        $this->createMockModelsTable(["test", "name"]);
        $model = MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        $this->assertEquals(1, count(DB::statement("SELECT * FROM mock_models")));
        $this->assertEquals("Name", $model->name);
        $this->assertEquals("Test", $model->test);
    }

    /**
     * @depends testCreateModel
     */
    public function testAll() {
        $this->createMockModelsTable(["test", "name"]);
        $model1 = MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        $model2 = MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        $model3 = MockModelFillable::create(["test" => "Test", "name" => "Name"]);

        $this->assertEquals([$model1, $model2, $model3], MockModelFillable::all());
    }

    /**
     * @depends testCreateModel
     */
    public function testWhereAndFirstWhere() {
        $this->createMockModelsTable(["test", "name"]);
        $model1 = MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        $model2 = MockModelFillable::create(["test" => "Search", "name" => "Foo"]);
        $model3 = MockModelFillable::create(["test" => "Search", "name" => "Foo"]);

        $this->assertEquals([$model2, $model3], MockModelFillable::where("test", "Search"));
        $this->assertEquals($model2, MockModelFillable::firstWhere("test", "Search"));
    }
}
