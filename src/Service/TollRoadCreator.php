<?php

namespace Maris\Symfony\TollRoad\Service;

use Maris\Symfony\Geo\Factory\LocationFactory;
use Maris\Symfony\Geo\Service\GeoCalculator;
use Maris\Symfony\Geo\Service\SphericalCalculator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use function PHPUnit\Framework\directoryExists;

/****
 * Создает файлы с платными дорогами
 */
class TollRoadCreator
{

    protected string $dir;

    protected string $bundleDir;

    protected GeoCalculator $calculator;

    /**
     * @param string $dir Директория новых yaml файлов с платными дорогами
     * @param GeoCalculator $calculator
     */
    public function __construct( string $dir , GeoCalculator $calculator )
    {
        $this->dir = rtrim(
            str_replace(['/', '\\'],DIRECTORY_SEPARATOR,$dir ),
            DIRECTORY_SEPARATOR
        );
        $this->dir = rtrim(str_replace($this->getBundleDir(),"",$this->dir),DIRECTORY_SEPARATOR);
        $this->calculator = $calculator;
    }

    /**
     * Возвращает директорию в которой расположен пакет.
     * @return string
     */
    protected function getBundleDir(): string
    {
        return realpath(dirname(__DIR__ ,count(explode("\\",self::class)) - 3 ));
    }




    public function recursiveIterateFiles( string $file, callable $handler ):void
    {
        $file = realpath($file);
        $path = ( is_dir($file))? $file : dirname($file);

        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($file) );

        /**@var SplFileInfo $item**/
        foreach ($iterator as $item) {
            if ($item->isFile() && str_contains( $item->getLinkTarget(), $path ))
                $handler( $item );
        }
    }

    /**
     * Путь к файлу
     * @param string $file Путь к файлу
     * @return void
     */
    public function create( string $file ):void
    {
        $path = realpath(( is_dir($file)) ? $file : dirname($file));
        $this->recursiveIterateFiles( $file,function ( SplFileInfo $item) use ( $file, $path )
        {

            $newFile = implode(DIRECTORY_SEPARATOR,[
                $this->getBundleDir(),
                ltrim($this->dir,DIRECTORY_SEPARATOR),
                ltrim(
                    str_replace( $path, "",
                        str_replace($path,"",$item->getLinkTarget() )
                    ),DIRECTORY_SEPARATOR
                )
            ]);

            if(!file_exists( dirname($newFile) ))
                mkdir( dirname($newFile) ,recursive: true);

            file_put_contents(
                $newFile,
                $this->createDataFile( $item->getLinkTarget() )
            );
        });
    }

    /***
     * Создает данные для записи в файл
     * @param string $file Ссылка на файл из которого нужно прочитать данные для создания нового файла
     * @return string
     */
    protected function createDataFile( string $file ):string
    {
        $data = Yaml::parseFile( $file );

        $calculator = new SphericalCalculator();
        $locationFactory = new LocationFactory();

        $data["bearing"] = $calculator->getInitialBearing(
            $locationFactory->create($data["location"]),
            $locationFactory->create($data["indicator"])
        );
        return Yaml::dump($data);
    }


    public function renameFile( $file ):void
    {dump(realpath($file));
        if( ($file = realpath($file)) === false )
            return;
dump($file);
        $this->recursiveIterateFiles( $file,function ( SplFileInfo $fileInfo ) use ($file) {
            $base = $fileInfo->getBasename();
            $dir = $fileInfo->getPath();
            $base = str_replace(" ",'', $base);
            $replace = [
                "(наМоскву)" => "-to-Moscow",
                "(наМинск)" => "-to-Minsk",
                "(наКраснодар)" => "-to-Krasnodar",
                "(наКиев)"=>"-to-Kiev",
                "(наПластуновскую)" => "-to-Plastunovskay",
                "(наМарьинскую)" => "-to-Marenskay"

            ];
            $base = str_replace(array_keys($replace),array_values($replace),$base);

            rename( $fileInfo->getRealPath(), $dir."/".$base );
        });


        /*if(!file_exists($file))
            return;

        if(is_dir( $file )){
            /***@var DirectoryIterator $fileInfo **/
            /*foreach (new DirectoryIterator($file) as $fileInfo)
                if( $fileInfo->isFile() )
                    $this->renameFile( $fileInfo->getLinkTarget() );
            return;
        }

        $base = basename($file);
        $dir = dirname($file);


        $base = str_replace(" ",'', $base);

        $replace = [
            "(наМоскву)" => "-to-Moscow",
            "(наМинск)" => "-to-Minsk",
            "(наКраснодар)" => "-to-Krasnodar",
            "(наКиев)"=>"-to-Kiev",
            "(наПластуновскую)" => "-to-Plastunovskay",
            "(наМарьинскую)" => "-to-Marenskay"

        ];
        $base = str_replace(array_keys($replace),array_values($replace),$base);

        rename($file, $dir."/".$base);*/

    }
}