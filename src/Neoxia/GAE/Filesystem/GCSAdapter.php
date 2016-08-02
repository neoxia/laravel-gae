<?php

namespace Neoxia\GAE\Filesystem;

use League\Flysystem\Adapter\Local as LocalAdapter;
use SplFileInfo;

class GCSAdapter extends LocalAdapter
{
    public function __construct(
        $root,
        $writeFlags = LOCK_EX,
        $linkHandling = self::DISALLOW_LINKS,
        array $permissions = []
    ) {
        try {
            parent::__construct($root, null, $linkHandling, $permissions);
        } catch (\LogicException $e) {
            $this->setPathPrefix($root);
            $this->writeFlags = $writeFlags;
        }
    }

    protected function ensureDirectory($root)
    {
        return $root;
    }

    protected function deleteFileInfoObject(SplFileInfo $file)
    {
        if ($file->getType() === 'dir') {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
}
