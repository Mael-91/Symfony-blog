<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

// TODO A revoir
class CacheService {

    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(AdapterInterface $cache) {

        $this->cache = $cache;
    }

    public function setCache(string $key, $value) {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            $item->set($value);
            $this->cache->save($item);
        }

        return $item->get();
    }
}