<?php
namespace Rs\Json;

use Rs\Json\Patch;

class PatchTestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnTargetDocumentWhenTestFails()
    {
        $expectedDocument = $targetDocument = '{"foo":"bar"}';
        $patchDocument = '[
          {"op":"test", "path":"/baz", "value":"qux"},
          {"op":"add", "path":"/baz", "value":"qux"}
        ]';

        $patch = new Patch($targetDocument, $patchDocument);
        $patchedDocument = $patch->apply();

        $this->assertJsonStringEqualsJsonString(
            $expectedDocument,
            $patchedDocument
        );
    }
    /**
     * @test
     */
    public function shouldReturnTargetDocumentWhenTestFailsForPriorPatch()
    {
        $expectedDocument = $targetDocument = '{"a":{"b":{"c": 100}}}';
        $patchDocument = '[
          {"op":"replace", "path":"/a/b/c", "value":42 },
          {"op":"test", "path":"/a/b/c", "value":"C" }
        ]';

        $patch = new Patch($targetDocument, $patchDocument);
        $patchedDocument = $patch->apply();

        $this->assertJsonStringEqualsJsonString(
            $expectedDocument,
            $patchedDocument
        );
    }
    /**
     * @test
     */
    public function shouldReturnTargetDocumentWhenUsingPointerEscapes()
    {
        $expectedDocument = $targetDocument = '{"/": 9, " ~1": 10}';
        $patchDocument = '[ {"op":"test", "path":"/~01", "value": 10} ]';

        $patch = new Patch($targetDocument, $patchDocument);
        $patchedDocument = $patch->apply();

        $this->assertJsonStringEqualsJsonString(
            $expectedDocument,
            $patchedDocument
        );
    }
    /**
     * @test
     */
    public function shouldReturnTargetDocumentWhenTestFailsForUnsuccessfulComparison()
    {
        $expectedDocument = $targetDocument = '{"/": 9, " ~1": 10}';
        $patchDocument = '[ {"op":"test", "path":"/~01", "value":"10"} ]';

        $patch = new Patch($targetDocument, $patchDocument);
        $patchedDocument = $patch->apply();

        $this->assertJsonStringEqualsJsonString(
            $expectedDocument,
            $patchedDocument
        );
    }
    /**
     * @test
     */
    public function shouldAllowUsageOfUriFragmentIdentifierInPatch()
    {
        $targetDocument = '{"foo":"bar"}';
        $patchDocument = '[{"op":"add", "path":"#/baz", "value":[1,2,3]}]';
        $expectedDocument = '{"foo":"bar","baz":[1,2,3]}';

        $patch = new Patch($targetDocument, $patchDocument);
        $patchedDocument = $patch->apply();

        $this->assertJsonStringEqualsJsonString(
            $expectedDocument,
            $patchedDocument
        );
    }
}