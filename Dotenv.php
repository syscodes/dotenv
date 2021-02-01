<?php 

/**
 * Lenevor Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file license.md.
 * It is also available through the world-wide-web at this URL:
 * https://lenevor.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@Lenevor.com so we can send you a copy immediately.
 *
 * @package     Lenevor
 * @subpackage  Base
 * @link        https://lenevor.com
 * @copyright   Copyright (c) 2019 - 2021 Alexander Campo <jalexcam@gmail.com>
 * @license     https://opensource.org/licenses/BSD-3-Clause New BSD license or see https://lenevor.com/license or see /license.md
 */

namespace Syscodes\Dotenv;

use InvalidArgumentException;
use Syscodes\Dotenv\Loader\Loader;
use Syscodes\Dotenv\Store\StoreBuilder;
use Syscodes\Dotenv\Repository\RepositoryCreator;

/**
 * Manages .env files.
 * 
 * @author Alexander Campo <jalexcam@gmail.com>
 */
final class Dotenv
{
    /**
     * The Loader instance.
     * 
     * @var \Syscodes\Dotenv\Loader\Loader $loader
     */
    protected $loader;

    /**
     * The Repository creator instance.
     * 
     * @var \Syscodes\Dotenv\Repository\RepositoryCreator $repository
     */
    protected $repository;

    /**
     * The file store instance.
     * 
     * @var \Syscodes\Dotenv\Repository\FileStore $store
     */
    protected $store;

    /**
     * Activate use of putenv, by default is true.
     * 
     * @var bool $usePutenv 
     */
    protected $usePutenv = true;

    /**
     * Constructor. Create a new Dotenv instance.
     * 
     * @param  \Syscodes\Dotenv\Store\StoreBuilder  $store
     * @param  \Syscodes\Dotenv\Loader\Loader  $loader
     * @param  \Syscodes\Dotenv\Repository\RepositoryCreator  $repository
     * 
     * @return void
     */
    public function __construct($store, Loader $loader, $repository)
    {
        $this->store      = $store;
        $this->loader     = $loader;
        $this->repository = $repository;
    }

    /**
     * Create a new Dotenv instance.
     * Builds the path to our file.
     * 
     * @param  \Syscodes\Dotenv\Repository\RepositoryCreator  $repository
     * @param  string|string[]  $path
     * @param  string|string[]  $names
     * @param  bool  $modeEnabled  (true by default)
     * 
     * @return \Syscodes\Dotenv\Dotenv
     */
    public static function create($repository, $paths, $names = null, bool $modeEnabled = true)
    {
        $store = $names === null ? StoreBuilder::createWithDefaultName() : StoreBuilder::createWithNoNames();

        foreach ((array) $paths as $path) {
            $store = $store->addPath($path);
        }
        
        foreach ((array) $names as $name) {
            $store = $store->addName($name);
        }

        if ($modeEnabled) {
            $store = $store->modeEnabled();
        }

        return new self($store->make(), new Loader($repository), $repository);
    }

    /**
     * Will load the .env file and process it. So that we end all settings in the PHP 
     * environment vars: getenv(), $_ENV, and $_SERVER.
     * 
     * @return bool
     */
    public function load()
    {        
        return $this->loader->load($this->store->read());
    }
}