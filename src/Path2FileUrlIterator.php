<?php

namespace alcamo\filesystem;

use alcamo\exception\AbsolutePathNeeded;

/// Convert paths to file:// URLs
class Path2FileUrlIterator extends \IteratorIterator
{
    private $isWindows_; ///< bool

    public function __construct(\Iterator $iterator, ?string $osFamily = null)
    {
        parent::__construct($iterator);

        $this->isWindows_ = ($osFamily ?? PHP_OS_FAMILY) == 'Windows';
    }

    /** @throw alcamo::exception::AbsolutePathNeeded if current() is not an
     *  absolute path. */
    public function current()
    {
        $current = parent::current();

        if ($this->isWindows_) {
            if ($current[1] != ':') {
                throw (new AbsolutePathNeeded())->setMessageContext(
                    [ 'path' => $current ]
                );
            }

            return 'file:///' . str_replace(
                [ '%3A', '%5C' ],
                [ ':', '/' ],
                rawurlencode($current)
            );
        } else {
            if ($current[0] != '/') {
                throw (new AbsolutePathNeeded())->setMessageContext(
                    [ 'path' => $current ]
                );
            }

            return 'file://' . str_replace('%2F', '/', rawurlencode($current));
        }
    }
}
