<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers DatabaseQuery
 * @covers DatabaseStatementResult
 * @covers DatabaseTabularResult
 */
final class DatabaseQueryIntegrationTest extends TestCase
{
    private function createSingleRowQuery()
    {
        return Symphony::Database()
            ->select()
            ->from('tbl_authors')
            ->where(['id' => 1])
            ->execute();
    }

    public function testNext()
    {
        $result = $this->createSingleRowQuery();
        $this->assertArrayHasKey('id', $result->next(), 'First record as the id key');
        $this->assertNull($result->next(), 'Second result is null');
    }

    public function testRows()
    {
        $rows = $this->createSingleRowQuery()->rows();
        $this->assertCount(1, $rows, 'Returns a single row');
    }

    /**
     * @expectedException DatabaseStatementException
     */
    public function testTooManyNext()
    {
        $result = $this->createSingleRowQuery();
        $this->assertNotNull($result->next(), 'First is not null');
        $this->assertNull($result->next(), 'Second result is null');
        $result->next(); // This one should throw!
    }
}
