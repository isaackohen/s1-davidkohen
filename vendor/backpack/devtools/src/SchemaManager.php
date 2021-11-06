<?php

namespace Backpack\DevTools;

use DB;
use Doctrine\DBAL\Types\Types;

class SchemaManager
{
    public $manager;

    public function __construct()
    {
        $this->manager = DB::connection()->getDoctrineSchemaManager();
        $platform = $this->manager->getDatabasePlatform();

        $doctrineTypeMapping = [
            'enum' => Types::STRING,
            'geometry' => Types::STRING,
            'point' => Types::STRING,
            'lineString' => Types::STRING,
            'polygon' => Types::STRING,
            'multiPoint' => Types::STRING,
            'multiLineString' => Types::STRING,
            'multiPolygon' => Types::STRING,
            'geometryCollection' => Types::STRING,
        ];

        foreach ($doctrineTypeMapping as $key => $value) {
            $this->manager->getDatabasePlatform()->registerDoctrineTypeMapping($key, $value);
        }
    }

    public function getManager()
    {
        return $this->manager;
    }
}
