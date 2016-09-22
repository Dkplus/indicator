<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use Doctrine\DBAL\Schema\Schema;

class IssueTable
{
    const TABLE_NAME = 'indicator_issues';

    public static function upToV1(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('id', 'string', ['length' => 36]);
        $table->addColumn('reporter_id', 'string', ['length' => 36]);
        $table->addColumn('title', 'string', ['length' => 255]);
        $table->addColumn('text', 'text', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['reporter_id']);
    }

    public static function downFromV1(Schema $schema)
    {
        $schema->dropTable(self::TABLE_NAME);
    }

    public static function upToV2(Schema $schema)
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->addColumn('issue_number', 'string', ['length' => 255]);
        $table->addColumn('external_service_id', 'string', ['length' => 255]);
        $table->addColumn('state', 'string', ['length' => 50]);
        $table->addColumn('type', 'string', ['length' => 50]);
    }

    public static function downFromV2(Schema $schema)
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->dropColumn('issue_number');
        $table->dropColumn('external_service_id');
        $table->dropColumn('state');
        $table->dropColumn('type');
    }

    public static function upToV3(Schema $schema)
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->addColumn('open', 'boolean');
    }

    public static function downFromV3(Schema $schema)
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->dropColumn('open');
    }
}
