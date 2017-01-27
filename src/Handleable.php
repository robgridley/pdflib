<?php

namespace Pdf;

interface Handleable
{
    /**
     * Get PDFlib handle for the instance.
     *
     * @return int
     */
    public function getHandle();
}
