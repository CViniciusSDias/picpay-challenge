<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Bridge\Doctrine\Types\UlidType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329015326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');

        $table->addColumn('id', UlidType::NAME);
        $table->addColumn('full_name', 'string');
        $table->addColumn('email', 'string');
        $table->addColumn('password', 'string');
        $table->addColumn('document', 'string');
        $table->addColumn('user_type', 'string');

        $table->setPrimaryKey(['id'])
            ->addUniqueConstraint(['document'])
            ->addUniqueConstraint(['email']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
