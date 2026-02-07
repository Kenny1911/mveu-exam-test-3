<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260207072653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create admin user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'INSERT INTO users VALUES(:id, :username, :password, :first_name, :last_name, :middle_name, :email, :phone, :roles)',
            [
                'id' => '019b16c4-63cb-7979-9e70-3450008137bf',
                'username' => 'adminka',
                'password' => '$2y$13$V.9Cat9f/pRnFh3TsCQEfeg8.08sfgMRgvHOSaHjTbbkzUHpB5Bo6',
                'first_name' => 'Админ',
                'last_name' => 'Админ',
                'middle_name' => '',
                'email' => 'admin@mail.local',
                'phone' => '+7(123)-456-78-90',
                'roles' => json_encode(['ROLE_ADMIN']),
            ],
        );

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM users WHERE id = \'019b16c4-63cb-7979-9e70-3450008137bf\'');
    }
}
