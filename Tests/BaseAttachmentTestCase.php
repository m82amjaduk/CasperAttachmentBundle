<?php
namespace Casper\AttachmentBundle\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BaseAttachmentTestCase extends WebTestCase
{
    public static $client;

    protected $em;

    protected $testEntities = array();

    public function setUp()
    {
        self::$client = $this->createClient();
    }

    protected function assertCountFiles($dir, $count, $msg = '')
    {
        $i = 0;
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false){
                if (!in_array($file, array('.', '..')) && !is_dir($dir.$file))
                    $i++;
            }
        }

        $this->assertEquals($i, $count, $msg);
    }

    protected function installTestEntities($names)
    {
        $names = (array) $names;
        foreach ($names as $name) {
            /** @noinspection PhpIncludeInspection */
            require_once __DIR__ . "/Fixtures/$name.php";
            $this->testEntities[] = "CasperAttachmentBundle:$name";
        }
    }

    protected function createUploadedImagePng($name)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(10, 10), $tmpFile);

        return new UploadedFile($tmpFile, "$name.png", null, null, null, true);
    }

    protected function rmDir($dir)
    {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) $this->rmDir("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }

    /**
     * @return EntityManager
     */
    public function getMockEntityManager()
    {
        if (!$this->em) {
            $db = __DIR__ . '/Fixtures/data.db3';
            if (!file_exists($db)) {
                touch($db);
            }
            $defEm = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
            $conn = self::$kernel
                ->getContainer()
                ->get('doctrine.dbal.connection_factory')
                ->createConnection(
                    array(
                        'driver'    => 'pdo_sqlite',
                        'user'     => 'root',
                        'password' => '',
                        'host'     => 'localhost',
                        'dbname'   => 'test',
                        'path'     => $db,
                    ),
                    $defEm->getConfiguration(),
                    $defEm->getEventManager()
                );
            $em = EntityManager::create($conn, $defEm->getConfiguration(), $defEm->getEventManager());
            self::$kernel->getContainer()->set('doctrine.orm.entity_manager', $em);

            $schema = array();
            foreach ($this->testEntities as $class) {
                $schema[] = $em->getClassMetadata($class);
            }

            $tool = new SchemaTool($em);
            $tool->dropSchema($schema);
            $tool->createSchema($schema);
            $tool->updateSchema($schema);

            $this->em = $em;
        }

        return $this->em;
    }

    protected function setNonPublicProperty(&$object, $name, $value)
    {
        $refObject   = new \ReflectionObject($object);
        $refProperty = $refObject->getProperty($name);
        $refProperty->setAccessible(true);
        $refProperty->setValue($object, $value);
    }

    protected function callNonPublicMethod($object, $name, $args = array())
    {
        $class = new \ReflectionClass($object);

        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}