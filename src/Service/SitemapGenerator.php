<?php

namespace TM\Service;

class SitemapGenerator
{
    /**
     * @var \XMLWriter
     */
    protected $sitemap;
    protected $opened_image_url = FALSE;

    /**
     * @param \XMLWriter $xmlWriter
     * @param string     $version
     * @param string     $charset
     * @param string     $scheme
     */
    public function __construct(\XMLWriter $xmlWriter, $version = '1.0', $charset = 'utf-8', $scheme = 'http://www.sitemaps.org/schemas/sitemap/0.9')
    {
        $this->sitemap = $xmlWriter;
        $this->sitemap->openMemory();

        $this->sitemap->startDocument($version, $charset);
        $this->sitemap->setIndent(true);

        $this->sitemap->startElement('urlset');
        if (is_string($scheme)) {
            $this->sitemap->writeAttribute('xmlns', $scheme);
        } elseif (is_array($scheme)) {
            foreach ($scheme as $attr => $val) {
              $this->sitemap->writeAttribute($attr, $val);
            }
        }
        $this->sitemap->endAttribute();
    }

    /**
     * @param string    $url
     * @param float     $priority
     * @param string    $changefreq
     * @param \DateTime $lastmod
     */
    public function addEntry($url, $priority = 1.0, $changefreq = 'yearly', \DateTime $lastmod = null)
    {
        $this->sitemap->startElement('url');

        $this->sitemap->writeElement('loc', $url);
        $this->sitemap->writeElement('priority', $priority);
        $this->sitemap->writeElement('changefreq', $changefreq);

        if ($lastmod instanceof \DateTime) {
            $this->sitemap->writeElement('lastmod', $lastmod->format('Y-m-d'));
        }

        $this->sitemap->endElement();
    }

    public function startNewImageUrl($url) {
        $sm = $this->sitemap;
        if ($this->opened_image_url === TRUE) {
          $sm->endElement();
        }

        $sm->startElement('url');
        $sm->writeElement('loc', $url);
        $this->opened_image_url = TRUE;
    }

    public function addImageEntry($image, $caption = null, $geo_location = null, $title = null, $license = null) {
        $sm = $this->sitemap;
        $sm->startElement('image:image');
        $sm->writeElement('image:loc', $image);
        if ($caption) $sm->writeElement('image:caption', $caption);
        if ($geo_location) $sm->writeElement('image:geo_location', $geo_location);
        if ($title) $sm->writeElement('image:title', $title);
        if ($license) $sm->writeElement('image:license', $license);
        $sm->endElement();
    }

    /**
     * @param bool $doFlush
     *
     * @return string
     */
    public function generate($doFlush = true)
    {
        if ($this->opened_image_url === TRUE) {
          $this->sitemap->endElement();
        }
        $this->sitemap->endElement();
        $this->sitemap->endDocument();

        return $this->sitemap->outputMemory($doFlush);
    }
}
