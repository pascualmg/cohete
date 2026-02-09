<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTablePost extends AbstractMigration
{
    public function change(): void
    {
        /**
         * Respecto al id ...
         * En lugar de utilizar una columna de identificación autoincremental,
         * esta migración utiliza una columna de identificación personalizada de tipo UUID.
         *
         * Debido a que MySQL no proporciona un tipo de columna `UUID` nativo, los UUIDs se almacenan
         * como `CHAR(36)`. Por lo tanto, en Phinx, se debe usar el tipo `string` con un límite de 36 para
         * representar el UUID.
         *
         * La opción ['id' => false] en el método `table` impide que Phinx cree automáticamente una
         * columna de identificación. En cambio, se crea explícitamente una columna `id` con un
         * límite de 36 caracteres para almacenar el UUID.
         *
         * La columna `id` es también la clave primaria de la tabla, y MySQL crea automáticamente
         * un índice para cualquier columna configurada como clave primaria. Esto garantiza que las
         * búsquedas y otros tipos de queries en la columna `id` son eficientes.
         *
         * Para generar UUIDs para nuevos registros en la tabla, se puede utilizar una biblioteca
         * como ramsey/uuid.
         *
         * Generado por AI Assistant
         */
        $table = $this->table('post', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('headline', 'string', ['limit' => 255])
            ->addColumn('articleBody', 'text')
            ->addColumn('author', 'string', ['limit' => 255])
            ->addColumn('datePublished', 'datetime')
            ->create();
    }
}
