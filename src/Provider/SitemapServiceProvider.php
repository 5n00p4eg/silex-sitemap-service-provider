<?php

namespace TM\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TM\Service\SitemapGenerator;

class SitemapServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $options = [
            'xml_writer' => new \XMLWriter,
            'version' => '1.0',
            'charset' => 'utf-8',
            'scheme' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
        ];

        if (isset($app['sitemap.options']) && is_array($app['sitemap.options'])) {
            $options = array_merge($options, $app['sitemap.options']);
        }

        $app['sitemap'] = function () use ($options) {
            return new SitemapGenerator(
                $options['xml_writer'], $options['version'], $options['charset'], $options['scheme']
            );
        };
        $image_options = [
            'xml_writer' => new \XMLWriter,
            'version' => '1.0',
            'charset' => 'utf-8',
            'scheme' => array(
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
                'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1'
            ),
        ];

        if (isset($app['sitemap.image_options']) && is_array($app['sitemap.image_options'])) {
            $image_options = array_merge($image_options, $app['sitemap.image_options']);
        }

        $app['sitemap_image'] = function () use ($image_options) {
            return new SitemapGenerator(
              $image_options['xml_writer'], $image_options['version'], $image_options['charset'], $image_options['scheme']
            );
        };

    }
}
