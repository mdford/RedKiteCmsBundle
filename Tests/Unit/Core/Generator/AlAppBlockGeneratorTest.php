<?php
/*
 * This file is part of the AlphaLemon CMS Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.alphalemon.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace AlphaLemon\AlphaLemonCmsBundle\Tests\Unit\Core\Generator;

use AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Generator\Base\AlAppGeneratorBase;
use AlphaLemon\AlphaLemonCmsBundle\Core\Generator\AlAppBlockGenerator;
use org\bovigo\vfs\vfsStream;

/**
 * AlAppBlockGeneratorTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class AlAppBlockGeneratorTest extends AlAppGeneratorBase
{
    private $blockGenerator;

    protected function setUp()
    {
        parent::setUp();

        $this->blockGenerator = new AlAppBlockGenerator($this->fileSystem, vfsStream::url('root'));
    }

    public function testBlockBundleIsGenerated()
    {
        $options = array(
            'description' => 'Fake block',
            'group' => 'fake-group',
            'strict' => false
        );
        $this->blockGenerator->generateExt('AlphaLemon\\Block\\FakeBlockBundle', 'FakeBlockBundle', vfsStream::url('root/src'), 'xml', '', $options);

        $expected = '<?php' . PHP_EOL;
        $expected .= '/*' . PHP_EOL;
        $expected .= ' * An AlphaLemonCms Block' . PHP_EOL;
        $expected .= ' */' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= 'namespace AlphaLemon\Block\FakeBlockBundle\Core\Block;' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= 'use AlphaLemon\AlphaLemonCmsBundle\Core\Content\Block\AlBlockManager;' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '/**' . PHP_EOL;
        $expected .= ' * Description of AlBlockManagerFakeBlock' . PHP_EOL;
        $expected .= ' */' . PHP_EOL;
        $expected .= 'class AlBlockManagerFakeBlock extends AlBlockManager' . PHP_EOL;
        $expected .= '{' . PHP_EOL;
        $expected .= '    public function getDefaultValue()' . PHP_EOL;
        $expected .= '    {' . PHP_EOL;
        $expected .= '        return array(\'HtmlContent\' => \'<p>Default content</p>\');' . PHP_EOL;
        $expected .= '    }' . PHP_EOL;
        $expected .= '}';

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Core/Block/AlBlockManagerFakeBlock.php');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $expected = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        $expected .= '<container xmlns="http://symfony.com/schema/dic/services"' . PHP_EOL;
        $expected .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $expected .= '        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '    <parameters>' . PHP_EOL;
        $expected .= '        <parameter key="fake_block.editor_settings" type="collection">' . PHP_EOL;
        $expected .= '            <parameter key="rich_editor">true</parameter>' . PHP_EOL;
        $expected .= '        </parameter>' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '        <parameter key="fake_block.block.class">AlphaLemon\Block\FakeBlockBundle\Core\Block\AlBlockManagerFakeBlock</parameter>' . PHP_EOL;
        $expected .= '    </parameters>' . PHP_EOL;
        $expected .= PHP_EOL;
        $expected .= '    <services>' . PHP_EOL;
        $expected .= '        <service id="fake_block.block" class="%fake_block.block.class%">' . PHP_EOL;
        $expected .= '            <argument type="service" id="alpha_lemon_cms.events_handler" />' . PHP_EOL;
        $expected .= '            <tag name="alphalemon_cms.blocks_factory.block" description="Fake block" type="FakeBlock" group="fake-group" />' . PHP_EOL;
        $expected .= '        </service>' . PHP_EOL;
        $expected .= '    </services>' . PHP_EOL;
        $expected .= '</container>';

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/app-block.xml');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $expected = 'imports:' . PHP_EOL;
        $expected .= '- { resource: "@FakeBlockBundle/Resources/config/app_block.xml" }';

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms.yml');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms_dev.yml');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms_test.yml');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $expected = '{' . PHP_EOL;
        $expected .= '    "bundles" : {' . PHP_EOL;
        $expected .= '        "AlphaLemon\\\\Block\\\\FakeBlockBundle\\\\FakeBlockBundle" : {' . PHP_EOL;
        $expected .= '            "environments" : ["all"]' . PHP_EOL;
        $expected .= '        }' . PHP_EOL;
        $expected .= '    }' . PHP_EOL;
        $expected .= '}';

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/autoload.json');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $this->assertFileNotExists(vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/composer.json'));

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/views/Block/fake_block_editor.html.twig');
        $this->assertFileExists($file);
        $this->assertEquals('{% extends \'AlphaLemonCmsBundle:Block:base_editor.html.twig\' %}', file_get_contents($file));
    }

    public function testBlockBundleIsGeneratedUsingStrictMode()
    {
        $options = array(
            'description' => 'Fake block',
            'group' => 'fake-group',
            'strict' => true
        );
        $this->blockGenerator->generateExt('AlphaLemon\\Block\\FakeBlockBundle', 'FakeBlockBundle', vfsStream::url('root/src'), 'xml', '', $options);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Core/Block/AlBlockManagerFakeBlock.php');
        $this->assertFileExists($file);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/app-block.xml');
        $this->assertFileExists($file);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms.yml');
        $this->assertFileExists($file);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms_dev.yml');
        $this->assertFileExists($file);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/config/config_alcms_test.yml');
        $this->assertFileExists($file);

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/autoload.json');
        $this->assertFileExists($file);

        $expected = '{' . PHP_EOL;
        $expected .= '    "autoload": {' . PHP_EOL;
        $expected .= '        "psr-0": { "AlphaLemon\\\\Block\\\\FakeBlockBundle\\\\FakeBlockBundle": ""' . PHP_EOL;
        $expected .= '        }' . PHP_EOL;
        $expected .= '    },' . PHP_EOL;
        $expected .= '    "target-dir" : "AlphaLemon/Block/FakeBlockBundle",' . PHP_EOL;
        $expected .= '    "minimum-stability": "dev"' . PHP_EOL;
        $expected .= '}';
        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/composer.json');
        $this->assertFileExists($file);
        $this->assertEquals($expected, file_get_contents($file));

        $file = vfsStream::url('root/src/AlphaLemon/Block/FakeBlockBundle/Resources/views/Block/fake_block_editor.html.twig');
    }
}