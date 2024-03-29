<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UlidType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329020346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create transactions table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('transactions');

        $table->addColumn('id', UlidType::NAME);
        $table->addColumn('sender_id', UlidType::NAME);
        $table->addColumn('receiver_id', UlidType::NAME);
        $table->addColumn('value_in_cents', 'integer');
        $table->addColumn('created_at', 'datetime_immutable');

        $table->setPrimaryKey(['id'])
            ->addForeignKeyConstraint('users', ['sender_id'], ['id'])
            ->addForeignKeyConstraint('users', ['receiver_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('transactions');
    }
}
